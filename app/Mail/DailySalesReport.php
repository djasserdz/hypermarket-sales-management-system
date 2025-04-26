<?php

namespace App\Mail;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;

class DailySalesReport extends Mailable implements ShouldQueue
{
    public $report;

    public function __construct($report)
    {
        $this->report = $report;
    }

    public function build()
    {
        return $this->subject('Daily Sales Report')
                    ->view('emails.daily_sales_report');
    }
}
