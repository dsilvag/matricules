<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Batchable;
use App\Models\Instance;
use App\Models\Vehicle;

class CreateVehiclesPadroJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $matricula;
    protected $domcod;
    protected $perscod;

    /**
     * Create a new job instance.
     */
    public function __construct($matricula, $domcod, $perscod)
    {
        $this->matricula = $matricula;
        $this->domcod = $domcod;
        $this->perscod = $perscod;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $instance = Instance::withoutEvents(function (){
                return Instance::updateOrCreate(
                    ['RESNUME' => 'PADRO','PERSCOD' => $this->perscod,'domicili_acces' => $this->domcod,'is_notificat' => true],
                    ['RESNUME' => 'PADRO','PERSCOD' => $this->perscod,'domicili_acces' => $this->domcod,'is_notificat' => true, 'data_inici' =>"2025-01-01",'data_fi' => "9999-12-31"]
                );
            });

            
            $vehicle = Vehicle::firstOrCreate(
                ['MATRICULA' => $this->matricula, 'instance_id' => $instance->id],
                ['MATRICULA' => $this->matricula, 'instance_id' => $instance->id]
            );

        } catch (\Exception $e) {
            throw $e;
        }
    }

}
