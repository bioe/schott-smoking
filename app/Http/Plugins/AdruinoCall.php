<?php

namespace App\Http\Plugins;

use App\Http\Plugins\ApiCore;

class AdruinoCall extends ApiCore
{
    public function __construct($endpoint)
    {
        $addSlash = substr($endpoint, -1) != "/" ? "/" : "";
        $this->endpoint = $endpoint . $addSlash;
    }

    public function getSensors()
    {
        return $this->get("sensors");
    }

    public function postInUnlock($duration)
    {
        $payload = ['door_open_seconds' => $duration];
        return $this->post("in/unlock", $payload);
    }

    public function postOutUnlock($duration)
    {
        $payload = ['door_open_seconds' => $duration];
        return $this->post("out/unlock", $payload);
    }
}
