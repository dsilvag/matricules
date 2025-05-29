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
        //\Log::info("CSV actualizado: {$filename}");
    }
}
