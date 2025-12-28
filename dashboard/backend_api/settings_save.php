<?php
/**
 * dashboard/backend_api/settings_save.php
 *
 * FIX:
 * - Same license saved again => DO NOT call activate (prevents counter going up)
 * - New/changed license => deactivate old first, then activate new
 * - Removed license => deactivate+clear first
 * - Only AFTER license side-effects => saveSettings($_POST)
 */

require_once __DIR__ . '/../../functions/function_backend.php';
require_once __DIR__ . '/../../app/autoload.php';

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
}
$dp = function (string $label, $value = null) use ($debug): void {
    if (!$debug) return;
    echo "== {$label} ==\n";
    if ($value !== null) {
        print_r($value);
        echo "\n";
    }
};

$projectRoot = dirname(__DIR__, 2);

// Provider base (Creem)
$proxyBase = 'https://api.minniark.com/v1/data/creem';
// or LemonSqueezy:
// $proxyBase = 'https://api.minniark.com/v1/data/lemonsqueezy';

$lm = new LicenseManager($projectRoot, $proxyBase);

$newKeyProvided = array_key_exists('site-license', $_POST);
$newKey = $newKeyProvided ? trim((string)($_POST['site-license'] ?? '')) : null;

// IMPORTANT: read old BEFORE saving settings
$oldKey = $lm->getRawLicenseKey();

$dp('Incoming POST', $_POST);
$dp('Old key (masked)', $lm->getSummary()['masked_key'] ?? '');
$dp('New key empty?', ($newKeyProvided && $newKey === '') ? 'yes' : 'no');

/* -------------------------------------------------------------
 * 1) LICENSE SIDE EFFECTS FIRST
 * ----------------------------------------------------------- */
if ($newKeyProvided) {

    // A) removed license (field cleared)
    if ($newKey === '') {
        $dp('Action', 'REMOVE => deactivateAndClear()');

        try {
            $res = $lm->deactivateAndClear();
            $dp('deactivateAndClear()', $res);
        } catch (Throwable $e) {
            $dp('deactivateAndClear ERROR', $e->getMessage());
        }

        // Ensure saved settings reflect removal
        $_POST['site-license'] = '';

    } else {
        // B) license set / changed / same
        $changed = ($oldKey !== '' && !hash_equals($oldKey, $newKey));
        $wasEmpty = ($oldKey === '');

        if ($changed) {
            $dp('Action', 'CHANGED => deactivate old, then save+activate new');

            // 1) deactivate old (best-effort, not fatal)
            try {
                $resOld = $lm->sync('deactivate', null, true);
                $dp('sync(deactivate old)', $resOld);
            } catch (Throwable $e) {
                $dp('sync(deactivate old) ERROR', $e->getMessage());
            }

            // 2) save new key (LicenseManager clears cache so old instance_id cannot leak)
            try {
                $lm->saveLicenseKey($newKey);
                $dp('saveLicenseKey(new)', 'ok');
            } catch (Throwable $e) {
                $dp('saveLicenseKey(new) ERROR', $e->getMessage());
            }

            // 3) activate new (ONLY because key changed)
            try {
                $resAct = $lm->sync('activate', null, true);
                $dp('sync(activate new)', $resAct);
            } catch (Throwable $e) {
                $dp('sync(activate new) ERROR', $e->getMessage());
            }

            // 4) validate to refresh cache (optional)
            try {
                $resVal = $lm->sync('validate', null, true);
                $dp('sync(validate)', $resVal);
            } catch (Throwable $e) {
                $dp('sync(validate) ERROR', $e->getMessage());
            }

        } elseif ($wasEmpty) {
            $dp('Action', 'NEW (was empty) => save+activate once');

            // first time set: save + activate once
            try {
                $lm->saveLicenseKey($newKey);
                $dp('saveLicenseKey(new)', 'ok');
            } catch (Throwable $e) {
                $dp('saveLicenseKey(new) ERROR', $e->getMessage());
            }

            try {
                $resAct = $lm->sync('activate', null, true);
                $dp('sync(activate)', $resAct);
            } catch (Throwable $e) {
                $dp('sync(activate) ERROR', $e->getMessage());
            }

            try {
                $resVal = $lm->sync('validate', null, true);
                $dp('sync(validate)', $resVal);
            } catch (Throwable $e) {
                $dp('sync(validate) ERROR', $e->getMessage());
            }

        } else {
            // SAME KEY saved again -> DO NOT ACTIVATE (prevents counter increment!)
            $dp('Action', 'SAME KEY => skip activate, just save + validate');

            // keep settings in sync (no side effect)
            try {
                $lm->saveLicenseKey($newKey);
                $dp('saveLicenseKey(same)', 'ok');
            } catch (Throwable $e) {
                $dp('saveLicenseKey(same) ERROR', $e->getMessage());
            }

            // Refresh cache only (safe)
            try {
                $resVal = $lm->sync('validate', null, true);
                $dp('sync(validate)', $resVal);
            } catch (Throwable $e) {
                $dp('sync(validate) ERROR', $e->getMessage());
            }
        }

        // persist final key in settings save
        $_POST['site-license'] = $newKey;
    }
}

/* -------------------------------------------------------------
 * 2) NOW SAVE SETTINGS
 * ----------------------------------------------------------- */
$dp('Saving settings via saveSettings()', null);

$result = false;
try {
    $result = saveSettings($_POST);
    $dp('saveSettings() result', $result ? 'true' : 'false');
} catch (Throwable $e) {
    $dp('saveSettings() ERROR', $e->getMessage());
    $result = false;
}

/* -------------------------------------------------------------
 * 3) Done
 * ----------------------------------------------------------- */
if ($debug) {
    try {
        $dp('Final summary', $lm->getSummary());
    } catch (Throwable $e) {
        $dp('Final summary ERROR', $e->getMessage());
    }
    exit;
}

redirectBack();
