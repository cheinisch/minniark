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
<?php if ($delete_modal): ?>
<el-dialog>
  <div id="deleteUserModal" class="fixed inset-0 z-50" role="dialog" aria-modal="true" aria-labelledby="delete-title">
    <el-dialog-backdrop class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/50 transition-opacity data-closed:opacity-0"></el-dialog-backdrop>

    <div tabindex="0" class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
      <el-dialog-panel
        class="relative transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all
               sm:my-8 sm:w-full sm:max-w-md sm:p-6
               dark:bg-black dark:outline dark:-outline-offset-1 dark:outline-white/10">

        <!-- Close -->
        <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
          <a href="?" class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-2 focus:outline-offset-2 focus:outline-indigo-600 dark:bg-black dark:hover:text-gray-300 dark:focus:outline-white">
            <span class="sr-only">Close</span>
            <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </a>
        </div>

        <!-- Header -->
        <div class="sm:flex sm:items-start">
          <div class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:size-10 dark:bg-red-500/10">
            <svg class="size-6 text-red-600 dark:text-red-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 18L18 6M6 6l12 12"/></svg>
          </div>
          <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
            <h2 id="delete-title" class="text-base font-semibold text-gray-900 dark:text-white">Delete Confirmation</h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
              Do you really want to delete the user:
              <span class="font-semibold"><?php echo htmlspecialchars($_GET['delete'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>?
            </p>
          </div>
        </div>

        <!-- Footer -->
        <div class="mt-6 sm:flex sm:flex-row-reverse">
          <a href="backend_api/user_edit_user.php?delete=<?php echo urlencode($_GET['delete'] ?? ''); ?>"
             class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-red-500 sm:ml-3 sm:w-auto">
            <?php echo languageString('general.delete'); ?>
          </a>
          <a href="?" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs inset-ring-1 inset-ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto dark:bg-white/10 dark:text-white dark:shadow-none dark:inset-ring-white/5 dark:hover:bg-white/20">
            <?php echo languageString('general.cancel'); ?>
          </a>
        </div>
      </el-dialog-panel>
    </div>
  </div>
</el-dialog>
<?php endif; ?>

<?php if ($wrong_user): ?>
<el-dialog>
  <div id="cannotDeleteSelfModal" class="fixed inset-0 z-50" role="dialog" aria-modal="true" aria-labelledby="selfdel-title">
    <el-dialog-backdrop class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/50 transition-opacity data-closed:opacity-0"></el-dialog-backdrop>

    <div tabindex="0" class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
      <el-dialog-panel
        class="relative transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all
               sm:my-8 sm:w-full sm:max-w-md sm:p-6
               dark:bg-black dark:outline dark:-outline-offset-1 dark:outline-white/10">

        <!-- Close -->
        <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
          <a href="?" class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-2 focus:outline-offset-2 focus:outline-indigo-600 dark:bg-black dark:hover:text-gray-300 dark:focus:outline-white">
            <span class="sr-only">Close</span>
            <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </a>
        </div>

        <div class="sm:flex sm:items-start">
          <div class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-amber-100 sm:mx-0 sm:size-10 dark:bg-amber-500/10">
            <svg class="size-6 text-amber-600 dark:text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 9v4m0 4h.01M12 3l9 4v6c0 5-4 9-9 9s-9-4-9-9V7l9-4z"/></svg>
          </div>
          <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
            <h2 id="selfdel-title" class="text-base font-semibold text-gray-900 dark:text-white">Delete Error</h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">You can’t delete your own user.</p>
          </div>
        </div>

        <div class="mt-6 sm:flex sm:flex-row-reverse">
          <a href="?" class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 sm:ml-3 sm:w-auto dark:bg-indigo-500 dark:hover:bg-indigo-400">
            <?php echo languageString('general.ok'); ?>
          </a>
        </div>
      </el-dialog-panel>
    </div>
  </div>
</el-dialog>
<?php endif; ?>


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

					<!-- Titel + Beschreibung aus dashboard.user.* -->
					<div class="mb-6">
						<h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
							<?php echo languageString('dashboard.user.title'); ?>
						</h1>
						<p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
							<?php echo languageString('dashboard.user.description'); ?>
						</p>
					</div>

					<div class="mt-6">
						<div class="mb-5">
							<?php if (isset($_GET['edit'])): ?>
							<form class="md:col-span-2 grid grid-cols-1 sm:grid-cols-4 gap-4"
								action="backend_api/user_edit_user.php?edit" method="post">

								<div>
									<label for="username" class="block text-xs font-medium text-gray-900 dark:text-gray-200">
										<?php echo languageString('dashboard.user.form.username'); ?>
									</label>
									<input
										type="text"
										id="username"
										name="username"
										value="<?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>"
										class="mt-1 block w-full rounded-md bg-white px-3 py-1.5 text-sm text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-sky-600 dark:bg-white/10 dark:text-white dark:outline-white/10"
									>
									<input type="hidden" name="username_old" value="<?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>">
								</div>

								<div>
									<label for="mail" class="block text-xs font-medium text-gray-900 dark:text-gray-200">
										<?php echo languageString('dashboard.user.form.mail'); ?>
									</label>
									<input
										type="email"
										id="mail"
										name="mail"
										value="<?php echo htmlspecialchars($mail, ENT_QUOTES, 'UTF-8'); ?>"
										class="mt-1 block w-full rounded-md bg-white px-3 py-1.5 text-sm text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-sky-600 dark:bg-white/10 dark:text-white dark:outline-white/10"
									>
								</div>

								<div>
									<label for="password" class="block text-xs font-medium text-gray-900 dark:text-gray-200">
										<?php echo languageString('dashboard.user.form.password'); ?>
									</label>
									<input
										type="password"
										id="password"
										name="password"
										placeholder="<?php echo languageString('dashboard.user.form.password_placeholder'); ?>"
										class="mt-1 block w-full rounded-md bg-white px-3 py-1.5 text-sm text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-sky-600 dark:bg-white/10 dark:text-white dark:outline-white/10"
									>
								</div>

								<div>
									<label for="userrole" class="block text-xs font-medium text-gray-900 dark:text-gray-200">
										<?php echo languageString('dashboard.user.form.userrole'); ?>
									</label>
									<select
										id="userrole"
										name="userrole"
										class="mt-1 block w-full rounded-md bg-white px-3 py-1.5 text-sm text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-sky-600 dark:bg-white/10 dark:text-white dark:outline-white/10"
									>
										<option value="user"  <?php echo $role === 'user'  ? 'selected' : ''; ?>>
											<?php echo languageString('dashboard.user.form.userrole_user'); ?>
										</option>
										<option value="admin" <?php echo $role === 'admin' ? 'selected' : ''; ?>>
											<?php echo languageString('dashboard.user.form.userrole_admin'); ?>
										</option>
									</select>
								</div>

								<div class="sm:col-span-2">
									<button
										type="submit"
										class="inline-flex items-center rounded-md bg-sky-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-sky-500"
									>
										<?php echo languageString('dashboard.user.form.submit'); ?>
									</button>
								</div>
							</form>
							<?php endif; ?>

							<?php if (isset($_GET['new'])): ?>
							<form class="md:col-span-2 grid grid-cols-1 sm:grid-cols-4 gap-4"
								action="backend_api/user_edit_user.php?new" method="post">

								<div>
									<label for="username" class="block text-xs font-medium text-gray-900 dark:text-gray-200">
										<?php echo languageString('dashboard.user.form.username'); ?>
									</label>
									<input
										type="text"
										id="username"
										name="username"
										required
										class="mt-1 block w-full rounded-md bg-white px-3 py-1.5 text-sm text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-sky-600 dark:bg-white/10 dark:text-white dark:outline-white/10"
									>
								</div>

								<div>
									<label for="mail" class="block text-xs font-medium text-gray-900 dark:text-gray-200">
										<?php echo languageString('dashboard.user.form.mail'); ?>
									</label>
									<input
										type="email"
										id="mail"
										name="mail"
										required
										class="mt-1 block w-full rounded-md bg-white px-3 py-1.5 text-sm text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-sky-600 dark:bg-white/10 dark:text-white dark:outline-white/10"
									>
								</div>

								<div>
									<label for="password" class="block text-xs font-medium text-gray-900 dark:text-gray-200">
										<?php echo languageString('dashboard.user.form.password'); ?>
									</label>
									<input
										type="password"
										id="password"
										name="password"
										required
										class="mt-1 block w-full rounded-md bg-white px-3 py-1.5 text-sm text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-sky-600 dark:bg-white/10 dark:text-white dark:outline-white/10"
									>
								</div>

								<div>
									<label for="userrole" class="block text-xs font-medium text-gray-900 dark:text-gray-200">
										<?php echo languageString('dashboard.user.form.userrole'); ?>
									</label>
									<select
										id="userrole"
										name="userrole"
										class="mt-1 block w-full rounded-md bg-white px-3 py-1.5 text-sm text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-sky-600 dark:bg-white/10 dark:text-white dark:outline-white/10"
									>
										<option value="user" selected>
											<?php echo languageString('dashboard.user.form.userrole_user'); ?>
										</option>
										<option value="admin">
											<?php echo languageString('dashboard.user.form.userrole_admin'); ?>
										</option>
									</select>
								</div>

								<div class="sm:col-span-2">
									<button
										type="submit"
										class="inline-flex items-center rounded-md bg-sky-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-sky-500"
									>
										<?php echo languageString('dashboard.user.form.submit'); ?>
									</button>
								</div>
							</form>
							<?php endif; ?>

						</div>

						<div class="overflow-hidden rounded-sm border border-black/10 dark:border-white/10 bg-white dark:bg-black/40 shadow-xs">
							<table class="min-w-full divide-y divide-black/10 dark:divide-white/10 text-sm text-gray-900 dark:text-gray-100">
								<thead class="bg-black/5 dark:bg-white/5">
									<tr>
										<th class="px-4 py-2 text-left font-semibold">
											<?php echo languageString('dashboard.user.table.username'); ?>
										</th>
										<th class="px-4 py-2 text-left font-semibold">
											<?php echo languageString('dashboard.user.table.mail'); ?>
										</th>
										<th class="px-4 py-2 text-left font-semibold">
											<?php echo languageString('dashboard.user.table.role'); ?>
										</th>
										<th class="px-4 py-2"></th>
									</tr>
								</thead>
								<tbody class="divide-y divide-black/10 dark:divide-white/10">
									<?= getAllUser(); ?>
								</tbody>
							</table>
						</div>

						<div class="mt-4">
							<a href="?new"
							   class="inline-flex items-center rounded-md bg-sky-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-sky-500">
								<?php echo languageString('dashboard.user.actions.create'); ?>
							</a>
						</div>
					</div>
				</div>
			</main>
		</div>
		<!-- <script src="js/navbar.js"></script> -->
		<script src="js/tailwind.js"></script>
		<script src="js/profile_settings.js"></script>

	</body>
</html>
