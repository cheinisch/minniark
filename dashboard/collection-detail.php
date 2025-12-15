<?php
  require_once( __DIR__ . "/../functions/function_backend.php");
  require_once __DIR__ . '/../vendor/autoload.php'; // Pfad zu Parsedown
  security_checklogin();

  $slug = isset($_GET['collection']) ? $_GET['collection'] : null;

  $collectiondata = getCollectionData($slug);

  $Parsedown = new Parsedown();
  $description = $collectiondata['description'] ?? '';
  $descriptionHtml = $Parsedown->text($description);

  $collectionTitle = generateSlug($collectiondata['name']);

  $image = $collectiondata['image'] ?? '';

  $headimage = null;

  if($collectiondata['image'] != null || $collectiondata['image'] != '')
  {
    $cacheImage = get_cacheimage($collectiondata['image'],"l");
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
								<img src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=indigo&shade=500" alt="Minniark" class="h-8 w-auto" />
							</div>
							<nav class="relative flex flex-1 flex-col">
								<?php include (__DIR__.'/layout/media_menu.php'); ?>
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
					<?php include (__DIR__.'/layout/media_menu.php'); ?>
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
								class="inline-flex items-center justify-start mx-2 py-2 border-b-2 border-gray-800 dark:border-gray-400 rounded-none
										no-underline text-base font-normal leading-tight appearance-none">
								<?php echo languageString('nav.images'); ?>
							</a>
							<a href="blog.php"
								class="inline-flex items-center justify-start mx-4 py-2 border-b border-gray-800 dark:border-gray-400 rounded-none
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
						<button type="button"
						id="uploadImageButton"
						class="inline-flex items-center gap-2 -m-2.5 p-2.5 text-gray-800 hover:text-gray-500 dark:text-gray-400 dark:hover:text-gray-300">
							<?php echo languageString('image.upload_image'); ?>
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-upload" viewBox="0 0 16 16">
								<path d="M8.5 11.5a.5.5 0 0 1-1 0V7.707L6.354 8.854a.5.5 0 1 1-.708-.708l2-2a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 7.707z"/>
  								<path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2M9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
							</svg>
							<span class="sr-only">Upload new Image</span>
							</button>
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

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

      <!-- Collection info -->
      <section class="lg:col-span-4">
        <div class="rounded-lg overflow-hidden bg-white dark:bg-black shadow-sm dark:outline dark:-outline-offset-1 dark:outline-white/10">
          <img src="<?php echo htmlspecialchars($headimage); ?>" class="w-full aspect-video object-cover" alt="Collection cover">

          <div class="p-5">
            <!-- view -->
            <div id="text-show-frame">
              <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">
                <?php echo htmlspecialchars($collectiondata['name'] ?? ''); ?>
              </h2>
              <div class="prose prose-sm mt-4 max-w-none dark:prose-invert text-gray-700 dark:text-gray-300">
                <?php echo $descriptionHtml; ?>
              </div>
            </div>

            <!-- edit -->
            <form action="backend_api/collection_update.php" method="post" class="mt-4">
              <div id="text-edit-frame" class="hidden space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-900 dark:text-gray-200">Title</label>
                  <input type="text" name="collection-title-edit" value="<?php echo htmlspecialchars($collectiondata['name'] ?? ''); ?>"
                    class="mt-2 block w-full rounded-md bg-white/5 px-3 py-2 text-base text-gray-900 dark:text-white
                           outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600
                           dark:bg-white/10 dark:outline-white/10">
                  <input type="hidden" name="collection-current-title" value="<?php echo htmlspecialchars($collectiondata['name'] ?? ''); ?>">
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-900 dark:text-gray-200">Description</label>
                  <textarea name="collection-description" rows="6"
                    class="mt-2 block w-full rounded-md bg-white/5 px-3 py-2 text-base text-gray-900 dark:text-white
                           outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600
                           dark:bg-white/10 dark:outline-white/10"><?php echo htmlspecialchars($collectiondata['description'] ?? ''); ?></textarea>
                </div>
              </div>

              <div class="mt-4 flex flex-wrap gap-2">
                <div id="normal-group" class="flex gap-2">
                  <button type="button" id="edit_text"
                    class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                    Edit
                  </button>

                  <a href="backend_api/delete.php?type=collection&filename=<?php echo urlencode(generateSlug($collectiondata['name'] ?? '')); ?>"
                     class="delete-link rounded-md px-3 py-2 text-sm font-semibold text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10">
                    Delete Collection
                  </a>
                </div>

                <div id="edit-group" class="hidden flex gap-2">
                  <button type="submit"
                    class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
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

<!-- Select Cover Image Modal -->
<el-dialog>
  <div id="addToAlbumImageModal" class="hidden fixed inset-0 z-50" role="dialog" aria-modal="true">
    <el-dialog-backdrop class="fixed inset-0 bg-black/50 backdrop-blur-[2px]"></el-dialog-backdrop>

    <div class="flex min-h-full items-center justify-center p-4">
      <div class="w-full max-w-5xl rounded-lg bg-white dark:bg-black p-6 shadow-xl dark:outline dark:-outline-offset-1 dark:outline-white/10">

        <div class="flex items-start justify-between gap-4">
          <div>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Select cover image</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Choose one image from albums inside this collection.</p>
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
          class="mt-4 w-full rounded-md px-3 py-2 text-sm border border-gray-300 dark:border-white/10 bg-white dark:bg-white/10 text-gray-900 dark:text-white">

        <form method="post" action="backend_api/collection_set_hero.php" class="mt-4">
          <input type="hidden" name="slug" value="<?php echo htmlspecialchars($slug); ?>">

          <div id="imageList" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 max-h-[60vh] overflow-y-auto">
            <?php
            // Erwartet: $albumsInCollection (array mit album slugs)
            $imageDir  = __DIR__ . '/../userdata/content/images/';
            $cachePath = '/cache/images/';

            require_once __DIR__ . '/../vendor/autoload.php';
            use Symfony\Component\Yaml\Yaml;

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

                $title = htmlspecialchars($meta['title'] ?? $imgName);
                $filename = htmlspecialchars($imgName);
                $thumb = $cachePath . $meta['guid'] . '_S.jpg';

                echo '
                  <label class="block text-sm text-center cursor-pointer cover-item" data-name="' . htmlspecialchars(mb_strtolower($title)) . '">
                    <input type="radio" name="image" value="' . $filename . '" class="sr-only peer">
                    <div class="peer-checked:ring-2 peer-checked:ring-indigo-500 rounded overflow-hidden border border-black/10 dark:border-white/10">
                      <img src="' . $thumb . '" alt="' . $title . '" class="object-cover w-full aspect-square">
                    </div>
                    <span class="block mt-1 truncate text-xs text-gray-700 dark:text-gray-300">' . $title . '</span>
                  </label>';
              }
            }
            ?>
          </div>

          <div class="mt-6 flex justify-end gap-2">
            <button type="button" id="cancelAddToAlbumImage"
              class="px-3 py-2 text-sm rounded-md bg-white inset-ring-1 inset-ring-gray-300 hover:bg-gray-50
                     dark:bg-white/10 dark:text-white dark:inset-ring-white/5 dark:hover:bg-white/20">
              Cancel
            </button>
            <button type="submit"
              class="px-3 py-2 text-sm rounded-md bg-indigo-600 text-white hover:bg-indigo-500">
              Save cover
            </button>
          </div>
        </form>

      </div>
    </div>
  </div>
