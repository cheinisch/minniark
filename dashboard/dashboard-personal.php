<?php

  require_once( __DIR__ . "/../functions/function_backend.php");
  $settingspage = "personal";
  security_checklogin();

?>

<!DOCTYPE html>
<html lang="<?php echo get_language(); ?>">
    <head>      
        <meta charset="UTF-8">        
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard - <?php echo get_sitename(); ?></title>

        <!-- Tailwind CSS -->
        <link rel="stylesheet" href="css/tailwind.css">
    </head>
    <body class="min-h-screen flex flex-col">
        <header>
            <nav class="bg-neutral-200 dark:bg-gray-950 shadow-sm">
                <div class="mx-auto max-w-12xl px-4 sm:px-6 lg:px-8">
                  <div class="flex h-16 justify-between">
                    <div class="flex">
                      <div class="mr-2 -ml-2 flex items-center md:hidden">
                        <!-- Mobile menu button -->
                        <button type="button" class="relative inline-flex items-center justify-center rounded-md p-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:ring-2 focus:ring-sky-500 focus:outline-hidden focus:ring-inset" aria-controls="mobile-menu" aria-expanded="false">
                          <span class="absolute -inset-0.5"></span>
                          <span class="sr-only">Open main menu</span>
                          <!--
                            Icon when menu is closed.
              
                            Menu open: "hidden", Menu closed: "block"
                          -->
                          <svg class="block size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                          </svg>
                          <!--
                            Icon when menu is open.
              
                            Menu open: "block", Menu closed: "hidden"
                          -->
                          <svg class="hidden size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                          </svg>
                        </button>
                      </div>
                      <div class="hidden md:ml-6 md:flex md:space-x-8">
                        <!-- Current: "border-indigo-500 text-gray-900", Default: "border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700" -->
                        <a href="dashboard.php" class="inline-flex items-center border-b-2 border-sky-400 px-1 pt-1 text-base font-medium text-sky-400">Dashboard</a>
                        <a href="media.php" class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-base font-medium text-gray-600 dark:text-gray-300 hover:border-sky-400 hover:text-sky-400">Images</a>
                        <a href="blog.php" class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-base font-medium text-gray-600 dark:text-gray-300 hover:border-sky-400 hover:text-sky-400">Blogposts</a>
                        <a href="pages.php" class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-base font-medium text-gray-600 dark:text-gray-300 hover:border-sky-400 hover:text-sky-400">Pages</a>
                      </div>
                    </div>
                    <div class="flex items-center">
                      <?php echo create_update_button(); ?>
                      <div class="hidden md:ml-4 md:flex md:shrink-0 md:items-center">
                        <button type="button" class="relative rounded-full bg-white p-1 text-gray-400 hover:text-gray-500 focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 focus:outline-hidden">
                          <span class="absolute -inset-1.5"></span>
                          <span class="sr-only">View notifications</span>
                          <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                          </svg>
                        </button>
              
                        <!-- Profile dropdown -->
                        <div class="relative ml-3">
                          <div>
                            <button type="button" class="relative flex rounded-full bg-white text-sm focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 focus:outline-hidden" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                              <span class="absolute -inset-1.5"></span>
                              <span class="sr-only">Open user menu</span>
                              <img class="size-8 rounded-full" src="<?php echo get_userimage($_SESSION['username']); ?>" alt="">
                            </button>
                          </div>
              
                          <!--
                            Dropdown menu, show/hide based on menu state.
              
                            Entering: "transition ease-out duration-200"
                              From: "transform opacity-0 scale-95"
                              To: "transform opacity-100 scale-100"
                            Leaving: "transition ease-in duration-75"
                              From: "transform opacity-100 scale-100"
                              To: "transform opacity-0 scale-95"
                          -->
                          <div class="absolute right-0 z-10 mt-2 w-48 origin-top-right  bg-white py-1 shadow-lg ring-1 ring-black/5 focus:outline-hidden hidden" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                            <!-- Active: "bg-gray-100 outline-hidden", Not Active: "" -->
                            <a href="dashboard-personal.php" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-0">Your Profile</a>
                            
                            <a href="login.php?logout=true" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-2">Sign out</a>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              
                <!-- Mobile menu, show/hide based on menu state. -->
                <div class="md:hidden" id="mobile-menu">
                  <div class="space-y-1 pt-2 pb-3">
                    <!-- Current: "bg-sky-50 border-sky-500 text-sky-700", Default: "border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700" -->
                    <a href="dashboard.php" class="block border-l-4 border-sky-400 py-2 pr-4 pl-3 text-base font-medium text-sky-400 sm:pr-6 sm:pl-5">Dashboard</a>
                    <a href="media.php" class="block border-l-4 border-transparent py-2 pr-4 pl-3 text-base font-medium text-gray-300 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-700 sm:pr-6 sm:pl-5">Images</a>
                    <a href="blog.php" class="block border-l-4 border-transparent py-2 pr-4 pl-3 text-base font-medium text-gray-300 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-700 sm:pr-6 sm:pl-5">Blogposts</a>
                    <a href="pages.php" class="block border-l-4 border-transparent py-2 pr-4 pl-3 text-base font-medium text-gray-300 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-700 sm:pr-6 sm:pl-5">Pages</a>
                  </div>
                  <div class="border-t border-gray-500 pt-4 pb-3">
                    <div class="mt-3 space-y-1">
                      <span class="block px-4 py-2 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6">Overview</span>
                      <div class="pl-5">
                        <a href="dashboard.php" class="block px-4 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6">Dashboard</a>
                      </div>
                      <span class="block px-4 py-2 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6">Settings</span>
                      <div class="pl-5">
                        <?php include('inc/dashboard-mainnav.php'); ?>
                      </div>
                    </div>
                  </div>
                  <div class="border-t border-gray-200 pt-4 pb-3">
                    <div class="flex items-center px-4 sm:px-6">
                      <div class="shrink-0">
                        <img class="size-10 rounded-full" src="<?php echo get_userimage($_SESSION['username']); ?>" alt="">
                      </div>
                      <div class="ml-3">
                        <div class="text-base font-medium text-gray-300"><?php echo get_username($_SESSION['username']); ?></div>
                        <div class="text-sm font-medium text-gray-500"><?php echo get_usermail($_SESSION['username']); ?></div>
                      </div>
                      <button type="button" class="relative ml-auto shrink-0 rounded-full bg-white p-1 text-gray-400 hover:text-gray-500 focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 focus:outline-hidden">
                        <span class="absolute -inset-1.5"></span>
                        <span class="sr-only">View notifications</span>
                        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                        </svg>
                      </button>
                    </div>
                    <div class="mt-3 space-y-1">
                      <a href="dashboard-personal.php" class="block px-4 py-2 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6">Your Profile</a>
                      
                      <a href="login.php?logout=true" class="block px-4 py-2 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6">Sign out</a>
                    </div>
                  </div>
                </div>
              </nav>
              
        </header>
        <div class="flex flex-1">
          <aside class="hidden md:block max-w-[280px] w-full bg-neutral-200 dark:bg-gray-950 overflow-auto flex-1">
            <?php include('inc/dashboard-sidenav.php'); ?>
          </aside>
          <main class="flex-1 bg-white dark:bg-neutral-900 p-6 overflow-auto">
            <!-- Settings forms -->
            <div class="divide-y divide-gray-400 dark:divide-white/5">
              <div class="grid max-w-7xl grid-cols-1 gap-x-8 gap-y-10 px-4 py-16 sm:px-6 md:grid-cols-3 lg:px-8">
                <div>
                  <h2 class="text-base/7 font-semibold text-gray-900 dark:text-white">Personal Information</h2>
                  <p class="mt-1 text-sm/6 text-gray-400">Use a permanent address where you can receive mail.</p>
                </div>

                <form class="md:col-span-2" action="backend_api/save_user_data.php?userdata" method="post" id="change-data-form">
                  <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:max-w-xl sm:grid-cols-6">
                    <!-- Notifications für Benutzerdaten -->
                    <div id="notification-success-user" class="hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 col-span-full relative mb-4" role="alert">
                      <strong class="font-bold">Success!</strong>
                      <span class="block sm:inline">Userdata has been saved.</span>
                    </div>

                    <div id="notification-error-user" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 col-span-full relative mb-4" role="alert">
                      <strong class="font-bold">Error!</strong>
                      <span class="block sm:inline">Something is wrong.</span>
                    </div>
                    <div class="col-span-3 md:col-span-full flex items-center gap-x-8">
                      <img src="<?php echo get_userimage($_SESSION['username']); ?>" alt="" class="size-24 flex-none bg-gray-800 object-cover">
                      <!--<div>
                        <button type="button" class=" bg-white/10 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-white/20">Change avatar</button>
                        <p class="mt-2 text-xs/5 text-gray-400">JPG, GIF or PNG. 1MB max.</p>
                      </div>-->
                    </div>

                    <div class="col-span-3">
                      <label for="first-name" class="block text-sm/6 font-medium text-gray-700 dark:text-white">Display name</label>
                      <div class="mt-2">
                        <input type="text" name="display-name" id="display-name" value="<?php echo get_displayname($_SESSION['username']); ?>" autocomplete="given-name" class="block w-full  bg-white/5 px-3 py-1.5 text-base text-gray-700 dark:text-white outline-1 -outline-offset-1 outline-gray-500 dark:outline-white/10 placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-sky-500 sm:text-sm/6">
                      </div>
                    </div>
                    <div class="col-span-3">
                      <label for="username" class="block text-sm/6 font-medium text-gray-700 dark:text-white">Username</label>
                      <div class="mt-2">
                        <div class="flex items-center">
                          <input type="text" name="username" id="username" value="<?php echo get_username($_SESSION['username']); ?>" class="block min-w-0 grow bg-white/5 px-3 py-1.5 text-base text-gray-700 dark:text-white outline-1 -outline-offset-1 outline-gray-500 dark:outline-white/10 placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-sky-500 sm:text-sm/6">
                          <input type="hidden" name="old_username" id="old_username" value="<?php echo get_username($_SESSION['username']); ?>" >
                        </div>
                      </div>
                    </div>

                    <div class="col-span-3 md:col-span-full">
                      <label for="email" class="block text-sm/6 font-medium text-gray-700 dark:text-white">Email address</label>
                      <div class="mt-2">
                        <input id="email" name="email" type="email" autocomplete="email" value="<?php echo get_usermail($_SESSION['username']); ?>" class="block w-full bg-white/5 px-3 py-1.5 text-base text-gray-700 dark:text-white outline-1 -outline-offset-1 outline-gray-500 dark:outline-white/10 placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-sky-500 sm:text-sm/6">
                      </div>
                    </div>
                  </div>

                  <div class="mt-8 flex">
                    <button type="submit" class=" bg-sky-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-sky-400 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-500">Save</button>
                  </div>
                </form>
              </div>
              <div class="grid max-w-7xl grid-cols-1 gap-x-8 gap-y-10 px-4 py-16 sm:px-6 md:grid-cols-3 lg:px-8">
                <div>
                  <h2 class="text-base/7 font-semibold text-gray-700 dark:text-white">Login Type</h2>
                  <p class="mt-1 text-sm/6 text-gray-400">Select between password and One Time Code via Mail</p>
                </div>

                <form class="md:col-span-2" action="backend_api/save_user_data.php?auth_type=<?php echo $_SESSION['username']; ?>" method="post" id="change-login-type-form">
                  <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:max-w-xl sm:grid-cols-6">
                    <!-- Erfolgsmeldung (grün) -->
                    <div id="notification-logintype-success" class="hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 col-span-full relative mb-4" role="alert">
                      <strong class="font-bold">Success!</strong>
                      <span class="block sm:inline">Login has been changed</span>
                    </div>

                    <!-- Fehlermeldung (rot) -->
                    <div id="notification-logintype-error" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 col-span-full relative mb-4" role="alert">
                      <strong class="font-bold">Error!</strong>
                      <span class="block sm:inline">Login has not been changed!</span>
                    </div>
                    <!-- Select Image Size -->
                    <div class="sm:col-span-full">
                      <label id="listbox-logintype-label" class="block text-sm/6 font-medium text-gray-700 dark:text-white">Default Login Type</label>
                      <div class="relative mt-2">
                        <button type="button" class="grid w-full cursor-default grid-cols-1 rounded-md bg-white py-1.5 pr-2 pl-3 text-left text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-sky-600 sm:text-sm/6" aria-haspopup="listbox-logintype" aria-expanded="true" aria-labelledby="listbox-image-label">
                          <span class="col-start-1 row-start-1 truncate pr-6"><?php echo get_logintype_select($_SESSION['username']); ?></span>
                          <svg class="col-start-1 row-start-1 size-5 self-center justify-self-end text-gray-500 sm:size-4" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true" data-slot="icon">
                            <path fill-rule="evenodd" d="M5.22 10.22a.75.75 0 0 1 1.06 0L8 11.94l1.72-1.72a.75.75 0 1 1 1.06 1.06l-2.25 2.25a.75.75 0 0 1-1.06 0l-2.25-2.25a.75.75 0 0 1 0-1.06ZM10.78 5.78a.75.75 0 0 1-1.06 0L8 4.06 6.28 5.78a.75.75 0 0 1-1.06-1.06l2.25-2.25a.75.75 0 0 1 1.06 0l2.25 2.25a.75.75 0 0 1 0 1.06Z" clip-rule="evenodd" />
                          </svg>
                        </button>
                        <ul class="hidden absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black/5 focus:outline-hidden sm:text-sm" tabindex="-1" role="listbox" aria-labelledby="listbox-logintype-label" aria-activedescendant="listbox-option-1">
                          <li class="relative cursor-default py-2 pr-9 pl-3 text-gray-900 select-none" id="listbox-image-option-0" role="option">
                            <!-- Selected: "font-semibold", Not Selected: "font-normal" -->
                            <span class="block truncate font-normal">password</span>
                            <span class="absolute inset-y-0 right-0 flex items-center pr-4 text-sky-600">
                              <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                              </svg>
                            </span>
                          </li>
                          <li class="relative cursor-default py-2 pr-9 pl-3 text-gray-900 select-none" id="listbox-image-option-1" role="option">
                            <!-- Selected: "font-semibold", Not Selected: "font-normal" -->
                            <span class="block truncate font-normal">mail</span>
                            <span class="hidden absolute inset-y-0 right-0 flex items-center pr-4 text-sky-600">
                              <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                              </svg>
                            </span>
                          </li>
                        </ul>
                      </div>
                    </div>
                    <input type="hidden" name="login_type" id="login_type" value="<?php echo get_logintype_select($_SESSION['username']); ?>">
                    <!-- Select ende -->

                  </div>

                  <div class="mt-8 flex">
                    <button type="submit" class=" bg-sky-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-sky-400 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-500 mr-5">Save</button>                    
                  </div>
                </form>
              </div>

              <div class="grid max-w-7xl grid-cols-1 gap-x-8 gap-y-10 px-4 py-16 sm:px-6 md:grid-cols-3 lg:px-8">
                <div>
                  <h2 class="text-base/7 font-semibold text-gray-900 dark:text-white">Change password</h2>
                  <p class="mt-1 text-sm/6 text-gray-400">Update your password associated with your account.</p>
                </div>

                <form class="md:col-span-2" action="backend_api/save_user_data.php?password=<?php echo $_SESSION['username']; ?>" method="post" id="change-password-form">
                  <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:max-w-xl sm:grid-cols-6">
                    <!-- Erfolgsmeldung (grün) -->
                    <div id="notification-success" class="hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 col-span-full relative mb-4" role="alert">
                      <strong class="font-bold">Success!</strong>
                      <span class="block sm:inline">Password has been changed.</span>
                    </div>

                    <!-- Fehlermeldung (rot) -->
                    <div id="notification-error" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 col-span-full relative mb-4" role="alert">
                      <strong class="font-bold">Error!</strong>
                      <span class="block sm:inline">The current password is wrong.</span>
                    </div>
                    
                    <div class="col-span-full">
                      <label for="current-password" class="block text-sm/6 font-medium text-gray-700 dark:text-white">Current password</label>
                      <div class="mt-2">
                        <input id="current-password" name="current_password" type="password" autocomplete="current-password" class="block w-full  bg-white/5 px-3 py-1.5 text-base text-gray-700 dark:text-white outline-1 -outline-offset-1 outline-gray-500 dark:outline-white/10 placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-sky-500 sm:text-sm/6">
                      </div>
                    </div> 

                    <div class="col-span-full">
                      <label for="new-password" class="block text-sm/6 font-medium text-gray-700 dark:text-white">New password</label>
                      <div class="mt-2">
                        <input id="new-password" name="new_password" type="password" autocomplete="new-password" class="block w-full  bg-white/5 px-3 py-1.5 text-base text-gray-700 dark:text-white outline-1 -outline-offset-1 outline-gray-500 dark:outline-white/10 placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-sky-500 sm:text-sm/6">
                      </div>
                    </div>

                    <div class="col-span-full">
                      <label for="confirm-password" class="block text-sm/6 font-medium text-gray-700 dark:text-white">Confirm password</label>
                      <div class="mt-2">
                        <input id="confirm-password" name="confirm_password" type="password" autocomplete="new-password" class="block w-full  bg-white/5 px-3 py-1.5 text-base text-gray-700 dark:text-white outline-1 -outline-offset-1 outline-gray-500 dark:outline-white/10 placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-sky-500 sm:text-sm/6">
                      </div>
                    </div>
                  </div>

                  <div class="mt-8 flex">
                    <button type="submit" class=" bg-sky-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-sky-400 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-500">Save</button>
                  </div>
                </form>
              </div>
            </div>
          </main>
        </div>
        <script src="js/tailwind.js"></script>
        <script src="js/change_password.js"></script>
        <script src="js/select_settings.js"></script>
    </body>
</html>