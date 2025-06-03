<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PenjarVehiclesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $record;
    protected $isPadro;

    /**
     * Create a new job instance.
     */
    public function __construct($record, $isPadro)
    {
        $this->record = $record;
        $this->isPadro = $isPadro;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $resultats = \App\Models\StreetBarriVell::obtenirLListaCotxes($this->record, false, $this->isPadro);

        // Hem posat un caràcter especial per forçar que el camp es tracti com a text
        // Així s'evita que Excel mostri #### i es pot veure correctament el valor
        $line = [
            $resultats['carrer'],
            $resultats['eliminats'],
            $resultats['insertats'],
            $resultats['errors'],
            $resultats['isPadro'] ? 'true' : 'false',
            "'" . date('Y-m-d H:i:s'),
            json_encode($resultats['detall_errors'], JSON_UNESCAPED_UNICODE),
        ];

        $filename = storage_path('app/vehicles_result.csv');
        if (file_exists($filename)) {
            $handle = fopen($filename, 'a');
            fputcsv($handle, $line, ';');
            fclose($handle);
        } else {
            Log::info("El CSV no existeix: {$filename}");
        }
    }
}
