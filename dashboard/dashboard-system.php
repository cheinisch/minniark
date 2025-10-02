<?php

  require_once( __DIR__ . "/../functions/function_backend.php");
  require_once '../vendor/autoload.php';
  $settingspage = "dashboard";
  security_checklogin();

  $news = getNewsFeed();

  $storage = getStorage();

  $version = getVersion();

?>

<!doctype html>
<html lang="<?php echo get_language(); ?>">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Blog Posts - <?php echo get_sitename(); ?></title>
		<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
		<!--<script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>-->
	</head>
	<body class="bg-white dark:bg-black">
		<el-dialog>
			<dialog id="sidebar" class="backdrop:bg-transparent lg:hidden">
				<el-dialog-backdrop class="fixed inset-0 bg-white/80 dark:bg-black/80 transition-opacity duration-300 ease-linear data-closed:opacity-0"></el-dialog-backdrop>
				<div tabindex="0" class="fixed inset-0 flex focus:outline-none">
					<el-dialog-panel class="group/dialog-panel relative mr-16 flex w-full max-w-xs flex-1 transform transition duration-300 ease-in-out data-closed:-translate-x-full">
						<div class="absolute top-0 left-full flex w-16 justify-center pt-5 duration-300 ease-in-out group-data-closed/dialog-panel:opacity-0">
							<button type="button" command="close" commandfor="sidebar" class="-m-2.5 p-2.5">
								<span class="sr-only">Close sidebar</span>
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 text-black dark:text-white">
									<path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
								</svg>
							</button>
						</div>
						<!-- Sidebar component, swap this element with another sidebar if you like -->
						<div class="relative flex grow flex-col gap-y-5 overflow-y-auto bg-white dark:bg-black px-6 pb-4 ring-1 ring-white/10 dark:before:pointer-events-none dark:before:absolute dark:before:inset-0 dark:before:bg-black/10">
							<div class="relative flex h-16 shrink-0 items-center">
								<img src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=indigo&shade=500" alt="Your Company" class="h-8 w-auto" />
							</div>
							<nav class="relative flex flex-1 flex-col">
								<?php include (__DIR__.'/layout/dashboard_menu.php'); ?>
							</nav>
						</div>
					</el-dialog-panel>
				</div>
			</dialog>
		</el-dialog>
		<!-- Static sidebar for desktop -->
		<div class="hidden bg-white dark:bg-black ring-1 ring-black/10 dark:ring-white/10 lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
			<!-- Sidebar component, swap this element with another sidebar if you like -->
			<div class="flex grow flex-col gap-y-5 overflow-y-auto bg-white dark:bg-black/10 px-6 pb-4">
				<div class="flex h-16 shrink-0 items-center">
					<img src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=indigo&shade=500" alt="Your Company" class="h-8 w-auto" />
				</div>
				<nav class="flex flex-1 flex-col">
					<?php include (__DIR__.'/layout/dashboard_menu.php'); ?>
				</nav>
			</div>
		</div>
		<div class="lg:pl-72">
			<div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-black/10 dark:border-gray-200 bg-white px-4 shadow-xs sm:gap-x-6 sm:px-6 lg:px-8  dark:border-white/10 bg-white dark:bg-black">
				<button type="button" command="show-modal" commandfor="sidebar" class="-m-2.5 p-2.5 text-gray-700 hover:text-gray-900 lg:hidden dark:text-gray-400 dark:hover:text-white">
					<span class="sr-only">Open sidebar</span>
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6">
						<path d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" stroke-linecap="round" stroke-linejoin="round" />
					</svg>
				</button>
				<!-- Separator -->
				<div aria-hidden="true" class="h-6 w-px bg-black/10 lg:hidden dark:bg-white/10"></div>
				<div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6 text-black dark:text-white">
					<div class="grid flex-1 grid-cols-1">
						<div class="hidden md:flex justify-start gap-2">
						    <a href="dashboard.php"
								class="inline-flex items-center justify-start mx-2 py-2 border-b hover:border-t border-gray-800 dark:border-gray-400 rounded-none
										no-underline text-base font-normal leading-tight appearance-none">
								<?php echo languageString('nav.dashboard'); ?>
							</a>
							<a href="media.php"
								class="inline-flex items-center justify-start mx-2 py-2 border-b border-gray-800 dark:border-gray-400 rounded-none
										no-underline text-base font-normal leading-tight appearance-none">
								<?php echo languageString('nav.images'); ?>
							</a>
							<a href="blog.php"
								class="inline-flex items-center justify-start mx-4 py-2 border-b-2 border-gray-800 dark:border-gray-400 rounded-none
										no-underline text-base font-normal leading-tight appearance-none">
								<?php echo languageString('nav.blogposts'); ?>
							</a>
							<a href="pages.php"
								class="inline-flex items-center justify-start mx-4 py-2 border-b border-gray-800 dark:border-gray-400 rounded-none
										no-underline text-base font-normal leading-tight appearance-none">
								<?php echo languageString('nav.pages'); ?>
							</a>
						</div>
					</div>
					<div class="flex items-center gap-x-4 lg:gap-x-6">
						<a href="blog-detail.php?post=new"
						id="newPageBtn"
						class="inline-flex items-center gap-2 -m-2.5 p-2.5 text-gray-800 hover:text-gray-500 dark:text-gray-400 dark:hover:text-gray-300">
							<?php echo languageString('blog.new_post'); ?>
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-upload" viewBox="0 0 16 16">
								<path d="M8 6.5a.5.5 0 0 1 .5.5v1.5H10a.5.5 0 0 1 0 1H8.5V11a.5.5 0 0 1-1 0V9.5H6a.5.5 0 0 1 0-1h1.5V7a.5.5 0 0 1 .5-.5"/>
  								<path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5z"/>
							</svg>
							<span class="sr-only">New Post</span>
						</a>
						<button type="button" class="-m-2.5 p-2.5 text-gray-800 hover:text-gray-500 dark:text-gray-400 dark:hover:text-gray-300">
							<span class="sr-only">View notifications</span>
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6">
								<path d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" stroke-linecap="round" stroke-linejoin="round" />
							</svg>
						</button>
						<!-- Separator -->
						<div aria-hidden="true" class="hidden lg:block lg:h-6 lg:w-px lg:bg-white dark:bg-black/10 dark:lg:bg-gray-100/10"></div>
						<!-- Profile dropdown -->
						
						<div data-dropdown class="relative">
							<button type="button" class="relative flex items-center"
									aria-haspopup="menu" aria-expanded="false" data-trigger>
								<span class="absolute -inset-1.5"></span>
								<span class="sr-only">Open user menu</span>
								<img src="<?php echo get_userimage($_SESSION['username']); ?>" alt=""
									class="size-8 rounded-full bg-gray-50 outline -outline-offset-1 outline-black/5 dark:bg-gray-800 dark:outline-white/10" />
								<span class="hidden lg:flex lg:items-center">
								<span aria-hidden="true" class="ml-4 text-sm/6 font-semibold text-gray-900 dark:text-white">
									<?php echo $_SESSION['username']; ?>
								</span>
								<svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true"
									class="ml-2 size-5 text-gray-400 dark:text-gray-500">
									<path d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" fill-rule="evenodd" />
								</svg>
								</span>
							</button>

							<!-- WICHTIG: kein popover/anchor; stattdessen hidden + role -->
							<div data-menu hidden role="menu" aria-labelledby=""
								class="w-32 origin-top-right rounded-md py-2 shadow-lg outline outline-gray-900/5 transition transition-discrete
										[--anchor-gap:--spacing(2.5)]
										data-closed:scale-95 data-closed:transform data-closed:opacity-0
										data-enter:duration-100 data-enter:ease-out data-leave:duration-75 data-leave:ease-in
										bg-white dark:bg-black dark:shadow-none dark:-outline-offset-1 dark:outline-white/10">

								<a href="dashboard-personal.php"
								class="block px-3 py-1 text-sm/6 text-gray-900 hover:bg-gray-50 focus:outline-hidden dark:text-white dark:hover:bg-white/5"
								role="menuitem">
								<?php echo languageString('nav.your_profile'); ?>
								</a>
								<a href="login.php?logout=true"
								class="block px-3 py-1 text-sm/6 text-gray-900 hover:bg-gray-50 focus:outline-hidden dark:text-white dark:hover:bg-white/5"
								role="menuitem">
								<?php echo languageString('nav.sign_out'); ?>
								</a>
							</div>
							</div>

					</div>
				</div>
			</div>
			<!-- Zweite Leiste: nur auf sm sichtbar -->
			<div class="sm:block md:hidden border-b border-gray-600 dark:border-gray-200 bg-white bg-white dark:bg-black dark:border-black dark:border-white/10">
				<div class="px-4 sm:px-6 lg:px-8 text-black dark:text-white">
					<nav class="flex gap-2 justify-center">
					<a href="dashboard.php"
						class="inline-flex items-center  py-2 border-b hover:border-t border-gray-800 dark:border-gray-400 rounded-none
								no-underline text-base font-normal leading-tight appearance-none">
						<?php echo languageString('nav.dashboard'); ?>
					</a>
					<a href="media.php"
						class="inline-flex items-center py-2 border-b-2 border-gray-800 dark:border-gray-400 rounded-none
								no-underline text-base font-normal leading-tight appearance-none">
						<?php echo languageString('nav.images'); ?>
					</a>
					<a href="blog.php"
						class="inline-flex items-center py-2 border-b border-gray-800 dark:border-gray-400 rounded-none
								no-underline text-base font-normal leading-tight appearance-none">
						<?php echo languageString('nav.blogposts'); ?>
					</a>
					<a href="pages.php"
						class="inline-flex items-center py-2 border-b border-gray-800 dark:border-gray-400 rounded-none
								no-underline text-base font-normal leading-tight appearance-none">
						<?php echo languageString('nav.pages'); ?>
					</a>
					</nav>
				</div>
			</div>
