<?php
require_once __DIR__ . '/../../functions/function_backend.php';
require_once __DIR__ . '/../../app/autoload.php'; // loads LicenseManager

// Optional: protect this endpoint too
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// security_checklogin();

function redirectBack(): void {
    header("Location: ../dashboard-system.php");
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    http_response_code(405);
    echo "Method Not Allowed";
    exit;
}

$debug = isset($_GET['debug']) && $_GET['debug'] === '1';
if ($debug) {
    header('Content-Type: text/plain; charset=utf-8');
    echo "POST:\n";
    print_r($_POST);
    echo "\n\n";
}

// 1) Save normal settings (settings.yml etc.)
$result = saveSettings($_POST);

// 2) Handle license side-effects via LicenseManager
if (array_key_exists('site-license', $_POST)) {
    $newKey = trim((string)($_POST['site-license'] ?? ''));

    // ✅ project root übergeben (…/dashboard/backend_api -> 2 Ebenen hoch)
    $lm = new LicenseManager(dirname(__DIR__, 2));

    $oldKey = $lm->getRawLicenseKey();

    if ($newKey === '') {
        // Feld geleert -> best-effort deaktivieren + local wipe (license+uuid+cache)
        if ($debug) echo "Removing license...\n";
        $res = $lm->deactivateAndClear();
        if ($debug) {
            echo "deactivateAndClear():\n";
            print_r($res);
            echo "\n";
        }

        // Zusätzlich: settings.yml hat evtl. durch saveSettings() license:'' geschrieben.
        // Das normalisiert LicenseManager beim saveSettings() intern bereits.
        // Falls du sicher gehen willst, kannst du auch:
        // $lm->saveLicenseKey('');

    } else {
        // Key gesetzt / geändert
        if ($oldKey !== '' && $oldKey !== $newKey) {
            // Alter Key deaktivieren (best effort; nicht fatal)
            if ($debug) echo "Deactivating old key...\n";
            try {
                $resOld = $lm->sync('deactivate', null, true);
                if ($debug) {
                    echo "sync(deactivate old):\n";
                    print_r($resOld);
                    echo "\n";
                }
            } catch (Throwable $e) {
                if ($debug) echo "deactivate old ERROR: " . $e->getMessage() . "\n";
            }
        }

        // Neuen Key speichern + aktivieren + cache refresh
        if ($debug) echo "Saving new key...\n";
        $lm->saveLicenseKey($newKey);

        if ($debug) echo "Activating new key...\n";
        try {
            $resAct = $lm->sync('activate', null, true);
            if ($debug) {
                echo "sync(activate new):\n";
                print_r($resAct);
                echo "\n";
            }
        } catch (Throwable $e) {
            // Nicht fatal, Dashboard soll Fehler später anzeigen
            if ($debug) echo "activate new ERROR: " . $e->getMessage() . "\n";
        }

        // validate (füllt cache) – nicht fatal
        if ($debug) echo "Validating (refresh cache)...\n";
        try {
            $resVal = $lm->sync('validate', null, true);
            if ($debug) {
                echo "sync(validate):\n";
                print_r($resVal);
                echo "\n";
            }
        } catch (Throwable $e) {
            if ($debug) echo "validate ERROR: " . $e->getMessage() . "\n";
        }
    }
}

if ($debug) {
    echo "\nResult saveSettings(): " . ($result ? "true" : "false") . "\n";
    exit;
}

redirectBack();
