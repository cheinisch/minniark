<?php

  require_once( __DIR__ . "/../functions/function_backend.php");
  $settingspage = "export";
  security_checklogin();

?>

<!DOCTYPE html>
<html lang="<?php echo get_language(); ?>">
    <head>      
        <meta charset="UTF-8">        
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard - <?php echo get_sitename(); ?></title>

        <!-- Tailwind CSS -->
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    </head>
    <body class="min-h-screen flex flex-col">
      <div id="backup-modal" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center">
        <div class="relative bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-md w-full mx-4">
          
          <!-- Schließen-X -->
          <button onclick="closeBackupModal()" class="absolute top-3 right-3 text-gray-500 hover:text-gray-800 dark:hover:text-white text-xl font-bold leading-none">
            &times;
          </button>

          <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Backup erstellt</h2>
          <p class="text-gray-600 dark:text-gray-300 mb-4">Dein Backup wurde erfolgreich erstellt. Du kannst es hier herunterladen:</p>
          
          <a id="backup-download-link" href="#" class="text-sky-600 hover:underline break-all" download></a>
        </div>
      </div>

        <header>
            <nav class="bg-neutral-100 dark:bg-gray-950 shadow-sm">
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
                      <div class="shrink-0">
                        <button type="button" class="relative inline-flex items-center gap-x-1.5  bg-sky-400 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-sky-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600">
                          <svg class="-ml-0.5 size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                          <path d="M12.5535 16.5061C12.4114 16.6615 12.2106 16.75 12 16.75C11.7894 16.75 11.5886 16.6615 11.4465 16.5061L7.44648 12.1311C7.16698 11.8254 7.18822 11.351 7.49392 11.0715C7.79963 10.792 8.27402 10.8132 8.55352 11.1189L11.25 14.0682V3C11.25 2.58579 11.5858 2.25 12 2.25C12.4142 2.25 12.75 2.58579 12.75 3V14.0682L15.4465 11.1189C15.726 10.8132 16.2004 10.792 16.5061 11.0715C16.8118 11.351 16.833 11.8254 16.5535 12.1311L12.5535 16.5061Z"/> 
                          <path d="M3.75 15C3.75 14.5858 3.41422 14.25 3 14.25C2.58579 14.25 2.25 14.5858 2.25 15V15.0549C2.24998 16.4225 2.24996 17.5248 2.36652 18.3918C2.48754 19.2919 2.74643 20.0497 3.34835 20.6516C3.95027 21.2536 4.70814 21.5125 5.60825 21.6335C6.47522 21.75 7.57754 21.75 8.94513 21.75H15.0549C16.4225 21.75 17.5248 21.75 18.3918 21.6335C19.2919 21.5125 20.0497 21.2536 20.6517 20.6516C21.2536 20.0497 21.5125 19.2919 21.6335 18.3918C21.75 17.5248 21.75 16.4225 21.75 15.0549V15C21.75 14.5858 21.4142 14.25 21 14.25C20.5858 14.25 20.25 14.5858 20.25 15C20.25 16.4354 20.2484 17.4365 20.1469 18.1919C20.0482 18.9257 19.8678 19.3142 19.591 19.591C19.3142 19.8678 18.9257 20.0482 18.1919 20.1469C17.4365 20.2484 16.4354 20.25 15 20.25H9C7.56459 20.25 6.56347 20.2484 5.80812 20.1469C5.07435 20.0482 4.68577 19.8678 4.40901 19.591C4.13225 19.3142 3.9518 18.9257 3.85315 18.1919C3.75159 17.4365 3.75 16.4354 3.75 15Z"/>
                          </svg>
                          Update available
                        </button>
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
                              <img class="size-8 rounded-full" src="<?php echo get_userimage(); ?>" alt="">
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
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-0">Your Profile</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-1">Settings</a>
                            <a href="login/logout.php" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-2">Sign out</a>
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
                        <a href="dashboard-system.php" class="block px-4 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6">System Settings</a>                        
                        <a href="dashboard-theme.php" class="block px-4 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6">Theme Settings</a>
                        <a href="dashboard-export_import.php" class="block px-4 text-base font-medium text-sky-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6">Export / Import</a>
                      </div>
                    </div>
                  </div>
                  <div class="border-t border-gray-200 pt-4 pb-3">
                    <div class="flex items-center px-4 sm:px-6">
                      <div class="shrink-0">
                        <img class="size-10 rounded-full" src="<?php echo get_userimage(); ?>" alt="">
                      </div>
                      <div class="ml-3">
                        <div class="text-base font-medium text-gray-300"><?php echo get_username(); ?></div>
                        <div class="text-sm font-medium text-gray-500"><?php echo get_usermail(); ?></div>
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
                      <a href="login/logout.php" class="block px-4 py-2 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6">Sign out</a>
                    </div>
                  </div>
                </div>
              </nav>
              
        </header>
        <div class="flex flex-1">
          <aside class="hidden md:block max-w-[250px] w-full bg-neutral-100 dark:bg-gray-950 overflow-auto flex-1">
              <?php include('inc/dashboard-sidenav.php'); ?>
          </aside>
          <main class="flex-1 bg-white dark:bg-neutral-900 p-6 overflow-auto">
            <!-- Settings forms -->
            <div class="divide-y divide-gray-400 dark:divide-white/5">
              <div class="grid max-w-7xl grid-cols-1 gap-x-8 gap-y-10 px-4 py-16 sm:px-6 md:grid-cols-3 lg:px-8">
                <div>
                  <h2 class="text-base/7 font-semibold text-gray-700 dark:text-white">Export Data</h2>
                  <p class="mt-1 text-sm/6 text-gray-400">Export your data as a zip file</p>
                </div>

                <div class="md:col-span-2" id="exportform">
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

                    <div class="col-span-4">
                      <label for="display-prefix" class="block text-sm/6 font-medium text-gray-700 dark:text-white">Prefix</label>
                      <div class="mt-2 flex gap-2">
                        <input type="text" name="display-prefix" id="display-prefix" value="<?php echo read_prefix(); ?>" class="block w-full  bg-white/5 px-3 py-1.5 text-base text-gray-700 dark:text-white outline-1 -outline-offset-1 outline-gray-500 dark:outline-white/10 placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-sky-500 sm:text-sm/6">
                        <button id="backup-pre-btn" class=" bg-sky-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-sky-400 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-500">Save Prefix</button>
                      </div>
                    </div>
                  </div>                 

                  <div class="mt-8 flex">
                    <button id="backup-btn" class=" bg-sky-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-sky-400 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-500">Generate Backup</button>
                  </div>
                </div>
              </div>

              <div class="grid max-w-7xl grid-cols-1 gap-x-8 gap-y-10 px-4 py-16 sm:px-6 md:grid-cols-3 lg:px-8">
                <div>
                  <h2 class="text-base/7 font-semibold text-gray-700 dark:text-white">Import data</h2>
                  <p class="mt-1 text-sm/6 text-gray-400">Import your backup. Please note, if your backup is larger than <?php echo get_uploadsize(); ?>, upload it using your FTP access.</p>
                </div>

                <form class="md:col-span-2" id="upload-backup-form">
                  <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:max-w-xl sm:grid-cols-6">
                    
                    <!-- Erfolgsmeldung -->
                    <div id="notification-success-upload" class="hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 col-span-full relative mb-4" role="alert">
                      <strong class="font-bold">Erfolg!</strong>
                      <span class="block sm:inline">Backup wurde erfolgreich hochgeladen.</span>
                    </div>

                    <!-- Fehlermeldung -->
                    <div id="notification-error-upload" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 col-span-full relative mb-4" role="alert">
                      <strong class="font-bold">Fehler!</strong>
                      <span class="block sm:inline">Beim Upload ist ein Fehler aufgetreten.</span>
                    </div>

                    <!-- Datei auswählen -->
                    <div class="col-span-full">
                      <label for="backup-file" class="block text-sm/6 font-medium text-white">Backup-Datei (ZIP)</label>
                      <div class="mt-2">
                        <input id="backup-file" name="backup_file" type="file" accept=".zip" required
                          class="block w-full text-white file:bg-sky-500 file:text-white file:border-none file:px-4 file:py-2 file:rounded file:cursor-pointer bg-white/5 px-3 py-1.5 text-base outline-1 outline-white/10 placeholder:text-gray-500 focus:outline-2 focus:outline-sky-500 sm:text-sm/6">
                      </div>
                    </div>
                  </div>

                  <div class="mt-8 flex">
                    <button type="submit"
                      class="bg-sky-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-sky-400 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-500">
                      Upload Backup
                    </button>
                  </div>
                </form>
              </div>
              <div class="grid max-w-7xl grid-cols-1 gap-x-8 gap-y-10 px-4 py-16 sm:px-6 md:grid-cols-3 lg:px-8">
                <div>
                  <h2 class="text-base/7 font-semibold text-gray-700 dark:text-white">Backup Files</h2>
                  <p class="mt-1 text-sm/6 text-gray-400">Download your backupfiles.</p>
                </div>

                <div class="md:col-span-2" id="change-password-form">
                  <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:max-w-xl sm:grid-cols-6">
                    <!-- Erfolgsmeldung (grün) -->
                    <div id="notification-success" class="hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 col-span-full relative mb-4" role="alert">
                      <strong class="font-bold">Erfolg!</strong>
                      <span class="block sm:inline">Dein Passwort wurde erfolgreich geändert.</span>
                    </div>

                    <!-- Fehlermeldung (rot) -->
                    <div id="notification-error" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 col-span-full relative mb-4" role="alert">
                      <strong class="font-bold">Fehler!</strong>
                      <span class="block sm:inline">Das aktuelle Passwort ist falsch.</span>
                    </div>
                    <table class="col-span-full text-center text-gray-700 dark:text-gray-400">
                      <thead>
                        <tr>
                          <th>Date</th>
                          <th>File</th>
                          <th>Filesize</th>
                          <th></th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php 
                          $backupfiles = read_backupfiles(); // richtig: direkt zuweisen, nicht mit []

                          foreach ($backupfiles as $file) {
                              echo '
                              <tr class="border-b border-gray-200 dark:border-gray-700">
                                  <td class="py-2">' . date('Y-m-d H:i', $file['timestamp']) . '</td>
                                  <td class="py-2"><a href="../backup/' . htmlspecialchars($file['name']) . '" class="hover:text-sky-600">' . htmlspecialchars($file['name']) . '</a></td>
                                  <td></td>
                                  <td class="py-2">
                                    <button
                                      class="text-sky-600 hover:text-sky-800 restore-backup-btn"
                                      data-filename="'.htmlspecialchars($file['name']).'"
                                      onclick="return confirm(\'Dieses Backup wirklich wiederherstellen?\');"
                                    >
                                      Restore
                                    </button>
                                  </td>
                                  <td class="py-2">
                                    <button
                                      class="text-red-600 hover:text-red-800 delete-backup-btn"
                                      data-filename="'.htmlspecialchars($file['name']).'"
                                      onclick="return confirm(\'Datei wirklich löschen?\');"
                                    >
                                      Delete
                                    </button>
                                  </td>
                              </tr>';
                          }
                        ?>
                      </tbody>
                    </table>
                  </div>

                </div>
              </div>
            </div>
          </main>
        </div>
        <script>
        function closeBackupModal() {
          document.getElementById('backup-modal').classList.add('hidden');
        }
        </script>
        <script src="js/tailwind.js"></script>
        <script src="js/backup.js"></script>
        <script src="js/backup_upload.js"></script>
        <script src="js/backup_delete.js"></script>
        <script src="js/backup_restore.js"></script>
    </body>
</html>