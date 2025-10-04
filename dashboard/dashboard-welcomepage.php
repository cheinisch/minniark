<?php
  require_once( __DIR__ . "/../functions/function_backend.php");
  $settingspage = "welcomepage";
  security_checklogin();

  $home = getHomeConfig();

  $imageDir = realpath(__DIR__ . '/../userdata/content/images');
  $images = [];

  if ($imageDir && is_dir($imageDir)) {
      foreach (glob($imageDir . '/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE) as $imgFile) {
          $images[] = basename($imgFile);
      }
  }

  $home['available_images'] = $images;

  $pagetype = "Welcome Page";
  if ($home['style'] === "album") { $pagetype = "Album"; }
  elseif ($home['style'] === "page") { $pagetype = "Page"; }
  else { $pagetype = "Welcome Page"; }

  if (empty($home['style'])) { $home['style'] = "start"; }

  $albumList = getAlbumList();
  $pageList  = get_Pages();

  $startvalueAlbum = isInList($home['startcontent'],$albumList);
  $startvaluePage  = isInListPage($home['startcontent'],$pageList);
?>
<!doctype html>
<html lang="<?php echo get_language(); ?>">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Welcome Page · <?php echo get_sitename(); ?></title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
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

      <!-- Main -->
      <main class="py-10 bg-white dark:bg-black">
        <div class="px-4 sm:px-6 lg:px-8 text-black dark:text-white">
          <!-- Toolbar -->
          <div class="mb-4 flex items-center justify-end gap-2">
            <a href="backend_api/cache.php"
               class="text-xs px-2 py-1 rounded border border-black/10 hover:bg-black/5 dark:border-white/10 dark:hover:bg-white/10">
              Clear Cache
            </a>
          </div>

          <!-- Sektion: Start Page Type -->
          <section class="rounded-sm border border-black/10 dark:border-white/10 bg-white dark:bg-black/40 shadow-xs mb-6">
            <header class="px-4 py-3 border-b border-black/10 dark:border-white/10">
              <h2 class="text-sm font-semibold">Start Page Type</h2>
              <p class="mt-1 text-xs text-black/60 dark:text-gray-400">Select some settings for the start page.</p>
            </header>
            <div class="p-4">
              <form id="welcome-type-form" action="backend_api/home_save.php" method="post" class="grid grid-cols-1 gap-x-6 gap-y-6 sm:max-w-xl">
                <!-- Custom Select Type -->
                <div>
                  <label id="listbox-type-label" class="block text-sm font-medium">Default Page Type</label>
                  <div class="relative mt-2">
                    <button type="button"
                            class="grid w-full cursor-default grid-cols-1 rounded-md bg-white py-1.5 pr-2 pl-3 text-left text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-sky-600 sm:text-sm/6 dark:bg-white/10 dark:text-white dark:outline-white/10"
                            aria-haspopup="listbox-type" aria-expanded="false" aria-labelledby="listbox-type-label">
                      <span class="col-start-1 row-start-1 truncate pr-6"><?php echo $pagetype; ?></span>
                      <svg class="col-start-1 row-start-1 size-5 self-center justify-self-end text-gray-500 sm:size-4" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.22 10.22a.75.75 0 0 1 1.06 0L8 11.94l1.72-1.72a.75.75 0 1 1 1.06 1.06l-2.25 2.25a.75.75 0 0 1-1.06 0l-2.25-2.25a.75.75 0 0 1 0-1.06ZM10.78 5.78a.75.75 0 0 1-1.06 0L8 4.06 6.28 5.78a.75.75 0 0 1-1.06-1.06l2.25-2.25a.75.75 0 0 1 1.06 0l2.25 2.25a.75.75 0 0 1 0 1.06Z" clip-rule="evenodd" />
                      </svg>
                    </button>
                    <ul class="hidden absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black/5 focus:outline-hidden sm:text-sm dark:bg-black dark:ring-white/10"
                        tabindex="-1" role="listbox" aria-labelledby="listbox-type-label">
                      <li class="relative cursor-default py-2 pr-9 pl-3 text-gray-900 dark:text-white select-none" role="option" data-value="start">
                        <span class="block truncate font-normal">Welcome Page</span>
                        <span class="absolute inset-y-0 right-0 hidden items-center pr-4 text-sky-600">
                          <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                          </svg>
                        </span>
                      </li>
                      <li class="relative cursor-default py-2 pr-9 pl-3 text-gray-900 dark:text-white select-none" role="option" data-value="page">
                        <span class="block truncate font-normal">Page</span>
                        <span class="absolute inset-y-0 right-0 hidden items-center pr-4 text-sky-600">
                          <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" /></svg>
                        </span>
                      </li>
                      <li class="relative cursor-default py-2 pr-9 pl-3 text-gray-900 dark:text-white select-none" role="option" data-value="album">
                        <span class="block truncate font-normal">Album</span>
                        <span class="absolute inset-y-0 right-0 hidden items-center pr-4 text-sky-600">
                          <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" /></svg>
                        </span>
                      </li>
                    </ul>
                  </div>
                  <input type="hidden" name="welcome_type" id="welcome_type" value="<?php echo $home['style']; ?>">
                </div>

                <!-- Page Select -->
                <div id="second_select_typ-page">
                  <label id="listbox-page-label" class="block text-sm font-medium">Select Page</label>
                  <div class="relative mt-2">
                    <button type="button"
                            class="grid w-full cursor-default grid-cols-1 rounded-md bg-white py-1.5 pr-2 pl-3 text-left text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-sky-600 sm:text-sm/6 dark:bg-white/10 dark:text-white dark:outline-white/10"
                            aria-haspopup="listbox-page" aria-expanded="false" aria-labelledby="listbox-page-label">
                      <span class="col-start-1 row-start-1 truncate pr-6"><?php echo $startvaluePage; ?></span>
                      <svg class="col-start-1 row-start-1 size-5 self-center justify-self-end text-gray-500 sm:size-4" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.22 10.22a.75.75 0 0 1 1.06 0L8 11.94l1.72-1.72a.75.75 0 1 1 1.06 1.06l-2.25 2.25a.75.75 0 0 1-1.06 0l-2.25-2.25a.75.75 0 0 1 0-1.06ZM10.78 5.78a.75.75 0 0 1-1.06 0L8 4.06 6.28 5.78a.75.75 0 0 1-1.06-1.06l2.25-2.25a.75.75 0 0 1 1.06 0l2.25 2.25a.75.75 0 0 1 0 1.06Z" clip-rule="evenodd"/></svg>
                    </button>
                    <ul class="hidden absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black/5 focus:outline-hidden sm:text-sm dark:bg-black dark:ring-white/10" tabindex="-1" role="listbox" aria-labelledby="listbox-page-label">
                      <?php foreach ($pageList as $page): ?>
                        <li class="relative cursor-default py-2 pr-9 pl-3 text-gray-900 dark:text-white select-none" role="option" data-value="<?php echo htmlspecialchars($page['slug']); ?>">
                          <span class="block truncate font-normal"><?php echo htmlspecialchars($page['title']); ?></span>
                          <span class="absolute inset-y-0 right-0 hidden items-center pr-4 text-sky-600">
                            <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" /></svg>
                          </span>
                        </li>
                      <?php endforeach; ?>
                    </ul>
                  </div>
                </div>

                <!-- Album Select -->
                <div id="second_select_typ-album">
                  <label id="listbox-album-label" class="block text-sm font-medium">Select Album</label>
                  <div class="relative mt-2">
                    <button type="button"
                            class="grid w-full cursor-default grid-cols-1 rounded-md bg-white py-1.5 pr-2 pl-3 text-left text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-sky-600 sm:text-sm/6 dark:bg-white/10 dark:text-white dark:outline-white/10"
                            aria-haspopup="listbox-album" aria-expanded="false" aria-labelledby="listbox-album-label">
                      <span class="col-start-1 row-start-1 truncate pr-6"><?php echo $startvalueAlbum; ?></span>
                      <svg class="col-start-1 row-start-1 size-5 self-center justify-self-end text-gray-500 sm:size-4" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.22 10.22a.75.75 0 0 1 1.06 0L8 11.94l1.72-1.72a.75.75 0 1 1 1.06 1.06l-2.25 2.25a.75.75 0 0 1-1.06 0l-2.25-2.25a.75.75 0 0 1 0-1.06ZM10.78 5.78a.75.75 0 0 1-1.06 0L8 4.06 6.28 5.78a.75.75 0 0 1-1.06-1.06l2.25-2.25a.75.75 0 0 1 1.06 0l2.25 2.25a.75.75 0 0 1 0 1.06Z" clip-rule="evenodd"/></svg>
                    </button>
                    <ul class="hidden absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black/5 focus:outline-hidden sm:text-sm dark:bg-black dark:ring-white/10" tabindex="-1" role="listbox" aria-labelledby="listbox-album-label">
                      <?php foreach ($albumList as $album): ?>
                        <li class="relative cursor-default py-2 pr-9 pl-3 text-gray-900 dark:text-white select-none" role="option" data-value="<?php echo htmlspecialchars($album['slug']); ?>">
                          <span class="block truncate font-normal"><?php echo htmlspecialchars($album['title']); ?></span>
                          <span class="absolute inset-y-0 right-0 hidden items-center pr-4 text-sky-600">
                            <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/></svg>
                          </span>
                        </li>
                      <?php endforeach; ?>
                    </ul>
                  </div>
                </div>

                <input type="hidden" name="welcome_content" id="welcome_content" value="<?php echo $home['startcontent']; ?>">

                <div class="pt-2">
                  <button type="submit" class="bg-sky-600 hover:bg-sky-500 px-3 py-2 text-sm font-semibold text-white rounded shadow-xs">
                    <?php echo languageString('general.save'); ?>
                  </button>
                </div>
              </form>
            </div>
          </section>

          <!-- Sektion: Welcome Page Content -->
          <section class="rounded-sm border border-black/10 dark:border-white/10 bg-white dark:bg-black/40 shadow-xs mb-6">
            <header class="px-4 py-3 border-b border-black/10 dark:border-white/10">
              <h2 class="text-sm font-semibold">Welcome Page Content</h2>
              <p class="mt-1 text-xs text-black/60 dark:text-gray-400">Some site information</p>
            </header>
            <div class="p-4">
              <form id="welcome-content-form" action="backend_api/home_save.php" method="post" class="grid grid-cols-1 gap-x-6 gap-y-6 sm:max-w-xl">
                <div>
                  <label for="headline" class="block text-sm font-medium">Headline</label>
                  <input type="text" name="headline" id="headline" value="<?php echo $home['headline']; ?>"
                         class="mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-sm text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-sky-600 dark:bg-white/10 dark:text-white dark:outline-white/10">
                </div>
                <div>
                  <label for="sub-headline" class="block text-sm font-medium">Sub Headline</label>
                  <input type="text" name="sub-headline" id="sub-headline" value="<?php echo $home['sub-headline']; ?>"
                         class="mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-sm text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-sky-600 dark:bg-white/10 dark:text-white dark:outline-white/10">
                </div>
                <div>
                  <label for="content" class="block text-sm font-medium">Content</label>
                  <textarea name="content" id="content" rows="6"
                            class="mt-2 block w-full rounded-md bg-white px-3 py-2 text-sm text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-sky-600 dark:bg-white/10 dark:text-white dark:outline-white/10"><?php echo $home['content']; ?></textarea>
                </div>
                <div class="pt-2">
                  <button type="submit" id="btnWelcomeSite" class="bg-sky-600 hover:bg-sky-500 px-3 py-2 text-sm font-semibold text-white rounded shadow-xs">
                    <?php echo languageString('general.save'); ?>
                  </button>
                </div>
              </form>
            </div>
          </section>

          <!-- Sektion: Background (Cover) -->
          <section class="rounded-sm border border-black/10 dark:border-white/10 bg-white dark:bg-black/40 shadow-xs">
            <header class="px-4 py-3 border-b border-black/10 dark:border-white/10">
              <h2 class="text-sm font-semibold">Background</h2>
              <p class="mt-1 text-xs text-black/60 dark:text-gray-400">Select an image or an album for the background cover.</p>
            </header>
            <div class="p-4">
              <form method="POST" action="backend_api/home_save.php" class="grid grid-cols-1 gap-4 sm:max-w-xl">
                <input type="hidden" name="cover" id="cover-input" value="">
                <input type="hidden" name="default_image_style" id="cover-style" value="">

                <div class="flex items-center gap-3">
                  <button type="button" id="open-cover-modal"
                          class="bg-sky-600 hover:bg-sky-500 px-3 py-2 text-sm font-semibold text-white rounded shadow-xs">
                    Select Image / Album
                  </button>
                  <button type="submit" id="save-cover-btn"
                          class="bg-emerald-600 hover:bg-emerald-500 px-3 py-2 text-sm font-semibold text-white rounded shadow-xs">
                    Save
                  </button>
                </div>

                <!-- Preview -->
                <div id="cover-preview" class="mt-2">
                  <?php
                    $image = $home['default_image'] ?? '';
                    $style = $home['default_image_style'] ?? '';
                  ?>
                  <?php if ($style === 'image' && $image): ?>
                    <img src="/userdata/content/images/<?php echo htmlspecialchars($image); ?>" alt="Cover Preview"
                         class="mt-2 w-40 rounded shadow border border-black/10 dark:border-white/10">
                    <p class="text-xs text-black/60 dark:text-gray-400 mt-1"><?php echo htmlspecialchars($image); ?></p>
                  <?php elseif ($style === 'album' && $image): ?>
                    <p class="mt-2 text-sm text-sky-600 font-semibold">Album: <?php echo htmlspecialchars($image); ?></p>
                  <?php endif; ?>
                </div>
              </form>
            </div>
          </section>
        </div>
      </main>
    </div>

    <!-- COVER SELECTOR MODAL (neues Design, el-dialog) -->
    <el-dialog>
      <div id="cover-modal" class="hidden fixed inset-0 z-50" role="dialog" aria-modal="true" aria-labelledby="cover-title">
        <el-dialog-backdrop class="fixed inset-0 bg-gray-500/75 dark:bg-black/60 transition-opacity data-closed:opacity-0"></el-dialog-backdrop>
        <div tabindex="0" class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
          <el-dialog-panel
            class="relative transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all
                   sm:my-8 sm:w-full sm:max-w-2xl sm:p-6
                   dark:bg-black dark:outline dark:-outline-offset-1 dark:outline-white/10">
            <!-- Close -->
            <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
              <button type="button" id="close-cover-modal"
                      class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-2 focus:outline-offset-2 focus:outline-sky-600
                             dark:bg-black dark:hover:text-gray-300 dark:focus:outline-white">
                <span class="sr-only">Close</span>
                <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                  <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
              </button>
            </div>

            <!-- Header -->
            <div class="sm:flex sm:items-start">
              <div class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-sky-100 sm:mx-0 sm:size-10 dark:bg-sky-500/10">
                <svg class="size-6 text-sky-600 dark:text-sky-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h6l2 2h10M3 7v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V9M9 17h6" />
                </svg>
              </div>
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <h2 id="cover-title" class="text-base font-semibold text-gray-900 dark:text-white">Select Background Image or Album</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Choose an album or a single image.</p>
              </div>
            </div>

            <!-- Body -->
            <div class="mt-4 space-y-6">
              <!-- Album -->
              <div>
                <label for="album-select" class="block text-sm font-medium text-gray-900 dark:text-gray-200">Select Album</label>
                <select id="album-select"
                        class="mt-2 block w-full rounded-md bg-white px-3 py-2 text-sm text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-sky-600 dark:bg-white/10 dark:text-white dark:outline-white/10">
                  <option value="">-- None --</option>
                  <?php foreach ($albumList as $album): ?>
                    <option value="<?php echo htmlspecialchars($album['slug']); ?>">
                      <?php echo 'Album: ' . htmlspecialchars($album['title']); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <!-- Images -->
              <div>
                <label class="block text-sm font-medium text-gray-900 dark:text-gray-200 mb-2">Or select a single image</label>
                <div class="grid grid-cols-3 gap-4 max-h-64 overflow-y-auto" id="image-gallery">
                  <?php foreach ($home['available_images'] as $img): ?>
                    <button type="button"
                            class="border border-black/10 dark:border-white/10 overflow-hidden cursor-pointer hover:ring-2 hover:ring-sky-500 transition rounded"
                            data-filename="<?php echo htmlspecialchars($img); ?>">
                      <img src="/userdata/content/images/<?php echo urlencode($img); ?>" alt="" class="w-full h-24 object-cover">
                      <p class="text-[11px] text-center mt-1 truncate px-1 text-black/70 dark:text-gray-300"><?php echo htmlspecialchars($img); ?></p>
                    </button>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>

            <!-- Footer -->
            <div class="mt-6 sm:flex sm:flex-row-reverse">
              <button type="button" id="confirm-cover-selection"
                      class="inline-flex w-full justify-center rounded-md bg-sky-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-sky-500 sm:ml-3 sm:w-auto">
                Use Selection
              </button>
              <button type="button" id="cancel-cover-selection"
                      class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs inset-ring-1 inset-ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto
                             dark:bg-white/10 dark:text-white dark:shadow-none dark:inset-ring-white/5 dark:hover:bg-white/20">
                Cancel
              </button>
            </div>
          </el-dialog-panel>
        </div>
      </div>
    </el-dialog>

    <!-- Scripts -->
    <script src="js/navbar.js"></script>
    <script src="js/tailwind.js"></script>

    <!-- Deine bestehenden Logiken -->
    <script src="js/update.js"></script>
    <script src="js/select_home.js"></script>
    <script src="js/save_settings.js"></script>
    <script src="js/cover_selector.js"></script>
    <script src="js/home_select_type.js"></script>

    <!-- Modal Open/Close helpers (falls nicht schon in cover_selector.js enthalten) -->
    <script>
      (function(){
        const modal   = document.getElementById('cover-modal');
        const openBtn = document.getElementById('open-cover-modal');
        const closeX  = document.getElementById('close-cover-modal');
        const cancel  = document.getElementById('cancel-cover-selection');

        function open(){ modal?.classList.remove('hidden'); }
        function close(){ modal?.classList.add('hidden'); }

        openBtn?.addEventListener('click', open);
        closeX?.addEventListener('click', close);
        cancel?.addEventListener('click', close);

        // Outside click schließt
        modal?.addEventListener('click', (e) => {
          const panel = modal.querySelector('el-dialog-panel');
          if (panel && !panel.contains(e.target)) close();
        });

        // ESC
        document.addEventListener('keydown', (e) => {
          if (!modal?.classList.contains('hidden') && e.key === 'Escape') close();
        });
      })();
    </script>
  </body>
</html>
