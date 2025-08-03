<?php

// app/Console/Commands/SendDailyReports.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Alert;
use App\Models\Camera;
use App\Models\Video;
use App\Models\Setting;
use Illuminate\Support\Facades\Mail;
use App\Mail\DailyReport;
use Carbon\Carbon;

class SendDailyReports extends Command
{
    protected $signature = 'reports:daily';
    protected $description = 'Send daily summary reports to admins';

    public function handle()
    {
        $recipients = explode(',', Setting::getValue('alert_email_recipients', ''));
        if (empty($recipients)) {
            $this->error('No recipients configured for daily reports');
            return;
        }

        // Get stats for the last 24 hours
        $startTime = Carbon::now()->subDay();

        $stats = [
            'total_cameras' => Camera::count(),
            'online_cameras' => Camera::where('is_online', true)->count(),
            'total_alerts' => Alert::where('created_at', '>=', $startTime)->count(),
            'unknown_faces' => Alert::where('created_at', '>=', $startTime)
                ->where('is_recognized', false)->count(),
            'recorded_videos' => Video::where('start_time', '>=', $startTime)->count(),
            'system_uptime' => '100%', // TODO: Implement actual uptime calculation
        ];

        foreach ($recipients as $email) {
            $email = trim($email);
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                Mail::to($email)->send(new DailyReport($stats));
                $this->info("Sent daily report to {$email}");
            }
        }
    }
}
