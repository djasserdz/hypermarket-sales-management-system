<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

class SendDailyReportEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $recipientEmail;
    public $mailable;

    /**
     * Create a new job instance.
     *
     * @param string $recipientEmail
     * @param \Illuminate\Mail\Mailable $mailable
     * @return void
     */
    public function __construct(string $recipientEmail, Mailable $mailable)
    {
        $this->recipientEmail = $recipientEmail;
        $this->mailable = $mailable;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->recipientEmail)->send($this->mailable);
    }
} 