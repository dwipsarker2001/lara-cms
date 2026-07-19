<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WeeklyReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $stats;

    /**
     * Create a new message instance.
     *
     * @param  array  $stats
     * @return void
     */
    public function __construct($stats)
    {
        $this->stats = $stats;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('marketing.reports.weekly_report')
            ->subject('Weekly Report');
    }
}
