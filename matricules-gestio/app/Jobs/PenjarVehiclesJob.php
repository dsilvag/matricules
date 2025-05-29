<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

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

        $detallErrorsText = json_encode($resultats['detall_errors'], JSON_UNESCAPED_UNICODE);

        $filename = 'vehicles_result.csv';

        $line = [
            $resultats['carrer'],
            $resultats['eliminats'],
            $resultats['insertats'],
            $resultats['errors'],
            $resultats['isPadro'] ? 'true' : 'false',
            date('Y-m-d H:i:s'),
            $detallErrorsText,
        ];

        // Verificar si l'arxiu existeix i te contingut
        if (Storage::exists($filename) && Storage::size($filename) > 0) {
            // Escriure tot menys el header
            $stream = Storage::append($filename, implode(';', array_map(function ($field) {
                $field = str_replace('"', '""', $field);
                if (strpos($field, ';') !== false || strpos($field, '"') !== false) {
                    return '"' . $field . '"';
                }
                return $field;
            }, $line)));
        } else {
            //Si el fitxer no existeix l'hem de crear
            $csvData = [
                ['street', 'eliminats', 'insertats', 'errors', 'isPadro','timestamps','detall_errors'], // Header
                $line
            ];

            $handle = fopen('php://temp', 'r+');
            foreach ($csvData as $row) {
                fputcsv($handle, $row,';');
            }
            rewind($handle);
            $csvContent = stream_get_contents($handle);
            fclose($handle);

            Storage::put($filename, $csvContent);
        }

        //\Log::info("CSV actualizado: {$filename}");
    }
}
