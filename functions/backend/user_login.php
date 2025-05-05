<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$userConfigPath = __DIR__ . '/../../userdata/config/user_config.php';
//$user = file_exists($userConfigPath) ? require $userConfigPath : [];


    function check_username($username)
    {
        global $user;
        $value = false;

        if($user['USERNAME'] == $username)
        {
            $value = true;
        }
    
        return $value;
    }


    function get_logintype($password)
    {
        global $user;
        $returnValue = "password";

        $returnValue = $user['AUTH_TYPE'];

        if($returnValue == 'mail' && $password == null)
        {
            send_otp_mail($user['EMAIL'], $user['USERNAME']);
        }

        return $returnValue;

    }

    function send_otp_mail($mail, $username)
    {

        $new_otp = generate_otp();
        send_loginmail($mail, $username, $new_otp);

        return null;
    }

    function check_password($password)
    {
        global $user;
        $value = false;
        if($user['AUTH_TYPE']== 'password'){
            error_log("Password Login");
            if(password_verify($password, $user['PASSWORD_HASH']))
            {
                $value = true;
            }
        }else if($user['AUTH_TYPE']== 'mail')
        {
            error_log("OTP Login");
            if(password_verify($password, $user['MAIL_AUTH_TOKEN']))
            {
                $value = true;
            }
        }
    
        return $value;
    }

    function generate_otp()
    {
        $opt = rand(100000,999999);
        
        save_opt($opt);

        return $opt;
    }

    function save_opt($opt)
    {
        global $user;

        $user['MAIL_AUTH_TOKEN'] = password_hash($opt, PASSWORD_DEFAULT);

        $configContent = "<?php\n//define('IMAGEPORTFOLIO', true);\nreturn " . var_export($user, true) . ";\n";
        $userConfigPath = __DIR__ . '/../../userdata/config/user_config.php';
        file_put_contents($userConfigPath, $configContent, LOCK_EX);
    }

    function send_loginmail($mail, $username, $opt)
    {

        error_log("Sendmail: ".$mail.", ".$username.", ".$opt);

        $to = $mail;
        $subject = "Framora Logincode";

        $message = "
        <html>
        <head>
        <title>Framora Login</title>
        </head>
        <body>
        <p>Hello ".$username.",</p>
        <p>Your Logincode for your Framora account is ".$opt."
        </body>
        </html>
        ";

        // Always set content-type when sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

        // More headers
        $headers .= 'From: <framora@image-portfolio.org>' . "\r\n";

        $sent = mail($to, $subject, $message, $headers);

        if (!$sent) {
            error_log("Sendmail failed for $to");
        } else {
            error_log("Sendmail succeeded for $to");
        }

    }