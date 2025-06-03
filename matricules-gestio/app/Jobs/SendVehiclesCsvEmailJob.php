<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Mail\VehiclesCsvMail;

class SendVehiclesCsvEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        Log::info('SendVehiclesCsvEmailJob instance created.');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('SendVehiclesCsvEmailJob started.');

        $filePath = 'vehicles_result.csv';

        if (!Storage::exists($filePath)) {
            Log::error("File {$filePath} does not exist. Email sending aborted.");
            return;
        }

        Log::info("File {$filePath} found. Preparing to send email to " . env('MAIL_DESTINATARI'));

        try {
            Mail::to(env('MAIL_DESTINATARI'))->send(new VehiclesCsvMail($filePath));
            Log::info('Email sent successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to send email: ' . $e->getMessage());
        }

        Log::info('SendVehiclesCsvEmailJob finished.');
    }
}
