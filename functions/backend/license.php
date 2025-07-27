<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;

function getLicenseKey()
{
    $settings = get_settings_array();
    return $settings['license'] ?? '';
}

function getLicenseInformation()
{
    $returnValue = [];

    $key = getLicenseKey();
    error_log("Licensekey: " . $key);
    if (!empty($key)) {

        $license_key = $key;
        $store_url = 'https://store.minniark.com';

        // REST API-Zugangsdaten
        $consumer_key = 'ck_ea423fb4a7fa4b19dc116de913ee3c1d3bdbe10f';
        $consumer_secret = 'cs_0e7e4a564cfa9cc1dd5d20c3b1e06493b25577b8';

        $check_url = "$store_url/wp-json/lmfwc/v2/licenses/$license_key";

        $ch = curl_init($check_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "$consumer_key:$consumer_secret");
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $data = json_decode($response, true);

        if ($http_code === 404 || (isset($data['code']) && $data['code'] === 'lmfwc_rest_data_error')) {
            $returnValue['valid'] = false;
            $returnValue['message'] = $data['message'] ?? 'License not found.';
            return $returnValue;
        }

        if (!isset($data['data'])) {
            $returnValue['valid'] = false;
            $returnValue['message'] = 'Unexpected response.';
            return $returnValue;
        }

        $licenseData = $data['data'];

        $returnValue['timesActivated'] = $licenseData['timesActivated'] ?? 0;
        $returnValue['timesActivatedMax'] = $licenseData['timesActivatedMax'] ?? 0;

        $validFor = $licenseData['validFor'] ?? null;
        $createdAt = new DateTime($licenseData['createdAt']);
        $expiresAtRaw = $licenseData['expiresAt'] ?? null;

        // Unbegrenzte Lizenz
        if ($validFor === 0) {
            $returnValue['active'] = true;
            $returnValue['valid'] = true;
            $returnValue['days'] = 0;
            $returnValue['expired'] = false;
            $returnValue['expired_date'] = null;
        }
        // Ablaufdatum vorhanden (aber keine Gültigkeitsdauer)
        elseif (is_null($validFor)) {
            if (!empty($expiresAtRaw)) {
                $expiresAt = new DateTime($expiresAtRaw);
                $now = new DateTime();
                $interval = $now->diff($expiresAt);
                $remainingDays = (int)$interval->format('%r%a');

                $returnValue['expired_date'] = $expiresAt->format('Y-m-d H:i:s');

                if ($remainingDays >= 0) {
                    $returnValue['active'] = true;
                    $returnValue['valid'] = true;
                    $returnValue['days'] = $remainingDays;
                    $returnValue['expired'] = false;
                } else {
                    $returnValue['active'] = true;
                    $returnValue['valid'] = true; // <- WICHTIG
                    $returnValue['days'] = 0;
                    $returnValue['expired'] = true;
                }
            } else {
                // Keine Information zur Gültigkeit
                $returnValue['valid'] = false;
                $returnValue['expired'] = true;
                $returnValue['message'] = 'No expiration information available.';
            }
        }
        // Gültigkeitsdauer gesetzt (normaler Ablauf)
        else {
            $expiresAt = clone $createdAt;
            $expiresAt->modify("+$validFor days");
            $now = new DateTime();
            $interval = $now->diff($expiresAt);
            $remainingDays = (int)$interval->format('%r%a');

            $returnValue['expired_date'] = $expiresAt->format('Y-m-d H:i:s');

            if ($remainingDays >= 0) {
                $returnValue['active'] = true;
                $returnValue['valid'] = true;
                $returnValue['days'] = $remainingDays;
                $returnValue['expired'] = false;
            } else {
                $returnValue['valid'] = false;
                $returnValue['expired'] = true;
            }
        }

        return $returnValue;
    }

    $returnValue['valid'] = false;
    $returnValue['message'] = 'No license key provided.';
    return $returnValue;
}
