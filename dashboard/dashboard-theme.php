<?php

  require_once( __DIR__ . "/../functions/function_backend.php");
  $settingspage = "theme";
  security_checklogin();

  $themes = getInstalledTemplates();
  $activeTheme = get_theme();


  $folder = null;
  $name = null;
  if(isset($_GET['selected']))
  {
    $folder = $_GET['selected'];
    $name = $_GET['name'];
  }

  $search = false;
  if(isset($_GET['search']))
  {
    $search = true;
  }

  $remove = false;
  if(isset($_GET['remove']))
  {
    $remove = true;
    $folder = $_GET['remove'];
    $name = $_GET['name'];
  }

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
    </head>
    <body class="min-h-screen flex flex-col">

      <?php
        if($search)
        {
      ?>

      <div class="relative z-10" role="dialog" aria-modal="true">
			<!--
				Background backdrop, show/hide based on modal state.

				Entering: "ease-out duration-300"
				From: "opacity-0"
				To: "opacity-100"
				Leaving: "ease-in duration-200"
				From: "opacity-100"
				To: "opacity-0"
			-->
			<div class="fixed inset-0 hidden bg-gray-500/75 transition-opacity md:block" aria-hidden="true"></div>

			<div class="fixed inset-0 z-10 w-screen overflow-y-auto">
				<div class="flex min-h-full items-stretch justify-center text-center md:items-center md:px-2 lg:px-4">
				<!-- This element is to trick the browser into centering the modal contents. -->
				<span class="hidden md:inline-block md:h-screen md:align-middle" aria-hidden="true">&#8203;</span>

				<!--
					Modal panel, show/hide based on modal state.

					Entering: "ease-out duration-300"
					From: "opacity-0 translate-y-4 md:translate-y-0 md:scale-95"
					To: "opacity-100 translate-y-0 md:scale-100"
					Leaving: "ease-in duration-200"
					From: "opacity-100 translate-y-0 md:scale-100"
					To: "opacity-0 translate-y-4 md:translate-y-0 md:scale-95"
				-->
				<div class="flex w-full transform text-left text-base transition md:my-8 md:max-w-5xl md:px-4 lg:max-w-7xl">
					<div class="relative flex w-full items-center overflow-hidden bg-white px-4 pt-14 pb-8 shadow-2xl sm:px-6 sm:pt-8 md:p-6 lg:p-8">
					<a href="?" class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 sm:top-8 sm:right-6 md:top-6 md:right-6 lg:top-8 lg:right-8">
						<span class="sr-only">Close</span>
						<svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
						<path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
						</svg>
          </a>

					<div class="w-full items-start gap-x-6 gap-y-8 lg:items-center lg:gap-x-8">
						
						<div class="w-full">
						<h2 class="text-xl font-medium text-gray-900 sm:pr-12">Search and install Themes</h2>

						<section aria-labelledby="information-heading" class="mt-1">
							<h3 id="information-heading" class="sr-only">Search</h3>
							<div class="min-w-0 flex-1 md:px-8 lg:px-0 xl:col-span-6">
								<div class="flex items-center px-6 py-4 md:mx-auto md:max-w-3xl lg:mx-0 lg:max-w-none xl:px-0">
									<div class="grid w-full grid-cols-1">
										<input type="search" name="search" class="col-start-1 row-start-1 block w-full bg-white py-1.5 pr-3 pl-10 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-sky-600 sm:text-sm/6" placeholder="Search" />
										<svg class="pointer-events-none col-start-1 row-start-1 ml-3 size-5 self-center text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
											<path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd" />
										</svg>
									</div>
								</div>
							</div>

						<section aria-labelledby="options-heading" class="mt-8">
							<h3 id="options-heading" class="sr-only">Product options</h3>
								<div class="mt-2 grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-3">
							<?php 
								$packagistThemes = getTemplatesPackagist();
								foreach ($packagistThemes as $theme): ?>
								<div class="bg-white border border-gray-200 overflow-hidden shadow hover:shadow-md transition-shadow" data-theme="<?= htmlspecialchars($theme['name']) ?>">
									<!--<img src="<?= htmlspecialchars($theme['image']) ?>" alt="Theme Preview" class="w-full h-auto">-->
									<div class="p-4">
										<h3 class="text-lg font-semibold"><?= htmlspecialchars($theme['name']) ?></h3>
										<p class="text-sm text-gray-500">Version: <?= htmlspecialchars($theme['version']) ?></p>
										<p class="text-sm text-gray-500">Author: <?= htmlspecialchars($theme['author']) ?></p>
										<a href="backend_api/theme_install.php?install=<?= htmlspecialchars($theme['name']) ?>" class="mt-2 inline-block text-sm text-white bg-sky-600 hover:bg-sky-500 p-1">Install Template</a>
									</div>
								</div>
							<?php endforeach; ?>
							</div>
						</section>
						</div>
					</div>
					</div>
				</div>
				</div>
			</div>
			</div>
      <?php
                }
      ?>

      <?php
        if($folder != null)
        {
        echo '
        <div class="relative z-50 " role="dialog" aria-modal="true">
          <div class="fixed inset-0 bg-gray-500/75 transition-opacity" aria-hidden="true"></div>
          <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto">
            <div class="relative w-full max-w-xl mx-auto shadow-lg bg-white p-6">
              <h2 class="text-xl font-semibold text-gray-800">Theme activation</h2>
              <p class="mt-4 text-gray-600">Set <i>'.$name.'</i> as active theme?</p>
              <div class="flex justify-end mt-6 space-x-3">
                <a href="backend_api/settheme.php?name='.$folder.'" class="px-4 py-2 bg-sky-600 text-white hover:bg-sky-500">Ok</a>
                <a href="?" class="px-4 py-2 bg-red-600 text-white hover:bg-red-500">'.languageString('general.cancel').'</a>
              </div>
            </div>
          </div>
        </div>';          
        }
      ?>

      <?php
        if($remove)
        {
        echo '
        <div class="relative z-50 " role="dialog" aria-modal="true">
          <div class="fixed inset-0 bg-gray-500/75 transition-opacity" aria-hidden="true"></div>
          <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto">
            <div class="relative w-full max-w-xl mx-auto shadow-lg bg-white p-6">
              <h2 class="text-xl font-semibold text-gray-800">Remove Theme</h2>
              <p class="mt-4 text-gray-600">Remove <i>'.$name.'</i> from the system?</p>
              <div class="flex justify-end mt-6 space-x-3">
                <a href="backend_api/thememodify.php?remove='.$folder.'" class="px-4 py-2 bg-sky-600 text-white hover:bg-sky-500">Ok</a>
                <a href="?" class="px-4 py-2 bg-red-600 text-white hover:bg-red-500">'.languageString('general.cancel').'</a>
              </div>
            </div>
          </div>
        </div>';          
        }
      ?>
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
                      <div class="shrink-0">
                        <?php echo createThemeUpdateButton(); ?>
                        <a href="?search" class="relative inline-flex items-center gap-x-1.5 bg-sky-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-sky-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600">
                            <svg class="-ml-0.5 w-5 h-5" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                              <path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/>
                            </svg>
                            Search new Themes
                        </a>
                    </div>
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
                            <a href="dashboard-personal.php" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-0"><?php echo languageString('nav.your_profile'); ?></a>
                            
                            <a href="login.php?logout=true" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-2"><?php echo languageString('nav.sign_out'); ?></a>
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
                        <a href="dashboard-system.php" class="block px-4 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6">System Settings</a>
                        <a href="dashboard-theme.php" class="block px-4 text-base font-medium text-sky-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6">Theme Settings</a>
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
                      <a href="dashboard-personal.php" class="block px-4 py-2 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6"><?php echo languageString('nav.your_profile'); ?></a>
                      
                      <a href="login.php?logout=true" class="block px-4 py-2 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6"><?php echo languageString('nav.sign_out'); ?></a>
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
              <div class="grid max-w-9xl grid-cols-1 gap-x-8 gap-y-10 px-4 py-16 sm:px-6 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 lg:px-8">
                <div>
                  <h2 class="text-base/7 font-semibold text-gray-900 dark:text-white">Theme selection</h2>
                  <p class="mt-1 text-sm/6 text-gray-400">Select the theme, you want.</p>
                </div>


                <div class="md:col-span-4">
                  <div class="grid w-full grid-cols-1 sm:grid-cols-4 gap-x-6 gap-y-8">
                    <?php foreach ($themes as $theme): ?>
                      <?php
                        $isActive = $theme['folder'] === $activeTheme;
                        $borderClass = $isActive ? 'border-emerald-600' : 'border-gray-200';
                      ?>
                      <div class="border-4 <?= $borderClass ?> overflow-hidden shadow-sm bg-white h-full">
                        <img src="../../userdata/template/<?= htmlspecialchars($theme['folder']) ?>/image.png"
                            alt="Theme Image" class="w-full h-auto">

                        <div class="p-4">
                          <h3 class="text-lg font-semibold"><?= htmlspecialchars($theme['name']) ?></h3>
                          <p class="text-sm text-gray-500">Version: <?= htmlspecialchars($theme['version']) ?></p>
                          <p class="text-sm text-gray-500">Author: <?= htmlspecialchars($theme['author']) ?></p>
                          <a href="<?= htmlspecialchars($theme['url']) ?>" target="_blank"
                            class="text-sm text-blue-600 underline">
                            <?= htmlspecialchars($theme['url']) ?>
                          </a>
                          <div class="mt-1">
                            <?php
                            if(!$isActive)
                             {
                              echo '<a href="?selected='.htmlspecialchars($theme['folder']).'&name='.htmlspecialchars($theme['name']).'" class="bg-sky-600 hover:bg-sky-500 text-white py-1 px-2 my-1">activate theme</a>';
                             }else{
                              echo '<span class="bg-emerald-600 text-white py-1 px-2 my-1">is active</span>';
                             }
                            ?>
                            <?php 
                            
                              if($theme['update_available'])
                              {
                                echo '<a href="backend_api/thememodify.php?update='.htmlspecialchars($theme['folder']).'" class="bg-sky-600 hover:bg-sky-500 text-white py-1 px-2 my-1">update</a>';
                              }
                            ?>
                          </div>
                          <div>
                            <?php echo '<a href="?remove='.htmlspecialchars($theme['folder']).'&name='.htmlspecialchars($theme['name']).'" class="pt-2 text-sm text-red-600">remove theme</a>'; ?>
                          </div>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>

                
              </div>

            </div>
          </main>
        </div>
        <script src="js/tailwind.js"></script>
        <script src="js/search_theme.js"></script>
    </body>
</html>
