<?php

namespace App\Http\Plugins;

abstract class ApiCore
{
    protected $endpoint = "http://localhost:8001/api/";
    protected $appId = "";
    protected $token = "";
    protected $auth = "5";
    public function setToken($token)
    {
        $this->token = $token;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setAuth($auth)
    {
        $this->auth = $auth;
    }

    public function getAuth()
    {
        return $this->auth;
    }

    public function post($path, $payload = null)
    {
        return $this->send("POST", $path, $payload);
    }

    public function put($path, $payload = null)
    {
        return $this->send("PUT", $path, $payload);
    }

    public function get($path, $payload = null)
    {
        return $this->send("GET", $path, $payload);
    }

    protected function headers()
    {
        return [
            'Content-Type: application/json; charset=utf-8',
            'Accept: application/json',
            'Cache-Control: no-cache',
            'appid: ' . $this->appId,
            'token: ' . $this->token,
            'auth: ' . $this->auth,
        ];
    }

    private function send(String $method, String $path, array $payload = null)
    {
        $url = $this->endpoint . $path;

        if ($method == "GET" && $payload != null) {
            $url = $url . "?" . http_build_query($payload);
        }
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers());
        curl_setopt($ch, CURLOPT_TIMEOUT, 5); //5 seconds

        if ($method == "POST" || $method == "PUT") {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

            if ($method == "PUT") {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            }
        }

        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($httpcode == "200" || $httpcode == "201") {
            if (is_string($response)) {
                $output = json_decode($response, true);
                return $output;
            } else {
                throw (new \Exception('Response is not a string'));
            }
        } else {
            throw (new \Exception(curl_error($ch)));
        }
    }
}
