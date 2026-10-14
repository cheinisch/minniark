<?php

    require_once __DIR__ . '/../session_security.php';

    /**
     * Requires a logged-in session for API endpoints.
     * State-changing requests must also carry a valid CSRF token.
     */
    function secure_API()
    {
        minniark_session_start();
        if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
            minniark_deny(403, 'Not logged in.');
        }

        csrf_protect();
    }

    /**
     * Like secure_API(), but additionally requires admin rights.
     */
    function secure_API_admin()
    {
        secure_API();
        if (!minniark_is_admin()) {
            minniark_deny(403, 'Admin rights required.');
        }
    }
