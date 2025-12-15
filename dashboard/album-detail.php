<?php
  require_once( __DIR__ . "/../functions/function_backend.php");
  require_once __DIR__ . '/../vendor/autoload.php'; // Pfad zu Parsedown
  security_checklogin();

  // Prüfen, ob ein bestimmtes Jahr übergeben wurde
  $filterYear = isset($_GET['year']) ? $_GET['year'] : null;
  $filterRating = isset($_GET['rating']) ? $_GET['rating'] : null;

  $albumTitle = isset($_GET['album']) ? $_GET['album'] : null;

  $albumdata = getAlbumData($albumTitle);

  $Parsedown = new Parsedown();
  $descriptionHtml = $Parsedown->text($albumdata['description']);

  $cacheImage = get_cacheimage($albumdata['headImage'],"l");

  $headimage = null;

  if($albumdata['headImage'] != null || $albumdata['headImage'] != '')
  {
    $headimage = "../cache/images/".$cacheImage;
  }else{
    $headimage = "img/placeholder.png";
  }

?>

<!doctype html>
<html lang="<?php echo get_language(); ?>">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Images - <?php echo get_sitename(); ?></title>
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-white dark:bg-black">

<!-- Mobile sidebar -->
<el-dialog>
  <dialog id="sidebar" class="backdrop:bg-transparent lg:hidden">
    <el-dialog-backdrop class="fixed inset-0 bg-white/80 dark:bg-black/80 transition-opacity duration-300 ease-linear data-closed:opacity-0"></el-dialog-backdrop>
    <div tabindex="0" class="fixed inset-0 flex focus:outline-none">
      <el-dialog-panel class="group/dialog-panel relative mr-16 flex w-full max-w-xs flex-1 transform transition duration-300 ease-in-out data-closed:-translate-x-full">
        <div class="absolute top-0 left-full flex w-16 justify-center pt-5 duration-300 ease-in-out group-data-closed/dialog-panel:opacity-0">
          <button type="button" command="close" commandfor="sidebar" class="-m-2.5 p-2.5">
            <span class="sr-only">Close sidebar</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="size-6 text-black dark:text-white">
              <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </button>
        </div>

        <div class="relative flex grow flex-col gap-y-5 overflow-y-auto bg-white dark:bg-black px-6 pb-4 ring-1 ring-white/10 dark:before:pointer-events-none dark:before:absolute dark:before:inset-0 dark:before:bg-black/10">
          <div class="relative flex h-16 shrink-0 items-center">
            <img src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=cyan&shade=500" alt="Minniark" class="h-8 w-auto" />
          </div>
          <nav class="relative flex flex-1 flex-col">
            <?php include (__DIR__.'/layout/media_menu.php'); ?>
          </nav>
        </div>
      </el-dialog-panel>
    </div>
  </dialog>
</el-dialog>

<!-- Desktop sidebar -->
<div class="hidden bg-white dark:bg-black ring-1 ring-black/10 dark:ring-white/10 lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
  <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-white dark:bg-black/10 px-6 pb-4">
    <div class="flex h-16 shrink-0 items-center">
      <img src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=cyan&shade=500" alt="Minniark" class="h-8 w-auto" />
    </div>
    <nav class="flex flex-1 flex-col">
      <?php include (__DIR__.'/layout/media_menu.php'); ?>
    </nav>
  </div>
</div>

