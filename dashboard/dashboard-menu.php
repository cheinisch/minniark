<?php

  require_once( __DIR__ . "/../functions/function_backend.php");
  $settingspage = "menu";
  security_checklogin();

  $nav_items = read_navigation();

?>

<!doctype html>
<html lang="<?php echo get_language(); ?>">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
    	<title><?php echo languageString('nav.dashboard'); ?> - <?php echo get_sitename(); ?></title>
		<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
		<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
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
								<img src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=cyan&shade=500" alt="Your Company" class="h-8 w-auto" />
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
					<img src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=cyan&shade=500" alt="Your Company" class="h-8 w-auto" />
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
			<main class="py-10 bg-white dark:bg-black">
				<div class="px-4 sm:px-6 lg:px-8 text-black dark:text-white">
					<div class="space-y-4">

					<?php
						// Sichere 1/0-Ausgabe für aria-checked + Klassen
						$navEnabledBool = is_nav_enabled();
						$navAria        = $navEnabledBool ? '1' : '0';
						$toggleBgClass  = $navEnabledBool ? 'bg-cyan-600' : 'bg-gray-400';
						$knobShiftClass = $navEnabledBool ? 'translate-x-5' : 'translate-x-0';
					?>

					<!-- Main Navigation Settings -->
					<section class="rounded-sm border border-black/10 dark:border-white/10 bg-white dark:bg-black/40 shadow-xs">
						<header class="px-4 py-3 border-b border-black/10 dark:border-white/10">
						<h2 class="text-sm font-semibold"><?php echo languageString('dashboard.menu.title'); ?></h2>
						<p class="mt-1 text-xs text-black/60 dark:text-gray-400">
							<?php echo languageString('dashboard.menu.description'); ?>
						</p>
						</header>

						<div class="px-4 py-4">
						<form class="md:col-span-2" action="backend_api/nav_change.php?save=active" method="post" id="change-map-form">
							<div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:max-w-xl sm:grid-cols-6">

							<div id="notification-map-success"
								class="hidden col-span-full rounded-sm border border-emerald-700/30 bg-emerald-500/10 text-emerald-300 px-3 py-2 text-xs"
								role="alert">
								<strong class="font-semibold"><?php echo languageString('general.success'); ?>!</strong>
								<span class="ml-1"><?php echo languageString('general.success_save'); ?></span>
							</div>
							<div id="notification-map-error"
								class="hidden col-span-full rounded-sm border border-red-700/30 bg-red-500/10 text-red-300 px-3 py-2 text-xs"
								role="alert">
								<strong class="font-semibold"><?php echo languageString('general.error'); ?>!</strong>
								<span class="ml-1"><?php echo languageString('general.error_message'); ?></span>
							</div>

							<!-- Toggle -->
							<div class="col-span-full">
								<div class="flex items-center justify-between">
								<span class="flex grow flex-col">
									<span class="text-sm font-medium" id="availability-label"><?php echo languageString('dashboard.menu.custommenu'); ?></span>
									<span class="text-xs text-black/60 dark:text-gray-400" id="availability-description"><?php echo languageString('dashboard.menu.custommenu_description'); ?></span>
								</span>

								<!-- EXAKT ein <span> im Button für JS: knob -->
								<button type="button"
										id="nav_enable"
										class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:ring-2 focus:ring-cyan-600 focus:ring-offset-2 focus:outline-hidden <?php echo $toggleBgClass; ?>"
										role="switch"
										aria-checked="<?php echo $navAria; ?>"
										aria-labelledby="availability-label"
										aria-describedby="availability-description">
									<span aria-hidden="true"
										class="pointer-events-none inline-block size-5 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out <?php echo $knobShiftClass; ?>"></span>
								</button>

								<!-- Hidden input, wird vom JS auf 1/0 gesetzt -->
								<input type="hidden" name="nav_enabled" id="nav_enabled" value="<?php echo $navAria; ?>">
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

					<!-- Custom Navigation Builder -->
					<section id="custom_nav" class="rounded-sm border border-black/10 dark:border-white/10 bg-white dark:bg-black/40 shadow-xs">
						<header class="px-4 py-3 border-b border-black/10 dark:border-white/10">
						<h2 class="text-sm font-semibold"><?php echo languageString('dashboard.menu.custommenu'); ?></h2>
						<p class="mt-1 text-xs text-black/60 dark:text-gray-400"><?php echo languageString('dashboard.menu.custommenu_config'); ?></p>
						</header>

						<div class="px-4 py-4">
						<div class="grid grid-cols-1 gap-6 md:grid-cols-2">
							<!-- Available Items -->
							<div>
							<h3 class="text-xs font-semibold mb-2"><?php echo languageString('dashboard.menu.avail_items'); ?></h3>
							<ul id="available_items"
								class="space-y-2 rounded-sm border border-dashed border-black/10 dark:border-white/10 bg-black/5 dark:bg-white/5 p-2 min-h-[150px]">
								<!-- Home -->
								<li draggable="true" data-label="Home" data-link="/home"
									class="cursor-move p-2 bg-white dark:bg-gray-950 border border-black/10 dark:border-white/10 shadow-xs text-sm">
								Home
								</li>

								<!-- Pages -->
								<?php
								$pages = get_pages();
								foreach ($pages as $page) {
									$title = htmlspecialchars($page['title']);
									$slug  = generateSlug($page['title']);
									echo '<li draggable="true" data-label="'.$title.'" data-link="/p/'.$slug.'" class="cursor-move p-2 bg-white dark:bg-gray-950 border border-black/10 dark:border-white/10 shadow-xs text-sm">Page: '.$title.'</li>';
								}
								?>

								<!-- Collections -->
								<?php
								$collections = getCollectionList();
								foreach ($collections as $collection) {
									$title = htmlspecialchars($collection['title']);
									$slug  = $collection['slug'];
									echo '<li draggable="true" data-label="'.$title.'" data-link="/collection/'.$slug.'" class="cursor-move p-2 bg-white dark:bg-gray-950 border border-black/10 dark:border-white/10 shadow-xs text-sm">Collection: '.$title.'</li>';
								}
								?>

								<!-- Albums -->
								<?php
								$albums = getAlbumList();
								foreach ($albums as $album) {
									$title = htmlspecialchars($album['title']);
									$slug  = $album['slug'];
									echo '<li draggable="true" data-label="'.$title.'" data-link="/gallery/'.$slug.'" class="cursor-move p-2 bg-white dark:bg-gray-950 border border-black/10 dark:border-white/10 shadow-xs text-sm">Album: '.$title.'</li>';
								}
								?>

								<!-- General -->
								<li draggable="true" data-label="Blog" data-link="/blog"
									class="cursor-move p-2 bg-white dark:bg-gray-950 border border-black/10 dark:border-white/10 shadow-xs text-sm">
								Blog
								</li>
								<li draggable="true" data-label="Timeline" data-link="/timeline"
									class="cursor-move p-2 bg-white dark:bg-gray-950 border border-black/10 dark:border-white/10 shadow-xs text-sm">
								Timeline
								</li>
								<li draggable="true" data-label="Map" data-link="/map"
									class="cursor-move p-2 bg-white dark:bg-gray-950 border border-black/10 dark:border-white/10 shadow-xs text-sm">
								Map
								</li>
							</ul>
							</div>

							<!-- Custom Menu -->
							<div>
							<h3 class="text-xs font-semibold mb-2"><?php echo languageString('dashboard.menucustommenu_items'); ?></h3>
							<ul id="menu_list"
								class="space-y-2 rounded-sm border border-dashed border-black/10 dark:border-white/10 bg-black/5 dark:bg-white/5 p-2 min-h-[150px]">
								<!-- wird per JS befüllt -->
							</ul>
							</div>
						</div>

						<!-- Add Custom Link -->
						<div class="mt-6">
							<h3 class="text-xs font-semibold mb-2"><?php echo languageString('dashboard.menu.custommenu_link'); ?></h3>
							<div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
							<input type="text" id="custom_label" placeholder="Label (e.g. Blog)"
									class="w-full sm:w-1/2 bg-white/5 px-3 py-1.5 text-sm outline-1 -outline-offset-1 outline-gray-500 dark:outline-white/10 placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-cyan-500" />
							<input type="text" id="custom_link" placeholder="URL (e.g. /blog)"
									class="w-full sm:w-1/2 bg-white/5 px-3 py-1.5 text-sm outline-1 -outline-offset-1 outline-gray-500 dark:outline-white/10 placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-cyan-500" />
							<button id="add_custom" type="button"
									class="text-xs px-2 py-1 rounded border border-black/10 hover:bg-black/5 dark:border-white/10 dark:hover:bg-white/10">
								<?php echo languageString('general.add'); ?>
							</button>
							</div>
						</div>

						<div class="mt-4 flex">
							<!-- unterhalb deines "Custom Navigation" Abschnitts -->
							<form id="save-menu-form" action="backend_api/nav_change.php?save=menu" method="POST" class="mt-4">
							<input type="hidden" name="menu_json" id="menu_json" value="">
							<button id="btn_nav" type="submit"
									class="text-xs px-2 py-1 rounded border border-black/10 hover:bg-black/5 dark:border-white/10 dark:hover:bg-white/10">
								<?php echo languageString('general.save'); ?>
							</button>
							</form>
						</div>
						</div>
					</section>

					</div>
				</div>
			</main>



		</div>
		<script>
            window.existingNav = <?php echo json_encode($nav_items, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>;
        </script>
		<!-- <script src="js/navbar.js"></script> -->
		<script src="js/tailwind.js"></script>
		<script src="js/update.js"></script>
		
        <script src="js/custom_nav.js"></script>  

	</body>
</html>
