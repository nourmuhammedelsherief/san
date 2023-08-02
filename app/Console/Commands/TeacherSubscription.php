<?php

namespace App\Console\Commands;

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
        \Log::info('check course user periods');
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
    }
}
