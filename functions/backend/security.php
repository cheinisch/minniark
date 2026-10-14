<?php

    require_once __DIR__ . '/../session_security.php';

    /**
     * Requires a logged-in session. Redirects to the login page otherwise.
     * State-changing requests (POST, PUT, …) must also carry a valid CSRF token.
     */
    function security_checklogin(){

        minniark_session_start();
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
            // logged in — continue
        } else {
            if (minniark_wants_json()) {
                minniark_deny(403, 'Not logged in.');
            }
            header("Location: " . dashboard_url('index.php'));
            exit;
        }

        csrf_protect();

    }

    // onlyAdmin is to check for Admin Only features (dashboard at this moment)
    function onlyAdmin()
    {
        if (!minniark_is_admin()) {
            if (minniark_wants_json()) {
                minniark_deny(403, 'Admin rights required.');
            }
            header("Location: " . dashboard_url('dashboard.php'));
            exit;
        }
    }

    /**
     * Relative URL to a file in /dashboard, from a dashboard page or a backend_api endpoint.
     */
    function dashboard_url(string $file): string
    {
        $dir = basename(dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        return ($dir === 'backend_api' ? '../' : '') . $file;
    }
