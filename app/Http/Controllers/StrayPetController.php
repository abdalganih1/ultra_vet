<?php

namespace App\Http\Controllers;

use App\Models\StrayPet;
use App\Models\IndependentTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Barryvdh\DomPDF\Facade\Pdf;

class StrayPetController extends Controller
{
    public function __construct()
    {
        // صلاحيات الوصول الأساسية للمتحكم
        // showPublic (عرض الملف العام): متاح للجميع (بما فيهم الضيوف)
        $this->middleware('auth')->except('showPublic'); // جميع الدوال تتطلب مصادقة إلا showPublic

        // صلاحيات محددة بناءً على الأدوار
        // دوال الإدارة والتحرير (Admin & Data Entry)
        $this->middleware('role:admin,data_entry')->only([
            'index',            // قائمة الحيوانات الشاردة
            'create',           // عرض نموذج إدخال/تعديل البيانات
            'edit',             // عرض نموذج إدخال/تعديل البيانات
            'storeOrUpdate',    // حفظ البيانات
            'show',             // عرض تفاصيل (يوجه للتعديل)
            'destroy',          // حذف فردي
            'bulkDestroy',      // حذف جماعي
            'showQRGenerationForm', // عرض نموذج توليد QR
            'generateQRCodes',  // توليد QR
            'printQRCodes'      // طباعة PDF
        ]);
    }

