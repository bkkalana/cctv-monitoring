<?php

// app/Mail/DailyReport.php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DailyReport extends Mailable
{
    use Queueable, SerializesModels;

    public $stats;

    public function __construct($stats)
    {
        $this->stats = $stats;
    }

    public function build()
    {
        return $this->subject('Daily CCTV System Report')
            ->markdown('emails.daily-report');
    }
}
