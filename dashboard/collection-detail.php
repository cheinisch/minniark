<?php
require_once(__DIR__ . "/../functions/function_backend.php");
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/autoload.php';

security_checklogin();

use Symfony\Component\Yaml\Yaml;

$slug = $_GET['collection'] ?? null;
$collectiondata = getCollectionData($slug);

// Markdown (Beschreibung)
$Parsedown = new Parsedown();
$description = $collectiondata['description'] ?? '';
$descriptionHtml = $Parsedown->text($description);

// Slugs / Titles
$collectionTitle     = generateSlug($collectiondata['name'] ?? '');
$collectionFile      = strtolower($collectionTitle);
$collectionTitleSlug = generateSlug($collectiondata['name'] ?? '');
$albumsInCollection  = $collectiondata['albums'] ?? [];

// Cover
$headimage = "img/placeholder.png";
if (!empty($collectiondata['image'])) {
  $cacheImage = get_cacheimage($collectiondata['image'], "l");
  $headimage  = "../cache/images/" . $cacheImage;
}

// IMPORTANT:
// This is a COLLECTION page. There is no $albumTitle here.
// The "add_images_to_album.php" modal would normally live on album-detail.php.
// To avoid warnings, we set a safe fallback.
$albumTitle = $albumTitle ?? '';
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
        <!-- Buttons -->
        <button type="button" id="selectCollectionImageBtn"
          class="inline-flex items-center gap-2 -m-2.5 p-2.5 text-gray-800 hover:text-gray-500 dark:text-gray-400 dark:hover:text-gray-300">
          Select cover
        </button>

        <button type="button" id="addAlbumtoCollectionBtn"
          class="inline-flex items-center gap-2 -m-2.5 p-2.5 text-gray-800 hover:text-gray-500 dark:text-gray-400 dark:hover:text-gray-300">
          Add album
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
  <main class="py-10 bg-white dark:bg-black">
    <div class="px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Collection info -->
        <section class="lg:col-span-4">
          <div class="rounded-lg overflow-hidden bg-white dark:bg-black shadow-sm dark:outline dark:-outline-offset-1 dark:outline-white/10">
            <img src="<?php echo htmlspecialchars($headimage, ENT_QUOTES); ?>"
                 class="w-full aspect-video object-cover" alt="Collection cover">

            <div class="p-5">
              <div id="text-show-frame">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">
                  <?php echo htmlspecialchars($collectiondata['name'] ?? '', ENT_QUOTES); ?>
                </h2>

                <div class="prose prose-sm mt-4 max-w-none dark:prose-invert text-gray-700 dark:text-gray-300">
                  <?php echo $descriptionHtml; ?>
                </div>
              </div>

              <form action="backend_api/collection_update.php" method="post" class="mt-4">
                <div id="text-edit-frame" class="hidden space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-gray-200">Title</label>
                    <input type="text" name="collection-title-edit"
                           value="<?php echo htmlspecialchars($collectiondata['name'] ?? '', ENT_QUOTES); ?>"
                           class="mt-2 block w-full rounded-md bg-white/5 px-3 py-2 text-base text-gray-900 dark:text-white
                                  outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-cyan-600
                                  dark:bg-white/10 dark:outline-white/10">
                    <input type="hidden" name="collection-current-title"
                           value="<?php echo htmlspecialchars($collectiondata['name'] ?? '', ENT_QUOTES); ?>">
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-gray-200">Description</label>
                    <textarea name="collection-description" rows="6"
                              class="mt-2 block w-full rounded-md bg-white/5 px-3 py-2 text-base text-gray-900 dark:text-white
                                     outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-cyan-600
                                     dark:bg-white/10 dark:outline-white/10"><?php echo htmlspecialchars($collectiondata['description'] ?? '', ENT_QUOTES); ?></textarea>
                  </div>
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
                  <div id="normal-group" class="flex gap-2">
                    <button type="button" id="edit_text"
                            class="rounded-md bg-cyan-600 px-3 py-2 text-sm font-semibold text-white hover:bg-cyan-500">
                      Edit
                    </button>

                    <a href="backend_api/delete.php?type=collection&filename=<?php echo urlencode(generateSlug($collectiondata['name'] ?? '')); ?>"
                       class="delete-link rounded-md px-3 py-2 text-sm font-semibold text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10">
                      Delete Collection
                    </a>
                  </div>

                  <div id="edit-group" class="hidden flex gap-2">
                    <button type="submit"
                            class="rounded-md bg-cyan-600 px-3 py-2 text-sm font-semibold text-white hover:bg-cyan-500">
                      Save
                    </button>

                    <button type="button" id="cancel_edit"
                            class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 inset-ring-1 inset-ring-gray-300 hover:bg-gray-50
                                   dark:bg-white/10 dark:text-white dark:inset-ring-white/5 dark:hover:bg-white/20">
                      Cancel
                    </button>
                  </div>
                </div>
              </form>

            </div>
          </div>
        </section>

        <!-- Gallery -->
        <section class="lg:col-span-8" id="image-list">
          <div class="flex flex-wrap gap-6 justify-center md:justify-start">
            <?php renderImageGalleryCollection($collectionFile); ?>
          </div>
        </section>

      </div>
    </div>
  </main>

  <!-- =================== MODALS =================== -->

  <!-- =================== SELECT COVER IMAGE MODAL (EL) =================== -->
  <el-dialog>
    <div id="addToAlbumImageModal"
         class="hidden fixed inset-0 z-50"
         role="dialog"
         aria-modal="true"
         aria-labelledby="cover-modal-title">

      <el-dialog-backdrop class="fixed inset-0 bg-black/50 backdrop-blur-[2px] transition-opacity"></el-dialog-backdrop>

      <div tabindex="0" class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <el-dialog-panel
          class="relative w-full max-w-5xl transform overflow-hidden rounded-lg
                 bg-white dark:bg-black px-6 py-5 text-left shadow-xl transition-all
                 sm:my-8 sm:p-6
                 dark:outline dark:-outline-offset-1 dark:outline-white/10
                 filter-none backdrop-blur-none">

          <div class="flex items-start justify-between gap-4">
            <div>
              <h2 id="cover-modal-title" class="text-lg font-semibold text-gray-900 dark:text-white">
                Select cover image
              </h2>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Choose one image from albums inside this collection.
              </p>
            </div>

            <button type="button" id="closeAddToAlbumImageModal"
                    class="rounded-md p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
              <span class="sr-only">Close</span>
              <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </button>
          </div>

          <input type="text" id="imageSearchInput"
                 placeholder="Search by image name..."
                 class="mt-4 w-full rounded-md px-3 py-2 text-sm
                        border border-gray-300 dark:border-white/10
                        bg-white dark:bg-white/10 text-gray-900 dark:text-white">

          <form method="post" action="backend_api/collection_set_hero.php" class="mt-4">
            <input type="hidden" name="slug" value="<?php echo htmlspecialchars($slug ?? '', ENT_QUOTES); ?>">

            <div id="imageList"
                 class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 max-h-[60vh] overflow-y-auto">
              <?php
                $imageDir  = __DIR__ . '/../userdata/content/images/';
                $cachePath = '../cache/images/';

                foreach ($albumsInCollection as $albumSlug) {
                  $album = getAlbumData($albumSlug);

                  foreach (($album['images'] ?? []) as $imgName) {
                    $ymlPath = $imageDir . pathinfo($imgName, PATHINFO_FILENAME) . '.yml';
                    if (!file_exists($ymlPath)) continue;

                    try {
                      $yamlData = Yaml::parseFile($ymlPath);
                      $meta = $yamlData['image'] ?? [];
                    } catch (Exception $e) {
                      continue;
                    }

                    if (empty($meta['guid'])) continue;

                    $title = $meta['title'] ?? $imgName;
                    $thumb = $cachePath . $meta['guid'] . '_S.jpg';

                    echo '
                      <label class="block text-sm text-center cursor-pointer cover-item"
                             data-name="' . htmlspecialchars(mb_strtolower($title), ENT_QUOTES) . '">
                        <input type="radio" name="image"
                               value="' . htmlspecialchars($imgName, ENT_QUOTES) . '"
                               class="sr-only peer">
                        <div class="rounded overflow-hidden border border-black/10 dark:border-white/10
                                    peer-checked:ring-2 peer-checked:ring-cyan-500">
                          <img src="' . htmlspecialchars($thumb, ENT_QUOTES) . '"
                               alt="' . htmlspecialchars($title, ENT_QUOTES) . '"
                               class="object-cover w-full aspect-square" loading="lazy">
                        </div>
                        <span class="block mt-1 truncate text-xs text-gray-700 dark:text-gray-300">'
                          . htmlspecialchars($title, ENT_QUOTES) .
                        '</span>
                      </label>';
                  }
                }
              ?>
            </div>

            <div class="mt-6 flex justify-end gap-2">
              <button type="button" id="cancelAddToAlbumImage"
                      class="px-3 py-2 text-sm rounded-md
                             bg-white inset-ring-1 inset-ring-gray-300 hover:bg-gray-50
                             dark:bg-white/10 dark:text-white
                             dark:inset-ring-white/5 dark:hover:bg-white/20">
                Cancel
              </button>
              <button type="submit"
                      class="px-3 py-2 text-sm rounded-md bg-cyan-600 text-white hover:bg-cyan-500">
                Save cover
              </button>
            </div>
          </form>

        </el-dialog-panel>
      </div>
    </div>
  </el-dialog>

  <!-- =================== ADD ALBUMS TO COLLECTION MODAL (EL) =================== -->
  <el-dialog>
    <div id="addTocollectionAlbumModal"
         class="hidden fixed inset-0 z-50"
         role="dialog"
         aria-modal="true"
         aria-labelledby="album-modal-title">

      <el-dialog-backdrop class="fixed inset-0 bg-black/50 backdrop-blur-[2px] transition-opacity"></el-dialog-backdrop>

      <div tabindex="0" class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <el-dialog-panel
          class="relative w-full max-w-5xl transform overflow-hidden rounded-lg
                 bg-white dark:bg-black px-6 py-5 text-left shadow-xl transition-all
                 sm:my-8 sm:p-6
                 dark:outline dark:-outline-offset-1 dark:outline-white/10
                 filter-none backdrop-blur-none">

          <div class="flex items-start justify-between gap-4">
            <div>
              <h2 id="album-modal-title" class="text-lg font-semibold text-gray-900 dark:text-white">
                Add albums to collection
              </h2>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Select one or more albums.
              </p>
            </div>

            <button type="button" id="closeAddTocollectionAlbumModal"
                    class="rounded-md p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
              <span class="sr-only">Close</span>
              <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </button>
          </div>

          <input type="text" id="albumSearchInput"
                 placeholder="Search by album name..."
                 class="mt-4 w-full rounded-md px-3 py-2 text-sm
                        border border-gray-300 dark:border-white/10
                        bg-white dark:bg-white/10 text-gray-900 dark:text-white">

          <form method="post" action="backend_api/add_albums_to_collection.php" class="mt-4">
            <input type="hidden" name="collection" value="<?php echo htmlspecialchars($collectionTitleSlug, ENT_QUOTES); ?>">

            <div id="albumList" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 max-h-[60vh] overflow-y-auto">
              <?php
                $allAlbums = getAlbumList();

                foreach ($allAlbums as $a) {
                  $thumb = 'img/placeholder.png';
                  if (!empty($a['image'])) {
                    $tmp = get_cacheimage($a['image'], 'M');
                    $thumb = '../cache/images/' . $tmp;
                  }

                  $title = $a['title'] ?? $a['name'] ?? $a['slug'];

                  echo '
                    <label class="block text-sm text-center cursor-pointer album-item"
                           data-name="' . htmlspecialchars(mb_strtolower($title), ENT_QUOTES) . '">
                      <input type="checkbox" name="albums[]" value="' . htmlspecialchars($a['slug'], ENT_QUOTES) . '" class="sr-only peer">
                      <div class="peer-checked:ring-2 peer-checked:ring-cyan-500 rounded overflow-hidden border border-black/10 dark:border-white/10">
                        <img src="' . htmlspecialchars($thumb, ENT_QUOTES) . '" alt="' . htmlspecialchars($title, ENT_QUOTES) . '" class="object-cover w-full aspect-square">
                      </div>
                      <span class="block mt-1 truncate text-xs text-gray-700 dark:text-gray-300">' . htmlspecialchars($title, ENT_QUOTES) . '</span>
                    </label>';
                }
              ?>
            </div>

            <div class="mt-6 flex justify-end gap-2">
              <button type="button" id="cancelAddTocollectionAlbum"
                      class="px-3 py-2 text-sm rounded-md
                             bg-white inset-ring-1 inset-ring-gray-300 hover:bg-gray-50
                             dark:bg-white/10 dark:text-white
                             dark:inset-ring-white/5 dark:hover:bg-white/20">
                Cancel
              </button>
              <button type="submit"
                      class="px-3 py-2 text-sm rounded-md bg-cyan-600 text-white hover:bg-cyan-500">
                Add selected
              </button>
            </div>
          </form>

        </el-dialog-panel>
      </div>
    </div>
  </el-dialog>

  <!-- =================== CONFIRM DELETE COLLECTION MODAL (EL) =================== -->
  <el-dialog>
    <div id="confirmModalcollection"
         class="hidden fixed inset-0 z-50"
         role="dialog"
         aria-modal="true"
         aria-labelledby="confirm-delete-title">

      <el-dialog-backdrop class="fixed inset-0 bg-black/50 backdrop-blur-[2px] transition-opacity"></el-dialog-backdrop>

      <div tabindex="0" class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <el-dialog-panel
          class="relative w-full max-w-lg transform overflow-hidden rounded-lg
                 bg-white dark:bg-black px-6 py-5 text-left shadow-xl transition-all
                 sm:my-8 sm:p-6
                 dark:outline dark:-outline-offset-1 dark:outline-white/10
                 filter-none backdrop-blur-none">

          <h2 id="confirm-delete-title" class="text-base font-semibold text-gray-900 dark:text-white">
            Delete collection
          </h2>
          <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
            Do you really want to delete this collection?
          </p>

          <div class="mt-6 flex justify-end gap-2">
            <button id="confirmcollectionNo" type="button"
                    class="px-3 py-2 text-sm rounded-md
                           bg-white inset-ring-1 inset-ring-gray-300 hover:bg-gray-50
                           dark:bg-white/10 dark:text-white
                           dark:inset-ring-white/5 dark:hover:bg-white/20">
              <?php echo languageString('general.cancel'); ?>
            </button>
            <button id="confirmcollectionYes" type="button"
                    class="px-3 py-2 text-sm rounded-md bg-red-600 text-white hover:bg-red-500">
              Delete
            </button>
          </div>

        </el-dialog-panel>
      </div>
    </div>
  </el-dialog>

