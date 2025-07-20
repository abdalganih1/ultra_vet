**خطة عمل محدثة (v2)**

تم تحديث الخطة لتشمل التفاصيل الإضافية وتوضيح آلية التنفيذ بشكل أدق بناءً على تحليل ملف `routes/web.php`.

**المرحلة الأولى: تعديلات قاعدة البيانات والنماذج (Models)**

1.  **إنشاء جدول للفرق المستقلة (`independent_teams`):**
    *   **الإجراء:** إنشاء ملف Migration لإنشاء جدول `independent_teams`.
    *   **ملف جديد:** `database/migrations/YYYY_MM_DD_HHMMSS_create_independent_teams_table.php`
    *   **التعديل:** سيحتوي على حقول: `id`, `name`, `province`.

2.  **تحديث جدول الحيوانات الشاردة (`stray_pets`):**
    *   **الإجراء:** إنشاء ملف Migration لإضافة حقل لربط الحيوان بالفريق المستقل.
    *   **ملف جديد:** `database/migrations/YYYY_MM_DD_HHMMSS_add_independent_team_id_to_stray_pets_table.php`
    *   **التعديل:** إضافة حقل `independent_team_id` (foreign key).

3.  **تحديث نماذج Eloquent:**
    *   **ملف جديد:** `app/Models/IndependentTeam.php`.
    *   **ملف سيتم تعديله:** `app/Models/StrayPet.php` لإضافة العلاقة مع `IndependentTeam`.

**المرحلة الثانية: لوحة تحكم المدير لإدارة الفرق (Admin CRUD)**

*   **الإجراء:** إنشاء نظام CRUD متكامل للمدير لإدارة الفرق.
*   **ملفات جديدة:**
    *   `app/Http/Controllers/Admin/IndependentTeamController.php`
    *   `resources/views/admin/teams/index.blade.php`
    *   `resources/views/admin/teams/create.blade.php`
    *   `resources/views/admin/teams/edit.blade.php`
*   **ملف سيتم تعديله:** `routes/web.php` لإضافة مسارات لوحة تحكم الفرق.

**المرحلة الثالثة: تحديث نماذج الإدخال وتوليد QR**

1.  **تحديث صفحة إدخال البيانات (`data_entry.blade.php`):**
    *   **الإجراء:** إضافة قائمة منسدلة لاختيار الفريق المستقل.
    *   **ملف سيتم تعديله:** `resources/views/stray_pets/create.blade.php` (بناءً على تحليل `routes.php`).
    *   **التعديل:** إضافة `<select>` لجلب الفرق من `IndependentTeam::all()`.

2.  **تحديث صفحة توليد QR (`generate_qrs.blade.php`):**
    *   **الإجراء:** تعديل حقول "البيانات الموحدة للتعبئة المسبقة".
    *   **ملف سيتم تعديله:** `resources/views/stray_pets/generate_qrs.blade.php` (اسم افتراضي، سيتم التأكد منه).
    *   **التعديل:**
        *   **إلغاء:** حقل "العمر التقديري" (`estimated_age`).
        *   **إضافة:** حقل "اسم السلالة (إن وجدت)" مع قيمة افتراضية "بلدي كنعاني".
        *   **إضافة:** حقل "اسم الجمعية المشرفة (إن وجدت)".
        *   **إضافة:** حقل "رقم الهاتف / واتساب للطوارئ".

**المرحلة الرابعة: تحديث صفحة عرض الحيوان العامة**

*   **الإجراء:** تطبيق التغييرات المطلوبة على الصفحة التي يراها المستخدم النهائي.
*   **ملفات سيتم تعديلها:**
    *   `app/Http/Controllers/StrayPetController.php`:
        *   **التعديل في دالة `showPublic`:**
            *   جلب العدد الإجمالي للحيوانات `StrayPet::count()`.
            *   تمرير هذا العدد إلى الـ view.
    *   `resources/views/stray_pets/public_view.blade.php`:
        *   **التعديل:**
            *   عرض العدد الإجمالي للحيوانات الشاردة.
            *   تغيير الشعار وتكبير��.
            *   تغيير النص "مبادرة UltraVet..." إلى "Add Hope to Life".
            *   ترجمة "النوع" و "الجنس" إلى العربية.
            *   عرض اسم "الفريق المستقل".
            *   إخفاء هوية مدخل البيانات.
            *   تغيير جملة "للتواصل أو للتبني" إلى النص الجديد.
            *   **إزالة الشريط العلوي:** حذف أو إخفاء الروابط (`تسجيل الدخول`, `انشاء حساب`, `لوحة التحكم`) من شريط التنقل في هذه الصفحة فقط.

**المرحلة الخامسة: الإحصائيات والصفحات العامة**

*   **الإجراء:** تحديث الصفحة الرئيسية وإضافة صفحات تعريفية.
*   **ملفات سيتم تعديلها:**
    *   `app/Http/Controllers/HomeController.php`:
        *   **التعديل:** إضافة استعلام لإحصاء الحالات لكل فريق في المحافظات المحددة.
    *   `resources/views/home.blade.php`:
        *   **التعديل:** عرض الإحصائيات الجديدة.
*   **ملفات جديدة:**
    *   `app/Http/Controllers/PageController.php`.
    *   `resources/views/pages/about.blade.php` (من نحن).
    *   `resources/views/pages/team.blade.php` (كادرنا).
    *   `resources/views/pages/contact.blade.php` (تواصل معنا).
*   **ملف سيتم تعديله:** `routes/web.php` لإضافة مسارات الصفحات الجديدة.