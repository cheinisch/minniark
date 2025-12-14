<?php
  require_once( __DIR__ . "/../functions/function_backend.php");
  require_once '../vendor/autoload.php';
  require_once __DIR__ . '/../app/autoload.php';

  security_checklogin();

  // Projektroot ermitteln (admin/.. = root)
  $rootDir = dirname(__DIR__);

  // Aktuelle Version (aus VERSION-Datei)
  $versionFile = $rootDir . '/VERSION';
  $currentVersion = is_file($versionFile) ? trim(@file_get_contents($versionFile)) : '';

  // Neueste verfügbare Version:
  // 1) Wenn Helper existiert, verwenden (kann Cache/Live prüfen)
  // 2) Sonst nur aus temp/version.json lesen (ohne Netz)
  $latestVersion = '';
  if (function_exists('updateNewVersionNumber')) {
      $latestVersion = (string) @updateNewVersionNumber();
  } else {
      $tempJson = $rootDir . '/temp/version.json';
      if (is_file($tempJson)) {
          $data = json_decode(@file_get_contents($tempJson), true);
          if (!empty($data['new_version_number'])) {
              $latestVersion = (string) $data['new_version_number'];
          }
      }
  }

  $showReleaseNotes = ($_GET['releasenotes'] ?? '') === 'show';
  if ($showReleaseNotes)
  {
	$releasenotedialog = '';
  }else{
	$releasenotedialog = 'hidden';
  }