<div class="lg:pl-72">
  <!-- Topbar -->
  <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-black/10 bg-white px-4 shadow-xs sm:gap-x-6 sm:px-6 lg:px-8 dark:border-white/10 dark:bg-black">
    <button type="button" command="show-modal" commandfor="sidebar" class="-m-2.5 p-2.5 text-gray-700 hover:text-gray-900 lg:hidden dark:text-gray-400 dark:hover:text-white">
      <span class="sr-only">Open sidebar</span>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="size-6">
        <path d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" stroke-linecap="round" stroke-linejoin="round" />
      </svg>
    </button>

    <div aria-hidden="true" class="h-6 w-px bg-black/10 lg:hidden dark:bg-white/10"></div>

    <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6 text-black dark:text-white">
      <div class="grid flex-1 grid-cols-1">
        <div class="hidden md:flex justify-start gap-2">
          <a href="dashboard.php" class="inline-flex items-center justify-start mx-2 py-2 border-b border-gray-800 dark:border-gray-400 rounded-none no-underline text-base font-normal leading-tight appearance-none">
            <?php echo languageString('nav.dashboard'); ?>
          </a>
          <a href="media.php" class="inline-flex items-center justify-start mx-2 py-2 border-b-2 border-gray-800 dark:border-gray-400 rounded-none no-underline text-base font-normal leading-tight appearance-none">
            <?php echo languageString('nav.images'); ?>
          </a>
          <a href="blog.php" class="inline-flex items-center justify-start mx-4 py-2 border-b border-gray-800 dark:border-gray-400 rounded-none no-underline text-base font-normal leading-tight appearance-none">
            <?php echo languageString('nav.blogposts'); ?>
          </a>
          <a href="pages.php" class="inline-flex items-center justify-start mx-4 py-2 border-b border-gray-800 dark:border-gray-400 rounded-none no-underline text-base font-normal leading-tight appearance-none">
            <?php echo languageString('nav.pages'); ?>
          </a>
        </div>
      </div>

      <div class="flex items-center gap-x-4 lg:gap-x-6">
        <button type="button" id="addImagetoAlbumBtn"
          class="inline-flex items-center gap-2 -m-2.5 p-2.5 text-gray-800 hover:text-gray-500 dark:text-gray-400 dark:hover:text-gray-300">
          Add image
        </button>

        <div aria-hidden="true" class="hidden lg:block lg:h-6 lg:w-px dark:bg-gray-100/10"></div>

        <!-- Profile dropdown -->
        <div data-dropdown class="relative">
          <button type="button" class="relative flex items-center" aria-haspopup="menu" aria-expanded="false" data-trigger>
            <span class="sr-only">Open user menu</span>
            <img src="<?php echo get_userimage($_SESSION['username']); ?>" alt=""
                 class="size-8 rounded-full bg-gray-50 outline -outline-offset-1 outline-black/5 dark:bg-gray-800 dark:outline-white/10" />
            <span class="hidden lg:flex lg:items-center">
              <span aria-hidden="true" class="ml-4 text-sm/6 font-semibold text-gray-900 dark:text-white">
                <?php echo $_SESSION['username']; ?>
              </span>
              <svg viewBox="0 0 20 20" fill="currentColor" class="ml-2 size-5 text-gray-400 dark:text-gray-500">
                <path d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" fill-rule="evenodd" />
              </svg>
            </span>
          </button>

          <div data-menu hidden role="menu"
               class="w-32 origin-top-right rounded-md py-2 shadow-lg outline outline-gray-900/5
                      bg-white dark:bg-black dark:shadow-none dark:-outline-offset-1 dark:outline-white/10">
            <a href="dashboard-personal.php" class="block px-3 py-1 text-sm/6 text-gray-900 hover:bg-gray-50 dark:text-white dark:hover:bg-white/5" role="menuitem">
              <?php echo languageString('nav.your_profile'); ?>
            </a>
            <a href="login.php?logout=true" class="block px-3 py-1 text-sm/6 text-gray-900 hover:bg-gray-50 dark:text-white dark:hover:bg-white/5" role="menuitem">
              <?php echo languageString('nav.sign_out'); ?>
            </a>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- Mobile second bar -->
  <div class="sm:block md:hidden border-b border-gray-600 dark:border-white/10 bg-white dark:bg-black">
    <div class="px-4 sm:px-6 lg:px-8 text-black dark:text-white">
      <nav class="flex gap-2 justify-center">
        <a href="dashboard.php" class="inline-flex items-center py-2 border-b hover:border-t border-gray-800 dark:border-gray-400 rounded-none no-underline text-base font-normal leading-tight appearance-none">
          <?php echo languageString('nav.dashboard'); ?>
        </a>
        <a href="media.php" class="inline-flex items-center py-2 border-b-2 border-gray-800 dark:border-gray-400 rounded-none no-underline text-base font-normal leading-tight appearance-none">
          <?php echo languageString('nav.images'); ?>
        </a>
        <a href="blog.php" class="inline-flex items-center py-2 border-b border-gray-800 dark:border-gray-400 rounded-none no-underline text-base font-normal leading-tight appearance-none">
          <?php echo languageString('nav.blogposts'); ?>
        </a>
        <a href="pages.php" class="inline-flex items-center py-2 border-b border-gray-800 dark:border-gray-400 rounded-none no-underline text-base font-normal leading-tight appearance-none">
          <?php echo languageString('nav.pages'); ?>
        </a>
      </nav>
    </div>
  </div>

  <!-- Main -->
  <!-- Main -->
  <main class="py-10 bg-white dark:bg-black">
    <div class="px-4 sm:px-6 lg:px-8">

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <!-- LEFT: Album Info -->
        <section class="lg:col-span-4">
          <div class="rounded-lg overflow-hidden bg-white dark:bg-black shadow-sm dark:outline dark:-outline-offset-1 dark:outline-white/10">

            <img
              src="<?php echo htmlspecialchars((string)$headimage, ENT_QUOTES); ?>"
              class="w-full aspect-video object-cover"
              alt="Album cover"
              loading="lazy"
            >

            <div class="p-5">
              <!-- VIEW MODE -->
              <div id="text-show-frame">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">
                  <?php echo htmlspecialchars((string)($albumdata['name'] ?? $albumTitle ?? ''), ENT_QUOTES); ?>
                </h2>

                <div class="prose prose-sm mt-4 max-w-none dark:prose-invert text-gray-700 dark:text-gray-300">
                  <?php echo (string)($descriptionHtml ?? ''); ?>
                </div>
              </div>

              <!-- EDIT MODE -->
              <form action="backend_api/album_update.php" method="post">
                <div id="text-edit-frame" class="hidden space-y-4 mt-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-800 dark:text-gray-200"><?php echo languageString('general.title'); ?></label>
                    <input
                      type="text"
                      name="album-title-edit"
                      value="<?php echo htmlspecialchars((string)($albumdata['name'] ?? $albumTitle ?? ''), ENT_QUOTES); ?>"
                      class="mt-1 w-full rounded-md border px-3 py-2 bg-white text-gray-900 border-gray-300
                             dark:bg-white/10 dark:text-white dark:border-white/10"
                    >
                    <input
                      type="hidden"
                      name="album-current-title"
                      value="<?php echo htmlspecialchars((string)($albumTitle ?? ''), ENT_QUOTES); ?>"
                    >
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-800 dark:text-gray-200"><?php echo languageString('general.description'); ?></label>
                    <textarea
                      name="album-description"
                      rows="6"
                      class="mt-1 w-full rounded-md border px-3 py-2 bg-white text-gray-900 border-gray-300
                             dark:bg-white/10 dark:text-white dark:border-white/10"
                    ><?php echo htmlspecialchars((string)($albumdata['description'] ?? ''), ENT_QUOTES); ?></textarea>
                  </div>
                </div>

                <!-- BUTTONS -->
                <div class="mt-4 flex gap-2">
                  <div id="normal-group" class="flex gap-2">
                    <button
                      type="button"
                      id="edit_text"
                      class="rounded-md bg-cyan-600 px-3 py-2 text-sm text-white hover:bg-cyan-500"
                    >
                      <?php echo languageString('general.edit'); ?>
                    </button>

                    <button
                      type="button"
                      id="openDeleteAlbumModal"
                      class="rounded-md px-3 py-2 text-sm text-red-600 hover:bg-red-100 dark:hover:bg-red-500/10"
                    >
                      <?php echo languageString('album.deleteAlbum'); ?>
                    </button>
                  </div>

                  <div id="edit-group" class="hidden flex gap-2">
                    <button
                      type="submit"
                      class="rounded-md bg-cyan-600 px-3 py-2 text-sm text-white hover:bg-cyan-500"
                    >
                      <?php echo languageString('general.save'); ?>
                    </button>
                    <button
                      type="button"
                      id="cancel_edit"
                      class="rounded-md bg-gray-200 px-3 py-2 text-sm hover:bg-gray-300 dark:bg-white/10 dark:hover:bg-white/15 text-gray-900 dark:text-white"
                    >
                      <?php echo languageString('general.cancel'); ?>
                    </button>
                  </div>
                </div>
              </form>

            </div>
          </div>
        </section>

        <!-- RIGHT: IMAGE GALLERY -->
        <section class="lg:col-span-8">
          <div class="flex flex-wrap gap-4">
            <?php renderImageGalleryAlbum((string)$albumTitle); ?>
          </div>
        </section>

      </div>
    </div>
  </main>

  <!-- ================= MODALE ================== -->$_COOKIE

  <el-dialog>
  <div id="deleteAlbumModal"
       class="hidden fixed inset-0 z-50"
       role="dialog"
       aria-modal="true"
       aria-labelledby="delete-album-title">

    <el-dialog-backdrop
      class="fixed inset-0 bg-black/50 backdrop-blur-[2px] transition-opacity">
    </el-dialog-backdrop>

    <div tabindex="0" class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
      <el-dialog-panel
        class="relative w-full max-w-lg transform overflow-hidden rounded-lg
               bg-white dark:bg-black px-6 py-5 text-left shadow-xl transition-all
               sm:my-8 sm:p-6
               dark:outline dark:-outline-offset-1 dark:outline-white/10
               filter-none backdrop-blur-none">

        <h2 id="delete-album-title"
            class="text-base font-semibold text-gray-900 dark:text-white">
          <?php echo languageString('album.deleteAlbum'); ?>
        </h2>

        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
          <?php echo languageString('album.deleteAlbumText'); ?>
        </p>

        <div class="mt-6 flex justify-end gap-2">
          <button id="cancelDeleteAlbum" type="button"
                  class="px-3 py-2 text-sm rounded-md
                         bg-white inset-ring-1 inset-ring-gray-300 hover:bg-gray-50
                         dark:bg-white/10 dark:text-white
                         dark:inset-ring-white/5 dark:hover:bg-white/20">
            <?php echo languageString('general.cancel'); ?>
          </button>

          <a href="backend_api/delete.php?type=album&filename=<?php echo urlencode((string)$albumTitle); ?>"
             class="px-3 py-2 text-sm rounded-md bg-red-600 text-white hover:bg-red-500">
            <?php echo languageString('general.delete'); ?>
          </a>
        </div>

      </el-dialog-panel>
    </div>
  </div>
