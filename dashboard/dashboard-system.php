<?php

  require_once( __DIR__ . "/../functions/function_backend.php");
  $settingspage = "system";
  security_checklogin();

?>

<!DOCTYPE html>
<html lang="<?php echo get_language(); ?>">
    <head>      
        <meta charset="UTF-8">        
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard - <?php echo get_sitename(); ?></title>

        <!-- FAV Icon -->
        <link rel="icon" type="image/png" href="../lib/img/favicon.png" />
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
                        <a href="dashboard.php" class="inline-flex items-center border-b-2 border-sky-400 px-1 pt-1 text-base font-medium text-sky-400"><?php echo languageString('nav.dashboard'); ?></a>
                        <a href="media.php" class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-base font-medium text-gray-600 dark:text-gray-300 hover:border-sky-400 hover:text-sky-400"><?php echo languageString('nav.images'); ?></a>
                        <a href="blog.php" class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-base font-medium text-gray-600 dark:text-gray-300 hover:border-sky-400 hover:text-sky-400"><?php echo languageString('nav.blogposts'); ?></a>
                        <a href="pages.php" class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-base font-medium text-gray-600 dark:text-gray-300 hover:border-sky-400 hover:text-sky-400"><?php echo languageString('nav.pages'); ?></a>
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
                    <a href="dashboard.php" class="block border-l-4 border-sky-400 py-2 pr-4 pl-3 text-base font-medium text-sky-400 sm:pr-6 sm:pl-5"><?php echo languageString('nav.dashboard'); ?></a>
                    <a href="media.php" class="block border-l-4 border-transparent py-2 pr-4 pl-3 text-base font-medium text-gray-300 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-700 sm:pr-6 sm:pl-5"><?php echo languageString('nav.images'); ?></a>
                    <a href="blog.php" class="block border-l-4 border-transparent py-2 pr-4 pl-3 text-base font-medium text-gray-300 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-700 sm:pr-6 sm:pl-5"><?php echo languageString('nav.blogposts'); ?></a>
                    <a href="pages.php" class="block border-l-4 border-transparent py-2 pr-4 pl-3 text-base font-medium text-gray-300 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-700 sm:pr-6 sm:pl-5"><?php echo languageString('nav.pages'); ?></a>
                  </div>
                  <div class="border-t border-gray-500 pt-4 pb-3">
                    <div class="mt-3 space-y-1">
                      <span class="block px-4 py-2 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6">Overview</span>
                      <div class="pl-5">
                        <a href="dashboard.php" class="block px-4 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6"><?php echo languageString('nav.dashboard'); ?></a>
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
                  <h2 class="text-base/7 font-semibold text-gray-700 dark:text-white">Site Information</h2>
                  <p class="mt-1 text-sm/6 text-gray-400">Some Site Information</p>
                </div>

                <form class="md:col-span-2" id="change-sitedata-form">
                  <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:max-w-xl sm:grid-cols-6">
                    <!-- Notifications für Benutzerdaten -->
                    <div id="notification-success-user" class="hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 col-span-full relative mb-4" role="alert">
                      <strong class="font-bold">Erfolg!</strong>
                      <span class="block sm:inline">Daten wurden gespeichert.</span>
                    </div>

                    <div id="notification-error-user" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 col-span-full relative mb-4" role="alert">
                      <strong class="font-bold">Fehler!</strong>
                      <span class="block sm:inline">Etwas ist schiefgelaufen.</span>
                    </div>

                    <div class="sm:col-span-full">
                      <label for="site-name" class="block text-sm/6 font-medium text-gray-700 dark:text-white">Site name</label>
                      <div class="mt-2">
                        <input type="text" name="site-name" id="site-name" value="<?php echo get_sitename(); ?>" class="block w-full  bg-white/5 px-3 py-1.5 text-base text-gray-700 dark:text-white outline-1 -outline-offset-1 outline-gray-500 dark:outline-white/10 placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-sky-500 sm:text-sm/6">
                      </div>
                    </div>

                    <div class="sm:col-span-full">
                      <label for="site-decription" class="block text-sm/6 font-medium text-gray-700 dark:text-white">Site Description</label>
                      <div class="mt-2">
                        <input type="text" name="site-decription" id="site-decription" value="<?php echo get_sitedescription(); ?>" class="block w-full  bg-white/5 px-3 py-1.5 text-base text-gray-700 dark:text-white outline-1 -outline-offset-1 outline-gray-500 dark:outline-white/10 placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-sky-500 sm:text-sm/6">
                      </div>
                    </div>

                    <!-- Select Language (en/de) -->
                   <div class="sm:col-span-full">
                    <label id="listbox-language-label" class="block text-sm/6 font-medium text-gray-700 dark:text-white">Language</label>
                    <div class="relative mt-2">
                      <button type="button" class="grid w-full cursor-default grid-cols-1 rounded-md bg-white py-1.5 pr-2 pl-3 text-left text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-sky-600 sm:text-sm/6" aria-haspopup="listbox-language" aria-expanded="true" aria-labelledby="listbox-language-label">
                        <span class="col-start-1 row-start-1 truncate pr-6"><?php echo get_language(); ?></span>
                        <svg class="col-start-1 row-start-1 size-5 self-center justify-self-end text-gray-500 sm:size-4" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true" data-slot="icon">
                          <path fill-rule="evenodd" d="M5.22 10.22a.75.75 0 0 1 1.06 0L8 11.94l1.72-1.72a.75.75 0 1 1 1.06 1.06l-2.25 2.25a.75.75 0 0 1-1.06 0l-2.25-2.25a.75.75 0 0 1 0-1.06ZM10.78 5.78a.75.75 0 0 1-1.06 0L8 4.06 6.28 5.78a.75.75 0 0 1-1.06-1.06l2.25-2.25a.75.75 0 0 1 1.06 0l2.25 2.25a.75.75 0 0 1 0 1.06Z" clip-rule="evenodd" />
                        </svg>
                      </button>
                      <ul class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black/5 focus:outline-hidden sm:text-sm" tabindex="-1" role="listbox" aria-labelledby="listbox-language-label" aria-activedescendant="listbox-option-1">
                      <?php 
                              $langs = getLangFiles();
                              $current = get_language();
                              foreach ($langs as $i => $lang): 
                              $selected = strcasecmp($lang, $current) === 0;
                        ?>
                        <li class="relative cursor-default py-2 pr-9 pl-3 text-gray-900 select-none"
                            id="listbox-language-option-<?= (int)$i; ?>"
                            role="option"
                            aria-selected="<?= $selected ? 'true' : 'false'; ?>"
                            data-lang="<?= htmlspecialchars($lang, ENT_QUOTES, 'UTF-8'); ?>">
                          <span class="block truncate <?= $selected ? 'font-semibold' : 'font-normal'; ?>">
                            <?= htmlspecialchars($lang, ENT_QUOTES, 'UTF-8'); ?>
                          </span>
                          <span class="<?= $selected ? '' : 'hidden'; ?> absolute inset-y-0 right-0 flex items-center pr-4 text-sky-600">
                            <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                              <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                            </svg>
                          </span>
                        </li>
                        <?php endforeach; ?>
                        <?php if (empty($langs)): ?>
                        <li class="relative cursor-default py-2 pr-9 pl-3 text-gray-500 select-none" role="option" aria-disabled="true">
                          <span class="block truncate italic">No languages found</span>
                        </li>
                        <?php endif; ?>
                      </ul>
                    </div>
                  </div>
                  <input type="hidden" name="language" id="selected-language" value="<?php echo get_language(); ?>">
                  <!-- Select ende -->
                  </div>
                  <div class="mt-8 flex">
                    <button type="submit" id="btnSiteSettings" class=" bg-sky-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-sky-400 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-500">Save</button>
                  </div>
                </form>
              </div>

              <div class="grid max-w-7xl grid-cols-1 gap-x-8 gap-y-10 px-4 py-16 sm:px-6 md:grid-cols-3 lg:px-8">
                <div>
                  <h2 class="text-base/7 font-semibold text-gray-700 dark:text-white">Image Settings</h2>
                  <p class="mt-1 text-sm/6 text-gray-400">Select some Settings for the Images</p>
                </div>

                <form class="md:col-span-2" id="change-image-size">
                  <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:max-w-xl sm:grid-cols-6">
                    <!-- Erfolgsmeldung (grün) -->
                    <div id="notification-success" class="hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 col-span-full relative mb-4" role="alert">
                      <strong class="font-bold">Erfolg!</strong>
                      <span class="block sm:inline">Bildgr&ouml;ße ge&auml;ndert!</span>
                    </div>

                    <!-- Fehlermeldung (rot) -->
                    <div id="notification-error" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 col-span-full relative mb-4" role="alert">
                      <strong class="font-bold">Fehler!</strong>
                      <span class="block sm:inline">Bildgr&ouml;ße nicht ge&auml;ndert!</span>
                    </div>
                    <!-- Select Image Size -->
                    <div class="sm:col-span-full">
                      <label id="listbox-image-label" class="block text-sm/6 font-medium text-gray-700 dark:text-white">Default Image size (for chached images)</label>
                      <div class="relative mt-2">
                        <button type="button" class="grid w-full cursor-default grid-cols-1 rounded-md bg-white py-1.5 pr-2 pl-3 text-left text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-sky-600 sm:text-sm/6" aria-haspopup="listbox-image" aria-expanded="true" aria-labelledby="listbox-image-label">
                          <span class="col-start-1 row-start-1 truncate pr-6"><?php echo get_imagesize(); ?></span>
                          <svg class="col-start-1 row-start-1 size-5 self-center justify-self-end text-gray-500 sm:size-4" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true" data-slot="icon">
                            <path fill-rule="evenodd" d="M5.22 10.22a.75.75 0 0 1 1.06 0L8 11.94l1.72-1.72a.75.75 0 1 1 1.06 1.06l-2.25 2.25a.75.75 0 0 1-1.06 0l-2.25-2.25a.75.75 0 0 1 0-1.06ZM10.78 5.78a.75.75 0 0 1-1.06 0L8 4.06 6.28 5.78a.75.75 0 0 1-1.06-1.06l2.25-2.25a.75.75 0 0 1 1.06 0l2.25 2.25a.75.75 0 0 1 0 1.06Z" clip-rule="evenodd" />
                          </svg>
                        </button>
                        <ul class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black/5 focus:outline-hidden sm:text-sm" tabindex="-1" role="listbox" aria-labelledby="listbox-image-label" aria-activedescendant="listbox-option-1">
                          <!--<li class="relative cursor-default py-2 pr-9 pl-3 text-gray-900 select-none" id="listbox-image-option-0" role="option">
                            
                            <span class="block truncate font-normal">S</span>
                            <span class="absolute inset-y-0 right-0 flex items-center pr-4 text-sky-600">
                              <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                              </svg>
                            </span>
                          </li>
                          <li class="relative cursor-default py-2 pr-9 pl-3 text-gray-900 select-none" id="listbox-image-option-1" role="option">
                            
                            <span class="block truncate font-normal">M</span>
                            <span class="hidden absolute inset-y-0 right-0 flex items-center pr-4 text-sky-600">
                              <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                              </svg>
                            </span>
                          </li>-->
                          <li class="relative cursor-default py-2 pr-9 pl-3 text-gray-900 select-none" id="listbox-image-option-2" role="option">
                            <!-- Selected: "font-semibold", Not Selected: "font-normal" -->
                            <span class="block truncate font-normal">L</span>
                            <span class="hidden absolute inset-y-0 right-0 flex items-center pr-4 text-sky-600">
                              <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                              </svg>
                            </span>
                          </li>
                          <li class="relative cursor-default py-2 pr-9 pl-3 text-gray-900 select-none" id="listbox-image-option-3" role="option">
                            <!-- Selected: "font-semibold", Not Selected: "font-normal" -->
                            <span class="block truncate font-normal">XL</span>
                            <span class="hidden absolute inset-y-0 right-0 flex items-center pr-4 text-sky-600">
                              <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                              </svg>
                            </span>
                          </li>
                          <li class="relative cursor-default py-2 pr-9 pl-3 text-gray-900 select-none" id="listbox-image-option-3" role="option">
                            <!-- Selected: "font-semibold", Not Selected: "font-normal" -->
                            <span class="block truncate font-normal">XXL</span>
                            <span class="hidden absolute inset-y-0 right-0 flex items-center pr-4 text-sky-600">
                              <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                              </svg>
                            </span>
                          </li>
                          <li class="relative cursor-default py-2 pr-9 pl-3 text-gray-900 select-none" id="listbox-image-option-3" role="option">
                            <!-- Selected: "font-semibold", Not Selected: "font-normal" -->
                            <span class="block truncate font-normal">Original</span>
                            <span class="hidden absolute inset-y-0 right-0 flex items-center pr-4 text-sky-600">
                              <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                              </svg>
                            </span>
                          </li>
                        </ul>
                      </div>
                    </div>
                    <input type="hidden" name="image_size" id="image_size" value="<?php echo get_imagesize(); ?>">
                    <!-- Select ende -->

                  </div>

                  <div class="mt-8 flex">
                    <button type="submit" class=" bg-sky-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-sky-400 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-500 mr-5">Save</button>
                    <button id="recreate-cache-button" class="bg-sky-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-sky-400 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-500">Recreate Cache</button>
                  </div>
                </form>
              </div>
              <div class="grid max-w-7xl grid-cols-1 gap-x-8 gap-y-10 px-4 py-16 sm:px-6 md:grid-cols-3 lg:px-8">
                <div>
                  <h2 class="text-base/7 font-semibold text-gray-700 dark:text-white">Timeline Settings</h2>
                  <p class="mt-1 text-sm/6 text-gray-400">Select some Settings for the Timeline</p>
                </div>

                <form class="md:col-span-2" id="change-timeline-form">
                  <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:max-w-xl sm:grid-cols-6">
                    <!-- Erfolgsmeldung (grün) -->
                    <div id="notification-timeline-success" class="hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 col-span-full relative mb-4" role="alert">
                      <strong class="font-bold">Success!</strong>
                      <span class="block sm:inline">Settings are saved.</span>
                    </div>

                    <!-- Fehlermeldung (rot) -->
                    <div id="notification-timeline-error" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 col-span-full relative mb-4" role="alert">
                      <strong class="font-bold">Error!</strong>
                      <span class="block sm:inline">Settings not changed.</span>
                    </div>
                    <div class="col-span-full">
                      <div class="flex items-center justify-between">
                        <span class="flex grow flex-col">
                          <span class="text-sm/6 font-medium text-gray-900 dark:text-white" id="availability-label">Enable Timeline</span>
                          <span class="text-sm text-gray-500" id="availability-description">Enable the timeline in the main navigation</span>
                        </span>
                        <!-- Enabled: "bg-indigo-600", Not Enabled: "bg-gray-200" -->
                        <button type="button" id="timline_enable" class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent bg-gray-400 transition-colors duration-200 ease-in-out focus:ring-2 focus:ring-sky-600 focus:ring-offset-2 focus:outline-hidden" role="switch" aria-checked="<?php echo is_timeline_enabled(); ?>" aria-labelledby="availability-label" aria-describedby="availability-description">
                          <!-- Enabled: "translate-x-5", Not Enabled: "translate-x-0" -->
                          <span aria-hidden="true" class="pointer-events-none inline-block size-5 translate-x-0 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out"></span>
                        </button>
                      </div>
                    </div>
                    <div class="col-span-full">
                      <div class="flex items-center justify-between">
                        <span class="flex grow flex-col">
                          <span class="text-sm/6 font-medium text-gray-900 dark:text-white" id="availability-label">Group by month </span>
                          <span class="text-sm text-gray-500" id="availability-description">Group the images by month</span>
                        </span>
                        <!-- Enabled: "bg-indigo-600", Not Enabled: "bg-gray-200" -->
                        <button type="button" id="timline_group" class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent bg-gray-400 transition-colors duration-200 ease-in-out focus:ring-2 focus:ring-sky-600 focus:ring-offset-2 focus:outline-hidden" role="switch" aria-checked="<?php echo is_timeline_grouped(); ?>" aria-labelledby="availability-label" aria-describedby="availability-description">
                          <!-- Enabled: "translate-x-5", Not Enabled: "translate-x-0" -->
                          <span aria-hidden="true" class="pointer-events-none inline-block size-5 translate-x-0 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out"></span>
                        </button>
                      </div>
                    </div>
                  </div>

                  <div class="mt-8 flex">
                    <button type="submit" id="btn_timeline" class="bg-sky-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-sky-400 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-500">Save</button>
                  </div>
                </form>
              </div>
              <div class="grid max-w-7xl grid-cols-1 gap-x-8 gap-y-10 px-4 py-16 sm:px-6 md:grid-cols-3 lg:px-8">
                <div>
                  <h2 class="text-base/7 font-semibold text-white">Map Settings</h2>
                  <p class="mt-1 text-sm/6 text-gray-400">Select some Settings for the Mapview</p>
                </div>

                <form class="md:col-span-2" id="change-map-form">
                  <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:max-w-xl sm:grid-cols-6">
                    <!-- Erfolgsmeldung (grün) -->
                    <div id="notification-map-success" class="hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 col-span-full relative mb-4" role="alert">
                      <strong class="font-bold">Success!</strong>
                      <span class="block sm:inline">Settings are saved.</span>
                    </div>

                    <!-- Fehlermeldung (rot) -->
                    <div id="notification-map-error" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 col-span-full relative mb-4" role="alert">
                      <strong class="font-bold">Error!</strong>
                      <span class="block sm:inline">Settings not changed.</span>
                    </div>
                    <div class="col-span-full">
                      <div class="flex items-center justify-between">
                        <span class="flex grow flex-col">
                          <span class="text-sm/6 font-medium text-gray-900 dark:text-white" id="availability-label">Enable Map</span>
                          <span class="text-sm text-gray-500" id="availability-description">Enables the map in the main navigation</span>
                        </span>
                        <!-- Enabled: "bg-indigo-600", Not Enabled: "bg-gray-200" -->
                        <button type="button" id="map_enable" class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent bg-gray-400 transition-colors duration-200 ease-in-out focus:ring-2 focus:ring-sky-600 focus:ring-offset-2 focus:outline-hidden" role="switch" aria-checked="<?php echo is_map_enabled(); ?>" aria-labelledby="availability-label" aria-describedby="availability-description">
                          <!-- Enabled: "translate-x-5", Not Enabled: "translate-x-0" -->
                          <span aria-hidden="true" class="pointer-events-none inline-block size-5 translate-x-0 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out"></span>
                        </button>
                      </div>
                    </div>
                  </div>

                  <div class="mt-8 flex">
                    <button type="submit" id="btn_map" class="bg-sky-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-sky-400 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-500">Save</button>
                  </div>
                </form>
              </div>
              <!-- Sitemap Settings -->
              <div class="grid max-w-7xl grid-cols-1 gap-x-8 gap-y-10 px-4 py-16 sm:px-6 md:grid-cols-3 lg:px-8">
              <div>
                <h2 class="text-base/7 font-semibold text-white">Sitemap Settings</h2>
                <p class="mt-1 text-sm/6 text-gray-400"></p>
              </div>

              <form class="md:col-span-2" id="change-sitemap-form">
                <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:max-w-xl sm:grid-cols-6">
                  <!-- Erfolgsmeldung (grün) -->
                    <div id="notification-sitemap-success" class="hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 col-span-full relative mb-4" role="alert">
                      <strong class="font-bold">Success!</strong>
                      <span class="block sm:inline">Settings are saved.</span>
                    </div>

                    <!-- Fehlermeldung (rot) -->
                    <div id="notification-sitemap-error" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 col-span-full relative mb-4" role="alert">
                      <strong class="font-bold">Error!</strong>
                      <span class="block sm:inline">Settings not changed.</span>
                    </div>
                  <div class="col-span-full">
                    <div class="flex items-center justify-between">
                      <span class="flex grow flex-col">
                        <span class="text-sm/6 font-medium text-gray-900 dark:text-white" id="availability-label">Enable Sitemap</span>
                        <span class="text-sm text-gray-500" id="availability-description">Enables the sitemap.xml in the root directory</span>
                      </span>
                      <!-- Enabled: "bg-indigo-600", Not Enabled: "bg-gray-200" -->
                      <button type="button" id="sitemap_enable" class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent bg-gray-400 transition-colors duration-200 ease-in-out focus:ring-2 focus:ring-sky-600 focus:ring-offset-2 focus:outline-hidden" role="switch" aria-checked="<?php echo is_sitemap_enabled(); ?>" aria-labelledby="availability-label" aria-describedby="availability-description">
                        <!-- Enabled: "translate-x-5", Not Enabled: "translate-x-0" -->
                        <span aria-hidden="true" class="pointer-events-none inline-block size-5 translate-x-0 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out"></span>
                      </button>
                    </div>
                  </div>
                  <div class="col-span-full">
                    <div class="flex items-center justify-between">
                      <span class="flex grow flex-col">
                        <span class="text-sm/6 font-medium text-gray-900 dark:text-white" id="availability-label">Enable Images in Sitemap</span>
                        <span class="text-sm text-gray-500" id="availability-description">Enables the sitemap.xml in the root directory</span>
                      </span>
                      <!-- Enabled: "bg-indigo-600", Not Enabled: "bg-gray-200" -->
                      <button type="button" id="sitemap_images_enable" class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent bg-gray-400 transition-colors duration-200 ease-in-out focus:ring-2 focus:ring-sky-600 focus:ring-offset-2 focus:outline-hidden" role="switch" aria-checked="<?php echo is_sitemap_images_enabled(); ?>" aria-labelledby="availability-label" aria-describedby="availability-description">
                        <!-- Enabled: "translate-x-5", Not Enabled: "translate-x-0" -->
                        <span aria-hidden="true" class="pointer-events-none inline-block size-5 translate-x-0 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out"></span>
                      </button>
                    </div>
                  </div>
                </div>

                <div class="mt-8 flex">
                  <button type="submit" id="btn_sitemap" class="bg-sky-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-sky-400 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-500">Save</button>
                </div>
              </form>
            </div>


              <!-- Supporter License -->
               <div class="grid max-w-7xl grid-cols-1 gap-x-8 gap-y-10 px-4 py-16 sm:px-6 md:grid-cols-3 lg:px-8">
                <div>
                  <h2 class="text-base/7 font-semibold text-gray-700 dark:text-white">Supporter License</h2>
                  <p class="mt-1 text-sm/6 text-gray-400">Add the additional supporter License Key. Required for some Plugins. You can buy the license <a href="https://store.minniark.com" class="text-sky-600 hover:text-sky-500">here</a>.</p>
                </div>

                <form class="md:col-span-2" id="change-sitelicense-form" action="backend_api/settings_save.php" method="post">
                  <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:max-w-xl sm:grid-cols-6">

                    <div class="sm:col-span-full">
                      <label for="site-license" class="block text-sm/6 font-medium text-gray-700 dark:text-white">
                        License Key
                      </label>
                      <?php
                      if(!empty(get_license()))
                      {
                        $info = getLicenseInformation();
                        ?>
                        <ul class="text-sm/6 font-medium text-gray-700 dark:text-white">
                          <?php if (!$info['valid']): ?>
                              <li>License is not valid<?php if (!empty($info['message'])) echo ': ' . htmlspecialchars($info['message']); ?>.</li>
                              <?php if (!empty($info['expired_date'])): ?>
                                  <li>Expired at: <?php echo htmlspecialchars($info['expired_date']); ?></li>
                              <?php endif; ?>
                          <?php else: ?>
                              <li>License is valid</li>

                              <?php if (empty($info['expired_date'])): ?>
                                  <li>Status: <span class="text-emerald-600">active</span></li>
                                  <li>Type: unlimited</li>
                              <?php else: ?>
                                  <li>Status: <?php echo $info['expired']
                                      ? '<span class="text-red-600">expired</span>'
                                      : '<span class="text-emerald-600">active</span>'; ?></li>

                                  <?php if (!$info['expired']): ?>
                                      <li>Remaining days: <?php echo $info['days']; ?></li>
                                  <?php endif; ?>

                                  <li>Expire date: <?php echo htmlspecialchars($info['expired_date']); ?></li>
                              <?php endif; ?>

                              <li>Activation: <?php echo (int)$info['timesActivated'] . ' / ' . (int)$info['timesActivatedMax']; ?></li>
                              
                          <?php endif; ?>
                          <?php

                                  if(license_isActive())
                                  {
                                    ?>
                              <li>Open AI Key: <?php echo getOpenAIKey(get_license()); ?></li>
                          
                                  <?php } ?>
                      </ul>



                        <?php
                      }
                      ?>
                      <div class="mt-2">
                        <input type="text" name="site-license" id="site-license" value="<?php echo get_license(); ?>" class="block w-full  bg-white/5 px-3 py-1.5 text-base text-gray-700 dark:text-white outline-1 -outline-offset-1 outline-gray-500 dark:outline-white/10 placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-sky-500 sm:text-sm/6">
                      </div>                      
                    </div>
                  </div>
                  <div class="mt-8 flex">
                    <button type="submit" id="btnSiteLicense" class=" bg-sky-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-sky-400 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-500">Save</button>
                  </div>                  
                </form>
              </div>
            </div>
          </main>
        </div>
        <script src="js/tailwind.js"></script>
        <script src="js/update.js"></script>
        <script src="js/select_settings.js"></script>
        <script src="js/save_settings.js"></script>
    </body>
</html>