    /**
     * عرض قائمة بجميع الحيوانات الشاردة (صفحة سجل الباركودات).
     * Accessible by: Admin, Data Entry
     */
    public function index(Request $request)
    {
        $query = StrayPet::with('qrCodeLink', 'independentTeam'); // Eager load relationships

        // Filter by data entry status
        if ($request->filled('status_filter')) {
            if ($request->status_filter === 'entered') {
                $query->where('data_entered_status', true);
            } elseif ($request->status_filter === 'pending') {
                $query->where('data_entered_status', false);
            }
        }

        // Filter by search term
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('serial_number', 'like', '%' . $search . '%')
                  ->orWhere('uuid', 'like', '%' . $search . '%')
                  ->orWhere('animal_type', 'like', '%' . $search . '%')
                  ->orWhereHas('independentTeam', function ($teamQuery) use ($search) {
                      $teamQuery->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        // Scope for non-admin users
        if (Auth::user()->isDataEntry()) {
            $query->where('independent_team_id', Auth::user()->independent_team_id);
        }
        
        $strayPets = $query->latest()->paginate(50)->withQueryString(); // Paginate 50 per page and append query string

        return view('stray_pets.index', compact('strayPets'));
    }

    /**
     * عرض نموذج إدخال/تعديل بيانات حيوان شارد.
     * Accessible by: Admin, Data Entry
     */
    public function create(Request $request, $uuid = null)
    {
        // UUID قد يأتي من الـ Route كـ parameter أو من الـ Query String
        $targetUuid = $uuid ?? $request->query('uuid');

        if (!$targetUuid) {
            return redirect()->route('qrcodes.stray.generate.form')
                             ->with('error', __('messages.data_entry_error_no_uuid'));
        }

        $strayPet = StrayPet::where('uuid', $targetUuid)->firstOrFail();
        $independentTeams = IndependentTeam::all();
        $governorates = \App\Models\Governorate::all();

        // --- Prefill Logic ---
        $lastVetName = Auth::user()->name; // Default to user's name
        $emergencyPhone = Auth::user()->phone; // Default to user's phone

        $userTeam = Auth::user()->independentTeam;

        if ($userTeam) {
            // Find the last pet entered by the same team to get the vet's name
            $lastPetByTeam = StrayPet::where('independent_team_id', $userTeam->id)
                                     ->whereNotNull('medical_supervisor_info->vet_name')
                                     ->latest('updated_at')
                                     ->first();
            
            if ($lastPetByTeam) {
                $lastVetName = $lastPetByTeam->medical_supervisor_info['vet_name'];
            }

            // Use the team's contact phone if available
            if ($userTeam->contact_phone) {
                $emergencyPhone = $userTeam->contact_phone;
            }
        }
        
        // Prefill data for the form
        $prefill = [
            'vet_name' => $strayPet->medical_supervisor_info['vet_name'] ?? $lastVetName,
            'emergency_contact_phone' => $strayPet->emergency_contact_phone ?? $emergencyPhone,
        ];

        return view('stray_pets.data_entry', compact('strayPet', 'prefill', 'independentTeams', 'governorates'));
    }

    /**
     * عرض نموذج إدخال/تعديل بيانات حيوان شارد.
     * Accessible by: Admin, Data Entry
     */
    public function edit(StrayPet $strayPet)
    {
        $independentTeams = IndependentTeam::all();
        $governorates = \App\Models\Governorate::all();

        // --- Prefill Logic ---
        $lastVetName = Auth::user()->name; // Default to user's name
        $emergencyPhone = Auth::user()->phone; // Default to user's phone

        $userTeam = Auth::user()->independentTeam;

        if ($userTeam) {
            // Find the last pet entered by the same team to get the vet's name
            $lastPetByTeam = StrayPet::where('independent_team_id', $userTeam->id)
                                     ->whereNotNull('medical_supervisor_info->vet_name')
                                     ->latest('updated_at')
                                     ->first();
            
            if ($lastPetByTeam) {
                $lastVetName = $lastPetByTeam->medical_supervisor_info['vet_name'];
            }

            // Use the team's contact phone if available
            if ($userTeam->contact_phone) {
                $emergencyPhone = $userTeam->contact_phone;
            }
        }
        
        // Prefill data for the form
        $prefill = [
            'vet_name' => $strayPet->medical_supervisor_info['vet_name'] ?? $lastVetName,
            'emergency_contact_phone' => $strayPet->emergency_contact_phone ?? $emergencyPhone,
        ];

        return view('stray_pets.data_entry', compact('strayPet', 'prefill', 'independentTeams', 'governorates'));
    }

    /**
     * حفظ (تحديث) بيانات حيوان شارد.
     * Accessible by: Admin, Data Entry
     */
    public function storeOrUpdate(Request $request)
    {
        $request->validate([
            'uuid' => 'required|uuid|exists:stray_pets,uuid',
            // Arabic fields
            'city_province' => 'nullable|string|max:255',
            'relocation_place' => 'nullable|string|max:255',
            'breed_name' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'distinguishing_marks' => 'nullable|string',
            // English fields
            'city_province_en' => 'nullable|string|max:255',
            'relocation_place_en' => 'nullable|string|max:255',
            'breed_name_en' => 'nullable|string|max:255',
            'color_en' => 'nullable|string|max:255',
            'distinguishing_marks_en' => 'nullable|string',
            // Other fields
            'animal_type' => 'nullable|string|max:255',
            'custom_animal_type' => 'nullable|string|max:255',
            'gender' => 'nullable|string|max:255',
            'estimated_age' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'emergency_contact' => 'nullable|string|max:255',
            'vetName' => 'nullable|string|max:255',
            'supervising_association' => 'required|string|max:255',
            'independent_team_id' => 'required|exists:independent_teams,id',
        ]);

        $strayPet = StrayPet::where('uuid', $request->uuid)->firstOrFail();
        
        // --- Generate Serial Number if it doesn't exist ---
        if (empty($strayPet->serial_number)) {
            $team = IndependentTeam::find($request->independent_team_id);
            $teamPrefix = $team ? strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $team->name), 0, 3)) : 'XXX';
            
            $animalCode = 'PET';
            if ($request->animal_type === 'كلب') {
                $animalCode = 'K9';
            } elseif ($request->animal_type === 'قطة') {
                $animalCode = 'CAT';
            }

            $count = StrayPet::where('independent_team_id', $request->independent_team_id)->whereNotNull('serial_number')->count() + 1;
            $paddedCount = str_pad($count, 5, '0', STR_PAD_LEFT);

            $strayPet->serial_number = "UV-{$teamPrefix}-{$animalCode}-{$paddedCount}";
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            if ($strayPet->image_path) {
                Storage::disk('public')->delete($strayPet->image_path);
            }
            $imagePath = $request->file('image')->store('stray_pets_images', 'public');
            $strayPet->image_path = $imagePath;
        }

        // Prepare complex data fields
        $medicalProcedures = [
            'surgery_details' => $request->surgeryDetails,
            'other_treatments' => $request->otherTreatments,
        ];
        $parasiteTreatments = [
            'internal' => [ 'treatment' => $request->internalParasiteTreatment, 'date' => $request->internalParasiteDate, 'dose' => $request->internalParasiteDose ],
            'external' => [ 'treatment' => $request->externalParasiteTreatment, 'date' => $request->externalParasiteDate, 'dose' => $request->externalParasiteDose ],
        ];
        $vaccinationsDetails = [];
        if ($request->has('vaccine_type')) {
            foreach ($request->vaccine_type as $key => $type) {
                if (!empty($type)) {
                    $vaccinationsDetails[] = [
                        'type' => $type,
                        'custom_name' => $request->other_vaccine_name[$key] ?? null,
                        'manufacturer' => $request->vaccine_manufacturer[$key] ?? null,
                        'date_given' => $request->vaccine_date_given[$key] ?? null,
                        'date_next' => $request->vaccine_date_next[$key] ?? null,
                    ];
                }
            }
        }
        $medicalSupervisorInfo = [
            'vet_name' => $request->vetName,
            'supervising_society' => $request->supervisingSociety,
        ];

