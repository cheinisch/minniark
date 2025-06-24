<?php

    require_once __DIR__ . '/../../functions/function_backend.php';

    if (isset($_GET['save']) && $_GET['save'] === 'active') {

        $value = $_POST['nav_enabled'];

        if($value == 1)
        {
            $data['custom_nav'] = true;
        }else{
            $data['custom_nav'] = false;
        }

        echo $value;

        print_r($data);

        var_dump($data);

        $return = saveSettings($data);

        if($return)
        {
            header("Location: ../dashboard-menu.php");
            exit;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['save'] === 'menu') {
        $menu = json_decode($_POST['menu_json'] ?? '[]', true);

        if (is_array($menu)) {
            $return = save_navigation($menu); // speichert in YAML
            if($return)
            {
                header("Location: ../dashboard-menu.php");
            }
        } else {
            //http_response_code(400); // Bad Request
        }
        exit;
    }


    