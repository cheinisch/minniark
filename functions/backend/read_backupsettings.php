<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

    function read_prefix(): string {

        error_log("Read Prefix");
        
        $guid = uniqid();
        $configPath = __DIR__ . '/../../userdata/config/backup_config.php';
    
        error_log("Config path: " . $configPath);
        if (file_exists($configPath)) {
            error_log("config exist");
            include $configPath;
            error_log("backup_prefix: ".$backup_prefix);
            if (isset($backup_prefix) && is_string($backup_prefix)) {
                echo $backup_prefix;
                return $backup_prefix;
            }
        }

        $value = write_guid($guid);
    
        // Fallback-Wert, falls nichts gesetzt
        return $value;
    }


    function write_guid($guid)
    {
        $configPath = __DIR__ . '/../../userdata/config/backup_config.php';

        // Inhalt schreiben
        $content = "<?php\n\$backup_prefix = '" . addslashes($guid) . "';\n";
        if (file_put_contents($configPath, $content)) {
            return $guid;
        }
    }