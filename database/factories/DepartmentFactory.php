<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Department>
 */
class DepartmentFactory extends Factory
{
    /**
     * The name of the corresponding model.
     *
     * @var string
     */
    protected $model = Department::class;

    /**
     * قائمة بأسماء الأقسام الطبية الشائعة.
     * يمكن إضافة المزيد حسب الحاجة.
     *
     * @var array
     */
    private static $medicalDepartmentNames = [
        'Cardiology',
        'Neurology',
        'Gastroenterology',
        'Pulmonology',
        'Orthopedics',
        'Pediatrics',
        'Dermatology',
        'Urology',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // لحل مشكلة "Maximum retries of 10000 reached without finding a unique value":
        // تحدث هذه المشكلة عندما تحاول إنشاء عدد من السجلات أكبر من عدد القيم الفريدة
        // المتوفرة في القائمة الثابتة 'medicalDepartmentNames'.
        // فمثلاً، إذا كانت القائمة تحتوي على 8 أسماء (كما هو الحال حالياً)، وحاولت إنشاء 9 أقسام،
        // فإن Faker لن يتمكن من العثور على اسم تاسع فريد من هذه القائمة المحدودة.

        // للحفاظ على نفس الأسماء دون تكرار، يجب عليك التأكد من أن عدد الأقسام
        // التي تقوم بإنشائها لا يتجاوز عدد الأسماء المتوفرة في القائمة
        // 'self::$medicalDepartmentNames'.
        // في حالتك الحالية، تحتوي القائمة على 8 أسماء.
        // لذا، عند استدعاء Department::factory() في DatabaseSeeder،
        // يجب أن يكون العدد 8 أو أقل.
        // على سبيل المثال:
        // Department::factory(8)->create(); // هذا سيستخدم كل الأسماء مرة واحدة بالضبط
        // Department::factory(5)->create(); // هذا سيستخدم 5 أسماء فريدة عشوائية من القائمة

        return [
            // تستخدم unique()->randomElement() لضمان أن كل اسم قسم يتم استخدامه مرة واحدة فقط
            // من القائمة المحددة لكل عملية seeding، طالما أن عدد السجلات المطلوبة لا يتجاوز
            // حجم القائمة.
            'name' => $this->faker->unique()->randomElement(self::$medicalDepartmentNames),

            // يولد نصًا أكثر واقعية كـ"وصف"
            'description' => $this->faker->realText(200),

            // يولد رابط صورة عشوائي بناءً على الكلمات الرئيسية
            'image' => $this->faker->imageUrl(640, 480, 'hospital medical', true, 'clinic room', true),
        ];
    }
}