?>
<!doctype html>
<html lang="<?php echo get_language(); ?>">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
    	<title><?php echo languageString('nav.dashboard'); ?> - <?php echo get_sitename(); ?></title>
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
								class="inline-flex items-center justify-start mx-2 py-2 border-b-2 border-gray-800 dark:border-gray-400 rounded-none
										no-underline text-base font-normal leading-tight appearance-none">
								<?php echo languageString('nav.dashboard'); ?>
							</a>
							<a href="media.php"
								class="inline-flex items-center justify-start mx-2 py-2 border-b border-gray-800 dark:border-gray-400 rounded-none
										no-underline text-base font-normal leading-tight appearance-none">
								<?php echo languageString('nav.images'); ?>
							</a>
							<a href="blog.php"
								class="inline-flex items-center justify-start mx-2 py-2 border-b border-gray-800 dark:border-gray-400 rounded-none
										no-underline text-base font-normal leading-tight appearance-none">
								<?php echo languageString('nav.blogposts'); ?>
							</a>
							<a href="pages.php"
								class="inline-flex items-center justify-start mx-2 py-2 border-b border-gray-800 dark:border-gray-400 rounded-none
										no-underline text-base font-normal leading-tight appearance-none">
								<?php echo languageString('nav.pages'); ?>
							</a>
						</div>
					</div>
					<div class="flex items-center gap-x-4 lg:gap-x-6">
						<div class="relative" id="notif-wrap">
    						<button type="button" id="notifBtn"
            					class="-m-2.5 p-2.5 text-gray-800 hover:text-gray-500 dark:text-gray-400 dark:hover:text-gray-300"
            					aria-haspopup="menu" aria-expanded="false" aria-controls="notifMenu">
      							<span class="sr-only">View notifications</span>
      							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
           							aria-hidden="true" class="w-6 h-6 shrink-0 block">
        							<path d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"
              							stroke-linecap="round" stroke-linejoin="round" />
      								</svg>
      								<!-- Badge wird per JS dynamisch eingefügt -->
    							</button>

								<!-- Dropdown wird per JS eingefügt -->
								<div id="notifMenu" hidden></div>
							</div>
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
						class="inline-flex items-center  py-2 border-b-2 border-gray-800 dark:border-gray-400 rounded-none
								no-underline text-base font-normal leading-tight appearance-none">
						<?php echo languageString('nav.dashboard'); ?>
					</a>
					<a href="media.php"
						class="inline-flex items-center py-2 border-b border-gray-800 dark:border-gray-400 rounded-none
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
  <main class="min-h-screen py-10 text-black dark:text-white">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">

      <section class="rounded-sm border border-black/10 dark:border-white/10 bg-white dark:bg-black/40 shadow-xs">
        <header class="px-4 py-4 border-b border-black/10 dark:border-white/10">
          <h1 class="text-base font-semibold"><?php echo languageString('dashboard.update.title'); ?></h1>
          <p class="mt-1 text-xs text-black/60 dark:text-gray-400">
            <?php echo languageString('dashboard.update.description'); ?>
          </p>
        </header>

        <div class="p-4 grid gap-4 sm:grid-cols-2">
          <!-- Aktuell installiert -->
          <div class="rounded-sm border border-black/10 dark:border-white/10 bg-white/70 dark:bg-white/5 p-4">
            <div class="flex items-center justify-between">
              <h2 class="text-sm font-semibold"><?php echo languageString('dashboard.update.installed_vers'); ?></h2>
              <span class="inline-flex items-center rounded-sm px-2 py-0.5 text-[11px] font-medium bg-gray-100 text-gray-800 dark:bg-white/10 dark:text-gray-200">
                <?php echo languageString('dashboard.update.local'); ?>
              </span>
            </div>
            <p class="mt-3 text-xl font-mono tracking-tight">
              <?= $currentVersion !== '' ? htmlspecialchars($currentVersion) : '—' ?>
            </p>
            <p class="mt-1 text-xs text-black/60 dark:text-gray-400">
              <?php echo languageString('dashboard.update.local_description'); ?>
            </p>
			<p class="mt-1 text-xs text-black/60 dark:text-gray-400">
				<a href="?releasenotes=show"><?php echo languageString('dashboard.update.local_releasenotes.url'); ?></a>
			</p>
          </div>

          <!-- Verfügbare neue Version -->
          <div class="rounded-sm border border-black/10 dark:border-white/10 bg-white/70 dark:bg-white/5 p-4">
            <div class="flex items-center justify-between">
              <h2 class="text-sm font-semibold"><?php echo languageString('dashboard.update.remote_vers'); ?></h2>
              <span class="inline-flex items-center rounded-sm px-2 py-0.5 text-[11px] font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-500/15 dark:text-indigo-300">
                <?php echo languageString('dashboard.update.remote'); ?>
              </span>
            </div>
            <p class="mt-3 text-xl font-mono tracking-tight">
              <?= $latestVersion !== '' ? htmlspecialchars($latestVersion) : '—' ?>
            </p>
            <p class="mt-1 text-xs text-black/60 dark:text-gray-400">
              <?php echo languageString('dashboard.update.remote_description'); ?>
            </p>
          </div>
        </div>

		<!-- Status/Info-Zeile -->
		<div class="px-4 pb-4">
		<?php if ($currentVersion !== '' && $latestVersion !== '' && version_compare($latestVersion, $currentVersion, '>')): ?>
			<div class="mt-2 rounded-sm border border-emerald-600/30 bg-emerald-500/10 text-emerald-800 dark:text-emerald-300 px-3 py-2 text-sm flex items-center justify-between gap-3">
			<span><?php echo languageString('dashboard.update.update_available'); ?></span>
			<?= function_exists('create_update_button') ? create_update_button(languageString('dashboard.update.update_now'), languageString('dashboard.update.update_docker')) : '' ?>
			</div>
		<?php elseif ($currentVersion !== '' && $latestVersion !== '' && version_compare($latestVersion, $currentVersion, '<=')): ?>
			<div class="mt-2 rounded-sm border border-gray-400/30 bg-gray-400/10 text-gray-800 dark:text-gray-300 px-3 py-2 text-sm">
			<?php echo languageString('dashboard.update.no_update'); ?>
			</div>
		<?php else: ?>
			<div class="mt-2 rounded-sm border border-yellow-600/30 bg-yellow-500/10 text-yellow-800 dark:text-yellow-300 px-3 py-2 text-sm">
			<?php echo languageString('dashboard.update.version_error'); ?>
			</div>
		<?php endif; ?>
		</div>
      </section>

    </div>
  </main>
		</div>
		
		<!-- RELEASE NOTES MODAL -->
		<el-dialog>
			<div id="releasenotes-modal" class="<?php echo $releasenotedialog; ?> fixed inset-0 z-50" role="dialog" aria-modal="true" aria-labelledby="releasenotes-title">
				<el-dialog-backdrop class="fixed inset-0 bg-gray-500/75 dark:bg-black/60 transition-opacity data-closed:opacity-0"></el-dialog-backdrop>

				<div tabindex="0" class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
					<el-dialog-panel
						class="relative transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all
						sm:my-8 sm:w-full sm:max-w-3xl sm:p-6
						dark:bg-black dark:outline dark:-outline-offset-1 dark:outline-white/10">

						<!-- Close (X) -->
						<div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
							<button type="button"
								id="close-releasenotes-modal"
								onclick="window.location='?';"
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
									<path stroke-linecap="round" stroke-linejoin="round" d="M8 6h8M8 10h8M8 14h6M6 4h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z" />
								</svg>
							</div>

							<div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
								<h2 id="releasenotes-title" class="text-base font-semibold text-gray-900 dark:text-white">
									<?php echo languageString('dashboard.update.local_releasenotes.dialog_title'); ?> <?php echo htmlspecialchars(Releasenotes::version(), ENT_QUOTES, 'UTF-8'); ?>
								</h2>
								<p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
									<?php echo languageString('dashboard.update.local_releasenotes.dialog_text'); ?>
								</p>
							</div>
						</div>

						<!-- Body -->
						<div class="mt-4">
						<div class="rounded-sm border border-black/10 dark:border-white/10
									bg-white/70 dark:bg-white/5 p-4
									max-h-[60vh] overflow-y-auto text-left">

							<!-- Inhalt der Release Notes -->
							<div class="text-sm leading-relaxed text-black dark:text-gray-200
										whitespace-pre-wrap break-words">
							<?php echo Releasenotes::text(); ?>
							</div>

						</div>
						</div>

					</el-dialog-panel>
				</div>
			</div>
		</el-dialog>




  		<script src="js/navbar.js"></script>
		<script src="js/tailwind.js"></script>
		<script src="js/update.js"></script>
		<script src="js/notify.js"></script>
	</body>
</html>
