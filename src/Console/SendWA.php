<?php

namespace Ramatimati\Waliby\Console;

use Illuminate\Console\Command;
use Ramatimati\Waliby\App\Models\Event;

class SendWA extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'waliby:send-wa';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'function to send whatsapp message from WALIBY';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $fetchJob = Job::with('event')->get();
        
        \Log::info($fetchJob);
    }
}
