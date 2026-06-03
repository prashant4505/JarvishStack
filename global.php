<?php

use Symfony\Component\VarDumper\VarDumper;

function dump(mixed $variable): void
{
    VarDumper::dump($variable);
}

function dd(mixed $variable): never
{
    VarDumper::dump($variable);
    die();
}
