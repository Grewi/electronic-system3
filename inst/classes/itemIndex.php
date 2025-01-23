<?php
namespace system\inst\classes;

interface itemIndex
{
    public function params() : void;
    public function files() : void;
    public function database() : void;
    public function finish() : void;
}