</div><!-- /lg:pl-72 -->

<script src="js/tailwind.js"></script>
<script src="js/collection_edit.js"></script>

<script>
/* Open/close modals + ESC */
(() => {
  const coverModal = document.getElementById('addToAlbumImageModal');
  const openCover  = document.getElementById('selectCollectionImageBtn');
  const closeCover = document.getElementById('closeAddToAlbumImageModal');
  const cancelCover= document.getElementById('cancelAddToAlbumImage');

  const albumsModal = document.getElementById('addTocollectionAlbumModal');
  const openAlbums  = document.getElementById('addAlbumtoCollectionBtn');
  const closeAlbums = document.getElementById('closeAddTocollectionAlbumModal');
  const cancelAlbums= document.getElementById('cancelAddTocollectionAlbum');

  const addImagesModal = document.getElementById('albumImagesModal');
  const openAddImages  = document.getElementById('openAlbumImagesModal');
  const closeAddImages = document.getElementById('closeAlbumImagesModal');
  const cancelAddImages= document.getElementById('cancelAlbumImagesModal');

  const open  = (m) => m?.classList.remove('hidden');
  const close = (m) => m?.classList.add('hidden');

  openCover?.addEventListener('click', () => open(coverModal));
  closeCover?.addEventListener('click', () => close(coverModal));
  cancelCover?.addEventListener('click', () => close(coverModal));

  openAlbums?.addEventListener('click', () => open(albumsModal));
  closeAlbums?.addEventListener('click', () => close(albumsModal));
  cancelAlbums?.addEventListener('click', () => close(albumsModal));

  openAddImages?.addEventListener('click', () => open(addImagesModal));
  closeAddImages?.addEventListener('click', () => close(addImagesModal));
  cancelAddImages?.addEventListener('click', () => close(addImagesModal));

  // Backdrop click close (only when clicking the root overlay)
  [coverModal, albumsModal, addImagesModal].forEach(m => {
    m?.addEventListener('click', (e) => { if (e.target === m) close(m); });
  });

  document.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape') return;
    close(coverModal);
    close(albumsModal);
    close(addImagesModal);
  });
})();
</script>

