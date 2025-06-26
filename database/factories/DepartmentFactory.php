<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Department; // تأكد من استيراد نموذج Department

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
     * @var array
     */
    private static $medicalDepartmentNames = [
       'Cardiology',
'Neurology',
'Gastroenterology',
'Pulmonology',
'Orthopedics' ,
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
        return [

            'name' => $this->faker->unique()->randomElement(self::$medicalDepartmentNames),

            'description' => $this->faker->realText(200), // يولد نصًا أكثر واقعية كـ"وصف"

            // يمكن تعديل الكلمات الرئيسية (مثل 'medical clinic') للحصول على صور مختلفة
            'image' => $this->faker->imageUrl(640, 480, 'hospital medical', true, 'clinic room', true),
        ];
    }
}