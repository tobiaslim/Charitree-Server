<?php
namespace App\Contracts;

interface IAuthenticate{
    public function login(array $credentials);
}