<script>
/* Search: cover images + albums */
(() => {
  const imgSearch = document.getElementById('imageSearchInput');
  const imgList   = document.getElementById('imageList');

  imgSearch?.addEventListener('input', () => {
    const q = (imgSearch.value || '').toLowerCase().trim();
    imgList?.querySelectorAll('.cover-item').forEach(el => {
      const name = el.getAttribute('data-name') || '';
      el.style.display = name.includes(q) ? '' : 'none';
    });
  });

  const albSearch = document.getElementById('albumSearchInput');
  const albList   = document.getElementById('albumList');

  albSearch?.addEventListener('input', () => {
    const q = (albSearch.value || '').toLowerCase().trim();
    albList?.querySelectorAll('.album-item').forEach(el => {
      const name = el.getAttribute('data-name') || '';
      el.style.display = name.includes(q) ? '' : 'none';
    });
  });
})();
</script>

<script>
/* Confirm delete collection */
(() => {
  let pending = null;
  const modal = document.getElementById('confirmModalcollection');
  const yes   = document.getElementById('confirmcollectionYes');
  const no    = document.getElementById('confirmcollectionNo');

  if (!modal || !yes || !no) return;

  document.addEventListener('click', (e) => {
    const a = e.target.closest('a.delete-link');
    if (!a) return;
    e.preventDefault();
    pending = a.href;
    modal.classList.remove('hidden');
  });

  no.addEventListener('click', () => {
    pending = null;
    modal.classList.add('hidden');
  });

  yes.addEventListener('click', () => {
    const href = pending;
    pending = null;
    modal.classList.add('hidden');
    if (href) window.location.assign(href);
  });

  modal.addEventListener('click', (e) => {
    if (e.target === modal) { pending = null; modal.classList.add('hidden'); }
  });
})();
</script>

<script>
/* Add images to album modal: search filter */
(() => {
  const search = document.getElementById('imageSearchInputAdd');
  const list   = document.getElementById('imageListAdd');

  search?.addEventListener('input', () => {
    const q = (search.value || '').toLowerCase().trim();
    list?.querySelectorAll('.image-item').forEach(el => {
      const name = el.getAttribute('data-name') || '';
      el.style.display = name.includes(q) ? '' : 'none';
    });
  });
})();
</script>

</body>
</html>
