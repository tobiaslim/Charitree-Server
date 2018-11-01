<?php
namespace App\Utility;

interface IHttpClient{
    public function request(string $method, string $URL, array $params = null, array $headers = null, \Closure $closure = null);

    public function getStatusCode();

    public function getResponseHeaders();

    public function getResponseBody();
}