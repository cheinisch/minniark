<?php

    require_once( __DIR__ . "/../functions/function_backend.php");

    // Input POSTS
    $username = $_POST['username'] ?? null;
    $password = $_POST['password'] ?? null;

    // Some Vars
    $step_2 = false;
    $user_exist = false;
    $user_wrong = false;
    $pass_wrong = false;
    $login_type = "";

    if($username != null){
        $user_exist = check_username($username);
        if($user_exist)
        {
            $step_2 = true;
        }else{
            $user_wrong = true;
        }
    }

   if($step_2)
   {
        $login_type = get_logintype($password);
   }

   if($username != null && $password != null)
   {
        if(check_password($password))
        {
            session_start();
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $user['USERNAME'];
            header("Location: dashboard.php");
            exit;
        }else{
            $pass_wrong = true;
        }
   }

?>


<!DOCTYPE html>
<html lang="<?php echo get_language(); ?>">
    <head>
        <meta charset="UTF-8">        
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login - <?php echo get_sitename(); ?></title>
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    </head>
    <body>
        <div class="w-full h-screen bg-neutral-200 dark:bg-gray-950 flex flex-col">
            <div id="username" class="<?php if($step_2){ echo "hidden"; } ?> bg-white rounded-none md:rounded md:max-w-md max-w-full m-auto md:min-w-md min-w-full">
                <div class="py-5 px-5">
                    <form id="user-form" method="post" action="login.php">
                        <div>
                            <h2 class="text-3xl text-sky-600"><?php echo get_sitename(); ?></h2>
                        </div>
                        <div class="text-2xl py-5">
                            <div class="pb-6">
                                Login
                            </div>
                            <div>
                                <input type="text" id="username" name="username" class="border-b focus:border-b-2 focus:border-sky-500 outline-none border-gray-400 min-w-full " placeholder="Username">
                                <span id="wrong-user" class="<?php if(!$user_wrong){ echo "invisible"; }?> text-sm text-red-500">Username is wrong</span>
                            </div>
                        </div>
                        <div class="py-5">
                            <div>
                                <button type="submit" class="bg-sky-600 hover:bg-sky-500 text-white px-5 py-2">Login</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Password -->
            <div id="password" class="<?php if(!$step_2){ echo "hidden"; }?>  bg-white rounded-none md:rounded md:max-w-md max-w-full m-auto md:min-w-md min-w-full">
                <div class="py-5 px-5">
                    <form id="pass-form" method="post" action="login.php">
                        <div>
                            <h2 class="text-xl text-sky-600 pb-3 mb-2">Login at <?php echo get_sitename(); ?></h2>
                        </div>
                        <div class="pb-5">
                            <nav class="flex" aria-label="Breadcrumb">
                                <ol role="list" class="flex items-center space-x-4">
                                    <li>
                                    <div>
                                        <a href="login.php" class="text-gray-400 hover:text-gray-500">
                                        <svg class="size-5 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                            <path fill-rule="evenodd" d="M9.293 2.293a1 1 0 0 1 1.414 0l7 7A1 1 0 0 1 17 11h-1v6a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-3a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-6H3a1 1 0 0 1-.707-1.707l7-7Z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="sr-only">Home</span>
                                        </a>
                                    </div>
                                    </li>
                                    <li>
                                    <div class="flex items-center">
                                        <svg class="size-5 shrink-0 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                        <path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700"><?php echo $username; ?></span>
                                    </div>
                                    </li>
                                </ol>
                            </nav>                          
                        </div>
                        <div class="text-2xl py-5">
                            <div class="pb-6">
                                <span id="passwordtype"><?php if($login_type == "password"){ echo "Password"; }else{ echo "Enter OTP"; } ?></span>
                            </div>
                            <div>
                                <input type="hidden" id="username" name="username" value="<?php echo $username; ?>">
                                <input type="password" name="password" id="password" class="border-b focus:border-b-2 focus:border-sky-500 outline-none border-gray-400 min-w-full " placeholder="Password">
                                <span id="wrong-pass" class="<?php if(!$pass_wrong){ echo "invisible"; }?> text-sm text-red-500">Password is wrong</span>
                            </div>
                        </div>
                        <div class="py-5">
                            <div>
                                <button type="submit" class="bg-sky-600 hover:bg-sky-500 text-white px-5 py-2">Login</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>