</el-dialog>

<el-dialog>
  <div id="deleteImageModal"
       class="hidden fixed inset-0 z-50"
       role="dialog"
       aria-modal="true"
       aria-labelledby="delete-image-title">

    <el-dialog-backdrop
      class="fixed inset-0 bg-black/50 backdrop-blur-[2px] transition-opacity">
    </el-dialog-backdrop>

    <div tabindex="0" class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
      <el-dialog-panel
        class="relative w-full max-w-md transform overflow-hidden rounded-lg
               bg-white dark:bg-black px-6 py-5 text-left shadow-xl transition-all
               sm:my-8 sm:p-6
               dark:outline dark:-outline-offset-1 dark:outline-white/10
               filter-none backdrop-blur-none">

        <h2 id="delete-image-title"
            class="text-base font-semibold text-gray-900 dark:text-white">
          <?php echo languageString('album.removeFromAlbum'); ?>
        </h2>

        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
          <?php echo languageString('album.removeFromAlbumText'); ?>
        </p>

        <div class="mt-6 flex justify-end gap-2">
          <button id="cancelDeleteImage" type="button"
                  class="px-3 py-2 text-sm rounded-md
                         bg-white inset-ring-1 inset-ring-gray-300 hover:bg-gray-50
                         dark:bg-white/10 dark:text-white
                         dark:inset-ring-white/5 dark:hover:bg-white/20">
            <?php echo languageString('general.cancel'); ?>
          </button>

          <a id="confirmDeleteImage"
             href="#"
             class="px-3 py-2 text-sm rounded-md bg-red-600 text-white hover:bg-red-500">
            <?php echo languageString('general.delete'); ?>
          </a>
        </div>

      </el-dialog-panel>
    </div>
  </div>
