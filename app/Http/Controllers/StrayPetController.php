<?php

namespace App\Http\Controllers;

use App\Models\StrayPet;
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
        $query = StrayPet::with('qrCodeLink'); // تحميل علاقة الـ QR Code مباشرة

        // تطبيق البحث إذا تم إدخال مصطلح بحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('serial_number', 'like', '%' . $search . '%')
                  ->orWhere('uuid', 'like', '%' . $search . '%')
                  ->orWhere('animal_type', 'like', '%' . $search . '%');
            });
        }
        
        $strayPetsWithQRs = $query->latest()->paginate(12); // الترتيب حسب الأحدث وتصفح النتائج

        // تجهيز البيانات لعرضها في الـ View
        $qrsToDisplay = [];
        foreach ($strayPetsWithQRs as $pet) {
            // توليد Data URI لكل QR Code للعرض المباشر في الـ View (بدون حفظ ملف مؤقت)
            $qr_data_uri = (new QRCode(new QROptions(['outputType' => QRCode::OUTPUT_IMAGE_PNG, 'scale' => 10])))
                            ->render(route('stray-pets.data-entry-form', ['uuid' => $pet->uuid]));

            $qrsToDisplay[] = [
                'strayPet' => $pet,
                'qr_image_data_uri' => $qr_data_uri,
                'qr_file_path' => $pet->qrCodeLink ? Storage::url($pet->qrCodeLink->qr_image_path) : null // مسار الملف المحفوظ للتحميل
            ];
        }
        
        // تمرير البيانات إلى View الخاص بالسجل/المعرض
        return view('stray_pets.qr_log_index', [ // تم تغيير اسم الـ View ليعكس المعرض
            'qrsToDisplay' => $qrsToDisplay,
            'pagination' => $strayPetsWithQRs // تمرير كائن التصفح لإنشاء الروابط
        ]);
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
            // إذا لم يتم توفير UUID، فهذا يشير إلى محاولة الوصول لنموذج غير مرتبط بـ QR
            // نوجه المستخدم إلى صفحة توليد QR لبدء العملية بشكل صحيح
            return redirect()->route('qrcodes.stray.generate.form')
                             ->with('error', 'يجب إدخال البيانات عبر QR Code صالح أو من خلال تحديد حيوان موجود.');
        }

        // البحث عن الحيوان الشارد بالـ UUID
        $strayPet = StrayPet::where('uuid', $targetUuid)->firstOrFail();
        
        // تجهيز بيانات التعبئة المسبقة للنموذج (سواء كانت موجودة في DB أو افتراضية)
        $prefill = [
            'animal_type' => $strayPet->animal_type ?? '',
            'gender' => $strayPet->gender ?? '',
            'estimated_age' => $strayPet->estimated_age ?? '',
            // تعبئة معلومات الاتصال والجهة الطبية الافتراضية من المستخدم الحالي إذا كانت فارغة
            'emergency_contact_phone' => $strayPet->emergency_contact_phone ?? (Auth::user()->phone ?? Auth::user()->email),
            'vet_name' => $strayPet->medical_supervisor_info['vet_name'] ?? Auth::user()->name,
            'supervising_society' => $strayPet->medical_supervisor_info['supervising_society'] ?? '',
        ];

        return view('stray_pets.data_entry', compact('strayPet', 'prefill'));
    }

    /**
     * حفظ (تحديث) بيانات حيوان شارد.
     * Accessible by: Admin, Data Entry
     */