</el-dialog>

<!-- Add Albums to Collection Modal -->
<el-dialog>
  <div id="addTocollectionAlbumModal" class="hidden fixed inset-0 z-50" role="dialog" aria-modal="true">
    <el-dialog-backdrop class="fixed inset-0 bg-black/50 backdrop-blur-[2px]"></el-dialog-backdrop>

    <div class="flex min-h-full items-center justify-center p-4">
      <div class="w-full max-w-5xl rounded-lg bg-white dark:bg-black p-6 shadow-xl dark:outline dark:-outline-offset-1 dark:outline-white/10">

        <div class="flex items-start justify-between gap-4">
          <div>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Add albums to collection</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Select one or more albums.</p>
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
          class="mt-4 w-full rounded-md px-3 py-2 text-sm border border-gray-300 dark:border-white/10 bg-white dark:bg-white/10 text-gray-900 dark:text-white">

        <form method="post" action="backend_api/add_albums_to_collection.php" class="mt-4">
          <input type="hidden" name="collection" value="<?php echo htmlspecialchars($collectionTitleSlug); ?>">

          <div id="albumList" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 max-h-[60vh] overflow-y-auto">
            <?php
            $allAlbums = getAlbumList();
            foreach ($allAlbums as $a) {
              $imageSrc = '';
              if (!empty($a['image'])) {
                $tmp = get_cacheimage($a['image'], 'M');
                $imageSrc = '../cache/images/' . $tmp;
              }
              $thumb = $imageSrc ?: 'img/placeholder.png';
              $title = $a['title'] ?? $a['name'] ?? $a['slug'];

              echo '
                <label class="block text-sm text-center cursor-pointer album-item" data-name="' . htmlspecialchars(mb_strtolower($title)) . '">
                  <input type="checkbox" name="albums[]" value="' . htmlspecialchars($a['slug']) . '" class="sr-only peer">
                  <div class="peer-checked:ring-2 peer-checked:ring-indigo-500 rounded overflow-hidden border border-black/10 dark:border-white/10">
                    <img src="' . htmlspecialchars($thumb) . '" alt="' . htmlspecialchars($title) . '" class="object-cover w-full aspect-square">
                  </div>
                  <span class="block mt-1 truncate text-xs text-gray-700 dark:text-gray-300">' . htmlspecialchars($title) . '</span>
                </label>';
            }
            ?>
          </div>

          <div class="mt-6 flex justify-end gap-2">
            <button type="button" id="cancelAddTocollectionAlbum"
              class="px-3 py-2 text-sm rounded-md bg-white inset-ring-1 inset-ring-gray-300 hover:bg-gray-50
                     dark:bg-white/10 dark:text-white dark:inset-ring-white/5 dark:hover:bg-white/20">
              Cancel
            </button>
            <button type="submit"
              class="px-3 py-2 text-sm rounded-md bg-indigo-600 text-white hover:bg-indigo-500">
              Add selected
            </button>
          </div>
        </form>

      </div>
    </div>
  </div>
