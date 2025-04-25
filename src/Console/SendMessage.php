<?php

namespace Ramatimati\Waliby\Console;

use Ramatimati\Waliby\App\Jobs\HandleQueue;
use Ramatimati\Waliby\App\Models\Job;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;
use Ramatimati\Waliby\Waliby;

class SendMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'waliby:send-message';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'function to send single whatsapp message from WALIBY every 30s';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        //
        $jobPriorityId = config('waliby.eventPriorityId');
        $getMessage = Job::orderBy('created_at', 'ASC')
            ->when($jobPriorityId, function($sub) use ($jobPriorityId){
                return $sub->where('event_id', $jobPriorityId);
            })
            ->orderBy('created_at', 'ASC')
            ->limit(3)
            ->get();

        if ($getMessage->isNotEmpty()) {
            Log::info($getMessage);
            HandleQueue::dispatch($getMessage);
        }
    }
}