        // Update the model
        $strayPet->fill([
            // Arabic fields
            'city_province' => $request->city_province,
            'relocation_place' => $request->relocation_place,
            'breed_name' => $request->breed_name,
            'color' => $request->color,
            'distinguishing_marks' => $request->distinguishing_marks,
            // English fields
            'city_province_en' => $request->city_province_en,
            'relocation_place_en' => $request->relocation_place_en,
            'breed_name_en' => $request->breed_name_en,
            'color_en' => $request->color_en,
            'distinguishing_marks_en' => $request->distinguishing_marks_en,
            // Other fields
            'animal_type' => $request->animal_type === 'آخر' ? $request->custom_animal_type : $request->animal_type,
            'custom_animal_type' => $request->animal_type === 'آخر' ? $request->custom_animal_type : null,
            'gender' => $request->gender,
            'estimated_age' => $request->estimated_age,
            'emergency_contact_phone' => $request->emergency_contact,
            'medical_procedures' => $medicalProcedures,
            'parasite_treatments' => $parasiteTreatments,
            'vaccinations_details' => $vaccinationsDetails,
            'medical_supervisor_info' => $medicalSupervisorInfo,
            'independent_team_id' => $request->independent_team_id,
            'supervising_association' => $request->supervising_association,
            'data_entered_status' => true,
            'last_updated_by' => Auth::id(),
        ]);

        $strayPet->save();
        
