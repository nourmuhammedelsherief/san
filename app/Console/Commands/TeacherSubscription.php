<?php

namespace App\Console\Commands;

use App\Models\School\SchoolSubscription;
use App\Models\Teacher\Teacher;
use Illuminate\Console\Command;

class TeacherSubscription extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:teacher-subscription';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check teachers subscription period';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = today()->format('Y-m-d');
        /**
         * handle tentative subscriptions
         */
        \Log::info('check schools and teachers subscription periods');
        $subscriptions = \App\Models\Teacher\TeacherSubscription::where('end_at' , '<' , $today)
            ->whereStatus('active')
            ->get();
        if ($subscriptions->count() > 0)
        {
            foreach ($subscriptions as $subscription)
            {
                $subscription->update([
                    'status'     => 'finished',
                    'payment'    => 'false',
                ]);
                $subscription->teacher->update([
                    'active'     => 'false',
                ]);
            }
        }


        $school_subscriptions = SchoolSubscription::where('end_at' , '<' , $today)
            ->whereStatus('active')
            ->get();
        if ($school_subscriptions->count() > 0)
        {
            foreach ($school_subscriptions as $school_subscription)
            {
                $school_subscription->update([
                    'status'     => 'finished',
                    'payment'    => 'false',
                ]);
                $school_subscription->school->update([
                    'status'     => 'finished',
                ]);
                // unActive the teachers belongs to school
                $teachers = Teacher::with('teacher_classrooms')
                    ->whereHas('teacher_classrooms', function ($q) use ($school_subscription){
                        $q->with('classroom');
                        $q->whereHas('classroom', function ($c) use ($school_subscription){
                            $c->whereSchoolId($school_subscription->school->id);
                        });
                    })->get();
                if ($teachers->count() > 0)
                {
                    foreach ($teachers as $teacher)
                    {
                        $teacher->update([
                            'active' => 'false',
                            'api_token' => null
                        ]);
                    }
                }

            }
        }
    }
}
