<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\VehiclesCsvMail;

class SendVehiclesCsvEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        
        $filePath = 'vehicles_result.csv';

        if (!Storage::exists($filePath)) {
            \Log::error('L\'arxiu vehicles_result.csv no existeix');
            return;
        }

        // Enviar correu
        Mail::to(env('MAIL_DESTINATARI'))->send(new VehiclesCsvMail($filePath));
    }
}
