<?php

    function hasCollections()
    {
        $hasCollections = false;

        $collectionDir = __DIR__ . '/../../userdata/content/collection/';

        // Suche nach YML-Dateien im Verzeichnis
        $ymlFiles = glob($collectionDir . '*.yml');

        if (!empty($ymlFiles)) {
            $hasCollections = true;
        }

        return $hasCollections;
    }