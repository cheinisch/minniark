<?php

  require_once( __DIR__ . "/../functions/function_backend.php");
  $settingspage = "menu";
  security_checklogin();

  $nav_items = read_navigation();

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
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
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
                        <!-- Current: "border-indigo-500 text-gray-900", Default: "border-transparent text-gray-500 hover:border-gray-400 hover:text-gray-700" -->
                        <a href="dashboard.php" class="inline-flex items-center border-b-2 border-sky-400 px-1 pt-1 text-base font-medium text-sky-400"><?php echo languageString('nav.dashboard'); ?></a>
                        <a href="media.php" class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-base font-medium text-gray-600 dark:text-gray-400 hover:border-sky-400 hover:text-sky-400"><?php echo languageString('nav.images'); ?></a>
                        <a href="blog.php" class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-base font-medium text-gray-600 dark:text-gray-400 hover:border-sky-400 hover:text-sky-400"><?php echo languageString('nav.blogposts'); ?></a>
                        <a href="pages.php" class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-base font-medium text-gray-600 dark:text-gray-400 hover:border-sky-400 hover:text-sky-400"><?php echo languageString('nav.pages'); ?></a>
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
                    <!-- Current: "bg-indigo-50 border-indigo-500 text-indigo-700", Default: "border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-400 hover:text-gray-700" -->
                    <a href="dashboard.php" class="block border-l-4 border-sky-400 py-2 pr-4 pl-3 text-base font-medium text-sky-400 sm:pr-6 sm:pl-5"><?php echo languageString('nav.dashboard'); ?></a>
                    <a href="media.php" class="block border-l-4 border-transparent py-2 pr-4 pl-3 text-base font-medium text-gray-400 hover:border-gray-400 hover:bg-gray-50 hover:text-gray-700 sm:pr-6 sm:pl-5"><?php echo languageString('nav.images'); ?></a>
                    <a href="blog.php" class="block border-l-4 border-transparent py-2 pr-4 pl-3 text-base font-medium text-gray-400 hover:border-gray-400 hover:bg-gray-50 hover:text-gray-700 sm:pr-6 sm:pl-5"><?php echo languageString('nav.blogposts'); ?></a>
                    <a href="pages.php" class="block border-l-4 border-transparent py-2 pr-4 pl-3 text-base font-medium text-gray-400 hover:border-gray-400 hover:bg-gray-50 hover:text-gray-700 sm:pr-6 sm:pl-5"><?php echo languageString('nav.pages'); ?></a>
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
                        <div class="text-base font-medium text-gray-400"><?php echo get_username($_SESSION['username']); ?></div>
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

              
            
            <div class="grid max-w-7xl grid-cols-1 gap-x-8 gap-y-10 px-4 py-16 sm:px-6 md:grid-cols-3 lg:px-8">
                <div>
                  <h2 class="text-base/7 font-semibold dark:text-white">Main Navigation Settings</h2>
                  <p class="mt-1 text-sm/6 dark:text-gray-400">Select some Settings for the Navigation</p>
                </div>

                
                <form class="md:col-span-2" action="backend_api/nav_change.php?save=active" method="post" id="change-map-form">
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
                          <span class="text-sm/6 font-medium text-gray-900 dark:text-white" id="availability-label">Custom Navigation</span>
                          <span class="text-sm text-gray-500" id="availability-description">Enables the custom navigation.</span>
                        </span>
                        <!-- Enabled: "bg-indigo-600", Not Enabled: "bg-gray-200" -->
                        <button type="button" id="nav_enable" class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent bg-gray-400 transition-colors duration-200 ease-in-out focus:ring-2 focus:ring-sky-600 focus:ring-offset-2 focus:outline-hidden" role="switch" aria-checked="<?php echo is_nav_enabled(); ?>" aria-labelledby="availability-label" aria-describedby="availability-description">
                          <!-- Enabled: "translate-x-5", Not Enabled: "translate-x-0" -->
                          <span aria-hidden="true" class="pointer-events-none inline-block size-5 translate-x-0 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out"></span>
                        </button>
                        <input type="hidden" name="nav_enabled" id="nav_enabled" value="0">
                      </div>
                    </div>
                  </div>

                  <div class="mt-8 flex">
                    <button type="submit" id="btn_map" class="bg-sky-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-sky-400 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-500">Save</button>
                  </div>
                </form>                
            </div>
            <div id="custom_nav" class="grid max-w-7xl grid-cols-1 gap-x-8 gap-y-10 px-4 py-16 sm:px-6 md:grid-cols-3 lg:px-8">
                <div>
                  <h2 class="text-base/7 font-semibold text-white">Custom Navigation</h2>
                  <p class="mt-1 text-sm/6 text-gray-400">Create your own nav menu</p>
                </div>

                <!-- nav start -->
                <div class="md:col-span-2" id="custom_nav_menu">
        <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:w-full sm:grid-cols-6">
          <div class="col-span-full">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <h3 class="text-sm/6 font-medium text-gray-900 dark:text-white">Available Items</h3>
                <ul id="available_items" class="space-y-2 bg-gray-100 dark:bg-neutral-900 p-2 border-gray-400 border border-dashed min-h-[150px]">
                    <!-- Home Item -->
                    <li draggable="true" data-label="Home" data-link="/home" class="cursor-move p-2 my-2 bg-white dark:bg-gray-950 shadow dark:text-gray-400">Home</li>
                    <!-- Pages -->
                    <?php
                        $pages = get_pages();
                        foreach($pages as $page)
                        {
                            echo '<li draggable="true" data-label="'.$page['title'].'" data-link="/p/'.generateSlug($page['title']).'" class="cursor-move p-2 my-2 bg-white dark:bg-gray-950 shadow dark:text-gray-400">Page: '.$page['title'].'</li>';
                        }               
                    ?>
                    <!-- Collection -->
                    <?php
                        $collections = getCollectionList();

                        foreach($collections as $collection)
                        {
                            echo '<li draggable="true" data-label="'.$collection['title'].'" data-link="/collection/'.$collection['slug'].'" class="cursor-move p-2 my-2 bg-white dark:bg-gray-950 shadow dark:text-gray-400">Collection: '.$collection['title'].'</li>';
                        }                    
                    ?>
                    <!-- Album -->
                    <?php
                        $albums = getAlbumList();

                        foreach($albums as $album)
                        {
                            echo '<li draggable="true" data-label="'.$album['title'].'" data-link="/gallery/'.$album['slug'].'" class="cursor-move p-2 my-2 bg-white dark:bg-gray-950 shadow dark:text-gray-400">Album: '.$album['title'].'</li>';
                        }                    
                    ?>
                    <!-- General Items  -->
                    <li draggable="true" data-label="Blog" data-link="/blog" class="cursor-move p-2 my-2 bg-white dark:bg-gray-950 shadow dark:text-gray-400">Blog</li>
                    <li draggable="true" data-label="Timeline" data-link="/timeline" class="cursor-move p-2 my-2 bg-white dark:bg-gray-950 shadow dark:text-gray-400">Timeline</li>
                    <li draggable="true" data-label="Map" data-link="/map" class="cursor-move p-2 my-2 bg-white dark:bg-gray-950 shadow dark:text-gray-400">Map</li>
                </ul>
              </div>
              <div>
                <h3 class="text-sm/6 font-medium text-gray-900 dark:text-white">Custom Menu</h3>
                <ul id="menu_list" class="space-y-2 bg-gray-50 p-2 dark:bg-neutral-900 border min-h-[150px] border-dashed border-gray-400"></ul>
              </div>
            </div>
            <div class="col-span-full mt-6">
              <h3 class="text-sm/6 font-medium text-gray-900 dark:text-white">Add Custom Link</h3>
              <div class="flex items-center gap-2">
                <input type="text" id="custom_label" placeholder="Label (e.g. Blog)" class="w-1/2 border px-2 py-1 dark:text-gray-400" />
                <input type="text" id="custom_link" placeholder="URL (e.g. /blog)" class="w-1/2 border px-2 py-1 dark:text-gray-400" />
                <button id="add_custom" type="button" class="bg-sky-600 text-white px-2 py-1 hover:bg-sky-500">Add</button>
              </div>
            </div>
          </div>
        </div>
        <div class="mt-8 flex">
          <button id="btn_nav" class="bg-sky-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-sky-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-500">Save</button>
        </div>
      </div>
                <!-- nav end -->
            </div>

            </div>
          </main>
        </div>
        <script>
            window.existingNav = <?php echo json_encode($nav_items, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>;
        </script>
        <script src="js/tailwind.js"></script>
        <script src="js/update.js"></script>
        <script src="js/custom_nav.js"></script>    
    </body>
</html>