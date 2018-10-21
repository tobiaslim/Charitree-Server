<?php
namespace App\Services\Contracts;

interface IAuthenticate{
    public function login(array $credentials);
}