<?php

use Symfony\Component\VarDumper\VarDumper;

function dump($variable){
    
    return VarDumper::dump($variable);
}

function dd($variable){
    
    return VarDumper::dump($variable); die();
}