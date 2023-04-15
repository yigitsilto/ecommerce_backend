<?php

namespace FleetCart;

class ParamConnectionHelper
{

    private $connection;

    public function setConnection(){
        $this->connection = new \SoapClient(env('PARAM_URL'));
    }

    public function getConnection(){
        return $this->connection;
    }

}
