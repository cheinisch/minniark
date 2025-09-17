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
    <title>Images - <?php echo get_sitename(); ?></title>
		<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
		<!--<script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>-->
	</head>
	<body class="bg-white dark:bg-black">

		<!-- Add Album Modal – neuer Stil, JS-tauglich (.hidden) -->
		<el-dialog>
		<div id="addAlbumModal" class="hidden fixed inset-0 z-50" role="dialog" aria-modal="true" aria-labelledby="album-modal-title">
			<!-- Backdrop -->
			<el-dialog-backdrop
			class="fixed inset-0 bg-gray-500/75 transition-opacity data-closed:opacity-0 dark:bg-gray-900/50">
			</el-dialog-backdrop>

			<!-- Centering -->
			<div tabindex="0" class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
			<!-- Panel -->
			<el-dialog-panel
				class="relative transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all
					sm:my-8 sm:w-full sm:max-w-lg sm:p-6
					dark:bg-black dark:outline dark:-outline-offset-1 dark:outline-white/10">

				<!-- Close oben rechts -->
				<div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
				<button type="button" id="closeAlbumModal"
						class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-2 focus:outline-offset-2 focus:outline-indigo-600
								dark:bg-black dark:hover:text-gray-300 dark:focus:outline-white">
					<span class="sr-only">Close</span>
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6">
					<path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
					</svg>
				</button>
				</div>

				<!-- Header -->
				<div class="sm:flex sm:items-start">
				<div class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-indigo-100 sm:mx-0 sm:size-10 dark:bg-indigo-500/10">
					<svg class="size-6 text-indigo-600 dark:text-indigo-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
					<path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h12M4 17h8" />
					</svg>
				</div>
				<div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
					<h2 id="album-modal-title" class="text-base font-semibold text-gray-900 dark:text-white">Create new Album</h2>
					<p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Name your album and add a description.</p>
				</div>
				</div>

				<!-- Body -->
				<form action="backend_api/album_create.php" method="post" class="mt-4">
				<div class="space-y-4">
					<div>
					<label for="album-title" class="block text-sm font-medium text-gray-900 dark:text-gray-200">Album Name</label>
					<input type="text" name="album-title" id="album-title" value=""
							placeholder="Enter album name"
							class="mt-2 block w-full rounded-md bg-white/5 px-3 py-2 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600
									dark:bg-white/10 dark:text-white dark:outline-white/10 dark:placeholder:text-gray-500" />
					</div>

					<div>
					<label for="album-description" class="block text-sm font-medium text-gray-900 dark:text-gray-200">Album description</label>
					<textarea name="album-description" id="album-description" rows="4"
								class="mt-2 block w-full rounded-md bg-white/5 px-3 py-2 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600
									dark:bg-white/10 dark:text-white dark:outline-white/10 dark:placeholder:text-gray-500"
								placeholder="Optional description"></textarea>
					</div>
				</div>

				<!-- Footer -->
				<div class="mt-6 sm:flex sm:flex-row-reverse">
					<button type="submit" id="saveAlbum"
							class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 sm:ml-3 sm:w-auto
								dark:bg-indigo-500 dark:shadow-none dark:hover:bg-indigo-400">
					Save
					</button>
					<button type="button" id="cancelAlbumModal"
							class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs inset-ring-1 inset-ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto
								dark:bg-white/10 dark:text-white dark:shadow-none dark:inset-ring-white/5 dark:hover:bg-white/20">
					Cancel
					</button>
				</div>
				</form>
			</el-dialog-panel>
			</div>
		</div>
		</el-dialog>

		<!-- Add Collection Modal -->
		<el-dialog>
		<!-- Wrapper, den dein JS per .hidden toggelt -->
		<div id="addCollectionModal" class="hidden fixed inset-0 z-50">
			<!-- Backdrop -->
			<el-dialog-backdrop
			class="fixed inset-0 bg-gray-500/75 transition-opacity data-closed:opacity-0 dark:bg-gray-900/50">
			</el-dialog-backdrop>

			<!-- Centering -->
			<div tabindex="0" class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
			<!-- Panel -->
			<el-dialog-panel
				class="relative transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all
					sm:my-8 sm:w-full sm:max-w-lg sm:p-6
					dark:bg-black dark:outline dark:-outline-offset-1 dark:outline-white/10">

				<!-- Close oben rechts -->
				<div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
				<button type="button" id="closeCollectionModal"
						class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-2 focus:outline-offset-2 focus:outline-indigo-600
								dark:bg-black dark:hover:text-gray-300 dark:focus:outline-white">
					<span class="sr-only">Close</span>
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6">
					<path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
					</svg>
				</button>
				</div>

				<!-- Header -->
				<div class="sm:flex sm:items-start">
				<div class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-indigo-100 sm:mx-0 sm:size-10 dark:bg-indigo-500/10">
					<svg class="size-6 text-indigo-600 dark:text-indigo-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
					<path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h12M4 17h8" />
					</svg>
				</div>
				<div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
					<h2 class="text-base font-semibold text-gray-900 dark:text-white">Create new Collection</h2>
					<p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Give your collection a name. You can edit details later.</p>
				</div>
				</div>

				<!-- Body -->
				<form action="backend_api/collection_create.php" method="post" class="mt-4">
				<div class="space-y-4">
					<div>
					<label for="collection-title" class="block text-sm font-medium text-gray-900 dark:text-gray-200">
						Collection Name
					</label>
					<input type="text" name="collection-title" id="collection-title" value=""
							placeholder="Enter collection name"
							class="mt-2 block w-full rounded-md bg-white/5 px-3 py-2 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600
									dark:bg-white/10 dark:text-white dark:outline-white/10 dark:placeholder:text-gray-500" />
					</div>
				</div>

				<!-- Footer -->
				<div class="mt-6 sm:mt-6 sm:flex sm:flex-row-reverse">
					<button type="submit"
							class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 sm:ml-3 sm:w-auto
								dark:bg-indigo-500 dark:shadow-none dark:hover:bg-indigo-400">
					Save
					</button>
					<button type="button" id="cancelCollectionModal"
							class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs inset-ring-1 inset-ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto
								dark:bg-white/10 dark:text-white dark:shadow-none dark:inset-ring-white/5 dark:hover:bg-white/20">
					Cancel
					</button>
				</div>
				</form>
			</el-dialog-panel>
			</div>
		</div>
		</el-dialog>

