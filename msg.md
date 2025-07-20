**خطة عمل محدثة**

شكراً على التوضيح. بعد مراجعة ملف الـ migration، تأكدت من أن الحقول المطلوبة موجودة بالفعل. سأقوم الآن بتحديث خطة العمل بناءً على هذه المعلومات.

**المرحلة الأولى: تعديلات قاعدة البيانات والنماذج (Models)**

1.  **إنشاء جدول للفرق المستقلة (`independent_teams`):**
    *   **الإجراء:** سأقوم بإنشاء ملف Migration جديد لإنشاء جدول لتخزين أسماء الفرق والمحافظات التابعة لها.
    *   **ملف جديد:** `database/migrations/YYYY_MM_DD_HHMMSS_create_independent_teams_table.php`
    *   **التعديل:** سيحتوي الجدول على حقول: `id`, `name`, `province`, `created_at`, `updated_at`.

2.  **تحديث جدول الحيوانات الشاردة (`stray_pets`):**
    *   **الإجراء:** سأقوم بإنشاء ملف Migration جديد لإضافة حقل لربط الحيوان بالفريق المستقل الذي عالجه.
    *   **ملف جديد:** `database/migrations/YYYY_MM_DD_HHMMSS_add_independent_team_id_to_stray_pets_table.php`
    *   **التعديل:** إضافة حقل `independent_team_id` (foreign key) الذي يشير إلى جدول `independent_teams`.

3.  **تحديث نماذج Eloquent:**
    *   **ملف جديد:** `app/Models/IndependentTeam.php` لتمثيل جدول الفرق.
    *   **ملف سيتم تعديله:** `app/Models/StrayPet.php` لإضافة العلاقة `belongsTo` مع نموذج `IndependentTeam` وإضافة `independent_team_id` إلى الخاصية `$fillable`.

**المرحلة الثانية: لوحة تحكم المدير لإدارة الفرق (Admin CRUD)**

*   **الإجراء:** إنشاء نظام متكامل للمدير لإدارة الفرق (CRUD).
*   **ملفات جديدة:**
    *   `app/Http/Controllers/Admin/IndependentTeamController.php`
    *   `resources/views/admin/teams/index.blade.php`
    *   `resources/views/admin/teams/create.blade.php`
    *   `resources/views/admin/teams/edit.blade.php`
*   **ملف سيتم تعديله:** `routes/web.php` لإضافة المسارات (routes) الخاصة بلوحة تحكم الفرق، مع حمايتها بـ middleware للتأكد من أن المستخدم هو المدير.

**المرحلة الثالثة: تحديث نماذج الإدخال**

*   **الإجراء:** تعديل الصفحات التي يستخدمها مدخل البيانات. (س��بحث عن الملفات `data_entry.blade.php` و `generate_qrs.blade.php`).
*   **ملفات سيتم تعديلها (بافتراض وجودها):**
    *   `resources/views/stray_pets/data_entry.blade.php`:
        *   **التعديل:** إضافة قائمة منسدلة (`<select>`) لاختيار "الفريق المستقل" من قائمة الفرق التي أضافها المدير.
    *   `resources/views/stray_pets/generate_qrs.blade.php`:
        *   **التعديل:** حذف حقل "العمر التقديري" (`estimated_age`). والتأكد من أن الحقول الأخرى المطلوبة (السلالة، الجمعية، هاتف الطوارئ) يتم التعامل معها بشكل صحيح في النموذج.

**المرحلة الرابعة: تحديث صفحة عرض الحيوان العامة (`public_view.blade.php`)**

*   **الإجراء:** تطبيق التغييرات الجمالية والوظيفية المطلوبة.
*   **ملف سيتم تعديله (بافتراض وجوده):** `resources/views/stray_pets/public_view.blade.php`
    *   **التعديل:**
        *   تغيير الشعار وتكبيره.
        *   تغيير النص "مبادرة UltraVet..." إلى "Add Hope to Life".
        *   ترجمة قيم "النوع" و "الجنس" إلى العرب��ة (مثال: `dog_baladi` -> `كلب بلدي`, `male` -> `ذكر`).
        *   عرض اسم "الفريق المستقل" الذي أجرى العملية.
        *   إخفاء هوية مدخل البيانات (`created_by`).
        *   تغيير جملة "للتواصل أو للتبني" إلى "التقط شاشة على موبايلك الى رمز QR ثم ارسلها الى رقم الطوارئ الموجود في الأعلى.".
        *   إزالة روابط "تسجيل الدخول"، "إنشاء حساب"، و "لوحة التحكم".

**المرحلة الخامسة: الإحصائيات والصفحات العامة**

*   **الإجراء:** تحديث الصفحة الرئيسية وإضافة صفحات تعريفية.
*   **ملفات سيتم تعديلها:**
    *   `app/Http/Controllers/HomeController.php` (أو ما يماثله):
        *   **التعديل:** إضافة استعلام لإحصاء عدد الحالات لكل فريق في المحافظات المحددة ("حلب", "حماه", "دمشق", "ريف دمشق", "درعا", "اللاذقية") وتمرير البيانات إلى الـ View.
    *   `resources/views/home.blade.php`:
        *   **التعديل:** عرض الإحصائيات الجديدة للفرق.
*   **ملفات جديدة:**
    *   `app/Http/Controllers/PageController.php`.
    *   `resources/views/pages/about.blade.php` (من نحن).
    *   `resources/views/pages/team.blade.php` (كادرنا).
    *   `resources/views/pages/contact.blade.php` (تواصل معنا).
*   **ملف سيتم تعديله:** `routes/web.php` لإضافة مسارات الصفحات الجديدة.
