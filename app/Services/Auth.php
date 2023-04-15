<?php

namespace FleetCart\Services;

class Auth
{
    function __construct($text)
    {
        $this->Data = $text;
        $this->G = new GeneralClass(env('CLIENT_CODE'), env('CLIENT_USERNAME'), env('CLIENT_PASSWORD'));
    }
}