<main class="py-10 bg-white dark:bg-black">
  <div class="px-4 sm:px-6 lg:px-8 text-black dark:text-white">
    <div class="space-y-4">

      <!-- Site Information -->
      <section class="rounded-sm border border-black/10 dark:border-white/10 bg-white dark:bg-black/40 shadow-xs">
        <header class="px-4 py-3 border-b border-black/10 dark:border-white/10">
          <h2 class="text-sm font-semibold">Site Information</h2>
          <p class="mt-1 text-xs text-black/60 dark:text-gray-400">Some Site Information</p>
        </header>

        <div class="px-4 py-4">
          <form class="md:col-span-2" id="change-sitedata-form">
            <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:max-w-xl sm:grid-cols-6">
              <!-- Notifications -->
              <div id="notification-success-user" class="hidden col-span-full rounded-sm border border-emerald-700/30 bg-emerald-500/10 text-emerald-300 px-3 py-2 text-xs" role="alert">
                <strong class="font-semibold">Erfolg!</strong>
                <span class="ml-1">Daten wurden gespeichert.</span>
              </div>
              <div id="notification-error-user" class="hidden col-span-full rounded-sm border border-red-700/30 bg-red-500/10 text-red-300 px-3 py-2 text-xs" role="alert">
                <strong class="font-semibold">Fehler!</strong>
                <span class="ml-1">Etwas ist schiefgelaufen.</span>
              </div>

              <div class="sm:col-span-full">
                <label for="site-name" class="block text-xs font-medium">Site name</label>
                <input type="text" name="site-name" id="site-name" value="<?php echo get_sitename(); ?>"
                       class="mt-1 block w-full bg-white/5 px-3 py-1.5 text-sm outline-1 -outline-offset-1 outline-gray-500 dark:outline-white/10 placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-sky-500">
              </div>

              <div class="sm:col-span-full">
                <label for="site-decription" class="block text-xs font-medium">Site Description</label>
                <input type="text" name="site-decription" id="site-decription" value="<?php echo get_sitedescription(); ?>"
                       class="mt-1 block w-full bg-white/5 px-3 py-1.5 text-sm outline-1 -outline-offset-1 outline-gray-500 dark:outline-white/10 placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-sky-500">
              </div>

              <!-- Language (normales select) -->
              <div class="sm:col-span-full">
                <label for="language-select" class="block text-xs font-medium">Language</label>
                <select id="language-select"
                        class="mt-1 block w-full rounded-md bg-white px-3 py-1.5 text-sm text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-sky-600">
                  <?php 
                    $langs = getLangFiles();
                    $current = get_language();
                    if (!empty($langs)):
                      foreach ($langs as $lang):
                        $sel = strcasecmp($lang, $current) === 0 ? 'selected' : '';
                        echo '<option value="'.htmlspecialchars($lang, ENT_QUOTES, 'UTF-8').'" '.$sel.'>'.htmlspecialchars($lang, ENT_QUOTES, 'UTF-8').'</option>';
                      endforeach;
                    else:
                      echo '<option value="">No languages found</option>';
                    endif;
                  ?>
                </select>
                <!-- Hidden bleibt wegen JS/Backend-Kompatibilität -->
                <input type="hidden" name="language" id="selected-language" value="<?php echo get_language(); ?>">
              </div>
            </div>

            <div class="mt-4 flex">
              <button type="submit" id="btnSiteSettings"
                      class="text-xs px-2 py-1 rounded border border-black/10 hover:bg-black/5 dark:border-white/10 dark:hover:bg-white/10">
                <?php echo languageString('general.save'); ?>
              </button>
            </div>
          </form>
        </div>
      </section>

      <!-- Image Settings -->
      <section class="rounded-sm border border-black/10 dark:border-white/10 bg-white dark:bg-black/40 shadow-xs">
        <header class="px-4 py-3 border-b border-black/10 dark:border-white/10">
          <h2 class="text-sm font-semibold">Image Settings</h2>
          <p class="mt-1 text-xs text-black/60 dark:text-gray-400">Select some Settings for the Images</p>
        </header>

        <div class="px-4 py-4">
          <form class="md:col-span-2" id="change-image-size">
            <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:max-w-xl sm:grid-cols-6">
              <div id="notification-success" class="hidden col-span-full rounded-sm border border-emerald-700/30 bg-emerald-500/10 text-emerald-300 px-3 py-2 text-xs" role="alert">
                <strong class="font-semibold">Erfolg!</strong>
                <span class="ml-1">Bildgröße geändert!</span>
              </div>
              <div id="notification-error" class="hidden col-span-full rounded-sm border border-red-700/30 bg-red-500/10 text-red-300 px-3 py-2 text-xs" role="alert">
                <strong class="font-semibold">Fehler!</strong>
                <span class="ml-1">Bildgröße nicht geändert!</span>
              </div>

              <!-- Image size (normales select) -->
              <div class="sm:col-span-full">
                <label for="image-size-select" class="block text-xs font-medium">Default Image size (for cached images)</label>
                <?php $imgSize = get_imagesize(); ?>
                <select id="image-size-select"
                        class="mt-1 block w-full rounded-md bg-white px-3 py-1.5 text-sm text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-sky-600">
                  <option value="L"   <?php echo $imgSize==='L'   ? 'selected' : ''; ?>>L</option>
                  <option value="XL"  <?php echo $imgSize==='XL'  ? 'selected' : ''; ?>>XL</option>
                  <option value="XXL" <?php echo $imgSize==='XXL' ? 'selected' : ''; ?>>XXL</option>
                  <option value="Original" <?php echo $imgSize==='Original' ? 'selected' : ''; ?>>Original</option>
                </select>
                <!-- Hidden bleibt bestehen -->
                <input type="hidden" name="image_size" id="image_size" value="<?php echo get_imagesize(); ?>">
              </div>
            </div>

            <div class="mt-4 flex gap-2">
              <button type="submit"
                      class="text-xs px-2 py-1 rounded border border-black/10 hover:bg-black/5 dark:border-white/10 dark:hover:bg-white/10">
                <?php echo languageString('general.save'); ?>
              </button>
              <button id="recreate-cache-button"
                      class="text-xs px-2 py-1 rounded border border-black/10 hover:bg-black/5 dark:border-white/10 dark:hover:bg-white/10">
                Recreate Cache
              </button>
            </div>
          </form>
        </div>
      </section>

      <!-- Timeline Settings -->
      <section class="rounded-sm border border-black/10 dark:border-white/10 bg-white dark:bg-black/40 shadow-xs">
        <header class="px-4 py-3 border-b border-black/10 dark:border-white/10">
          <h2 class="text-sm font-semibold">Timeline Settings</h2>
          <p class="mt-1 text-xs text-black/60 dark:text-gray-400">Select some Settings for the Timeline</p>
        </header>

        <div class="px-4 py-4">
          <form class="md:col-span-2" id="change-timeline-form">
            <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:max-w-xl sm:grid-cols-6">
              <div id="notification-timeline-success" class="hidden col-span-full rounded-sm border border-emerald-700/30 bg-emerald-500/10 text-emerald-300 px-3 py-2 text-xs" role="alert">
                <strong class="font-semibold">Success!</strong>
                <span class="ml-1">Settings are saved.</span>
              </div>
              <div id="notification-timeline-error" class="hidden col-span-full rounded-sm border border-red-700/30 bg-red-500/10 text-red-300 px-3 py-2 text-xs" role="alert">
                <strong class="font-semibold">Error!</strong>
                <span class="ml-1">Settings not changed.</span>
              </div>

              <div class="col-span-full">
                <div class="flex items-center justify-between">
                  <span class="flex grow flex-col">
                    <span class="text-sm font-medium">Enable Timeline</span>
                    <span class="text-xs text-black/60 dark:text-gray-400">Enable the timeline in the main navigation</span>
                  </span>
                  <button type="button" id="timline_enable"
                          class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent bg-gray-400 transition-colors duration-200 ease-in-out focus:ring-2 focus:ring-sky-600 focus:ring-offset-2 focus:outline-hidden"
                          role="switch" aria-checked="<?php echo is_timeline_enabled(); ?>">
                    <span aria-hidden="true" class="pointer-events-none inline-block size-5 translate-x-0 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out"></span>
                  </button>
                </div>
              </div>

              <div class="col-span-full">
                <div class="flex items-center justify-between">
                  <span class="flex grow flex-col">
                    <span class="text-sm font-medium">Group by month</span>
                    <span class="text-xs text-black/60 dark:text-gray-400">Group the images by month</span>
                  </span>
                  <button type="button" id="timline_group"
                          class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent bg-gray-400 transition-colors duration-200 ease-in-out focus:ring-2 focus:ring-sky-600 focus:ring-offset-2 focus:outline-hidden"
                          role="switch" aria-checked="<?php echo is_timeline_grouped(); ?>">
                    <span aria-hidden="true" class="pointer-events-none inline-block size-5 translate-x-0 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out"></span>
                  </button>
                </div>
              </div>
            </div>

            <div class="mt-4 flex">
              <button type="submit" id="btn_timeline"
                      class="text-xs px-2 py-1 rounded border border-black/10 hover:bg-black/5 dark:border-white/10 dark:hover:bg-white/10">
                <?php echo languageString('general.save'); ?>
              </button>
            </div>
          </form>
        </div>
      </section>

      <!-- Map Settings -->
      <section class="rounded-sm border border-black/10 dark:border-white/10 bg-white dark:bg-black/40 shadow-xs">
        <header class="px-4 py-3 border-b border-black/10 dark:border-white/10">
          <h2 class="text-sm font-semibold">Map Settings</h2>
          <p class="mt-1 text-xs text-black/60 dark:text-gray-400">Select some Settings for the Mapview</p>
        </header>

        <div class="px-4 py-4">
          <form class="md:col-span-2" id="change-map-form">
            <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:max-w-xl sm:grid-cols-6">
              <div id="notification-map-success" class="hidden col-span-full rounded-sm border border-emerald-700/30 bg-emerald-500/10 text-emerald-300 px-3 py-2 text-xs" role="alert">
                <strong class="font-semibold">Success!</strong>
                <span class="ml-1">Settings are saved.</span>
              </div>
              <div id="notification-map-error" class="hidden col-span-full rounded-sm border border-red-700/30 bg-red-500/10 text-red-300 px-3 py-2 text-xs" role="alert">
                <strong class="font-semibold">Error!</strong>
                <span class="ml-1">Settings not changed.</span>
              </div>

              <div class="col-span-full">
                <div class="flex items-center justify-between">
                  <span class="flex grow flex-col">
                    <span class="text-sm font-medium">Enable Map</span>
                    <span class="text-xs text-black/60 dark:text-gray-400">Enables the map in the main navigation</span>
                  </span>
                  <button type="button" id="map_enable"
                          class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent bg-gray-400 transition-colors duration-200 ease-in-out focus:ring-2 focus:ring-sky-600 focus:ring-offset-2 focus:outline-hidden"
                          role="switch" aria-checked="<?php echo is_map_enabled(); ?>">
                    <span aria-hidden="true" class="pointer-events-none inline-block size-5 translate-x-0 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out"></span>
                  </button>
                </div>
              </div>
            </div>

            <div class="mt-4 flex">
              <button type="submit" id="btn_map"
                      class="text-xs px-2 py-1 rounded border border-black/10 hover:bg-black/5 dark:border-white/10 dark:hover:bg-white/10">
                <?php echo languageString('general.save'); ?>
              </button>
            </div>
          </form>
        </div>
      </section>

      <!-- Sitemap Settings -->
      <section class="rounded-sm border border-black/10 dark:border-white/10 bg-white dark:bg-black/40 shadow-xs">
        <header class="px-4 py-3 border-b border-black/10 dark:border-white/10">
          <h2 class="text-sm font-semibold">Sitemap Settings</h2>
        </header>

        <div class="px-4 py-4">
          <form class="md:col-span-2" id="change-sitemap-form">
            <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:max-w-xl sm:grid-cols-6">
              <div id="notification-sitemap-success" class="hidden col-span-full rounded-sm border border-emerald-700/30 bg-emerald-500/10 text-emerald-300 px-3 py-2 text-xs" role="alert">
                <strong class="font-semibold">Success!</strong>
                <span class="ml-1">Settings are saved.</span>
              </div>
              <div id="notification-sitemap-error" class="hidden col-span-full rounded-sm border border-red-700/30 bg-red-500/10 text-red-300 px-3 py-2 text-xs" role="alert">
                <strong class="font-semibold">Error!</strong>
                <span class="ml-1">Settings not changed.</span>
              </div>

              <div class="col-span-full">
                <div class="flex items-center justify-between">
                  <span class="flex grow flex-col">
                    <span class="text-sm font-medium">Enable Sitemap</span>
                    <span class="text-xs text-black/60 dark:text-gray-400">Enables the sitemap.xml in the root directory</span>
                  </span>
                  <button type="button" id="sitemap_enable"
                          class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent bg-gray-400 transition-colors duration-200 ease-in-out focus:ring-2 focus:ring-sky-600 focus:ring-offset-2 focus:outline-hidden"
                          role="switch" aria-checked="<?php echo is_sitemap_enabled(); ?>">
                    <span aria-hidden="true" class="pointer-events-none inline-block size-5 translate-x-0 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out"></span>
                  </button>
                </div>
              </div>

              <div class="col-span-full">
                <div class="flex items-center justify-between">
                  <span class="flex grow flex-col">
                    <span class="text-sm font-medium">Enable Images in Sitemap</span>
                    <span class="text-xs text-black/60 dark:text-gray-400">Include images in the sitemap.xml</span>
                  </span>
                  <button type="button" id="sitemap_images_enable"
                          class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent bg-gray-400 transition-colors duration-200 ease-in-out focus:ring-2 focus:ring-sky-600 focus:ring-offset-2 focus:outline-hidden"
                          role="switch" aria-checked="<?php echo is_sitemap_images_enabled(); ?>">
                    <span aria-hidden="true" class="pointer-events-none inline-block size-5 translate-x-0 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out"></span>
                  </button>
                </div>
              </div>
            </div>

            <div class="mt-4 flex">
              <button type="submit" id="btn_sitemap"
                      class="text-xs px-2 py-1 rounded border border-black/10 hover:bg-black/5 dark:border-white/10 dark:hover:bg-white/10">
                <?php echo languageString('general.save'); ?>
              </button>
            </div>
          </form>
        </div>
      </section>

      <!-- Supporter License -->
      <section class="rounded-sm border border-black/10 dark:border-white/10 bg-white dark:bg-black/40 shadow-xs">
        <header class="px-4 py-3 border-b border-black/10 dark:border-white/10">
          <h2 class="text-sm font-semibold">Supporter License</h2>
          <p class="mt-1 text-xs text-black/60 dark:text-gray-400">
            Add the additional supporter License Key. Required for some Plugins.
            You can buy the license <a href="https://store.minniark.com" class="underline">here</a>.
          </p>
        </header>

        <div class="px-4 py-4">
          <form class="md:col-span-2" id="change-sitelicense-form" action="backend_api/settings_save.php" method="post">
            <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:max-w-xl sm:grid-cols-6">
              <div class="sm:col-span-full">
                <label for="site-license" class="block text-xs font-medium">License Key</label>

                <?php if(!empty(get_license())): $info = getLicenseInformation(); ?>
                  <ul class="mt-2 text-sm text-black/80 dark:text-gray-300">
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
                        <li>Status: <?php echo $info['expired'] ? '<span class="text-red-600">expired</span>' : '<span class="text-emerald-600">active</span>'; ?></li>
                        <?php if (!$info['expired']): ?>
                          <li>Remaining days: <?php echo $info['days']; ?></li>
                        <?php endif; ?>
                        <li>Expire date: <?php echo htmlspecialchars($info['expired_date']); ?></li>
                      <?php endif; ?>
                      <li>Activation: <?php echo (int)$info['timesActivated'] . ' / ' . (int)$info['timesActivatedMax']; ?></li>
                    <?php endif; ?>
                  </ul>
                <?php endif; ?>

                <input type="text" name="site-license" id="site-license" value="<?php echo get_license(); ?>"
                       class="mt-2 block w-full bg-white/5 px-3 py-1.5 text-sm outline-1 -outline-offset-1 outline-gray-500 dark:outline-white/10 placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-sky-500">
              </div>
            </div>

            <div class="mt-4 flex">
              <button type="submit" id="btnSiteLicense"
                      class="text-xs px-2 py-1 rounded border border-black/10 hover:bg-black/5 dark:border-white/10 dark:hover:bg-white/10">
                <?php echo languageString('general.save'); ?>
              </button>
            </div>
          </form>
        </div>
      </section>

    </div>
  </div>

  <!-- Sync-Skripte für Language & Image Size (halten Hidden-Felder kompatibel) -->
  <script>
    (function () {
      // Language
      const langHidden = document.getElementById('selected-language');
      const langSelect = document.getElementById('language-select');
      if (langHidden && langSelect) {
        // initial sicherstellen
        if (langHidden.value) langSelect.value = langHidden.value;
        langSelect.addEventListener('change', () => { langHidden.value = langSelect.value; });
      }

      // Image size
      const imgHidden = document.getElementById('image_size');
      const imgSelect = document.getElementById('image-size-select');
      if (imgHidden && imgSelect) {
        if (imgHidden.value) imgSelect.value = imgHidden.value;
        imgSelect.addEventListener('change', () => { imgHidden.value = imgSelect.value; });
      }
    })();
  </script>
