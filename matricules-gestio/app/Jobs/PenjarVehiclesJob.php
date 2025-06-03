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

        // Preparem la línia per escriure al CSV
        $line = [
            $resultats['carrer'],
            $resultats['eliminats'],
            $resultats['insertats'],
            $resultats['errors'],
            $resultats['isPadro'] ? 'true' : 'false',
            "'" . date('Y-m-d H:i:s'),
            json_encode($resultats['detall_errors'], JSON_UNESCAPED_UNICODE),
        ];

        $filename = storage_path('app/private/vehicles_result.csv');

        if (file_exists($filename)) {
            // Obrim el fitxer en mode afegir
            $handle = fopen($filename, 'a');

            if ($handle) {
                // Intentem bloquejar el fitxer exclusivament per evitar escriptura concurrent
                if (flock($handle, LOCK_EX)) {
                    fputcsv($handle, $line, ';');   // Escrivim la línia al CSV
                    fflush($handle);                // Ens assegurem que es desa al disc
                    flock($handle, LOCK_UN);        // Alliberem el bloqueig
                } else {
                    \Log::warning("No s'ha pogut bloquejar el fitxer CSV per escriure: {$filename}");
                }
                fclose($handle);
            } else {
                \Log::error("No s'ha pogut obrir el fitxer CSV per escriure: {$filename}");
            }
        } else {
            \Log::info("El fitxer CSV no existeix: {$filename}");
        }
    }
}
