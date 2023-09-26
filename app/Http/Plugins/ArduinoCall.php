<?php

namespace App\Http\Plugins;

use App\Http\Plugins\ApiCore;

class ArduinoCall extends ApiCore
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
        $payload = ['duration' => $duration];
        return $this->post("in/unlock", $payload);
    }

    public function postOutUnlock($duration)
    {
        $payload = ['duration' => $duration];
        return $this->post("out/unlock", $payload);
    }
}
