<?php
  require_once(__DIR__ . "/../functions/function_backend.php");
  require_once __DIR__ . '/../vendor/autoload.php';
  security_checklogin();

  // Album
  $albumTitle = isset($_GET['album']) ? $_GET['album'] : null;
  $albumdata  = getAlbumData($albumTitle);

  // Optional: Collection-Slug für Cover-Selection aus Collection-Alben
  $collectionSlug = isset($_GET['collection']) ? $_GET['collection'] : null;

  // Description -> HTML
  $Parsedown = new Parsedown();
  $descriptionHtml = $Parsedown->text($albumdata['description'] ?? '');

  // Headimage
  $headimage = "img/placeholder.png";
  if (!empty($albumdata['headImage'])) {
    $cacheImage = get_cacheimage($albumdata['headImage'], "l");
    if (!empty($cacheImage)) {
      $headimage = "../cache/images/" . $cacheImage;
    }
  }

  // Data fürs "Add Images"-Modal
  $allImages = getAllUploadedImages(); // erwartet Array mit ['filename','title'] etc.
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

    <!-- Album Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

      <!-- Album Info -->
      <section class="lg:col-span-4">
        <div class="rounded-lg overflow-hidden bg-white dark:bg-black shadow-sm dark:outline dark:-outline-offset-1 dark:outline-white/10">
          <img src="<?php echo htmlspecialchars($headimage); ?>" class="w-full aspect-video object-cover" alt="Album cover">

          <div class="p-5">
            <!-- View mode -->
            <div id="text-show-frame">
              <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">
                <?php echo htmlspecialchars($albumdata['name']); ?>
              </h2>

              <div class="prose prose-sm mt-4 max-w-none dark:prose-invert text-gray-700 dark:text-gray-300">
                <?php echo $descriptionHtml; ?>
              </div>
            </div>

            <!-- Edit mode -->
            <form action="backend_api/album_update.php" method="post" class="mt-4">
              <div id="text-edit-frame" class="hidden space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-900 dark:text-gray-200">Title</label>
                  <input
                    type="text"
                    name="album-title-edit"
                    value="<?php echo htmlspecialchars($albumdata['name']); ?>"
                    class="mt-2 block w-full rounded-md bg-white/5 px-3 py-2 text-base
                           text-gray-900 dark:text-white outline-1 outline-gray-300
                           focus:outline-2 focus:outline-indigo-600
                           dark:bg-white/10 dark:outline-white/10">
                  <input type="hidden" name="album-current-title" value="<?php echo htmlspecialchars($albumdata['name']); ?>">
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-900 dark:text-gray-200">Description</label>
                  <textarea
                    name="album-description"
                    rows="6"
                    class="mt-2 block w-full rounded-md bg-white/5 px-3 py-2 text-base
                           text-gray-900 dark:text-white outline-1 outline-gray-300
                           focus:outline-2 focus:outline-indigo-600
                           dark:bg-white/10 dark:outline-white/10"><?php
                    echo htmlspecialchars($albumdata['description']);
                  ?></textarea>
                </div>
              </div>

              <div class="mt-4 flex gap-2">
                <div id="normal-group">
                  <button type="button" id="edit_text"
                    class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                    Edit
                  </button>
                </div>

                <div id="edit-group" class="hidden flex gap-2">
                  <button type="submit"
                    class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                    Save
                  </button>
                  <button type="button" id="cancel_edit"
                    class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900
                           inset-ring-1 inset-ring-gray-300 hover:bg-gray-50
                           dark:bg-white/10 dark:text-white dark:inset-ring-white/5">
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
          <?php renderImageGalleryAlbum($albumdata['slug']); ?>
        </div>
      </section>

    </div>
  </div>
</main>

<!-- ================= MODALS ================= -->