</el-dialog>

<!-- Remove album from collection confirm (.confirm-link) -->
<el-dialog>
  <dialog id="confirmModal" class="fixed inset-0 size-auto max-h-none max-w-none overflow-y-auto bg-transparent backdrop:bg-transparent">
    <el-dialog-backdrop class="fixed inset-0 bg-black/50 backdrop-blur-[2px]"></el-dialog-backdrop>
    <div tabindex="0" class="flex min-h-full items-center justify-center p-4 text-center">
      <div class="w-full max-w-lg rounded-lg bg-white dark:bg-black p-6 shadow-xl dark:outline dark:-outline-offset-1 dark:outline-white/10">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Remove album</h2>
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Do you really want to remove this album from the collection?</p>
        <div class="mt-6 flex justify-end gap-2">
          <button id="confirmNo" type="button"
            class="px-3 py-2 text-sm rounded-md bg-white inset-ring-1 inset-ring-gray-300 hover:bg-gray-50
                   dark:bg-white/10 dark:text-white dark:inset-ring-white/5 dark:hover:bg-white/20">
            <?php echo languageString('general.cancel'); ?>
          </button>
          <button id="confirmYes" type="button"
            class="px-3 py-2 text-sm rounded-md bg-red-600 text-white hover:bg-red-500">
            Remove
          </button>
        </div>
      </div>
    </div>
  </dialog>
</el-dialog>

<!-- Delete collection confirm (.delete-link) -->
<el-dialog>
  <dialog id="confirmModalcollection" class="fixed inset-0 size-auto max-h-none max-w-none overflow-y-auto bg-transparent backdrop:bg-transparent">
    <el-dialog-backdrop class="fixed inset-0 bg-black/50 backdrop-blur-[2px]"></el-dialog-backdrop>
    <div tabindex="0" class="flex min-h-full items-center justify-center p-4 text-center">
      <div class="w-full max-w-lg rounded-lg bg-white dark:bg-black p-6 shadow-xl dark:outline dark:-outline-offset-1 dark:outline-white/10">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Delete collection</h2>
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Do you really want to delete this collection?</p>
        <div class="mt-6 flex justify-end gap-2">
          <button id="confirmcollectionNo" type="button"
            class="px-3 py-2 text-sm rounded-md bg-white inset-ring-1 inset-ring-gray-300 hover:bg-gray-50
                   dark:bg-white/10 dark:text-white dark:inset-ring-white/5 dark:hover:bg-white/20">
            <?php echo languageString('general.cancel'); ?>
          </button>
          <button id="confirmcollectionYes" type="button"
            class="px-3 py-2 text-sm rounded-md bg-red-600 text-white hover:bg-red-500">
            Delete
          </button>
        </div>
      </div>
    </div>
  </dialog>
</el-dialog>

<script src="js/tailwind.js"></script>
<script src="js/collection_edit.js"></script>

