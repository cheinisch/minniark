<?php

    require_once(__DIR__ . "/../vendor/autoload.php");

    use Symfony\Component\Yaml\Yaml;

    foreach (glob(__DIR__ . '/backend/*.php') as $file) {
        require_once $file;
    }

    