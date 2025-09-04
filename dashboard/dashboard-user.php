<?php

  require_once( __DIR__ . "/../functions/function_backend.php");
  $settingspage = "user";
  security_checklogin();
  onlyAdmin();


  $usernameEdit = $_GET['edit'] ?? null;
  $usernameDelete = $_GET['delete'] ?? null;
  $wrong_user = false;
  $delete_modal = false;


  if($usernameEdit != null)
  {
    $userdata = getUserDataFromUsername($usernameEdit);
    $username = $userdata['username'];
    $mail = $userdata['mail'];
    $role = $userdata['userrole'];
  }

  if(isset($_GET['delete']))
  {
    if($usernameDelete == $_SESSION['username'])
    {
      $wrong_user = true;
    }else{
      $delete_modal = true;
    }
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
      <!-- delete Modal -->
       <?php

        if($delete_modal)
        {
          ?>
    <div id="deleteModal" class="relative z-50 " role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500/75 transition-opacity" aria-hidden="true"></div>
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto">
          <div class="relative w-full max-w-xl mx-auto shadow-lg bg-white p-6">
            <h2 class="text-xl font-semibold text-gray-800">Delete Confirmation</h2>
            <p class="mt-4 text-gray-600">Do you really want to delete the user: <?php echo $_GET['delete']; ?></p>
            <div class="flex justify-end mt-6 space-x-3">
              <a href="?" id="cancelDelete" class="px-4 py-2 bg-sky-500 text-white hover:bg-sky-600">Cancel</a>
              <a href="backend_api/user_edit_user.php?delete=<?php echo $_GET['delete'] ?>"  id="confirmDelete" class="px-4 py-2 bg-red-500 text-white hover:bg-red-600">Delete</a>
            </div>
          </div>
        </div>
      </div>
      <!-- current user delete Modal -->
       <?php
        }

        if($wrong_user)
        {
       ?>
    <div id="deleteModal" class="relative z-50 " role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500/75 transition-opacity" aria-hidden="true"></div>
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto">
          <div class="relative w-full max-w-xl mx-auto shadow-lg bg-white p-6">
            <h2 class="text-xl font-semibold text-gray-800">Delete Error</h2>
            <p class="mt-4 text-gray-600">You can't delete your own user</p>
            <div class="flex justify-end mt-6 space-x-3">
              <a href="?" class="px-4 py-2 bg-sky-600 text-white hover:bg-sky-500">Okay</a>
            </div>
          </div>
        </div>
      </div>
      <?php
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
                          <div class="absolute right-0 z-10 mt-2 w-48 origin-top-right  bg-white py-1 shadow-lg ring-1 ring-black/5 focus:outline-hidden hidden" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
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
                        <?php include('inc/dashboard-mainnav.php'); ?>
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
              <div class="grid max-w-7xl grid-cols-1 gap-x-8 gap-y-10 px-4 py-16 sm:px-6 md:grid-cols-4 lg:px-8">
                <div>
                  <h2 class="text-base/7 font-semibold text-gray-900 dark:text-white">Account Information</h2>
                  <p class="mt-1 text-sm/6 text-gray-400">Show all user accounts.</p>
                </div>
                <div class="md:col-span-3">
                  <!-- Edit Form-->
                   <?php

                    if (isset($_GET['edit']))
                    {
                  ?>
                  <form class="flex flex-wrap items-center gap-4" action="backend_api/user_edit_user.php?edit" method="post">
                    <div class="flex flex-col sm:flex-row sm:space-x-4 space-y-4 sm:space-y-0">
                      <div class="flex flex-col">
                        <label for="username" class="mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">Username</label>
                        <input
                          type="text"
                          id="username"
                          name="username"
                          placeholder="Username"
                          value="<?php echo $username; ?>"
                          class="px-4 py-2 border border-gray-300 dark:text-white"
                        />
                      <input
                          type="hidden"
                          id="username_old"
                          name="username_old"
                          value="<?php echo $username; ?>"
                        />
                      </div>

                      <div class="flex flex-col">
                        <label for="mail" class="mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">E-Mail</label>
                        <input
                          type="email"
                          id="mail"
                          name="mail"
                          value="<?php echo $mail; ?>"
                          placeholder="E-Mail"
                          class="px-4 py-2 border border-gray-300 dark:text-white"
                        />
                      </div>

                      <div class="flex flex-col">
                        <label for="password" class="mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                        <input
                          type="password"
                          id="password"
                          name="password"
                          placeholder="leave blank for no change"
                          class="px-4 py-2 border border-gray-300 dark:text-white"
                        />
                      </div>

                      <div class="flex flex-col">
                        <label for="userrole" class="mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">User Role</label>
                        <select
                          id="userrole"
                          name="userrole"
                          class="px-4 py-2 border border-gray-300 bg-white text-gray-700"
                        >
                          <option value="user" <?php if($role == 'user'){ echo "selected"; } ?>>User</option>
                          <option value="admin" <?php if($role == 'admin'){ echo "selected"; } ?>>Admin</option>
                        </select>
                      </div>

                      <div class="flex flex-col justify-end">
                        <button
                          type="submit"
                          class="px-4 py-2 bg-sky-600 text-white hover:bg-sky-500"
                        >
                          Save
                        </button>
                      </div>
                    </div>

                  </form>
                  <?php

                    }
                  ?>
                  <!-- New Form-->
                   <?php

                    if (isset($_GET['new']))
                    {
                  ?>
                  <form class="flex flex-wrap items-center gap-4" action="backend_api/user_edit_user.php?new" method="post">
                    <div class="flex flex-col sm:flex-row sm:space-x-4 space-y-4 sm:space-y-0">
                      <div class="flex flex-col">
                        <label for="username" class="mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">Username</label>
                        <input
                          type="text"
                          id="username"
                          name="username"
                          placeholder="Username"
                          class="px-4 py-2 border border-gray-300 dark:text-white"
                          required
                        />
                      </div>

                      <div class="flex flex-col">
                        <label for="mail" class="mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">E-Mail</label>
                        <input
                          type="email"
                          id="mail"
                          name="mail"
                          placeholder="E-Mail"
                          class="px-4 py-2 border border-gray-300 dark:text-white"
                          required
                        />
                      </div>

                      <div class="flex flex-col">
                        <label for="password" class="mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                        <input
                          type="password"
                          id="password"
                          name="password"
                          class="px-4 py-2 border border-gray-300 dark:text-white"
                          required
                        />
                      </div>

                      <div class="flex flex-col">
                        <label for="userrole" class="mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">User Role</label>
                        <select
                          id="userrole"
                          name="userrole"
                          class="px-4 py-2 border border-gray-300 bg-white text-gray-700"
                        >
                          <option value="user" selected>User</option>
                          <option value="admin">Admin</option>
                        </select>
                      </div>

                      <div class="flex flex-col justify-end">
                        <button
                          type="submit"
                          class="px-4 py-2 bg-sky-600 text-white hover:bg-sky-500"
                        >
                          Save
                        </button>
                      </div>
                    </div>

                  </form>
                  <?php

                    }
                  ?>
                  <table class="table-auto w-full text-gray-900 dark:text-white">
                    <thead>    
                      <tr class="border-b">      
                        <th class="py-2">Username</th>
                        <th class="py-2">Mail</th>
                        <th class="py-2">Role</th>
                        <th class="py-2"></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?= getAllUser(); ?>
                    </tbody>
                  </table>
                  <div class="py-5">
                    <a href="?new" class="px-4 py-2 bg-sky-600 text-white hover:bg-sky-500">Create new User</a>
                  </div>
                </div>
              </div>
            </div>
          </main>
        </div>
        <script src="js/tailwind.js"></script>
        <script src="js/update.js"></script>
        <script src="js/change_password.js"></script>
        <script src="js/select_settings.js"></script>
    </body>
</html>