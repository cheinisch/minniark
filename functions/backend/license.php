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

    if (empty($key)) {
        $returnValue['valid'] = false;
        $returnValue['message'] = 'No license key provided.';
        return $returnValue;
    }

    $license_key = $key;
    $store_url = 'https://store.minniark.com';
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
    $status = $licenseData['status'] ?? 0;

    if ($validFor === 0) {
        // explizit unbegrenzt
        $returnValue['active'] = true;
        $returnValue['valid'] = true;
        $returnValue['days'] = 0;
        $returnValue['expired'] = false;
        $returnValue['expired_date'] = null;
    } elseif (is_null($validFor)) {
        if (!empty($expiresAtRaw)) {
            // Ablaufdatum vorhanden
            $expiresAt = new DateTime($expiresAtRaw);
            $now = new DateTime();
            $interval = $now->diff($expiresAt);
            $remainingDays = (int)$interval->format('%r%a');

            $returnValue['expired_date'] = $expiresAt->format('Y-m-d H:i:s');
            $returnValue['valid'] = true;
            $returnValue['active'] = true;
            $returnValue['expired'] = $remainingDays < 0;
            $returnValue['days'] = max(0, $remainingDays);
        } elseif ($status == 2) {
            // unlimited ohne expiresAt, aber aktiv → gültig
            $returnValue['active'] = true;
            $returnValue['valid'] = true;
            $returnValue['days'] = 0;
            $returnValue['expired'] = false;
            $returnValue['expired_date'] = null;
        } else {
            // weder Laufzeit noch Aktiv → ungültig
            $returnValue['valid'] = false;
            $returnValue['expired'] = true;
            $returnValue['message'] = 'No expiration information available.';
        }
    } else {
        // Gültigkeitsdauer berechnen
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
            $returnValue['active'] = true;
            $returnValue['valid'] = true;
            $returnValue['days'] = 0;
            $returnValue['expired'] = true;
        }
    }

    return $returnValue;
}



function activateKey(string $key): void
{
    $store_url = 'https://store.minniark.com';
    $consumer_key = 'ck_ea423fb4a7fa4b19dc116de913ee3c1d3bdbe10f';
    $consumer_secret = 'cs_0e7e4a564cfa9cc1dd5d20c3b1e06493b25577b8';

    $url = "$store_url/wp-json/lmfwc/v2/licenses/activate/" . urlencode($key);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, "$consumer_key:$consumer_secret");

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code !== 200) {
        error_log("License activation failed ($http_code): $response");
    } else {
        error_log("License activated successfully: $key");
    }
}



function deactivateKey(string $key): void
{
    $store_url = 'https://store.minniark.com';
    $consumer_key = 'ck_ea423fb4a7fa4b19dc116de913ee3c1d3bdbe10f';
    $consumer_secret = 'cs_0e7e4a564cfa9cc1dd5d20c3b1e06493b25577b8';

    $url = "$store_url/wp-json/lmfwc/v2/licenses/deactivate/" . urlencode($key);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, "$consumer_key:$consumer_secret");

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code !== 200) {
        error_log("License deactivation failed ($http_code): $response");
    } else {
        error_log("License deactivated successfully: $key");
    }
}

