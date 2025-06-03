<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require_once __DIR__ . '/../../vendor/autoload.php';

    use Symfony\Component\Yaml\Yaml;

    $user = null; // wird durch getUserFromUsername() gefüllt


    function check_username($username): bool
    {
        $ymlDir = __DIR__ . '/../../userdata/config/user/';
        if (!is_dir($ymlDir)) {
            return false;
        }

        $files = scandir($ymlDir);

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) !== 'yml') {
                continue;
            }

            $filePath = $ymlDir . $file;
            $data = Symfony\Component\Yaml\Yaml::parseFile($filePath);

            if (
                isset($data['user']['username']) &&
                strtolower($data['user']['username']) === strtolower($username)
            ) {
                return true;
            }
        }

        return false;
    }



    function get_logintype(string $username): string
    {
        $user = getUserDataFromUsername($username);

        if (!$user || !isset($user['auth_type'])) {
            return 'password'; // Fallback
        }

        return $user['auth_type'];
    }

    function send_otp_mail($mail, $username)
    {

        $new_otp = generate_otp();
        send_loginmail($mail, $username, $new_otp);

        return null;
    }

    function check_password($password, $username): bool
    {
        $user = getUserDataFromUsername($username);

        if (!$user) return false;

        if ($user['auth_type'] === 'password') {
            return password_verify($password, $user['password']);
        } elseif ($user['auth_type'] === 'mail' && isset($user['mail_auth_token'])) {
            return password_verify($password, $user['mail_auth_token']);
        }

        return false;
    }

    function generate_otp()
    {
        $opt = rand(100000,999999);
        
        save_opt($opt);

        return $opt;
    }

    function save_otp($otp): void
    {
        global $user;

        if (!$user) return;

        $user['mail_auth_token'] = password_hash($otp, PASSWORD_DEFAULT);

        // Benutzerdatei aktualisieren
        $username = $user['username'];
        updateUserData($username, ['mail_auth_token' => $user['mail_auth_token']], null);
    }

    function send_loginmail($mail, $username, $opt)
    {

        error_log("Sendmail: ".$mail.", ".$username.", ".$opt);

        $to = $mail;
        $subject = "Minniark Logincode";

        $message = "
        <html>
        <head>
        <title>Minniark Login</title>
        </head>
        <body>
        <p>Hello ".$username.",</p>
        <p>Your Logincode for your Minniark account is ".$opt."
        </body>
        </html>
        ";

        // Always set content-type when sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

        // More headers
        $headers .= 'From: <minniark@minniark.app>' . "\r\n";

        $sent = mail($to, $subject, $message, $headers);

        if (!$sent) {
            error_log("Sendmail failed for $to");
        } else {
            error_log("Sendmail succeeded for $to");
        }

    }