        return redirect()->route('stray-pets.index')->with('success', __('messages.pet_data_saved_success'));
    }

    /**
     * عرض تفاصيل حيوان شارد (يوجه إلى صفحة التعديل/الإدخال).
     * Accessible by: Admin, Data Entry
     */
    public function show(string $uuid)
    {
        // ببساطة، نوجه المستخدم إلى صفحة التعديل/الإدخال للـ UUID المحدد
        return redirect()->route('stray-pets.data-entry-form', ['uuid' => $uuid]);
    }

    /**
     * عرض تفاصيل حيوان شارد للعامة (عبر QR Code).
     * Accessible by: Everyone (Guest included)
     */
    public function showPublic(string $uuid)
    {
        $strayPet = StrayPet::with('independentTeam')->where('uuid', $uuid)->firstOrFail();
        $totalPets = StrayPet::whereNotNull('serial_number')->count();

        // إذا لم يتم إدخال الرقم التسلسلي بعد، نعرض صفحة "قيد الإدخال"
        if (empty($strayPet->serial_number)) {
            return view('stray_pets.public_view_pending', compact('strayPet'));
        }
        // وإلا، نعرض الصفحة العامة بجميع الب��انات
        return view('stray_pets.public_view', compact('strayPet', 'totalPets'));
    }

    /**
     * عرض نموذج توليد أكواد QR.
     * Accessible by: Admin, Data Entry
     */
    public function showQRGenerationForm()
    {
        $user = Auth::user();
        $governorates = \App\Models\Governorate::orderBy('name')->get();
        $independentTeams = IndependentTeam::orderBy('name')->get();

        $selectedGovernorate = null;
        $selectedTeam = null;
        $supervisingAssociation = null;

        if ($user->role !== 'admin') {
            $selectedTeam = $user->independentTeam;
            if($selectedTeam) {
                $selectedGovernorate = $selectedTeam->governorate;
                $supervisingAssociation = $selectedTeam->supervising_association;
            }
        }

        return view('stray_pets.generate_qrs', compact('governorates', 'independentTeams', 'selectedGovernorate', 'selectedTeam', 'supervisingAssociation'));
    }

    /**
     * توليد مجموعة من أكواد QR للحيوانات الشاردة.
     * Accessible by: Admin, Data Entry
     */
    public function generateQRCodes(Request $request)
    {
        $request->validate([ 
            'num_qrcodes' => 'required|integer|min:1|max:500',
            'governorate_id_prefill' => 'required|exists:governorates,id',
            'independent_team_id_prefill' => 'required|exists:independent_teams,id',
            'supervising_association_prefill' => 'required|string|max:255',
        ]);
        
        $generatedQRs = [];
        $prefillData = $request->only(['animal_type_prefill', 'gender_prefill', 'breed_name_prefill', 'emergency_phone_prefill']);
        $defaultEmergencyContact = $prefillData['emergency_phone_prefill'] ?? Auth::user()->phone ?? Auth::user()->email;
        $defaultVetName = Auth::user()->name;

        for ($i = 0; $i < $request->num_qrcodes; $i++) {
            $strayPet = StrayPet::create([
                'uuid' => (string) Str::uuid(),
                'animal_type' => $prefillData['animal_type_prefill'] ?? null,
                'gender' => $prefillData['gender_prefill'] ?? null,
                'breed_name' => $prefillData['breed_name_prefill'] ?? 'بلدي كنعاني',
                'emergency_contact_phone' => $defaultEmergencyContact,
                'supervising_association' => $request->supervising_association_prefill,
                'independent_team_id' => $request->independent_team_id_prefill,
                'medical_supervisor_info' => [
                    'vet_name' => $defaultVetName,
                    'supervising_society' => '', // This field is now redundant
                ],
                'created_by' => Auth::id(),
                'last_updated_by' => Auth::id(),
            ]);

            $urlToEncode = route('stray-pets.public-view', ['uuid' => $strayPet->uuid]);
            $options = new QROptions(['outputType' => QRCode::OUTPUT_IMAGE_PNG, 'eccLevel' => QRCode::ECC_L, 'scale' => 10]);
            $base64Image = (new QRCode($options))->render($urlToEncode);
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));
            $qrImagePath = 'qrcodes/stray_pets/' . $strayPet->uuid . '.png';
            Storage::disk('public')->put($qrImagePath, $imageData);
            $strayPet->qrCodeLink()->create(['qr_identifier' => $strayPet->uuid, 'qr_image_path' => $qrImagePath]);
            
            $generatedQRs[] = [
                'strayPet' => $strayPet,
                'qr_image_data_uri' => $base64Image,
                'qr_file_path' => Storage::url($qrImagePath)
            ];
        }
        
        return view('stray_pets.qr_print_preview', compact('generatedQRs'))->with('success', __('messages.generate_qr_success'));
    }

    /**
     * طباعة أكواد QR إلى PDF.
     * Accessible by: Admin, Data Entry
     */
    public function printQRCodes(Request $request)
    {
        $request->validate([ 'uuids' => 'required|array', 'uuids.*' => 'required|uuid|exists:stray_pets,uuid' ]);
        
        $strayPets = StrayPet::whereIn('uuid', $request->uuids)->get();
        $qrsToPrint = [];

        foreach ($strayPets as $pet) {
            $qrsToPrint[] = [
                'name' => $pet->serial_number ?? ($pet->animal_type ?? 'حيوان غير مسمى') . ' (' . Str::limit($pet->uuid, 8, '') . ')',
'qr_data_uri' => (new QRCode(new QROptions(['outputType' => QRCode::OUTPUT_IMAGE_PNG, 'scale' => 5])))->render(route('stray-pets.public-view', ['uuid' => $pet->uuid])),            ];
        }
        
        $chunks = array_chunk($qrsToPrint, 9); // تقسيم الأكواد إلى مجموعات 9 لكل صفحة
        $pdf = Pdf::loadView('stray_pets.qr_pdf_template', compact('chunks'));
        
        return $pdf->download('stray_pets_qrcodes_' . date('Ymd_His') . '.pdf');
    }

    /**
     * حذف حيوان شارد فردي.
     * Accessible by: Admin, Data Entry
     */
    public function destroy(string $uuid)
    {
        $strayPet = StrayPet::where('uuid', $uuid)->firstOrFail();
        $strayPet->delete(); // This will now perform a soft delete
        
        return redirect()->route('stray-pets.index')->with('success', __('messages.pet_moved_to_trash_success'));
    }

    /**
     * الحذف الجماعي للحيوانات الشاردة.
     * Accessible by: Admin, Data Entry
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([ 'uuids' => 'required|array', 'uuids.*' => 'required|uuid|exists:stray_pets,uuid' ]);
        
        $deletedCount = StrayPet::whereIn('uuid', $request->uuids)->delete(); // Soft delete multiple
        
        return redirect()->route('stray-pets.index')->with('success', trans_choice('messages.pets_moved_to_trash_success', $deletedCount));
    }

    // --- Trash Bin Methods ---

    public function trashIndex()
    {
        $trashedPets = StrayPet::onlyTrashed()->latest('deleted_at')->paginate(50);
        return view('admin.trash.index', compact('trashedPets'));
    }

    public function trashRestore($id)
    {
        $pet = StrayPet::onlyTrashed()->findOrFail($id);
        $pet->restore();
        return redirect()->route('admin.trash.index')->with('success', __('messages.pet_restored_success'));
    }

    public function trashDestroy($id)
    {
        $pet = StrayPet::onlyTrashed()->findOrFail($id);
        // Optional: Delete associated files from storage before force deleting
        if ($pet->image_path) {
            Storage::disk('public')->delete($pet->image_path);
        }
        if ($pet->qrCodeLink && $pet->qrCodeLink->qr_image_path) {
            Storage::disk('public')->delete($pet->qrCodeLink->qr_image_path);
        }
        $pet->forceDelete();
        return redirect()->route('admin.trash.index')->with('success', __('messages.pet_permanently_deleted_success'));
    }
}