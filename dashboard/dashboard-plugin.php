<?php

  require_once( __DIR__ . "/../functions/function_backend.php");
  $settingspage = "plugin";
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
                          <div class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black/5 focus:outline-hidden hidden" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                            <!-- Active: "bg-gray-100 outline-hidden", Not Active: "" -->
                            <a href="dashboard-personal.php" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-0">Your Profile</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-1">Settings</a>
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
                    <!-- Current: "bg-indigo-50 border-indigo-500 text-indigo-700", Default: "border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700" -->
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
                        <a href="dashboard-user.php" class="block px-4 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6">User Settings</a>
                        <a href="dashboard-system.php" class="block px-4 text-base font-medium text-sky-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6">System Settings</a>
                        <a href="dashboard-theme.php" class="block px-4 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6">Theme Settings</a>
                        <a href="dashboard-export_import.php" class="block px-4 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6">Export / Import</a>
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
                      <a href="#" class="block px-4 py-2 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6">Your Profile</a>
                      <a href="#" class="block px-4 py-2 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6">Settings</a>
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
              <?php
                $pluginBaseDir = __DIR__ . '/../userdata/plugins';
                $pluginDirs = glob($pluginBaseDir . '/*', GLOB_ONLYDIR);
                foreach ($pluginDirs as $pluginDir) {
                  echo "<!-- Plugin Element -->";
                  renderPluginSettings($pluginDir);
                }
              ?>

          </main>
        </div>
        <script src="js/tailwind.js"></script>
        <script src="js/update.js"></script>
        <script src="js/plugins.js"></script>
    </body>
</html>