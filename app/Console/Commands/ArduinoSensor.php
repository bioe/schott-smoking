<?php

namespace App\Console\Commands;

use App\Http\Plugins\AdruinoCall;
use App\Models\Sensor;
use App\Models\Station;
use Exception;
use Illuminate\Console\Command;

class ArduinoSensor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'arduino:sensors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Air, Temp value from Arduino';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $stations = Station::where('active', true)->get();
        foreach ($stations as $station) {
            if (empty($station->ip)) continue;

            $this->comment($station->ip);

            //Call Arduino Api
            $call = new AdruinoCall($station->ip);
            try {
                $response = $call->getSensors();
                if ($response != null) {
                    //Save Value
                    if (!empty($response['iaq'])) {
                        Sensor::create([
                            'station_id' => $station->id,
                            'type' => SENSOR_AIR,
                            'value' => number_format($response['iaq'], 2)
                        ]);
                    }

                    if (!empty($response['raw_temperature'])) {
                        Sensor::create([
                            'station_id' => $station->id,
                            'type' => SENSOR_TEMP,
                            'value' =>  number_format($response['raw_temperature'], 2)
                        ]);
                    }
                }
            } catch (Exception $e) {
                \Log::error($e);
            }
        }
    }
}
