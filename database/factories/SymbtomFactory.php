<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Symbtom;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Symbtom>
 */
class SymbtomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Symbtom::class;
    public static $symptoms = [
        // Cardiology
        ['en' => 'Chest Pain', 'ar' => 'ألم في الصدر'],
        ['en' => 'Shortness of Breath', 'ar' => 'ضيق في التنفس'],
        ['en' => 'Palpitations', 'ar' => 'خفقان القلب'],
        ['en' => 'Swelling in Legs', 'ar' => 'تورم الساقين'],
        ['en' => 'Fatigue', 'ar' => 'إرهاق'],
        ["en" => "Rapid Heartbeat", "ar" => "خفقان سريع"],
        ["en" => "Fainting", "ar" => "إغماء"],
        ["en" => "High Blood Pressure", "ar" => "ارتفاع ضغط الدم"],
        // Neurology
        ['en' => 'Headaches', 'ar' => 'صداع'],
        ['en' => 'Dizziness', 'ar' => 'دوخة'],
        ['en' => 'Numbness', 'ar' => 'خدر'],
        ['en' => 'Muscle Weakness', 'ar' => 'ضعف العضلات'],
        ['en' => 'Seizures', 'ar' => 'نوبات صرع'],
        ['en' => 'Memory Loss', 'ar' => 'فقدان الذاكرة'],
        ['en' => 'Tingling', 'ar' => 'تنميل'],
        ['en' => 'Loss of Consciousness', 'ar' => 'فقدان الوعي'],
        ['en' => 'Tremors', 'ar' => 'رعشة'],
        ['en' => 'Vision Problems', 'ar' => 'مشاكل في الرؤية'],
        // Gastroenterology
        ['en' => 'Abdominal Pain', 'ar' => 'ألم في البطن'],
        ['en' => 'Nausea', 'ar' => 'غثيان'],
        ['en' => 'Diarrhea', 'ar' => 'إسهال'],
        ['en' => 'Constipation', 'ar' => 'إمساك'],
        ['en' => 'Heartburn', 'ar' => 'حرقة المعدة'],
        ['en' => 'Bloating', 'ar' => 'انتفاخ'],
        [
            'en' => 'Vomiting',
            'ar' => 'قيء'
        ],
        [
            'en' => 'Blood in Stool',
            'ar' => 'دم في البراز'
        ],
        [
            'en' => 'Loss of Appetite',
            'ar' => 'فقدان الشهية'
        ],
        [
            'en' => 'Difficulty Swallowing',
            'ar' => 'صعوبة في البلع'
        ],
        // Pulmonology
        ['en' => 'Chronic Cough', 'ar' => 'سعال مزمن'],
        ['en' => 'Wheezing', 'ar' => 'صفير في التنفس'],
        ['en' => 'Shortness of Breath (Dyspnea)', 'ar' => 'ضيق في التنفس (زلة تنفسية)'],
        ['en' => 'Chest Tightness', 'ar' => 'ضيق في الصدر'],
        ['en' => 'Frequent Respiratory Infections', 'ar' => 'عدوى تنفسية متكررة'],
        [
            'en' => 'Coughing Up Blood',
            'ar' => 'السعال مع دم'
        ],
        [
            'en' => 'Snoring',
            'ar' => 'الشخير'
        ],
        [
            'en' => 'Hoarseness',
            'ar' => 'بحة الصوت'
        ],
        // Orthopedics
        ['en' => 'Joint Pain', 'ar' => 'ألم المفاصل'],
        ['en' => 'Swelling of Joints', 'ar' => 'تورم المفاصل'],
        ['en' => 'Limited Range of Motion', 'ar' => 'نطاق حركة محدود'],
        ['en' => 'Muscle Pain', 'ar' => 'ألم العضلات'],
        ['en' => 'Fractures', 'ar' => 'كسور'],
        ['en' => 'Back Pain', 'ar' => 'ألم في الظهر'],
        [
            'en' => 'Neck Pain',
            'ar' => 'ألم الرقبة'
        ],
        [
            'en' => 'Bone Pain',
            'ar' => 'ألم العظام'
        ],
        [
            'en' => 'Stiffness',
            'ar' => 'تصلب'
        ],
        // Pediatrics
        ['en' => 'Fever (in children)', 'ar' => 'حمى (عند الأطفال)'],
        ['en' => 'Rash (in children)', 'ar' => 'طفح جلدي (عند الأطفال)'],
        ['en' => 'Ear Infection', 'ar' => 'عدوى الأذن'],
        ['en' => 'Sore Throat', 'ar' => 'التهاب الحلق'],
        ['en' => 'Growth Delays', 'ar' => 'تأخر في النمو'],
        ['en' => 'Behavioral Changes', 'ar' => 'تغيرات سلوكية'],
        [
            'en' => 'Poor Appetite',
            'ar' => 'ضعف الشهية'
        ],
        [
            'en' => 'Developmental Delays',
            'ar' => 'تأخر في النمو'
        ],
        [
            'en' => 'Bedwetting',
            'ar' => 'التبول اللاإرادي'
        ],
        // Dermatology
        ['en' => 'Skin Rash', 'ar' => 'طفح جلدي'],
        ['en' => 'Itching (Pruritus)', 'ar' => 'حكة (حِكاك)'],
        ['en' => 'Acne', 'ar' => 'حب الشباب'],
        ['en' => 'Eczema', 'ar' => 'أكزيما'],
        ['en' => 'Psoriasis', 'ar' => 'صدفية'],
        ['en' => 'Moles/Skin Lesions', 'ar' => 'شامات/آفات جلدية'],
        [
            'en' => 'Hair Loss',
            'ar' => 'تساقط الشعر'
        ],
        [
            'en' => 'Nail Changes',
            'ar' => 'تغيرات الأظافر'
        ],
        [
            'en' => 'Skin Discoloration',
            'ar' => 'تغير لون الجلد'
        ],
        // Urology
        ['en' => 'Frequent Urination', 'ar' => 'تبول متكرر'],
        ['en' => 'Painful Urination (Dysuria)', 'ar' => 'تبول مؤلم (عُسر التبول)'],
        ['en' => 'Blood in Urine (Hematuria)', 'ar' => 'دم في البول (بيلة دموية)'],
        ['en' => 'Kidney Stones', 'ar' => 'حصى الكلى'],
        ['en' => 'Urinary Incontinence', 'ar' => 'سلس البول'],
        [
            'en' => 'Urinary Retention',
            'ar' => 'احتباس البول'
        ],
        [
            'en' => 'Testicular Pain',
            'ar' => 'ألم في الخصية'
        ],
    ];
    private static $index = 0;
    public function definition(): array
    {
        $symptom = self::$symptoms[self::$index];
        self::$index++;

        return [
            'symbtom_name' => [
                'en' => $symptom['en'],
                'ar' => $symptom['ar'],
            ],
        ];
    }
}
