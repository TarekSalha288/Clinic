<?php

namespace App\Jobs;

use App\Models\User; // افترض أنك تريد إشعار مستخدم معين
use App\Notifications\ReminderNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon; // للتلاعب بالتواريخ
use App\Models\Apointment;
class SendReminderNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


//    protected $reminderDate;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
   public function handle()
    {
        $appointmentsToRemind = Apointment::whereBetween(
            'apointment_date',
            [
                Carbon::now()->addHours(7)->toDateTimeString(),
                Carbon::now()->addHours(9)->toDateTimeString()
            ]
        )
        ->where('status', 'accepted')->whereNot('apoitment_status','unapp')
        ->whereNull('reminder_sent_at')
        ->get();
        foreach ($appointmentsToRemind as $appointment) {
            $user = $appointment->patient->user;
            if ($user) {
          // Notify the user
//         if($user->fcm_token){
//   app('App\Services\FcmService')->sendNotification(
//                 $user->fcm_token,
//                 "Don't forget your appointment",
//                 "You have appointment at  ",
//                 ['appointment_date' => $appointment->apointment_date]
//             );
//         }
                $user->notify(new ReminderNotification($appointment->apointment_date));
                $appointment->update(['reminder_sent_at' => Carbon::now()]);
            }
        }
    }
}