<?php
    foreach (glob(__DIR__ . '/backend/*.php') as $file) {
        require_once $file;
    }

    