<!-- Add Images to Album -->
<el-dialog>
  <div id="addToAlbumImageModal" class="hidden fixed inset-0 z-50">
    <el-dialog-backdrop class="fixed inset-0 bg-black/50 backdrop-blur-sm"></el-dialog-backdrop>

    <div class="flex min-h-full items-center justify-center p-4">
      <div class="w-full max-w-5xl rounded-lg bg-white dark:bg-black p-6 shadow-xl dark:outline dark:-outline-offset-1 dark:outline-white/10">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
          Add images to album
        </h2>

        <input
          type="text"
          id="imageSearchInputAdd"
          placeholder="Search images..."
          class="mb-4 w-full rounded-md px-3 py-2 text-sm
                 border border-gray-300 dark:border-white/10
                 bg-white dark:bg-white/10 text-gray-900 dark:text-white">

        <form method="post" action="backend_api/add_images_to_album.php">
          <input type="hidden" name="album" value="<?php echo htmlspecialchars($albumTitle); ?>">

          <div id="imageListAdd" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 max-h-[60vh] overflow-y-auto">
            <?php foreach (getAllUploadedImages() as $img): ?>
              <label class="text-center cursor-pointer image-item"
                     data-name="<?php echo htmlspecialchars(mb_strtolower($img['title'] ?? $img['filename'])); ?>">
                <input type="checkbox" name="images[]" value="<?php echo htmlspecialchars($img['filename']); ?>" class="sr-only peer">
                <div class="peer-checked:ring-2 peer-checked:ring-indigo-500 rounded overflow-hidden">
                  <img src="../userdata/content/images/<?php echo htmlspecialchars($img['filename']); ?>"
                       class="aspect-square object-cover">
                </div>
                <span class="block mt-1 text-xs truncate text-gray-700 dark:text-gray-300">
                  <?php echo htmlspecialchars($img['title'] ?? $img['filename']); ?>
                </span>
              </label>
            <?php endforeach; ?>
          </div>

          <div class="mt-6 flex justify-end gap-2">
            <button type="button" id="cancelAddToAlbumImage"
              class="px-3 py-2 text-sm rounded-md bg-gray-200 dark:bg-white/10">
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

<!-- Delete Image Modal -->
<el-dialog>
  <dialog id="deleteImageModal" class="fixed inset-0">
    <el-dialog-backdrop class="fixed inset-0 bg-black/50"></el-dialog-backdrop>

    <div class="flex min-h-full items-center justify-center p-4">
      <div class="bg-white dark:bg-black rounded-lg p-6 max-w-md w-full shadow-xl">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
          Remove image
        </h3>
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
          Do you really want to remove this image from the album?
        </p>

        <div class="mt-6 flex justify-end gap-2">
          <button id="confirmNo"
            class="px-3 py-2 text-sm rounded-md bg-gray-200 dark:bg-white/10">
            Cancel
          </button>
          <button id="confirmYes"
            class="px-3 py-2 text-sm rounded-md bg-red-600 text-white hover:bg-red-500">
            Delete
          </button>
        </div>
      </div>
    </div>
  </dialog>
</el-dialog>

<!-- ================= SCRIPTS ================= -->

<script src="js/album_edit.js"></script>
<script src="js/tailwind.js"></script>

<script>
/* Add Images Modal */
(() => {
  const modal = document.getElementById('addToAlbumImageModal');
  document.getElementById('addImagetoAlbumBtn')?.onclick = () => modal.classList.remove('hidden');
  document.getElementById('cancelAddToAlbumImage')?.onclick = () => modal.classList.add('hidden');

  const search = document.getElementById('imageSearchInputAdd');
  search?.addEventListener('input', () => {
    const q = search.value.toLowerCase();
    document.querySelectorAll('#imageListAdd .image-item').forEach(el => {
      el.style.display = el.dataset.name.includes(q) ? '' : 'none';
    });
  });
})();
</script>

<script>
/* Delete image confirmation */
(() => {
  let pending = null;
  const dlg = document.getElementById('deleteImageModal');

  document.getElementById('image-list')?.addEventListener('click', e => {
    const a = e.target.closest('a.confirm-link');
    if (!a) return;
    e.preventDefault();
    pending = a.href;
    dlg.showModal();
  });

  document.getElementById('confirmYes').onclick = () => {
    if (pending) location.href = pending;
  };
  document.getElementById('confirmNo').onclick = () => dlg.close();
})();
</script>
<script>
(() => {
  // Delegation: funktioniert für alle Bilder aus renderImageGalleryAlbum()
  const imageList = document.getElementById('image-list');
  if (!imageList) return;

  const closeAll = (except = null) => {
    imageList.querySelectorAll('.dropdown').forEach(dd => {
      if (dd !== except) dd.classList.add('hidden');
    });
  };

  imageList.addEventListener('click', (e) => {
    // 1) Klick auf 3-Punkte Button (oder das SVG darin)
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

    // 2) Klick im Dropdown selbst -> nichts schließen
    if (e.target.closest('.dropdown')) return;

    // 3) sonst: Dropdowns schließen
    closeAll();
  });

  // Click außerhalb der Image-Liste schließt auch
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
