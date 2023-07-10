<?php

namespace App\Console\Commands;

use App\Models\UserCourse;
use Illuminate\Console\Command;

class CoursePeriod extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'course:period';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'check course user periods';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = today()->format('Y-m-d');
        /**
         * handle tentative subscriptions
         */
        \Log::info('check course user periods');
        $courses = UserCourse::where('end_at' , '<' , $today)
            ->whereStatus('active')
            ->get();
        if ($courses->count() > 0)
        {
            foreach ($courses as $course)
            {
                $course->update([
                    'status'     => 'finished',
                    'payment'    => 'false',
                ]);
            }
        }
    }
}
