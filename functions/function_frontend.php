<?php

    foreach (glob(__DIR__ . '/frontend/*.php') as $file) {
        require_once $file;
    }

    