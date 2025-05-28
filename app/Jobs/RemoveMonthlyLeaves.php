<?php

namespace App\Jobs;

use App\Models\MounthlyLeave;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RemoveMonthlyLeaves implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
    MounthlyLeave::whereMonth('created_at', '<', now()->month)->delete();
    }
}