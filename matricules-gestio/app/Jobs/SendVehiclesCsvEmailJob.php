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
        if(env('DEBUG_MAIL') || self::checkIfWrong()) {
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
    private static function checkIfWrong(): bool
    {
        $filePath = 'vehicles_result.csv';

        if (!Storage::exists($filePath)) {
            Log::error("File {$filePath} does not exist when checking for errors.");
            return false;
        }

        $contents = Storage::get($filePath);
        $lines = explode("\n", $contents);

        // La primera línea son los headers, los usamos para saber el índice de "errors"
        $headers = str_getcsv(array_shift($lines), ";");
        $errorsIndex = array_search('errors', $headers);

        if ($errorsIndex === false) {
            Log::error("No 'errors' column found in CSV.");
            return false;
        }

        foreach ($lines as $line) {
            if (trim($line) === '') continue; // saltar líneas vacías

            $fields = str_getcsv($line, ";");

            if (isset($fields[$errorsIndex]) && intval($fields[$errorsIndex]) > 0) {
                Log::info("CSV error detected in line: " . $line);
                return true;
            }
        }
        return false;
    }
}