public function storeOrUpdate(Request $request)
{
    $request->validate([
        'uuid' => 'required|uuid|exists:stray_pets,uuid',
        'serial_number' => 'nullable|string|max:255|unique:stray_pets,serial_number,' . $request->uuid . ',uuid',
        'city_province' => 'nullable|string|max:255', //nullable
        'relocation_place' => 'nullable|string|max:255', //nullable
        'animal_type' => 'nullable|string|max:255', //nullable
        'custom_animal_type' => 'nullable|string|max:255',
        'breed_name' => 'nullable|string|max:255',
        'gender' => 'nullable|string|max:255', //nullable
        'estimated_age' => 'nullable|string|max:255', //nullable
        'color' => 'nullable|string|max:255', //nullable
        'distinguishing_marks' => 'nullable|string',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'emergency_contact' => 'nullable|string|max:255', //nullable
        'vetName' => 'nullable|string|max:255', //nullable
        'supervisingSociety' => 'nullable|string|max:255', //nullable
        // تأكد من أن أي حقول JSON ذات صلة بـ arrays (مثل vaccine_type[]) ليست مطلوبة إلا إذا أردت
    ]);

        $strayPet = StrayPet::where('uuid', $request->uuid)->firstOrFail();
        
        // معالجة رفع الصورة
        if ($request->hasFile('image')) {
            if ($strayPet->image_path) { // حذف الصورة القديمة إذا وجدت
                Storage::disk('public')->delete($strayPet->image_path);
            }
            $imagePath = $request->file('image')->store('stray_pets_images', 'public');
            $strayPet->image_path = $imagePath;
        }

        // جمع البيانات المعقدة في JSON (تأكد من أن المفاتيح تتطابق مع الأسماء في النموذج)
        $medicalProcedures = [
            'surgery_details' => $request->surgeryDetails,
            'other_treatments' => $request->otherTreatments,
        ];
        $parasiteTreatments = [
            'internal' => [ 'treatment' => $request->internalParasiteTreatment, 'date' => $request->internalParasiteDate, 'dose' => $request->internalParasiteDose ],
            'external' => [ 'treatment' => $request->externalParasiteTreatment, 'date' => $request->externalParasiteDate, 'dose' => $request->externalParasiteDose ],
        ];
        $vaccinationsDetails = [];
        if ($request->has('vaccine_type')) { // إذا كان هناك أي مدخل لقاح
            foreach ($request->vaccine_type as $key => $type) {
                if (!empty($type)) { // تأكد من أن نوع اللقاح ليس فارغاً
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

        // تحديث بيانات الحيوان في قاعدة البيانات
        $strayPet->fill([
            'serial_number' => $request->serial_number,
            'city_province' => $request->city_province,
            'relocation_place' => $request->relocation_place,
            'animal_type' => $request->animal_type,
            'custom_animal_type' => $request->custom_animal_type,
            'breed_name' => $request->breed_name,
            'gender' => $request->gender,
            'estimated_age' => $request->estimated_age,
            'color' => $request->color,
            'distinguishing_marks' => $request->distinguishingMarks,
            'emergency_contact_phone' => $request->emergency_contact,
            'medical_procedures' => $medicalProcedures,
            'parasite_treatments' => $parasiteTreatments,
            'vaccinations_details' => $vaccinationsDetails,
            'medical_supervisor_info' => $medicalSupervisorInfo,
            'last_updated_by' => Auth::id(), // تعيين من قام بالتحديث
        ]);

        $strayPet->save();
        
        return redirect()->route('stray-pets.log')->with('success', 'تم حفظ بيانات الحيوان بنجاح!');
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
        $strayPet = StrayPet::where('uuid', $uuid)->firstOrFail();
        
        // إذا لم يتم إدخال الرقم التسلسلي بعد، نعرض صفحة "قيد الإدخال"
        if (empty($strayPet->serial_number)) {
            return view('stray_pets.public_view_pending', compact('strayPet'));
        }
        // وإلا، نعرض الصفحة العامة بجميع البيانات
        return view('stray_pets.public_view', compact('strayPet'));
    }

    /**
     * عرض نموذج توليد أكواد QR.
     * Accessible by: Admin, Data Entry
     */
    public function showQRGenerationForm()
    {
        return view('stray_pets.generate_qrs');
    }

    /**
     * توليد مجموعة من أكواد QR للحيوانات الشاردة.
     * Accessible by: Admin, Data Entry
     */
    public function generateQRCodes(Request $request)
    {
        $request->validate([ 'num_qrcodes' => 'required|integer|min:1|max:500' ]);
        
        $generatedQRs = [];
        $prefillData = $request->only(['animal_type_prefill', 'gender_prefill', 'estimated_age_prefill']);
        $defaultEmergencyContact = Auth::user()->phone ?? Auth::user()->email;
        $defaultVetName = Auth::user()->name;

        for ($i = 0; $i < $request->num_qrcodes; $i++) {
            $strayPet = StrayPet::create([
                'uuid' => (string) Str::uuid(),
                'animal_type' => $prefillData['animal_type_prefill'] ?? null,
                'gender' => $prefillData['gender_prefill'] ?? null,
                'estimated_age' => $prefillData['estimated_age_prefill'] ?? null,
                'emergency_contact_phone' => $defaultEmergencyContact,
                'medical_supervisor_info' => ['vet_name' => $defaultVetName],
                'created_by' => Auth::id(),
                'last_updated_by' => Auth::id(),
            ]);

$urlToEncode = route('stray-pets.public-view', ['uuid' => $strayPet->uuid]);            $options = new QROptions(['outputType' => QRCode::OUTPUT_IMAGE_PNG, 'eccLevel' => QRCode::ECC_L, 'scale' => 10]);
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
        
        return view('stray_pets.qr_print_preview', compact('generatedQRs'))->with('success', 'تم توليد رموز QR بنجاح!');
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
        
        // حذف الصور والـ QR Code من التخزين أولاً
        if ($strayPet->image_path) {
            Storage::disk('public')->delete($strayPet->image_path);
        }
        if ($strayPet->qrCodeLink && $strayPet->qrCodeLink->qr_image_path) {
            Storage::disk('public')->delete($strayPet->qrCodeLink->qr_image_path);
        }
        
        $strayPet->delete(); // ثم حذف السجل من قاعدة البيانات
        
        return redirect()->route('stray-pets.log')->with('success', 'تم حذف الحيوان الشارد بنجاح.');
    }

    /**
     * الحذف الجماعي للحيوانات الشاردة.
     * Accessible by: Admin, Data Entry
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([ 'uuids' => 'required|array', 'uuids.*' => 'required|uuid|exists:stray_pets,uuid' ]);
        
        $deletedCount = 0;
        foreach ($request->uuids as $uuid) {
            $strayPet = StrayPet::where('uuid', $uuid)->first(); // البحث عن كل حيوان بشكل فردي
            if ($strayPet) {
                // حذف الصور والـ QR Code من التخزين
                if ($strayPet->image_path) {
                    Storage::disk('public')->delete($strayPet->image_path);
                }
                if ($strayPet->qrCodeLink && $strayPet->qrCodeLink->qr_image_path) {
                    Storage::disk('public')->delete($strayPet->qrCodeLink->qr_image_path);
                }
                $strayPet->delete(); // حذف السجل
                $deletedCount++;
            }
        }
        
        return redirect()->route('stray-pets.log')->with('success', "تم حذف $deletedCount حيوان بنجاح.");
    }
}