</el-dialog>

<el-dialog>
  <div id="addImageModal"
       class="hidden fixed inset-0 z-50"
       role="dialog"
       aria-modal="true"
       aria-labelledby="add-image-title">

    <el-dialog-backdrop
      class="fixed inset-0 bg-black/50 backdrop-blur-[2px] transition-opacity">
    </el-dialog-backdrop>

    <div tabindex="0" class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
      <el-dialog-panel
        class="relative w-full max-w-5xl transform overflow-hidden rounded-lg
               bg-white dark:bg-black px-6 py-5 text-left shadow-xl transition-all
               sm:my-8 sm:p-6
               dark:outline dark:-outline-offset-1 dark:outline-white/10
               filter-none backdrop-blur-none">

        <div class="flex items-start justify-between gap-4 mb-4">
          <h2 id="add-image-title" class="text-lg font-semibold text-gray-900 dark:text-white">
            <?php echo languageString('album.addToAlbum'); ?>
          </h2>

          <button type="button" id="closeAddImageModal"
                  class="rounded-md p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </button>
        </div>

        <input type="text"
               id="addImageSearch"
               placeholder="Search images…"
               class="mb-4 w-full rounded-md px-3 py-2 text-sm
                      border border-gray-300 dark:border-white/10
                      bg-white dark:bg-white/10 text-gray-900 dark:text-white">

        <form method="post" action="backend_api/add_images_to_album.php">
          <input type="hidden" name="album"
                 value="<?php echo htmlspecialchars((string)$albumTitle, ENT_QUOTES); ?>">

          <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5
                      gap-4 max-h-[60vh] overflow-y-auto">

            <?php foreach (getAllUploadedImages() as $img): ?>
              <label class="cursor-pointer text-center image-item"
                     data-name="<?php echo htmlspecialchars(mb_strtolower($img['title'] ?? $img['filename']), ENT_QUOTES); ?>">

                <input type="checkbox"
                       name="images[]"
                       value="<?php echo htmlspecialchars($img['filename'], ENT_QUOTES); ?>"
                       class="sr-only peer">

                <div class="rounded overflow-hidden border border-black/10 dark:border-white/10
                            peer-checked:ring-2 peer-checked:ring-cyan-500">
                  <img src="../userdata/content/images/<?php echo htmlspecialchars($img['filename'], ENT_QUOTES); ?>"
                       class="aspect-square object-cover w-full"
                       loading="lazy" alt="">
                </div>

                <span class="block mt-1 text-xs truncate text-gray-700 dark:text-gray-300">
                  <?php echo htmlspecialchars($img['title'] ?? $img['filename'], ENT_QUOTES); ?>
                </span>
              </label>
            <?php endforeach; ?>

          </div>

          <div class="mt-6 flex justify-end gap-2">
            <button type="button" id="cancelAddImageModal"
                    class="px-3 py-2 text-sm rounded-md
                           bg-white inset-ring-1 inset-ring-gray-300 hover:bg-gray-50
                           dark:bg-white/10 dark:text-white
                           dark:inset-ring-white/5 dark:hover:bg-white/20">
              <?php echo languageString('general.cancel'); ?>
            </button>

            <button type="submit"
                    class="px-3 py-2 text-sm rounded-md bg-cyan-600 text-white hover:bg-cyan-500">
              <?php echo languageString('album.addSelectedImage'); ?>
            </button>
          </div>
        </form>

      </el-dialog-panel>
    </div>
  </div>
