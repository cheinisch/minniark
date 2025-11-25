<?php
  require_once(__DIR__ . "/../functions/function_backend.php");
  $settingspage = "export";
  security_checklogin();

  $backup_guid = read_prefix();

  $success = isset($_GET['success']);
  $restore_success = (isset($_GET['restore']) && $_GET['restore'] === 'success');
  $restore_error = (isset($_GET['restore']) && $_GET['restore'] !== 'success');
?>
<!doctype html>
<html lang="<?php echo get_language(); ?>">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo languageString('nav.dashboard'); ?> - <?php echo get_sitename(); ?></title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  </head>
  <body class="bg-white dark:bg-black text-black dark:text-white">
<!--==================== Modals (neuer Stil via el-dialog) ====================-->
    <el-dialog>
      <!-- Backup success -->
      <dialog id="backupSuccess" <?php if($success) echo 'open'; ?> class="backdrop:bg-transparent">
        <el-dialog-backdrop class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/60 transition-opacity data-closed:opacity-0"></el-dialog-backdrop>
        <div tabindex="0" class="fixed inset-0 flex items-end justify-center p-4 text-center sm:items-center sm:p-0">
          <el-dialog-panel class="relative transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all
                               sm:my-8 sm:w-full sm:max-w-md sm:p-6 dark:bg-black dark:outline dark:-outline-offset-1 dark:outline-white/10">
            <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
              <button type="button" command="close" commandfor="backupSuccess" class="rounded-md bg-white text-gray-400 hover:text-gray-500 dark:bg-black">
                <span class="sr-only"><?php echo languageString('general.close'); ?></span>
                <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </button>
            </div>
            <div class="sm:flex sm:items-start">
              <div class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-emerald-100 sm:mx-0 sm:size-10 dark:bg-emerald-500/10">
                <svg class="size-6 text-emerald-600 dark:text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4.5 12.75l6 6 9-13.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </div>
              <div class="mt-3 text-left sm:mt-0 sm:ml-4">
                <h2 class="text-base font-semibold text-gray-600 dark:text-gray-400"><?php echo languageString('dashboard.export.popup.success_title'); ?></h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400"><?php echo languageString('dashboard.export.popup.success_description'); ?></p>
              </div>
            </div>
            <div class="mt-6 sm:flex sm:flex-row-reverse">
              <a href="?" class="inline-flex w-full justify-center rounded-md bg-sky-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-sky-500 sm:ml-3 sm:w-auto"><?php echo languageString('general.ok'); ?></a>
              <button type="button" command="close" commandfor="backupSuccess" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 inset-ring-1 inset-ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto dark:bg-white/10 dark:text-white dark:inset-ring-white/5 dark:hover:bg-white/20"><?php echo languageString('general.close'); ?></button>
            </div>
          </el-dialog-panel>
        </div>
      </dialog>

      <!-- Restore success -->
      <dialog id="restoreSuccess" <?php if($restore_success) echo 'open'; ?> class="backdrop:bg-transparent">
        <el-dialog-backdrop class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/60 transition-opacity data-closed:opacity-0"></el-dialog-backdrop>
        <div tabindex="0" class="fixed inset-0 flex items-end justify-center p-4 text-center sm:items-center sm:p-0">
          <el-dialog-panel class="relative transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all
                               sm:my-8 sm:w-full sm:max-w-md sm:p-6 dark:bg-black dark:outline dark:-outline-offset-1 dark:outline-white/10">
            <div class="sm:flex sm:items-start">
              <div class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-emerald-100 sm:mx-0 sm:size-10 dark:bg-emerald-500/10">
                <svg class="size-6 text-emerald-600 dark:text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4.5 12.75l6 6 9-13.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </div>
              <div class="mt-3 text-left sm:mt-0 sm:ml-4">
                <h2 class="text-base font-semibold text-gray-600 dark:text-gray-400"><?php echo languageString('dashboard.export.popup.restore_title'); ?></h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400"><?php echo languageString('dashboard.export.popup.restore_description'); ?></p>
              </div>
            </div>
            <div class="mt-6 sm:flex sm:flex-row-reverse">
              <a href="?" class="inline-flex w-full justify-center rounded-md bg-sky-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-sky-500 sm:ml-3 sm:w-auto"><?php echo languageString('general.ok'); ?></a>
              <button type="button" command="close" commandfor="restoreSuccess" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 inset-ring-1 inset-ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto dark:bg-white/10 dark:text-white dark:inset-ring-white/5 dark:hover:bg-white/20"><?php echo languageString('general.close'); ?></button>
            </div>
          </el-dialog-panel>
        </div>
      </dialog>

      <!-- Restore error -->
      <dialog id="restoreError" <?php if($restore_error) echo 'open'; ?> class="backdrop:bg-transparent">
        <el-dialog-backdrop class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/60 transition-opacity data-closed:opacity-0"></el-dialog-backdrop>
        <div tabindex="0" class="fixed inset-0 flex items-end justify-center p-4 text-center sm:items-center sm:p-0">
          <el-dialog-panel class="relative transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all
                               sm:my-8 sm:w-full sm:max-w-md sm:p-6 dark:bg-black dark:outline dark:-outline-offset-1 dark:outline-white/10">
            <div class="sm:flex sm:items-start">
              <div class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:size-10 dark:bg-red-500/10">
                <svg class="size-6 text-red-600 dark:text-red-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 9v4m0 4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </div>
              <div class="mt-3 text-left sm:mt-0 sm:ml-4">
                <h2 class="text-base font-semibold text-gray-600 dark:text-gray-400"><?php echo languageString('dashboard.export.popup.restore_error'); ?></h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400"><?php echo languageString('dashboard.export.popup.restore_error_description'); ?></p>
              </div>
            </div>
            <div class="mt-6 sm:flex sm:flex-row-reverse">
              <a href="?" class="inline-flex w-full justify-center rounded-md bg-sky-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-sky-500 sm:ml-3 sm:w-auto"><?php echo languageString('general.ok'); ?></a>
              <button type="button" command="close" commandfor="restoreError" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 inset-ring-1 inset-ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto dark:bg:white/10 dark:text-white dark:inset-ring-white/5 dark:hover:bg-white/20"><?php echo languageString('general.close'); ?></button>
            </div>
          </el-dialog-panel>
        </div>
      </dialog>
    </el-dialog>

    <!--================SIDEBAR / NAV ===============================-->

		<el-dialog>
			<dialog id="sidebar" class="backdrop:bg-transparent lg:hidden">
				<el-dialog-backdrop class="fixed inset-0 bg-white/80 dark:bg-black/80 transition-opacity duration-300 ease-linear data-closed:opacity-0"></el-dialog-backdrop>
				<div tabindex="0" class="fixed inset-0 flex focus:outline-none">
					<el-dialog-panel class="group/dialog-panel relative mr-16 flex w-full max-w-xs flex-1 transform transition duration-300 ease-in-out data-closed:-translate-x-full">
						<div class="absolute top-0 left-full flex w-16 justify-center pt-5 duration-300 ease-in-out group-data-closed/dialog-panel:opacity-0">
							<button type="button" command="close" commandfor="sidebar" class="-m-2.5 p-2.5">
								<span class="sr-only"><?php echo languageString('general.close'); ?> sidebar</span>
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

      <!--==================== Main ====================-->
      <main class="py-10 bg-white dark:bg-black">
        <div class="px-4 sm:px-6 lg:px-8">
          <!-- Export -->
          <section class="rounded-sm border border-black/10 dark:border-white/10 bg-white dark:bg-black/40 shadow-xs mb-6">
            <header class="px-4 py-3 border-b border-black/10 dark:border-white/10">
              <h2 class="text-sm font-semibold"><?php echo languageString('dashboard.export.export'); ?></h2>
              <p class="text-xs text-black/60 dark:text-gray-400"><?php echo languageString('dashboard.export.export_description'); ?></p>
            </header>
            <div class="p-4">
              <a href="backend_api/backup.php"
                 id="backup-btn-new"
                 class="inline-flex items-center gap-2 bg-sky-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-sky-500">
                <?php echo languageString('dashboard.export.generate_backup'); ?>
              </a>
            </div>
          </section>

          <!-- Import -->
          <section class="rounded-sm border border-black/10 dark:border-white/10 bg-white dark:bg-black/40 shadow-xs mb-6">
            <header class="px-4 py-3 border-b border-black/10 dark:border-white/10">
              <h2 class="text-sm font-semibold"><?php echo languageString('dashboard.export.import'); ?></h2>
              <p class="text-xs text-black/60 dark:text-gray-400">
                If your backup is larger than <?php echo get_uploadsize(); ?>, please upload it via FTP.
              </p>
            </header>
            <div class="p-4">
              <!-- Alerts -->
              <div id="notification-success-upload" class="hidden rounded-sm border border-emerald-500/30 bg-emerald-500/10 px-3 py-2 text-sm text-emerald-700 mb-3">
                <strong class="font-semibold">Success:</strong> Backup uploaded.
              </div>
              <div id="notification-error-upload" class="hidden rounded-sm border border-red-500/30 bg-red-500/10 px-3 py-2 text-sm text-red-700 mb-3">
                <strong class="font-semibold">Error:</strong> Upload failed.
              </div>

              <form id="upload-backup-form" class="space-y-3">
                <div>
                  <label for="backup-file" class="block text-sm font-medium"><?php echo languageString('dashboard.export.import_file'); ?></label>
                  <input id="backup-file" name="backup_file" type="file" accept=".zip" required
                         class="mt-2 block w-full text-black dark:text-white file:bg-sky-600 file:text-white file:border-none file:px-4 file:py-2 file:rounded file:cursor-pointer bg-white/5 px-3 py-2 text-sm outline -outline-offset-1 outline-black/10 focus:outline-2 focus:outline-sky-600 dark:outline-white/10">
                </div>
                <button type="submit"
                        class="bg-sky-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-sky-500">
                  <?php echo languageString('dashboard.export.import_upload'); ?>
                </button>
              </form>
            </div>
          </section>

          <!-- Backup Files -->
          <section class="rounded-sm border border-black/10 dark:border-white/10 bg-white dark:bg-black/40 shadow-xs">
            <header class="px-4 py-3 border-b border-black/10 dark:border-white/10">
              <h2 class="text-sm font-semibold"><?php echo languageString('dashboard.export.file'); ?></h2>
              <p class="text-xs text-black/60 dark:text-gray-400"><?php echo languageString('dashboard.export.file_description'); ?></p>
            </header>
            <div class="p-4 overflow-x-auto">
              <table class="w-full text-sm">
                <thead class="text-left text-black/60 dark:text-gray-400">
                  <tr class="border-b border-black/10 dark:border-white/10">
                    <th class="py-2 pr-4"><?php echo languageString('dashboard.export.file_date'); ?></th>
                    <th class="py-2 pr-4"><?php echo languageString('dashboard.export.file_name'); ?></th>
                    <th class="py-2 pr-4"><?php echo languageString('dashboard.export.file_size'); ?></th>
                    <th class="py-2 pr-4"></th>
                    <th class="py-2"></th>
                  </tr>
                </thead>
                <tbody class="text-black/80 dark:text-gray-300 divide-y divide-black/10 dark:divide-white/10">
                  <?php
                    $backupfiles = read_backupfiles();
                    foreach ($backupfiles as $file) {
                      $filesize = isset($file['size']) ? $file['size'] : '';
                      echo '
                      <tr>
                        <td class="py-2 pr-4">'.date('Y-m-d H:i', $file['timestamp']).'</td>
                        <td class="py-2 pr-4">
                          <a class="hover:underline" href="../backup/'.htmlspecialchars($file['name']).'">'
                            .htmlspecialchars($file['name']).'
                          </a>
                        </td>
                        <td class="py-2 pr-4">'.$filesize.'</td>
                        <td class="py-2 pr-4">
                          <a href="backend_api/restore.php?filename='.htmlspecialchars($file['name']).'"
                             class="text-sky-600 hover:text-sky-800 restore-backup-btn"
                             data-filename="'.htmlspecialchars($file['name']).'"
                             onclick="return confirm(\'Do you really want to restore this backup?\');">
                            Restore
                          </a>
                        </td>
                        <td class="py-2">
                          <button class="text-red-600 hover:text-red-800 delete-backup-btn"
                                  data-filename="'.htmlspecialchars($file['name']).'"
                                  onclick="return confirm(\'Delete this file?\');">
                            Delete
                          </button>
                        </td>
                      </tr>';
                    }
                  ?>
                </tbody>
              </table>
            </div>
          </section>
        </div>
      </main>
    </div>

    

    <script src="js/navbar.js"></script>
    <script src="js/tailwind.js"></script>
    <script src="js/update.js"></script>
    <script>
      // AJAX Upload (optional – falls du schon upload-backup-form via JS behandelst, kannst du das hier ersetzen/entfernen)
      document.getElementById('upload-backup-form')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const form = e.currentTarget;
        const file = document.getElementById('backup-file')?.files?.[0];
        if (!file) return;

        const fd = new FormData();
        fd.append('backup_file', file);

        try {
          const res = await fetch('backend_api/upload_backup.php', { method: 'POST', body: fd });
          const json = await res.json();
          const ok = json?.success;
          document.getElementById('notification-success-upload')?.classList.toggle('hidden', !ok);
          document.getElementById('notification-error-upload')?.classList.toggle('hidden', !!ok);
          if (ok) setTimeout(() => location.reload(), 800);
        } catch (err) {
          document.getElementById('notification-success-upload')?.classList.add('hidden');
          document.getElementById('notification-error-upload')?.classList.remove('hidden');
          console.error(err);
        }
      });
    </script>
  </body>
</html>
