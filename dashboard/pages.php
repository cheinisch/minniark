<?php

  require_once( __DIR__ . "/../functions/function_backend.php");
  security_checklogin();

  // Prüfen, ob ein bestimmtes Jahr übergeben wurde
  $filterYear = isset($_GET['year']) ? $_GET['year'] : null;
  $filterRating = isset($_GET['rating']) ? $_GET['rating'] : null;
  $filterTag = isset($_GET['tag']) ? $_GET['tag'] : null;
  $filterCountry = isset($_GET['country']) ? $_GET['country'] : null;

  $sort = isset($_GET['sort']) ? $_GET['sort'] : null;
  $direction = isset($_GET['dir']) ? $_GET['dir'] : null;
?>

<!doctype html>
<html lang="<?php echo get_language(); ?>">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pages - <?php echo get_sitename(); ?></title>
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
								<?php include (__DIR__.'/layout/pages_menu.php'); ?>
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
					<?php include (__DIR__.'/layout/pages_menu.php'); ?>
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
								class="inline-flex items-center justify-start mx-4 py-2 border-b border-gray-800 dark:border-gray-400 rounded-none
										no-underline text-base font-normal leading-tight appearance-none">
								<?php echo languageString('nav.blogposts'); ?>
							</a>
							<a href="pages.php"
								class="inline-flex items-center justify-start mx-4 py-2 border-b-2 border-gray-800 dark:border-gray-400 rounded-none
										no-underline text-base font-normal leading-tight appearance-none">
								<?php echo languageString('nav.pages'); ?>
							</a>
						</div>
					</div>
					<div class="flex items-center gap-x-4 lg:gap-x-6">
						<a href="page-detail.php?post=new"
						id="newPageBtn"
						class="inline-flex items-center gap-2 -m-2.5 p-2.5 text-gray-800 hover:text-gray-500 dark:text-gray-400 dark:hover:text-gray-300">
							<?php echo languageString('page.new_page'); ?>
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-upload" viewBox="0 0 16 16">
								<path d="M8 6.5a.5.5 0 0 1 .5.5v1.5H10a.5.5 0 0 1 0 1H8.5V11a.5.5 0 0 1-1 0V9.5H6a.5.5 0 0 1 0-1h1.5V7a.5.5 0 0 1 .5-.5"/>
  								<path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5z"/>
							</svg>
							<span class="sr-only">New Page</span>
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
				<div class="px-4 sm:px-6 lg:px-8">
					<!-- Your content -->
					<!-- Blogliste – Admin Dashboard -->
					<section class="rounded-sm border border-black/10 dark:border-white/10 bg-white dark:bg-black/40 shadow-xs">

					<div class="divide-y divide-black/10 dark:divide-white/10">
						<!-- Eintrag 1 -->
						<?php
						$pages = get_pages($filterYear);
						foreach($pages as $page)
						{
							if($page['cover'] == "" || $page['cover'] == null)
							{
								$page['cover'] = "img/placeholder.png";
							}else{
								$page['cover'] = get_cached_image_dashboard($page['cover'], 'M');
							}
										
						?>
						<article class="p-4 flex gap-4">
						<img src="<?php echo $page['cover']; ?>" alt="" class="size-24 object-cover rounded border border-black/10 dark:border-white/10 hidden sm:block" />
						<div class="flex-1 min-w-0">
							<div class="flex items-start justify-between gap-4">
							<a href="page-detail.php?edit=<?php echo $page['slug']; ?>" class="text-base font-semibold text-black hover:underline dark:text-white">
								<?php echo $page['title']; ?>
							</a>
							<span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-500/10 dark:text-emerald-300">
								Published
							</span>
							</div>
							<div class="mt-1 text-xs text-black/60 dark:text-gray-400">
							<span>by <strong>alex</strong></span>
							<span class="mx-2">•</span>
							<time datetime="2025-06-12">12 Jun 2025</time>
							<span class="mx-2">•</span>
							<span>5 min read</span>
							</div>

							<p class="mt-2 line-clamp-2 text-sm text-black/80 dark:text-gray-300">
							<?php echo $page['content']; ?>
							</p>
						</div>
						<div class="shrink-0 flex flex-col gap-2">
							<a href="<a href="page-detail.php?edit=<?php echo $page['slug']; ?>"
							class="text-xs text-black dark:text-white px-2 py-1 rounded border border-black/10 hover:bg-black/5 dark:border-white/10 dark:hover:bg-white/10">Edit</a>
							<a href="backend_api/delete_post.php?id=1"
							class="text-xs px-2 py-1 rounded bg-red-600 text-white hover:bg-red-500 dark:bg-red-500 dark:hover:bg-red-400">Delete</a>
						</div>
						</article>
						<?php } ?>
					</div>
					</section>

				</div>
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