</el-dialog>


  <!-- ================= SCRIPTS ================= -->
   <script src="js/tailwind.js"></script>
   <script>
(() => {
  const modal = document.getElementById('addImageModal');

  document.getElementById('addImagetoAlbumBtn')?.addEventListener('click', () => {
    modal.classList.remove('hidden');
  });

  document.getElementById('closeAddImageModal')?.addEventListener('click', () => {
    modal.classList.add('hidden');
  });

  document.getElementById('cancelAddImageModal')?.addEventListener('click', () => {
    modal.classList.add('hidden');
  });

  // Close on backdrop click
  modal?.addEventListener('click', (e) => {
    if (e.target === modal) modal.classList.add('hidden');
  });
})();
</script>

  <script>
    // Toggle edit mode
    (() => {
      const show = document.getElementById('text-show-frame');
      const edit = document.getElementById('text-edit-frame');
      const normal = document.getElementById('normal-group');
      const editg = document.getElementById('edit-group');

      document.getElementById('edit_text')?.addEventListener('click', () => {
        show?.classList.add('hidden');
        edit?.classList.remove('hidden');
        normal?.classList.add('hidden');
        editg?.classList.remove('hidden');
      });

      document.getElementById('cancel_edit')?.addEventListener('click', () => {
        edit?.classList.add('hidden');
        show?.classList.remove('hidden');
        editg?.classList.add('hidden');
        normal?.classList.remove('hidden');
      });
    })();
  </script>

  <script>
    // Dropdown per image
    document.addEventListener('click', (e) => {
      document.querySelectorAll('.dropdown').forEach(d => d.classList.add('hidden'));
      const btn = e.target.closest('button[data-filename]');
      if (btn) {
        const menu = btn.closest('.relative')?.querySelector('.dropdown');
        if (menu) {
          menu.classList.toggle('hidden');
          e.stopPropagation();
        }
      }
    });
  </script>

  <script>
    // Image delete confirm modal
    (() => {
      const modal = document.getElementById('deleteImageModal');
      const confirm = document.getElementById('confirmDeleteImage');

      document.addEventListener('click', (e) => {
        const link = e.target.closest('.image-delete-link');
        if (!link) return;
        e.preventDefault();
        confirm.href = link.href;
        modal.classList.remove('hidden');
      });

      document.getElementById('cancelDeleteImage')?.addEventListener('click', () => {
        modal.classList.add('hidden');
      });

      // close on backdrop click
      modal?.addEventListener('click', (e) => {
        if (e.target === modal) modal.classList.add('hidden');
      });
    })();
  </script>

  <script>
    // Album delete confirm modal
    (() => {
      const modal = document.getElementById('deleteAlbumModal');

      document.getElementById('openDeleteAlbumModal')?.addEventListener('click', () => {
        modal.classList.remove('hidden');
      });

      document.getElementById('cancelDeleteAlbum')?.addEventListener('click', () => {
        modal.classList.add('hidden');
      });

      // close on backdrop click
      modal?.addEventListener('click', (e) => {
        if (e.target === modal) modal.classList.add('hidden');
      });
    })();
  </script>


</body>
</html>
