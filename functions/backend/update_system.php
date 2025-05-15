<?php

    function create_button()
    {
        $docker = is_docker();
        $isNewer = is_newer();

        
        $updateBtn = "";
        $updateDockerBtn = "";

        if($isNewer)
        {
            $updateBtn = '
            <div class="shrink-0">
                <button type="button" id="update-btn" class="relative inline-flex items-center gap-x-1.5 bg-sky-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-sky-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600">
                    <svg class="-ml-0.5 size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                    <path d="M12.5535 16.5061C12.4114 16.6615 12.2106 16.75 12 16.75C11.7894 16.75 11.5886 16.6615 11.4465 16.5061L7.44648 12.1311C7.16698 11.8254 7.18822 11.351 7.49392 11.0715C7.79963 10.792 8.27402 10.8132 8.55352 11.1189L11.25 14.0682V3C11.25 2.58579 11.5858 2.25 12 2.25C12.4142 2.25 12.75 2.58579 12.75 3V14.0682L15.4465 11.1189C15.726 10.8132 16.2004 10.792 16.5061 11.0715C16.8118 11.351 16.833 11.8254 16.5535 12.1311L12.5535 16.5061Z"/> 
                    <path d="M3.75 15C3.75 14.5858 3.41422 14.25 3 14.25C2.58579 14.25 2.25 14.5858 2.25 15V15.0549C2.24998 16.4225 2.24996 17.5248 2.36652 18.3918C2.48754 19.2919 2.74643 20.0497 3.34835 20.6516C3.95027 21.2536 4.70814 21.5125 5.60825 21.6335C6.47522 21.75 7.57754 21.75 8.94513 21.75H15.0549C16.4225 21.75 17.5248 21.75 18.3918 21.6335C19.2919 21.5125 20.0497 21.2536 20.6517 20.6516C21.2536 20.0497 21.5125 19.2919 21.6335 18.3918C21.75 17.5248 21.75 16.4225 21.75 15.0549V15C21.75 14.5858 21.4142 14.25 21 14.25C20.5858 14.25 20.25 14.5858 20.25 15C20.25 16.4354 20.2484 17.4365 20.1469 18.1919C20.0482 18.9257 19.8678 19.3142 19.591 19.591C19.3142 19.8678 18.9257 20.0482 18.1919 20.1469C17.4365 20.2484 16.4354 20.25 15 20.25H9C7.56459 20.25 6.56347 20.2484 5.80812 20.1469C5.07435 20.0482 4.68577 19.8678 4.40901 19.591C4.13225 19.3142 3.9518 18.9257 3.85315 18.1919C3.75159 17.4365 3.75 16.4354 3.75 15Z"/>
                    </svg>
                    Update available
                </button>
            </div>';
            $updateDockerBtn = '
            <div class="shrink-0">
                <button type="button" id="update-btn-docker" class="relative inline-flex items-center gap-x-1.5 bg-sky-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-sky-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600">
                    <svg class="-ml-0.5 size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                    <path d="M12.5535 16.5061C12.4114 16.6615 12.2106 16.75 12 16.75C11.7894 16.75 11.5886 16.6615 11.4465 16.5061L7.44648 12.1311C7.16698 11.8254 7.18822 11.351 7.49392 11.0715C7.79963 10.792 8.27402 10.8132 8.55352 11.1189L11.25 14.0682V3C11.25 2.58579 11.5858 2.25 12 2.25C12.4142 2.25 12.75 2.58579 12.75 3V14.0682L15.4465 11.1189C15.726 10.8132 16.2004 10.792 16.5061 11.0715C16.8118 11.351 16.833 11.8254 16.5535 12.1311L12.5535 16.5061Z"/> 
                    <path d="M3.75 15C3.75 14.5858 3.41422 14.25 3 14.25C2.58579 14.25 2.25 14.5858 2.25 15V15.0549C2.24998 16.4225 2.24996 17.5248 2.36652 18.3918C2.48754 19.2919 2.74643 20.0497 3.34835 20.6516C3.95027 21.2536 4.70814 21.5125 5.60825 21.6335C6.47522 21.75 7.57754 21.75 8.94513 21.75H15.0549C16.4225 21.75 17.5248 21.75 18.3918 21.6335C19.2919 21.5125 20.0497 21.2536 20.6517 20.6516C21.2536 20.0497 21.5125 19.2919 21.6335 18.3918C21.75 17.5248 21.75 16.4225 21.75 15.0549V15C21.75 14.5858 21.4142 14.25 21 14.25C20.5858 14.25 20.25 14.5858 20.25 15C20.25 16.4354 20.2484 17.4365 20.1469 18.1919C20.0482 18.9257 19.8678 19.3142 19.591 19.591C19.3142 19.8678 18.9257 20.0482 18.1919 20.1469C17.4365 20.2484 16.4354 20.25 15 20.25H9C7.56459 20.25 6.56347 20.2484 5.80812 20.1469C5.07435 20.0482 4.68577 19.8678 4.40901 19.591C4.13225 19.3142 3.9518 18.9257 3.85315 18.1919C3.75159 17.4365 3.75 16.4354 3.75 15Z"/>
                    </svg>
                    New Docker Image available
                </button>
            </div>';
        }

        if(!$docker)
        {
            return $updateBtn;
        }else{
            return $updateDockerBtn;
        }

        return $updateDockerBtn;
        
    }

    function is_newer(): bool
    {
        $tempDir = __DIR__ . '/../../temp';
        $tempFile = $tempDir . '/version.json';

        // Prüfen, ob die Datei existiert und jünger als 24h ist
        if (file_exists($tempFile) && (time() - filemtime($tempFile) < 86400)) {
            $versionData = json_decode(file_get_contents($tempFile), true);
            if (isset($versionData['new_version_available'])) {
                return $versionData['new_version_available'];
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Fehler: Versionseintrag ungültig in version.json!"]);
                exit;
            }
        }

        // GitHub API URL für den aktuellsten Release
        $url = "https://api.github.com/repos/cheinisch/Image-Portfolio/releases/latest";

        // GitHub benötigt einen User-Agent Header
        $options = [
            "http" => [
                "header" => "User-Agent: PHP\r\n"
            ]
        ];

        $context = stream_context_create($options);
        $json = file_get_contents($url, false, $context);
        $data = json_decode($json, true);

        if (!isset($data['tag_name'])) {
            http_response_code(500);
            echo json_encode(["error" => "Fehler: Tag-Name nicht gefunden!"]);
            exit;
        }

        $tagName = $data['tag_name'];
        $gitVersion = str_replace("v", "", $tagName);

        // Lokale Version lesen
        $versionFile = __DIR__ . '/../../VERSION';
        if (!file_exists($versionFile)) {
            http_response_code(500);
            echo json_encode(["error" => "Datei VERSION nicht gefunden!"]);
            exit;
        }

        $currentVersion = trim(file_get_contents($versionFile));
        $newVersionAvailable = version_compare($currentVersion, $gitVersion, "<");

        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $versionData = [
            "new_version_available" => $newVersionAvailable,
            "new_version_number" => $gitVersion,
            "new_version_url" => "https://github.com/cheinisch/Image-Portfolio/archive/refs/tags/{$tagName}.zip",
            "last_check" => time()
        ];

        file_put_contents($tempFile, json_encode($versionData, JSON_PRETTY_PRINT));

        return $newVersionAvailable;
    }



    function is_docker():bool
    {
        $dockerTestFile = __DIR__.'/../../docker/dockerversion.ini';

        if(file_exists($dockerTestFile))
        {
            return true;
        }else{
            return false;
        }
    }