<?php

namespace Ramatimati\Waliby\Console;

use Ramatimati\Waliby\App\Traits\sentWATrait;
use Ramatimati\Waliby\App\Models\JobLog;
use Ramatimati\Waliby\App\Models\Event;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendWA extends Command
{
    use sentWATrait;

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
        $fetchEvent = Event::where('event_type', 'recurring')->get();

        foreach ($fetchEvent as $key => $value) {
            if ($value->scheduled_every == 'daily') {
                if ($value->scheduled_at == Carbon::now()->format('H')) {
                    if ($value->last_processed == null || Carbon::parse($value->last_processed)->format('Y-m-d H') != Carbon::now()->format('Y-m-d H')) {
                        try {
                            Event::where('id', $value->id)->update([
                                'last_processed' => Carbon::now()->format('Y-m-d H:i:s')
                            ]);
    
                            $this->send($value->id);
    
                            $joblog = JobLog::create([
                                'event_id' => $value->id,
                                'reserved_at' => Carbon::now()->format('Y-m-d H:i:s'),
                            ])->id;
    
                            $status = 'success';
                            $exception = null;
                        } catch (\Throwable $th) {
                            $exception = $th->getMessage();
                            $status = 'failed';
                            \Log::info($exception);
                        }
                    }
                }
            }elseif ($value->scheduled_every == 'weekly') {
                if ($value->scheduled_at == Carbon::now()->dayOfWeek && Carbon::now()->format('H') == 10) {
                    $last = Carbon::parse($value->last_processed)->dayOfWeek;
                    if ($value->last_processed == null || $last != Carbon::now()->dayOfWeek) {
                        try {
                            Event::where('id', $value->id)->update([
                                'last_processed' => Carbon::now()->format('Y-m-d H:i:s')
                            ]);
    
                            $this->send($value->id);
    
                            $joblog = JobLog::create([
                                'event_id' => $value->id,
                                'reserved_at' => Carbon::now()->format('Y-m-d H:i:s'),
                            ])->id;
    
                            $status = 'success';
                            $exception = null;
                        } catch (\Throwable $th) {
                            $exception = $th->getMessage();
                            $status = 'failed';
                            \Log::info($exception);
                        }
                    }
                }
            }elseif ($value->scheduled_every == 'monthly') {
                if ($value->scheduled_at == Carbon::now()->format('d') && Carbon::now()->format('H') == 10) {
                    $last = Carbon::parse($value->last_processed)->format('d');
                    if ($value->last_processed == null || $last != Carbon::now()->format('d')) {
                        try {
                            Event::where('id', $value->id)->update([
                                'last_processed' => Carbon::now()->format('Y-m-d H:i:s')
                            ]);
    
                            $this->send($value->id);
    
                            $joblog = JobLog::create([
                                'event_id' => $value->id,
                                'reserved_at' => Carbon::now()->format('Y-m-d H:i:s'),
                            ])->id;
    
                            $status = 'success';
                            $exception = null;
                        } catch (\Throwable $th) {
                            $exception = $th->getMessage();
                            $status = 'failed';
                            \Log::info($exception);
                        }
                    }
                }
            }elseif ($value->scheduled_every == 'yearly') {
                // \Log::info(Carbon::now()->firstOfMonth(1));
                if ($value->scheduled_at == Carbon::now()->format('m') && Carbon::now()->firstOfMonth(1) && Carbon::now()->format('H') == 10) {
                    $last = Carbon::parse($value->last_processed)->format('m');
                    if ($value->last_processed == null || $last != Carbon::now()->format('m')) {
                        try {
                            Event::where('id', $value->id)->update([
                                'last_processed' => Carbon::now()->format('Y-m-d H:i:s')
                            ]);
    
                            $this->send($value->id);
    
                            $joblog = JobLog::create([
                                'event_id' => $value->id,
                                'reserved_at' => Carbon::now()->format('Y-m-d H:i:s'),
                            ])->id;
    
                            $status = 'success';
                            $exception = null;
                        } catch (\Throwable $th) {
                            $exception = $th->getMessage();
                            $status = 'failed';
                            \Log::info($exception);
                        }
                    }
                }
            }

            // update job finished at
            try {
                JobLog::where('id', $joblog)->update([
                    'finished_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'status' => $status,
                    'exception' => $exception
                ]);
            } catch (\Throwable $th) {
                \Log::info('WALIBY JOB NOT FOUND : '.$th->getMessage());
            }
        }
    }
}
