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

        $new_otp = generate_otp($username);
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
            error_log("OTP: ".$password." DATA ".$user['mail_auth_token']);
            if(password_verify($password, $user['mail_auth_token']))
            {
            }else{
                error_log("Token is wrong");
            }
            return password_verify($password, $user['mail_auth_token']);
        }

        return false;
    }

    function generate_otp($username)
    {
        error_log("Generate OPT");

        $opt = rand(100000,999999);
        
        save_otp($opt, $username);

        return $opt;
    }

    function save_otp($otp, $username): void
    {

        $user['mail_auth_token'] = password_hash($otp, PASSWORD_DEFAULT);
        //$user['mail_auth_token'] = $otp;

        $result = updateUserData($username, ['mail_auth_token' => $user['mail_auth_token']], $username);
    }

    function send_loginmail($mail, $username, $opt)
    {
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
        $headers .= 'From: <minniark@image-portfolio.org>' . "\r\n";

        $sent = mail($to, $subject, $message, $headers);

        if (!$sent) {
            error_log("Sendmail failed for $to");
        } else {
            error_log("Sendmail succeeded for $to");
        }

    }