<script>
  // Open/close modals (Select Cover + Add Album)
  (() => {
    const coverModal = document.getElementById('addToAlbumImageModal');
    const openCover = document.getElementById('selectCollectionImageBtn');
    const closeCover = document.getElementById('closeAddToAlbumImageModal');
    const cancelCover = document.getElementById('cancelAddToAlbumImage');

    const albumModal = document.getElementById('addTocollectionAlbumModal');
    const openAlbum = document.getElementById('addAlbumtoCollectionBtn');
    const closeAlbum = document.getElementById('closeAddTocollectionAlbumModal');
    const cancelAlbum = document.getElementById('cancelAddTocollectionAlbum');

    const open = (m) => m?.classList.remove('hidden');
    const close = (m) => m?.classList.add('hidden');

    openCover?.addEventListener('click', () => open(coverModal));
    closeCover?.addEventListener('click', () => close(coverModal));
    cancelCover?.addEventListener('click', () => close(coverModal));

    openAlbum?.addEventListener('click', () => open(albumModal));
    closeAlbum?.addEventListener('click', () => close(albumModal));
    cancelAlbum?.addEventListener('click', () => close(albumModal));

    document.addEventListener('keydown', (e) => {
      if (e.key !== 'Escape') return;
      close(coverModal);
      close(albumModal);
    });
  })();
</script>

<script>
  // Search inside modals
  (() => {
    const imgSearch = document.getElementById('imageSearchInput');
    const imgList = document.getElementById('imageList');

    imgSearch?.addEventListener('input', () => {
      const q = (imgSearch.value || '').toLowerCase().trim();
      imgList?.querySelectorAll('.cover-item').forEach(el => {
        const name = el.getAttribute('data-name') || '';
        el.style.display = name.includes(q) ? '' : 'none';
      });
    });

    const albSearch = document.getElementById('albumSearchInput');
    const albList = document.getElementById('albumList');

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
  // Confirm modal for removing album from collection (.confirm-link)
  (() => {
    let pending = null;
    const dlg = document.getElementById('confirmModal');
    const yes = document.getElementById('confirmYes');
    const no = document.getElementById('confirmNo');

    if (!dlg || !yes || !no) return;

    document.addEventListener('click', (e) => {
      const a = e.target.closest('a.confirm-link');
      if (!a) return;
      e.preventDefault();
      pending = a.href;
      dlg.showModal?.();
    });

    no.addEventListener('click', () => {
      pending = null;
      if (dlg.open) dlg.close();
    });

    yes.addEventListener('click', () => {
      const href = pending;
      pending = null;
      if (dlg.open) dlg.close();
      if (href) window.location.assign(href);
    });

    dlg.addEventListener('close', () => pending = null);
  })();
</script>

<script>
  // Confirm modal for deleting collection (.delete-link)
  (() => {
    let pending = null;
    const dlg = document.getElementById('confirmModalcollection');
    const yes = document.getElementById('confirmcollectionYes');
    const no = document.getElementById('confirmcollectionNo');

    if (!dlg || !yes || !no) return;

    document.addEventListener('click', (e) => {
      const a = e.target.closest('a.delete-link');
      if (!a) return;
      e.preventDefault();
      pending = a.href;
      dlg.showModal?.();
    });

    no.addEventListener('click', () => {
      pending = null;
      if (dlg.open) dlg.close();
    });

    yes.addEventListener('click', () => {
      const href = pending;
      pending = null;
      if (dlg.open) dlg.close();
      if (href) window.location.assign(href);
    });

    dlg.addEventListener('close', () => pending = null);
  })();
</script>

<script>
  /**
   * Dropdowns in der Bilderliste (für dein Markup: button[data-filename] + .dropdown.hidden ...)
   * Fix: "..." öffnet Menü, Klick außerhalb schließt, ESC schließt.
   */
  (() => {
    const imageList = document.getElementById('image-list');
    if (!imageList) return;

    const closeAll = (except = null) => {
      imageList.querySelectorAll('.dropdown').forEach(dd => {
        if (dd !== except) dd.classList.add('hidden');
      });
    };

    imageList.addEventListener('click', (e) => {
      const btn = e.target.closest('button[data-filename]');
      if (btn) {
        e.preventDefault();
        e.stopPropagation();

        const wrap = btn.closest('.relative.inline-block');
        const dd = wrap?.querySelector('.dropdown');
        if (!dd) return;

        const willOpen = dd.classList.contains('hidden');
        closeAll(dd);
        dd.classList.toggle('hidden', !willOpen);
        return;
      }

      if (e.target.closest('.dropdown')) return;
      closeAll();
    });

    document.addEventListener('click', (e) => {
      if (!e.target.closest('#image-list')) closeAll();
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') closeAll();
    });
  })();
</script>

</body>
</html>
