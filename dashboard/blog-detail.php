<?php
  require_once(__DIR__ . "/../functions/function_backend.php");
  security_checklogin();

  $new  = $_GET['new']  ?? null;
  $edit = $_GET['edit'] ?? null;

  if ($edit !== null) {
    $essay = getEssayData($edit);
  } else {
    $essay = [
      'title'         => null,
      'slug'          => null,
      'content'       => null,
      'tags'          => null,
      'is_published'  => "false",
      'cover'         => "",
      'published_at'  => date('Y-m-d'),
    ];
  }
?>
<!doctype html>
<html lang="<?php echo get_language(); ?>">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo languageString('general.edit'); ?>: <?php echo $essay['title']; ?> - <?php echo get_sitename(); ?></title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <!-- EasyMDE -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.css">
    <script src="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.js"></script>
  </head>
  <body class="bg-white dark:bg-black text-black dark:text-white">

    <!-- ==================== Sidebar (mobile) ==================== -->
    <el-dialog>
      <dialog id="sidebar" class="backdrop:bg-transparent lg:hidden">
        <el-dialog-backdrop class="fixed inset-0 bg-white/80 dark:bg-black/80 transition-opacity duration-300 ease-linear data-closed:opacity-0"></el-dialog-backdrop>
        <div tabindex="0" class="fixed inset-0 flex focus:outline-none">
          <el-dialog-panel class="group/dialog-panel relative mr-16 flex w-full max-w-xs flex-1 transform transition duration-300 ease-in-out data-closed:-translate-x-full">
            <div class="absolute top-0 left-full flex w-16 justify-center pt-5 duration-300 ease-in-out group-data-closed/dialog-panel:opacity-0">
              <button type="button" command="close" commandfor="sidebar" class="-m-2.5 p-2.5">
                <span class="sr-only">Close sidebar</span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6 text-black dark:text-white">
                  <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
              </button>
            </div>
            <div class="relative flex grow flex-col gap-y-5 overflow-y-auto bg-white dark:bg-black px-6 pb-4 ring-1 ring-white/10 dark:before:pointer-events-none dark:before:absolute dark:before:inset-0 dark:before:bg-black/10">
              <div class="relative flex h-16 shrink-0 items-center">
                <img src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=cyan&shade=500" alt="Your Company" class="h-8 w-auto" />
              </div>
              <nav class="relative flex flex-1 flex-col">
                <?php include (__DIR__.'/layout/blog_menu.php'); ?>
              </nav>
            </div>
          </el-dialog-panel>
        </div>
      </dialog>
    </el-dialog>

    <!-- Static sidebar for desktop -->
    <div class="hidden bg-white dark:bg-black ring-1 ring-black/10 dark:ring-white/10 lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
      <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-white dark:bg-black/10 px-6 pb-4">
        <div class="flex h-16 shrink-0 items-center">
          <img src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=cyan&shade=500" alt="Your Company" class="h-8 w-auto" />
        </div>
        <nav class="flex flex-1 flex-col">
          <?php include (__DIR__.'/layout/blog_menu.php'); ?>
        </nav>
      </div>
    </div>

    <div class="lg:pl-72">
      <!-- Topbar -->
      <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-black/10 dark:border-white/10 bg-white px-4 shadow-xs sm:gap-x-6 sm:px-6 lg:px-8 dark:bg-black">
        <button type="button" command="show-modal" commandfor="sidebar" class="-m-2.5 p-2.5 text-gray-700 hover:text-gray-900 lg:hidden dark:text-gray-400 dark:hover:text-white">
          <span class="sr-only">Open sidebar</span>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6">
            <path d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </button>
        <div aria-hidden="true" class="h-6 w-px bg-black/10 lg:hidden dark:bg-white/10"></div>

        <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6 text-black dark:text-white">
          <div class="grid flex-1 grid-cols-1">
            <div class="hidden md:flex justify-start gap-2">
              <a href="dashboard.php"
								class="inline-flex items-center justify-start mx-2 py-2 border-b border-gray-800 dark:border-gray-400 rounded-none
										no-underline text-base font-normal leading-tight appearance-none">
                <?php echo languageString('nav.dashboard'); ?>
              </a>
              <a href="media.php"
								class="inline-flex items-center justify-start mx-2 py-2 border-b border-gray-800 dark:border-gray-400 rounded-none
										no-underline text-base font-normal leading-tight appearance-none">
                <?php echo languageString('nav.images'); ?>
              </a>
              <a href="blog.php" class="inline-flex items-center justify-start mx-2 py-2 border-b-2 border-gray-800 dark:border-gray-400 rounded-none no-underline text-base font-normal leading-tight appearance-none">
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
            <?php
            if ($edit !== null) {
            ?>
            <button type="button" id="delete-button" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold border border-black/20 rounded hover:bg-black/5 dark:border-white/20 dark:hover:bg-white/10">
              <svg class="-ml-0.5 size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
              </svg>
              <?php echo languageString('blog.delete'); ?>
            </button>
            <?php
            }
            ?>
            <button type="button" class="-m-2.5 p-2.5 text-gray-800 hover:text-gray-500 dark:text-gray-400 dark:hover:text-gray-300">
              <span class="sr-only">View notifications</span>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6">
                <path d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </button>

            <div aria-hidden="true" class="hidden lg:block lg:h-6 lg:w-px lg:bg-white dark:bg-black/10 dark:lg:bg-gray-100/10"></div>

            <!-- Profile Dropdown -->
            <div data-dropdown class="relative">
              <button type="button" class="relative flex items-center" aria-haspopup="menu" aria-expanded="false" data-trigger>
                <span class="absolute -inset-1.5"></span>
                <span class="sr-only">Open user menu</span>
                <img src="<?php echo get_userimage($_SESSION['username']); ?>" alt="" class="size-8 rounded-full bg-gray-50 outline -outline-offset-1 outline-black/5 dark:bg-gray-800 dark:outline-white/10" />
                <span class="hidden lg:flex lg:items-center">
                  <span aria-hidden="true" class="ml-4 text-sm/6 font-semibold text-gray-900 dark:text-white">
                    <?php echo $_SESSION['username']; ?>
                  </span>
                  <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="ml-2 size-5 text-gray-400 dark:text-gray-500">
                    <path d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" fill-rule="evenodd" />
                  </svg>
                </span>
              </button>

              <div data-menu hidden role="menu" class="w-32 origin-top-right rounded-md py-2 shadow-lg outline outline-gray-900/5 transition bg-white dark:bg-black dark:shadow-none dark:-outline-offset-1 dark:outline-white/10">
                <a href="dashboard-personal.php" class="block px-3 py-1 text-sm/6 text-gray-900 hover:bg-gray-50 focus:outline-hidden dark:text-white dark:hover:bg-white/5" role="menuitem">
                  <?php echo languageString('nav.your_profile'); ?>
                </a>
                <a href="login.php?logout=true" class="block px-3 py-1 text-sm/6 text-gray-900 hover:bg-gray-50 focus:outline-hidden dark:text-white dark:hover:bg-white/5" role="menuitem">
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
						class="inline-flex items-center py-2 border-b border-gray-800 dark:border-gray-400 rounded-none
								no-underline text-base font-normal leading-tight appearance-none">
						<?php echo languageString('nav.dashboard'); ?>
					</a>
					<a href="media.php"
						class="inline-flex items-center py-2 border-b border-gray-800 dark:border-gray-400 rounded-none
								no-underline text-base font-normal leading-tight appearance-none">
						<?php echo languageString('nav.images'); ?>
					</a>
					<a href="blog.php"
						class="inline-flex items-center py-2 border-b-2 border-gray-800 dark:border-gray-400 rounded-none
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

      <!-- ==================== Main ==================== -->
      <main class="py-10 bg-white dark:bg-black">
        <div class="px-4 sm:px-6 lg:px-8">
          <form id="essayForm" action="backend_api/essay_save.php" method="post" class="space-y-8 max-w-5xl mx-auto">

            <!-- Main Content -->
            <section class="rounded-sm border border-black/10 dark:border-white/10 bg-white dark:bg-black/40 shadow-xs">
              <div class="p-4 grid grid-cols-1 gap-6 sm:grid-cols-6">
                <!-- Title -->
                <div class="sm:col-span-4">
                  <label for="title" class="block text-xs font-medium"><?php echo languageString('general.title'); ?></label>
                  <div class="mt-1">
                    <input type="text" name="title" id="title" placeholder="Title" value="<?php echo $essay['title']; ?>"
                           class="block w-full bg-white dark:bg-black px-3 py-2 text-sm outline outline-1 -outline-offset-1 outline-black/10 dark:outline-white/10 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-cyan-600" />
                  </div>
                </div>

                <!-- Foldername -->
                <div class="sm:col-span-4">
                  <label for="foldername" class="block text-xs font-medium"><?php echo languageString('blog.foldername'); ?></label>
                  <div class="mt-1 flex items-stretch overflow-hidden outline outline-1 -outline-offset-1 outline-black/10 dark:outline-white/10 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-cyan-600">
                    <span class="shrink-0 px-3 py-2 text-xs text-black/60 dark:text-gray-400 bg-black/5 dark:bg-white/5 select-none">/userdata/content/essays/</span>
                    <input type="text" name="foldername" id="foldername" readonly value="<?php echo $essay['slug']; ?>"
                           class="min-w-0 grow bg-white dark:bg-black px-3 py-2 text-sm focus:outline-none" />
                    <input type="hidden" id="original_foldername" name="original_foldername" value="<?php echo $essay['slug']; ?>">
                  </div>
                </div>

                <!-- Content -->
                <div class="col-span-full">
                  <label for="content" class="block text-xs font-medium"><?php echo languageString('blog.content'); ?></label>
                  <div class="mt-1">
                    <textarea name="content" id="content" rows="12"
                              class="block w-full bg-white dark:bg-black px-3 py-2 text-sm outline outline-1 -outline-offset-1 outline-black/10 dark:outline-white/10 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-cyan-600"><?php echo $essay['content']; ?></textarea>
                  </div>
                </div>

                <!-- Tags -->
                <div class="sm:col-span-4">
                  <label for="tags" class="block text-xs font-medium"><?php echo languageString('general.tags'); ?></label>
                  <div class="mt-1">
                    <input type="text" name="tags" id="tags" placeholder="tag1, tag2"
                           value="<?php echo isset($essay['tags']) ? htmlspecialchars(implode(', ', $essay['tags']), ENT_QUOTES, 'UTF-8') : ''; ?>"
                           class="block w-full bg-white dark:bg-black px-3 py-2 text-sm outline outline-1 -outline-offset-1 outline-black/10 dark:outline-white/10 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-cyan-600" />
                  </div>
                </div>

                <!-- Hero Image -->
                <div class="col-span-full">
                  <input type="hidden" name="cover" id="cover" value="<?php echo $essay['cover']; ?>">
                  <label class="block text-xs font-medium mb-2"><?php echo languageString('blog.heroimage'); ?></label>

                  <div class="w-full max-w-xl rounded border border-black/10 dark:border-white/10 overflow-hidden bg-black/5 dark:bg-white/5">
                    <img id="coverPreview" src="<?php echo get_cached_image_dashboard($essay['cover'], 'M'); ?>" alt="Cover Preview" class="w-full h-56 object-cover" />
                  </div>

                  <div class="mt-2 flex gap-2">
                    <button type="button" id="openCoverModalBtn"
                            class="text-xs px-3 py-1.5 rounded border border-black/20 hover:bg-black/5 dark:border-white/20 dark:hover:bg-white/10">
                      <?php echo languageString('blog.select-heroimage'); ?>
                    </button>
                    <button type="button" id="removeHeroImg"
                            class="text-xs px-3 py-1.5 rounded border border-black/20 hover:bg-black/5 dark:border-white/20 dark:hover:bg-white/10">
                      <?php echo languageString('blog.remove-heroimage'); ?>
                    </button>
                  </div>
                </div>
              </div>
            </section>

            <!-- Post Settings -->
            <section class="rounded-sm border border-black/10 dark:border-white/10 bg-white dark:bg-black/40 shadow-xs">
              <header class="px-4 py-3 border-b border-black/10 dark:border-white/10">
                <h2 class="text-sm font-semibold"><?php echo languageString('blog.settings.title'); ?></h2>
                <p class="mt-1 text-xs text-black/60 dark:text-gray-400"><?php echo languageString('blog.settings.description'); ?></p>
              </header>

              <div class="p-4 grid grid-cols-1 gap-6 sm:grid-cols-6">
                <!-- Published Toggle -->
                <div class="col-span-full">
                  <div class="flex items-center justify-between">
                    <span class="flex grow flex-col">
                      <span class="text-sm font-medium" id="availability-label"><?php echo languageString('blog.settings.published'); ?></span>
                      <span class="text-xs text-black/60 dark:text-gray-400" id="availability-description"><?php echo languageString('blog.settings.published-description'); ?></span>
                    </span>

                    <button type="button" id="is_published"
                            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent bg-gray-400 transition-colors duration-200 ease-in-out focus:ring-2 focus:ring-cyan-600 focus:ring-offset-2 focus:outline-hidden"
                            role="switch"
                            aria-checked="<?php echo ($essay['is_published'] === true || $essay['is_published'] === 'true') ? 'true' : 'false'; ?>"
                            aria-labelledby="availability-label" aria-describedby="availability-description">
                      <span aria-hidden="true" class="pointer-events-none inline-block size-5 translate-x-0 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out"></span>
                    </button>
                    <input type="hidden" name="is_published" id="is_published_input"
                           value="<?php echo ($essay['is_published'] === true || $essay['is_published'] === 'true') ? 'true' : 'false'; ?>">
                  </div>
                </div>

                <!-- Publishing Date -->
                <div class="sm:col-span-3">
                  <label for="published_at" class="block text-xs font-medium mb-1"><?php echo languageString('blog.settings.published-date'); ?></label>
                  <input type="date" id="published_at" name="published_at"
                         class="w-full border border-black/10 dark:border-white/10 px-3 py-2 text-sm bg-white dark:bg-neutral-900"
                         value="<?php echo $essay['published_at']; ?>"/>
                </div>

                <!-- Actions -->
                <div class="col-span-full flex items-center justify-end gap-3">
                  <a href="blog.php"
                     class="text-xs px-3 py-2 rounded border border-black/20 hover:bg-black/5 dark:border-white/20 dark:hover:bg-white/10">
                    <?php echo languageString('general.cancel'); ?>
                  </a>
                  <button type="submit"
                          class="text-xs px-3 py-2 rounded border border-black/20 hover:bg-black/5 dark:border-white/20 dark:hover:bg-white/10">
                    <?php echo languageString('general.save'); ?>
                  </button>
                </div>
              </div>
            </section>

            <!-- Plugins -->
            <?php
              $pluginDirs = glob(__DIR__ . '/../userdata/plugins/*', GLOB_ONLYDIR);
              foreach ($pluginDirs as $pluginDir) {
                $meta  = $pluginDir . '/plugin.json';
                $set   = $pluginDir . '/settings.json';
                $postJ = $pluginDir . '/admin/post.json';
                if (!file_exists($meta) || !file_exists($set) || !file_exists($postJ)) continue;
                $settings = json_decode(file_get_contents($set), true);
                if (!is_array($settings) || empty($settings['enabled'])) continue;
                $metaData = json_decode(file_get_contents($meta), true);
                $pluginName = htmlspecialchars($metaData['name'] ?? basename($pluginDir));
                $fields = json_decode(file_get_contents($postJ), true)['fields'] ?? [];

                echo '<section class="rounded-sm border border-black/10 dark:border-white/10 bg-white dark:bg-black/40 shadow-xs">';
                echo '  <header class="px-4 py-3 border-b border-black/10 dark:border-white/10"><h2 class="text-sm font-semibold">'. $pluginName .'</h2></header>';
                echo '  <div class="p-4 grid grid-cols-1 gap-6 sm:grid-cols-6">';
                foreach ($fields as $field) {
                  $key   = $field['key'];
                  $type  = $field['type'] ?? 'text';
                  $label = htmlspecialchars($field['label'] ?? $key);
                  $hint  = htmlspecialchars($field['hint'] ?? '');
                  $value = $essay[$key] ?? ($type === 'toggle' ? false : '');

                  echo '<div class="col-span-full">';
                  if ($type === 'toggle') {
                    $checked = $value ? 'true' : 'false';
                    echo '  <div class="flex items-center justify-between">';
                    echo '    <span class="flex grow flex-col">';
                    echo '      <span class="text-sm font-medium">'. $label .'</span>';
                    if ($hint) echo '  <span class="text-xs text-black/60 dark:text-gray-400">'. $hint .'</span>';
                    echo '    </span>';
                    echo '    <button type="button" id="'. $key .'" class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent bg-gray-400 transition-colors focus:ring-2 focus:ring-cyan-600" role="switch" aria-checked="'. $checked .'">';
                    echo '      <span aria-hidden="true" class="pointer-events-none inline-block size-5 '.($value ? 'translate-x-5':'translate-x-0').' transform rounded-full bg-white shadow-sm ring-0 transition"></span>';
                    echo '    </button>';
                    echo '    <input type="hidden" name="'. $key .'" id="'. $key .'-input" value="'. ($value ? 'true' : 'false') .'">';
                    echo '  </div>';
                  } else {
                    echo '  <label class="block text-xs font-medium mb-1">'. $label .'</label>';
                    echo '  <input type="'. $type .'" id="'. $key .'" name="'. $key .'" value="'. htmlspecialchars($value) .'" class="block w-full border border-black/10 dark:border-white/10 px-3 py-2 text-sm bg-white dark:bg-neutral-900">';
                    if ($hint) echo '  <p class="mt-1 text-xs text-black/60 dark:text-gray-400">'. $hint .'</p>';
                  }
                  echo '</div>';
                }
                echo '  </div>';
                echo '</section>';
              }
            ?>
          </form>
        </div>
      </main>
    </div>

    <!-- ==================== Delete Modal (el-dialog) ==================== -->
    <el-dialog>
      <dialog id="deleteModal" class="backdrop:bg-transparent">
        <el-dialog-backdrop class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/60 transition-opacity data-closed:opacity-0"></el-dialog-backdrop>
        <div tabindex="0" class="fixed inset-0 flex items-end justify-center p-4 text-center sm:items-center sm:p-0">
          <el-dialog-panel
            class="relative transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all
                   sm:my-8 sm:w-full sm:max-w-md sm:p-6 dark:bg-black dark:outline dark:-outline-offset-1 dark:outline-white/10">
            <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
              <button type="button" command="close" commandfor="deleteModal" class="rounded-md bg-white text-gray-400 hover:text-gray-500 dark:bg-black">
                <span class="sr-only">Close</span>
                <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                  <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </button>
            </div>

            <div class="sm:flex sm:items-start">
              <div class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:size-10 dark:bg-red-500/10">
                <svg class="size-6 text-red-600 dark:text-red-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                  <path d="M12 9v4m0 4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
              <div class="mt-3 text-left sm:mt-0 sm:ml-4">
                <h2 class="text-base font-semibold">Delete Confirmation</h2>
                <p class="mt-2 text-sm text-black/70 dark:text-gray-400">Do you really want to delete this post?</p>
              </div>
            </div>

            <div class="mt-6 sm:flex sm:flex-row-reverse">
              <button id="confirmDelete"
                      class="inline-flex w-full justify-center rounded-md px-3 py-2 text-sm font-semibold border border-black/20 hover:bg-black/5 sm:ml-3 sm:w-auto dark:border-white/20 dark:hover:bg-white/10">
                Delete
              </button>
              <button id="cancelDelete" type="button" command="close" commandfor="deleteModal"
                      class="mt-3 inline-flex w-full justify-center rounded-md px-3 py-2 text-sm font-semibold border border-black/20 hover:bg-black/5 sm:mt-0 sm:w-auto dark:border-white/20 dark:hover:bg-white/10">
                <?php echo languageString('general.cancel'); ?>
              </button>
            </div>
          </el-dialog-panel>
        </div>
      </dialog>
    </el-dialog>

    <!-- ========== Cover Picker Modal (el-dialog) ========== -->
    <el-dialog>
      <dialog id="coverModal" class="backdrop:bg-transparent">
        <el-dialog-backdrop class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/60 transition-opacity data-closed:opacity-0"></el-dialog-backdrop>
        <div tabindex="0" class="fixed inset-0 flex items-end justify-center p-4 text-center sm:items-center sm:p-0">
          <el-dialog-panel
            class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all
                   sm:my-8 sm:w-full sm:max-w-2xl sm:p-6 p-4 dark:bg-black dark:outline dark:-outline-offset-1 dark:outline-white/10">

            <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
              <button type="button" command="close" commandfor="coverModal" class="rounded-md bg-white text-gray-400 hover:text-gray-500 dark:bg-black">
                <span class="sr-only">Close</span>
                <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </button>
            </div>

            <h2 class="text-sm font-semibold">Select Hero Image</h2>
            <div class="mt-4 grid grid-cols-2 md:grid-cols-3 gap-4 max-h-[60vh] overflow-y-auto" id="cover-choose">
              <?php
                $imageDir = realpath(__DIR__ . '/../userdata/content/images');
                $images = [];
                if ($imageDir && is_dir($imageDir)) {
                  foreach (glob($imageDir.'/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE) as $imgFile) {
                    $images[] = basename($imgFile);
                  }
                }
                foreach ($images as $img):
                  $path = "/userdata/content/images/" . urlencode($img);
                  echo "<img src='$path' class='w-full h-28 object-cover rounded border border-black/10 dark:border-white/10 cursor-pointer hover:opacity-80' onclick=\"selectCover('$path')\">";
                endforeach;
              ?>
            </div>

            <div class="mt-6 sm:flex sm:flex-row-reverse">
              <button type="button" command="close" commandfor="coverModal"
                      class="inline-flex w-full justify-center rounded-md px-3 py-2 text-sm font-semibold border border-black/20 hover:bg-black/5 sm:ml-3 sm:w-auto dark:border-white/20 dark:hover:bg-white/10">
                Close
              </button>
            </div>
          </el-dialog-panel>
        </div>
      </dialog>
    </el-dialog>

    <!-- Scripts -->
    <!-- <script src="js/navbar.js"></script> -->
    <script src="js/tailwind.js"></script>
    <script src="js/remove_hero_image.js"></script>

    <script>
      // EasyMDE init
      document.addEventListener("DOMContentLoaded", function () {
        window.easyMDE = new EasyMDE({
          element: document.getElementById("content"),
          spellChecker: false,
          autosave: { enabled: false },
          placeholder: "Please enter your content",
          toolbar: ["bold","italic","heading","|","quote","unordered-list","ordered-list","|","preview","guide"]
        });
      });

      // Slug & Foldername (wie Pages-Layout)
      document.addEventListener("DOMContentLoaded", function () {
        const titleInput  = document.getElementById("title");
        const folderInput = document.getElementById("foldername");
        function slugify(text){
          const map={'ä':'ae','ö':'oe','ü':'ue','ß':'ss','à':'a','á':'a','è':'e','é':'e','ì':'i','í':'i','ò':'o','ó':'o','ù':'u','ú':'u','ñ':'n'};
          return text.toString().toLowerCase().trim()
            .replace(/[äöüßàáèéìíòóùúñ]/g,m=>map[m]).replace(/[^a-z0-9]+/g,'-').replace(/^-+|-+$/g,'');
        }
        async function checkAndGenerateFolder(slug){
          const response = await fetch('backend_api/check_foldername.php?base='+encodeURIComponent(slug));
          const data = await response.json();
          folderInput.value = data.suggested;
        }
        titleInput.addEventListener("input", () => {
          const base = slugify(titleInput.value);
          if (base) checkAndGenerateFolder(base); else folderInput.value = '';
        });
      });

      // Publish toggle
      document.addEventListener("DOMContentLoaded", function () {
        const toggleBtn   = document.getElementById("is_published");
        const knob        = toggleBtn.querySelector("span");
        const hiddenInput = document.getElementById("is_published_input");
        function updateToggleUI(enabled){
          toggleBtn.setAttribute("aria-checked", enabled ? "true" : "false");
          toggleBtn.classList.toggle("bg-gray-400", !enabled);
          toggleBtn.classList.toggle("bg-cyan-600", enabled);
          knob.classList.toggle("translate-x-5", enabled);
          knob.classList.toggle("translate-x-0", !enabled);
          hiddenInput.value = enabled ? "true" : "false";
        }
        updateToggleUI(toggleBtn.getAttribute("aria-checked")==="true");
        toggleBtn.addEventListener("click", () => {
          updateToggleUI(!(toggleBtn.getAttribute("aria-checked")==="true"));
        });
      });

      // Cover Modal
      const openCoverModalBtn = document.getElementById("openCoverModalBtn");
      if (openCoverModalBtn) {
        openCoverModalBtn.addEventListener("click", () => {
          document.getElementById('coverModal').setAttribute('open','');
        });
      }
      function closeCoverModal(){ document.getElementById('coverModal').removeAttribute('open'); }
      function selectCover(path){
        const filename = path.split('/').pop();
        document.getElementById('cover').value = filename;
        document.getElementById('coverPreview').src = path;
        closeCoverModal();
      }
      window.selectCover = selectCover;

      // Delete modal
      document.getElementById('delete-button').addEventListener('click', () => {
        document.getElementById('deleteModal').setAttribute('open','');
      });
      document.getElementById('cancelDelete').addEventListener('click', () => {
        document.getElementById('deleteModal').removeAttribute('open');
      });
      document.getElementById('confirmDelete').addEventListener('click', function() {
        const filename = "<?php echo $essay['source_path'] ?? '';?>";
        if (!filename) return document.getElementById('deleteModal').removeAttribute('open');
        window.location.href = `/dashboard/backend_api/delete.php?type=essay&filename=${encodeURIComponent(filename)}`;
      });
    </script>
  </body>
</html>  