<!-- Assign to Album Modal – neuer Stil, kompatibel mit deinem JS -->
<el-dialog>
  <div id="assignToAlbumModal" class="hidden fixed inset-0 z-50" role="dialog" aria-modal="true" aria-labelledby="assign-title">
    <!-- Backdrop -->
    <el-dialog-backdrop
      class="fixed inset-0 bg-gray-500/75 transition-opacity data-closed:opacity-0 dark:bg-gray-900/50">
    </el-dialog-backdrop>

    <!-- Centering -->
    <div tabindex="0" class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
      <!-- Panel -->
      <el-dialog-panel
        class="relative transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all
               sm:my-8 sm:w-full sm:max-w-lg sm:p-6
               dark:bg-black dark:outline dark:-outline-offset-1 dark:outline-white/10">

        <!-- Close oben rechts -->
        <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
          <button type="button" id="closeAssignToAlbumModal"
                  class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-2 focus:outline-offset-2 focus:outline-indigo-600
                         dark:bg-black dark:hover:text-gray-300 dark:focus:outline-white">
            <span class="sr-only">Close</span>
            <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
              <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </button>
        </div>

        <!-- Header -->
        <div class="sm:flex sm:items-start">
          <div class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-indigo-100 sm:mx-0 sm:size-10 dark:bg-indigo-500/10">
            <svg class="size-6 text-indigo-600 dark:text-indigo-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h6l2 2h10M3 7v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V9M9 17h6" />
            </svg>
          </div>
          <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
            <h2 id="assign-title" class="text-base font-semibold text-gray-900 dark:text-white">Add picture to an album</h2>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Choose the album you want to assign the image to.</p>
          </div>
        </div>

        <!-- Body -->
        <form id="assignToAlbumForm" method="post" action="backend_api/assign_image_to_album.php" class="mt-4 w-full">
          <input type="hidden" name="image" id="assignImageFilename">

          <label for="albumSelect" class="block text-sm font-medium text-gray-900 dark:text-gray-200">Choose album</label>
          <select id="albumSelect" name="album"
                  class="mt-2 block w-full rounded-md bg-white/5 px-3 py-2 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600
                         dark:bg-white/10 dark:text-white dark:outline-white/10">
            <?php
              $albums = getAlbumList();
              foreach ($albums as $album) {
                echo '<option value="' . htmlspecialchars($album['slug']) . '">' . htmlspecialchars($album['title']) . '</option>';
              }
            ?>
          </select>

          <!-- Footer -->
          <div class="mt-6 sm:flex sm:flex-row-reverse">
            <button type="submit"
                    class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 sm:ml-3 sm:w-auto
                           dark:bg-indigo-500 dark:shadow-none dark:hover:bg-indigo-400">
              Assign
            </button>
            <button type="button" id="cancelAssignAlbum"
                    class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs inset-ring-1 inset-ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto
                           dark:bg-white/10 dark:text-white dark:shadow-none dark:inset-ring-white/5 dark:hover:bg-white/20">
              Cancel
            </button>
          </div>
        </form>
      </el-dialog-panel>
    </div>
  </div>