</main>
			


		</div>
		<script src="js/album_collection.js"></script>
		<script src="js/navbar.js"></script>
		<script src="js/tailwind.js"></script>
		<script src="js/profile_settings.js"></script>
        <script src="js/file_upload.js"></script>
        <script>
			(() => {
			let pendingLink = null;

			const dlg     = document.getElementById('deleteImageModal');
			const btnYes  = document.getElementById('confirmYes'); // Delete (rot)
			const btnNo   = document.getElementById('confirmNo');  // Cancel

			if (!dlg || !btnYes || !btnNo) return;

			// Delegation: Klick auf einen Delete-Link in der Bilderliste
			const imageList = document.getElementById('image-list');
			if (imageList) {
				imageList.addEventListener('click', (e) => {
				const a = e.target.closest('a.confirm-link, a[href*="backend_api/delete.php"]');
				if (!a) return;
				e.preventDefault();
				pendingLink = a.href;

				// Optional: Titel/Body im Modal anpassen (wenn du willst)
				// document.getElementById('dialog-title').textContent = 'Bild löschen?';

				// Neues Dialog öffnen
				if (typeof dlg.showModal === 'function') {
					dlg.showModal();
				} else {
					// Fallback (sollte selten nötig sein)
					dlg.setAttribute('open', '');
				}
				});
			}

			// Bestätigen -> weiterleiten
			btnYes.addEventListener('click', () => {
				const href = pendingLink;
				pendingLink = null;
				if (dlg.open) dlg.close();
				if (href) window.location.assign(href);
			});

			// Abbrechen -> schließen
			btnNo.addEventListener('click', () => {
				pendingLink = null;
				if (dlg.open) dlg.close();
			});

			// Dialog wird anderweitig geschlossen (Esc etc.)
			dlg.addEventListener('close', () => { pendingLink = null; });
			})();
		</script>
		<script>
          let pendingLink = null;

          // Klick auf bestätigungspflichtige Links
          document.querySelectorAll('.confirm-link').forEach(link => {
            link.addEventListener('click', function (e) {
              e.preventDefault();
              pendingLink = this.href;
              document.getElementById('confirmModal').classList.remove('hidden');
            });
          });

          // Abbrechen → Modal schließen
          document.getElementById('confirmYes').addEventListener('click', () => {
            document.getElementById('confirmModal').classList.add('hidden');
            pendingLink = null;
          });

          // Bestätigen → Weiterleitung
          document.getElementById('confirmNo').addEventListener('click', () => {
            if (pendingLink) {
              window.location.href = pendingLink;
            }
          });
        </script>
        <script>
          document.querySelectorAll('.assign-to-album-btn').forEach(button => {
            button.addEventListener('click', () => {
              const filename = button.getAttribute('data-filename');
              document.getElementById('assignImageFilename').value = filename;
              document.getElementById('assignToAlbumModal').classList.remove('hidden');
            });
          });

          document.getElementById('cancelAssignAlbum').addEventListener('click', () => {
            document.getElementById('assignToAlbumModal').classList.add('hidden');
          });

          document.getElementById('closeAssignToAlbumModal').addEventListener('click', () => {
            document.getElementById('assignToAlbumModal').classList.add('hidden');
          });
        </script>
        <!--<script>
        document.getElementById('location').addEventListener('change', function () {
            const url = this.value;
            window.location.href = url; // Weiterleitung zur gewählten URL
        });
      </script>-->

	</body>
</html>