</el-dialog>



		<!-- Upload Image Modal -->
		<el-dialog>
		<!-- Wrapper, den dein JS zeigt/versteckt -->
		<div id="uploadModal" class="hidden fixed inset-0 z-50">
			<!-- Backdrop -->
			<el-dialog-backdrop
			class="fixed inset-0 bg-gray-500/75 transition-opacity data-closed:opacity-0 dark:bg-gray-900/50">
			</el-dialog-backdrop>

			<!-- Centering -->
			<div tabindex="0" class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
			<!-- Panel -->
			<el-dialog-panel
				class="relative transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all
					sm:my-8 sm:w-full sm:max-w-xl sm:p-6
					dark:bg-black dark:outline dark:-outline-offset-1 dark:outline-white/10">

				<!-- Close oben rechts -->
				<div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
				<button id="closeUpload" type="button"
						class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-2 focus:outline-offset-2 focus:outline-indigo-600
								dark:bg-black dark:hover:text-gray-300 dark:focus:outline-white">
					<span class="sr-only">Close</span>
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6">
					<path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
					</svg>
				</button>
				</div>

				<!-- Header -->
				<div class="sm:flex sm:items-start">
				<div class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-indigo-100 sm:mx-0 sm:size-10 dark:bg-indigo-500/10">
					<svg class="size-6 text-indigo-600 dark:text-indigo-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
					<path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5V18a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-1.5M7.5 11.5 12 7m0 0 4.5 4.5M12 7v10" />
					</svg>
				</div>
				<div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
					<h3 id="upload-title" class="text-base font-semibold text-gray-900 dark:text-white">
					<?= \languageString('image.upload_image') ?>
					</h3>
					<p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
					<?= \languageString('image.upload_hint') ?: 'Click or drop files below. PNG, JPG up to ' . htmlspecialchars(get_uploadsize()) ?>
					</p>
				</div>
				</div>

				<!-- Body -->
				<div class="mt-4">
				<label for="fileInput" class="sr-only">
					<?= \languageString('image.upload_file_label') ?: 'Upload Files' ?>
				</label>

				<!-- Dropzone -->
				<div id="uploadBox"
					class="mt-2 flex justify-center rounded-md border-2 border-dashed border-black/10 p-6 cursor-pointer
							hover:bg-black/[0.02] dark:border-white/10 dark:hover:bg-white/[0.03]">
					<div class="text-center">
					<svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5V18a2 2 0 002 2h14a2 2 0 002-2v-1.5M7.5 11.5L12 7m0 0l4.5 4.5M12 7v10" />
					</svg>
					<p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
						<?= \languageString('image.drop_or_click') ?: 'Click or drop files here' ?>
					</p>
					<p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
						PNG, JPG — <?= htmlspecialchars(get_uploadsize()) ?>
					</p>
					</div>
				</div>

				<input id="fileInput" type="file" class="hidden" multiple>

				<!-- Progress -->
				<div id="progressContainer" class="mt-4 w-full rounded-full h-2.5 bg-black/5 dark:bg-white/10">
					<div id="progressBar" class="h-2.5 rounded-full bg-indigo-600 text-xs text-center text-white dark:bg-indigo-500" style="width:0%"></div>
				</div>

				<!-- Messages -->
				<div id="messageBox" class="mt-2 text-sm text-gray-700 dark:text-gray-300"></div>
				</div>

				<!-- Footer -->
				<div class="mt-5 sm:mt-6 sm:flex sm:flex-row-reverse">
				<button type="button" id="closeUpload"
						class="inline-flex w-full justify-center rounded-md bg-gray-900 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-gray-800 sm:ml-3 sm:w-auto
								dark:bg-white/10 dark:text-white dark:shadow-none dark:hover:bg-white/20">
					<?= \languageString('general.close') ?: 'Close' ?>
				</button>
				<button type="button" id="triggerFileInput"
						class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs inset-ring-1 inset-ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto
								dark:bg-white/10 dark:text-white dark:shadow-none dark:inset-ring-white/5 dark:hover:bg-white/20">
					<?= \languageString('image.choose_files') ?: 'Choose files' ?>
				</button>
				</div>
			</el-dialog-panel>
			</div>
		</div>
		</el-dialog>
		<!-- Upload Modal end -->
		<!-- delete image modal -->
		<el-dialog>
			<dialog id="deleteImageModal" aria-labelledby="dialog-title" class="fixed inset-0 size-auto max-h-none max-w-none overflow-y-auto bg-transparent backdrop:bg-transparent">
				<el-dialog-backdrop class="fixed inset-0 bg-gray-500/75 transition-opacity data-closed:opacity-0 data-enter:duration-300 data-enter:ease-out data-leave:duration-200 data-leave:ease-in dark:bg-gray-900/50"></el-dialog-backdrop>

				<div tabindex="0" class="flex min-h-full items-end justify-center p-4 text-center focus:outline-none sm:items-center sm:p-0">
				<el-dialog-panel class="relative transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all data-closed:translate-y-4 data-closed:opacity-0 data-enter:duration-300 data-enter:ease-out data-leave:duration-200 data-leave:ease-in sm:my-8 sm:w-full sm:max-w-lg sm:p-6 data-closed:sm:translate-y-0 data-closed:sm:scale-95 dark:bg-black dark:outline dark:-outline-offset-1 dark:outline-white/10">
					<div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
					<button type="button" command="close" commandfor="deleteImageModal" class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-2 focus:outline-offset-2 focus:outline-indigo-600 dark:bg-black dark:hover:text-gray-300 dark:focus:outline-white">
						<span class="sr-only">Close</span>
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6">
						<path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
						</svg>
					</button>
					</div>
					<div class="sm:flex sm:items-start">
					<div class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:size-10 dark:bg-red-500/10">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 text-red-600 dark:text-red-400">
						<path d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" stroke-linecap="round" stroke-linejoin="round" />
						</svg>
					</div>
					<div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
						<h3 id="dialog-title" class="text-base font-semibold text-gray-900 dark:text-white">Deactivate account</h3>
						<div class="mt-2">
						<p class="text-sm text-gray-500 dark:text-gray-400">Are you sure you want to deactivate your account? All of your data will be permanently removed from our servers forever. This action cannot be undone.</p>
						</div>
					</div>
					</div>
					<div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
					<button type="button" id="confirmYes" command="close" commandfor="deleteImageModal" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-red-500 sm:ml-3 sm:w-auto dark:bg-red-500 dark:shadow-none dark:hover:bg-red-400"><?php echo languageString('general.delete'); ?></button>
					<button type="button" id="confirmNo" command="close" commandfor="deleteImageModal" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs inset-ring-1 inset-ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto dark:bg-white/10 dark:text-white dark:shadow-none dark:inset-ring-white/5 dark:hover:bg-white/20"><?php echo languageString('general.cancel'); ?></button>
					</div>
				</el-dialog-panel>
				</div>
			</dialog>
		</el-dialog>
		<!-- delete image modal ende  --> 
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
								<ul role="list" class="flex flex-1 flex-col gap-y-7">
									<li>
										<ul role="list" class="-mx-2 space-y-1">
											<li>
												<!-- Current: "bg-white/5 text-white", Default: "text-gray-400 hover:text-white hover:bg-white/5" -->
												<a href="#" class="group flex gap-x-3 rounded-md bg-white/5 p-2 text-sm/6 font-semibold text-white">
													<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 shrink-0">
														<path d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" stroke-linecap="round" stroke-linejoin="round" />
													</svg>
													<?php echo languageString('nav.images'); ?>
												</a>
											</li>
											<!-- Dashboard 2 mit Dropdown -->
											<li>
												<button type="button"
													class="w-full group flex items-center gap-x-3 rounded-md p-2 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white"
													data-collapse-target="next"
													aria-expanded="false">
													<!-- Icon -->
													<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6 shrink-0">
													<path d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12" stroke-linecap="round" stroke-linejoin="round" />
													<path d="M4.5 9.75V21h6.375v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21H19.5V9.75" stroke-linecap="round" stroke-linejoin="round" />
													</svg>
													<span class="flex-1 text-left"><?php echo languageString('general.albums'); ?></span>
													<!-- Chevron -->
													<svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"
														class="size-5 shrink-0 transition-transform duration-200" data-chevron>
													<path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.25a.75.75 0 0 1-1.06 0L5.25 8.29a.75.75 0 0 1-.02-1.08z" clip-rule="evenodd" />
													</svg>
												</button>

												<!-- Unterpunkte -->
												<ul id="submenu-albums" class="mt-1 space-y-1 hidden">
													<li>
													<a href="#"
														id="add-album"
														class="group flex items-center rounded-md px-1 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white">
														<?php echo languageString('general.add_new'); ?>
													</a>
													</li>
													<?php 

													$albums = getAlbumList();

													foreach($albums as $album)
													{
													echo '<li id="'.$album['title'].'">
															<a href="album-detail.php?album='.$album['slug'].'" class="group flex items-center rounded-md px-1 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white">'.$album['title'].'</a>
															</li>';
													}                    
													?>
												</ul>
											</li>
											<!-- Dashboard 2 mit Dropdown -->
											<li>
												<button type="button"
													class="w-full group flex items-center gap-x-3 rounded-md p-2 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white"
													data-collapse-target="next"
													aria-expanded="false">
													<!-- Icon -->
													<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6 shrink-0">
													<path d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12" stroke-linecap="round" stroke-linejoin="round" />
													<path d="M4.5 9.75V21h6.375v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21H19.5V9.75" stroke-linecap="round" stroke-linejoin="round" />
													</svg>
													<span class="flex-1 text-left"><?php echo languageString('general.collections'); ?></span>
													<!-- Chevron -->
													<svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"
														class="size-5 shrink-0 transition-transform duration-200" data-chevron>
													<path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.25a.75.75 0 0 1-1.06 0L5.25 8.29a.75.75 0 0 1-.02-1.08z" clip-rule="evenodd" />
													</svg>
												</button>

												<!-- Unterpunkte -->
												<ul id="submenu-collection" class="mt-1 space-y-1 hidden">
													<li>
													<a href="#"
														id="add-collection"
														class="group flex items-center rounded-md px-1 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white">
														<?php echo languageString('general.add_new'); ?>
													</a>
													</li>
													<?php 

													$collections = getCollectionList();

													foreach($collections as $collection)
													{
													echo '<li id="'.$collection['title'].'">
															<a href="collection-detail.php?collection='.$collection['slug'].'" class="group flex items-center rounded-md px-1 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white">'.$collection['title'].'</a>
															</li>';
													}                    
													?>
												</ul>
											</li>
										</ul>
									</li>
									<li>
										<div class="text-xs/6 font-semibold text-gray-400"><?php echo languageString('image.filter_images'); ?></div>
										<ul role="list" class="-mx-2 mt-2 space-y-1">
											<li>
												<!-- Current: "bg-white/5 text-white", Default: "text-gray-400 hover:text-white hover:bg-white/5" -->
												<a href="?" class="group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold text-gray-400 hover:bg-white/5 hover:text-white">
												<span class="flex size-6 shrink-0 items-center justify-center rounded-lg border border-black dark:border-white/10 bg-white/5 text-[0.625rem] font-medium text-gray-400 group-hover:border-black dark:border-white/20 group-hover:text-white">R</span>
												<span class="truncate"><?php echo languageString('image.reset_filter'); ?></span>
												</a>
											</li>
											<li>
												<button type="button"
													class="w-full group flex items-center gap-x-3 rounded-md p-2 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white"
													data-collapse-target="next"
													aria-expanded="false">
													<!-- Icon -->
													<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6 shrink-0">
													<path d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12" stroke-linecap="round" stroke-linejoin="round" />
													<path d="M4.5 9.75V21h6.375v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21H19.5V9.75" stroke-linecap="round" stroke-linejoin="round" />
													</svg>
													<span class="flex-1 text-left"><?php echo languageString('general.ratings'); ?></span>
													<!-- Chevron -->
													<svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"
														class="size-5 shrink-0 transition-transform duration-200" data-chevron>
													<path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.25a.75.75 0 0 1-1.06 0L5.25 8.29a.75.75 0 0 1-.02-1.08z" clip-rule="evenodd" />
													</svg>
												</button>

												<!-- Unterpunkte -->
												<ul id="submenu-dashboard-2" class="mt-1 space-y-1 hidden">
												<?php get_ratinglist(false); ?>  
												</ul>
											</li>
											<li>
												<button type="button"
													class="w-full group flex items-center gap-x-3 rounded-md p-2 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white"
													data-collapse-target="next"
													aria-expanded="false">
													<!-- Icon -->
													<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6 shrink-0">
													<path d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12" stroke-linecap="round" stroke-linejoin="round" />
													<path d="M4.5 9.75V21h6.375v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21H19.5V9.75" stroke-linecap="round" stroke-linejoin="round" />
													</svg>
													<span class="flex-1 text-left"><?php echo languageString('general.year'); ?></span>
													<!-- Chevron -->
													<svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"
														class="size-5 shrink-0 transition-transform duration-200" data-chevron>
													<path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.25a.75.75 0 0 1-1.06 0L5.25 8.29a.75.75 0 0 1-.02-1.08z" clip-rule="evenodd" />
													</svg>
												</button>

												<!-- Unterpunkte -->
												<ul id="submenu-dashboard-2" class="mt-1 space-y-1 hidden">
												<?php get_imageyearlist(false); ?>
												</ul>
											</li>
											<li>
												<button type="button"
													class="w-full group flex items-center gap-x-3 rounded-md p-2 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white"
													data-collapse-target="next"
													aria-expanded="false">
													<!-- Icon -->
													<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6 shrink-0">
													<path d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12" stroke-linecap="round" stroke-linejoin="round" />
													<path d="M4.5 9.75V21h6.375v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21H19.5V9.75" stroke-linecap="round" stroke-linejoin="round" />
													</svg>
													<span class="flex-1 text-left"><?php echo languageString('general.countries'); ?></span>
													<!-- Chevron -->
													<svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"
														class="size-5 shrink-0 transition-transform duration-200" data-chevron>
													<path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.25a.75.75 0 0 1-1.06 0L5.25 8.29a.75.75 0 0 1-.02-1.08z" clip-rule="evenodd" />
													</svg>
												</button>

												<!-- Unterpunkte -->
												<ul id="submenu-dashboard-2" class="mt-1 space-y-1 hidden">
												<?php getCountries(false); ?>
												</ul>
											</li>
											<li>
												<button type="button"
													class="w-full group flex items-center gap-x-3 rounded-md p-2 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-black hover:dark:text-white"
													data-collapse-target="next"
													aria-expanded="false">
													<!-- Icon -->
													<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6 shrink-0">
													<path d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12" stroke-linecap="round" stroke-linejoin="round" />
													<path d="M4.5 9.75V21h6.375v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21H19.5V9.75" stroke-linecap="round" stroke-linejoin="round" />
													</svg>
													<span class="flex-1 text-left"><?php echo languageString('general.tags'); ?></span>
													<!-- Chevron -->
													<svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"
														class="size-5 shrink-0 transition-transform duration-200" data-chevron>
													<path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.25a.75.75 0 0 1-1.06 0L5.25 8.29a.75.75 0 0 1-.02-1.08z" clip-rule="evenodd" />
													</svg>
												</button>

												<!-- Unterpunkte -->
												<ul id="submenu-dashboard-2" class="mt-1 space-y-1 hidden">
													<?php getTagsList(false); ?>
												</ul>
											</li>
										</ul>
									</li>
								</ul>
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
					<ul role="list" class="flex flex-1 flex-col gap-y-7">
						<li>
							<ul role="list" class="-mx-2 space-y-1">
								<li>
									<!-- Current: "bg-white/5 text-white", Default: "text-gray-400 hover:text-white hover:bg-white/5" -->
									<a href="#" class="group flex gap-x-3 rounded-md bg-white/5 p-2 text-sm/6 font-semibold text-white">
										<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 shrink-0">
											<path d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" stroke-linecap="round" stroke-linejoin="round" />
										</svg>
										<?php echo languageString('nav.images'); ?>
									</a>
								</li>
								<!-- Dashboard 2 mit Dropdown -->
								<li>
									<button type="button"
										class="w-full group flex items-center gap-x-3 rounded-md p-2 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white"
										data-collapse-target="next"
										aria-expanded="false">
										<!-- Icon -->
										<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6 shrink-0">
										<path d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12" stroke-linecap="round" stroke-linejoin="round" />
										<path d="M4.5 9.75V21h6.375v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21H19.5V9.75" stroke-linecap="round" stroke-linejoin="round" />
										</svg>
										<span class="flex-1 text-left"><?php echo languageString('general.albums'); ?></span>
										<!-- Chevron -->
										<svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"
											class="size-5 shrink-0 transition-transform duration-200" data-chevron>
										<path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.25a.75.75 0 0 1-1.06 0L5.25 8.29a.75.75 0 0 1-.02-1.08z" clip-rule="evenodd" />
										</svg>
									</button>

									<!-- Unterpunkte -->
									<ul id="submenu-albums" class="mt-1 space-y-1 hidden">
										<li>
										<a href="#"
											id="add-album"
											class="group flex items-center rounded-md px-1 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white">
											<?php echo languageString('general.add_new'); ?>
										</a>
										</li>
										<?php 

										$albums = getAlbumList();

										foreach($albums as $album)
										{
										echo '<li id="'.$album['title'].'">
												<a href="album-detail.php?album='.$album['slug'].'" class="group flex items-center rounded-md px-1 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white">'.$album['title'].'</a>
												</li>';
										}                    
										?>
									</ul>
								</li>
								<!-- Dashboard 2 mit Dropdown -->
								<li>
									<button type="button"
										class="w-full group flex items-center gap-x-3 rounded-md p-2 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white"
										data-collapse-target="next"
										aria-expanded="false">
										<!-- Icon -->
										<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6 shrink-0">
										<path d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12" stroke-linecap="round" stroke-linejoin="round" />
										<path d="M4.5 9.75V21h6.375v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21H19.5V9.75" stroke-linecap="round" stroke-linejoin="round" />
										</svg>
										<span class="flex-1 text-left"><?php echo languageString('general.collections'); ?></span>
										<!-- Chevron -->
										<svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"
											class="size-5 shrink-0 transition-transform duration-200" data-chevron>
										<path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.25a.75.75 0 0 1-1.06 0L5.25 8.29a.75.75 0 0 1-.02-1.08z" clip-rule="evenodd" />
										</svg>
									</button>

									<!-- Unterpunkte -->
									<ul id="submenu-collection" class="mt-1 space-y-1 hidden">
										<li>
										<a href="#"
											id="add-collection"
											class="group flex items-center rounded-md px-1 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white">
											<?php echo languageString('general.add_new'); ?>
										</a>
										</li>
										<?php 

										$collections = getCollectionList();

										foreach($collections as $collection)
										{
										echo '<li id="'.$collection['title'].'">
												<a href="collection-detail.php?collection='.$collection['slug'].'" class="group flex items-center rounded-md px-1 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white">'.$collection['title'].'</a>
												</li>';
										}                    
										?>
									</ul>
								</li>
							</ul>
						</li>
						<li>
							<div class="text-xs/6 font-semibold text-gray-400"><?php echo languageString('image.filter_images'); ?></div>
							<ul role="list" class="-mx-2 mt-2 space-y-1">
               					 <li>
									<!-- Current: "bg-white/5 text-white", Default: "text-gray-400 hover:text-white hover:bg-white/5" -->
									<a href="?" class="group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold text-gray-400 hover:bg-white/5 hover:text-white">
									<span class="flex size-6 shrink-0 items-center justify-center rounded-lg border border-black dark:border-white/10 bg-white/5 text-[0.625rem] font-medium text-gray-400 group-hover:border-black dark:border-white/20 group-hover:text-white">R</span>
									<span class="truncate"><?php echo languageString('image.reset_filter'); ?></span>
									</a>
								</li>
								<li>
									<button type="button"
										class="w-full group flex items-center gap-x-3 rounded-md p-2 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white"
										data-collapse-target="next"
										aria-expanded="false">
										<!-- Icon -->
										<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6 shrink-0">
										<path d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12" stroke-linecap="round" stroke-linejoin="round" />
										<path d="M4.5 9.75V21h6.375v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21H19.5V9.75" stroke-linecap="round" stroke-linejoin="round" />
										</svg>
										<span class="flex-1 text-left"><?php echo languageString('general.ratings'); ?></span>
										<!-- Chevron -->
										<svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"
											class="size-5 shrink-0 transition-transform duration-200" data-chevron>
										<path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.25a.75.75 0 0 1-1.06 0L5.25 8.29a.75.75 0 0 1-.02-1.08z" clip-rule="evenodd" />
										</svg>
									</button>

									<!-- Unterpunkte -->
									<ul id="submenu-dashboard-2" class="mt-1 space-y-1 hidden">
									<?php get_ratinglist(false); ?>  
									</ul>
								</li>
								<li>
									<button type="button"
										class="w-full group flex items-center gap-x-3 rounded-md p-2 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white"
										data-collapse-target="next"
										aria-expanded="false">
										<!-- Icon -->
										<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6 shrink-0">
										<path d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12" stroke-linecap="round" stroke-linejoin="round" />
										<path d="M4.5 9.75V21h6.375v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21H19.5V9.75" stroke-linecap="round" stroke-linejoin="round" />
										</svg>
										<span class="flex-1 text-left"><?php echo languageString('general.year'); ?></span>
										<!-- Chevron -->
										<svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"
											class="size-5 shrink-0 transition-transform duration-200" data-chevron>
										<path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.25a.75.75 0 0 1-1.06 0L5.25 8.29a.75.75 0 0 1-.02-1.08z" clip-rule="evenodd" />
										</svg>
									</button>

									<!-- Unterpunkte -->
									<ul id="submenu-dashboard-2" class="mt-1 space-y-1 hidden">
									<?php get_imageyearlist(false); ?>
									</ul>
								</li>
								<li>
									<button type="button"
										class="w-full group flex items-center gap-x-3 rounded-md p-2 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white"
										data-collapse-target="next"
										aria-expanded="false">
										<!-- Icon -->
										<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6 shrink-0">
										<path d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12" stroke-linecap="round" stroke-linejoin="round" />
										<path d="M4.5 9.75V21h6.375v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21H19.5V9.75" stroke-linecap="round" stroke-linejoin="round" />
										</svg>
										<span class="flex-1 text-left"><?php echo languageString('general.countries'); ?></span>
										<!-- Chevron -->
										<svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"
											class="size-5 shrink-0 transition-transform duration-200" data-chevron>
										<path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.25a.75.75 0 0 1-1.06 0L5.25 8.29a.75.75 0 0 1-.02-1.08z" clip-rule="evenodd" />
										</svg>
									</button>

									<!-- Unterpunkte -->
									<ul id="submenu-dashboard-2" class="mt-1 space-y-1 hidden">
									<?php getCountries(false); ?>
									</ul>
								</li>
								<li>
									<button type="button"
										class="w-full group flex items-center gap-x-3 rounded-md p-2 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-black hover:dark:text-white"
										data-collapse-target="next"
										aria-expanded="false">
										<!-- Icon -->
										<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6 shrink-0">
										<path d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12" stroke-linecap="round" stroke-linejoin="round" />
										<path d="M4.5 9.75V21h6.375v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21H19.5V9.75" stroke-linecap="round" stroke-linejoin="round" />
										</svg>
										<span class="flex-1 text-left"><?php echo languageString('general.tags'); ?></span>
										<!-- Chevron -->
										<svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"
											class="size-5 shrink-0 transition-transform duration-200" data-chevron>
										<path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.25a.75.75 0 0 1-1.06 0L5.25 8.29a.75.75 0 0 1-.02-1.08z" clip-rule="evenodd" />
										</svg>
									</button>

									<!-- Unterpunkte -->
									<ul id="submenu-dashboard-2" class="mt-1 space-y-1 hidden">
										<?php getTagsList(false); ?>
									</ul>
								</li>
							</ul>
						</li>
					</ul>
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
						
						<div id="profileDropdown" class="relative" data-dropdown>
							<button class="relative flex items-center" aria-haspopup="menu" aria-expanded="false">
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
									<path d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z"
										clip-rule="evenodd" fill-rule="evenodd" />
								</svg>
								</span>
							</button>

							<!-- Menü: jetzt ein DIV; Popover-API bleibt nutzbar -->
							<div data-menu popover anchor="bottom end"
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
          <section id="image-list" aria-label="Image List">
            <div class="flex flex-wrap gap-6 justify-center md:justify-start">
            <?php
                renderImageGallery($filterYear, $filterRating, $filterTag, $filterCountry, $sort, $direction); // Galerie ausgeben              
              ?>
            </div>
          </section>
				</div>
			</main>
		</div>
		<script src="js/album_collection.js"></script>
		<script src="js/tailwind.js"></script>
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
