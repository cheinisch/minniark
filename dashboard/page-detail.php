<?php
  require_once( __DIR__ . "/../functions/function_backend.php");
  security_checklogin();

  // Prüfen, ob ein bestimmtes Jahr übergeben wurde
  $new = isset($_GET['new']) ? $_GET['new'] : null;
  $edit = isset($_GET['edit']) ? $_GET['edit'] : null;

  if($edit != null)
  {
    $page = GetPageData($edit);
  }else{
    $page['title'] = null;
    $page['slug'] = null;
    $page['content'] = null;
    $page['is_published'] = "false";
    $page['cover'] = "";
  }

?>

<!doctype html>
<html lang="<?php echo get_language(); ?>">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pages - <?php echo get_sitename(); ?></title>
		<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
		<!--<script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>-->
    <!-- EasyMDE CSS -->
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.css">

      <!-- EasyMDE JS -->
      <script src="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.js"></script>
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
								<img src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=indigo&shade=500" alt="Your Company" class="h-8 w-auto" />
							</div>
							<nav class="relative flex flex-1 flex-col">
								<?php include (__DIR__.'/layout/pages_menu.php'); ?>
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
					<?php include (__DIR__.'/layout/pages_menu.php'); ?>
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
								class="inline-flex items-center justify-start mx-4 py-2 border-b border-gray-800 dark:border-gray-400 rounded-none
										no-underline text-base font-normal leading-tight appearance-none">
								<?php echo languageString('nav.blogposts'); ?>
							</a>
							<a href="pages.php"
								class="inline-flex items-center justify-start mx-4 py-2 border-b-2 border-gray-800 dark:border-gray-400 rounded-none
										no-underline text-base font-normal leading-tight appearance-none">
								<?php echo languageString('nav.pages'); ?>
							</a>
						</div>
					</div>
					<div class="flex items-center gap-x-4 lg:gap-x-6">
						<a href="page-detail.php?post=new"
						id="newPageBtn"
						class="inline-flex items-center gap-2 -m-2.5 p-2.5 text-gray-800 hover:text-gray-500 dark:text-gray-400 dark:hover:text-gray-300">
							<?php echo languageString('page.new_page'); ?>
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-upload" viewBox="0 0 16 16">
								<path d="M8 6.5a.5.5 0 0 1 .5.5v1.5H10a.5.5 0 0 1 0 1H8.5V11a.5.5 0 0 1-1 0V9.5H6a.5.5 0 0 1 0-1h1.5V7a.5.5 0 0 1 .5-.5"/>
  								<path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5z"/>
							</svg>
							<span class="sr-only">New Page</span>
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
				<div class="px-4 sm:px-6 lg:px-8">
          <form id="pageForm" action="backend_api/page_save.php" method="post" class="space-y-8 max-w-5xl mx-auto">

            <!-- Main Content -->
            <section class="rounded-sm border border-black/10 dark:border-white/10 bg-white dark:bg-black/40 shadow-xs">

              <div class="p-4 grid grid-cols-1 gap-6 sm:grid-cols-6">
                <!-- Title -->
                <div class="sm:col-span-4">
                  <label for="title" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Title</label>
                  <div class="mt-1">
                    <input
                      type="text" name="title" id="title" placeholder="title"
                      value="<?php echo $page['title']; ?>"
                      class="block w-full rounded bg-white dark:bg-black px-3 py-2 text-sm text-gray-900 dark:text-white
                            outline outline-1 -outline-offset-1 outline-gray-300 dark:outline-white/10
                            placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-sky-600" />
                  </div>
                </div>

                <!-- Foldername -->
                <div class="sm:col-span-4">
                  <label for="foldername" class="block text-xs font-medium text-gray-700 dark:text-gray-300"><?php echo languageString('page.foldername'); ?></label>
                  <div class="mt-1 flex items-stretch rounded overflow-hidden outline outline-1 -outline-offset-1 outline-gray-300 dark:outline-white/10 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-sky-600">
                    <span class="shrink-0 px-3 py-2 text-xs text-gray-600 dark:text-gray-400 bg-black/5 dark:bg-white/5 select-none">
                      /userdata/content/pages/
                    </span>
                    <input
                      type="text" name="foldername" id="foldername" readonly
                      value="<?php echo $page['slug']; ?>"
                      class="min-w-0 grow bg-white dark:bg-black px-3 py-2 text-sm text-gray-900 dark:text-white focus:outline-none" />
                    <input type="hidden" id="original_foldername" name="original_foldername" value="<?php echo $page['slug']; ?>">
                  </div>
                </div>

                <!-- Content -->
                <div class="col-span-full">
                  <label for="content" class="block text-xs font-medium text-gray-700 dark:text-gray-300"><?php echo languageString('page.content'); ?></label>
                  <div class="mt-1 bg-white">
                    <textarea
                      name="content" id="content" rows="10"
                      class="block w-full rounded bg-white dark:bg-white px-3 py-2 text-sm text-gray-900 dark:text-white
                            outline outline-1 -outline-offset-1 outline-gray-300 dark:outline-white/10
                            placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-sky-600"><?php echo $page['content']; ?></textarea>
                  </div>
                </div>

                <!-- Hero Image -->
                <div class="col-span-full">
                  <input type="hidden" name="cover" id="cover" value="<?php echo $page['cover']; ?>">
                  <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Hero Image</label>

                  <div class="w-full max-w-xl rounded border border-black/10 dark:border-white/10 overflow-hidden bg-black/5 dark:bg-white/5">
                    <img
                      id="coverPreview"
                      src="<?php echo get_cached_image_dashboard($page['cover'], 'M'); ?>"
                      alt="Cover Preview"
                      class="w-full h-56 object-cover" />
                  </div>

                  <div class="mt-2 flex gap-2">
                    <button type="button" id="openCoverModalBtn"
                            class="text-xs px-3 py-1.5 rounded bg-sky-600 text-white hover:bg-sky-500">
                      Select Hero Image
                    </button>
                    <button type="button" id="removeHeroImg"
                            class="text-xs px-3 py-1.5 rounded bg-rose-600 text-white hover:bg-rose-500">
                      Remove Hero Image
                    </button>
                  </div>
                </div>
              </div>
            </section>

            <!-- Page Settings -->
            <section class="rounded-sm border border-black/10 dark:border-white/10 bg-white dark:bg-black/40 shadow-xs">
              <header class="px-4 py-3 border-b border-black/10 dark:border-white/10">
                <h2 class="text-sm font-semibold text-black dark:text-white">Page Settings</h2>
                <p class="mt-1 text-xs text-black/60 dark:text-gray-400">
                  Control visibility and save changes.
                </p>
              </header>

              <div class="p-4 grid grid-cols-1 gap-6 sm:grid-cols-6">
                <!-- Published Toggle -->
                <div class="col-span-full">
                  <div class="flex items-center justify-between">
                    <span class="flex grow flex-col">
                      <span class="text-sm font-medium text-black dark:text-white" id="availability-label">Is published</span>
                      <span class="text-xs text-black/60 dark:text-gray-400" id="availability-description">Change between visible and invisible</span>
                    </span>

                    <button type="button" id="is_published"
                            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent bg-gray-400 transition-colors duration-200 ease-in-out focus:ring-2 focus:ring-sky-600 focus:ring-offset-2 focus:outline-hidden"
                            role="switch"
                            aria-checked="<?php echo $page['is_published'] === true ? "true" : "false"; ?>"
                            aria-labelledby="availability-label" aria-describedby="availability-description">
                      <span aria-hidden="true"
                            class="pointer-events-none inline-block size-5 translate-x-0 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out"></span>
                    </button>
                    <input type="hidden" name="is_published" id="is_published_input" value="<?php echo $page['is_published'] === true ? "true" : "false"; ?>">
                  </div>
                </div>

                <!-- Actions -->
                <div class="col-span-full flex items-center justify-end gap-3">
                  <a href="pages.php"
                    class="text-xs px-3 py-2 rounded bg-rose-600 text-white hover:bg-rose-500">
                    <?php echo languageString('general.cancel'); ?>
                  </a>
                  <button type="submit"
                          class="text-xs px-3 py-2 rounded bg-sky-600 text-white shadow-xs hover:bg-sky-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600">
                    <?php echo languageString('general.save'); ?>
                  </button>
                </div>
              </div>
            </section>
          </form>

				</div>
			</main>
		</div>
		
		<script src="js/navbar.js"></script>
		<script src="js/tailwind.js"></script>
		<script src="js/profile_settings.js"></script>
    <script>
      document.addEventListener("DOMContentLoaded", function () {
        window.easyMDE = new EasyMDE({
          element: document.getElementById("content"),
          spellChecker: false,
          autosave: { enabled: false },
          placeholder: "Please enter your content",
          toolbar: ["bold", "italic", "heading", "|", "quote", "unordered-list", "ordered-list", "|", "preview", "guide"]
        });
      });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
          console.log("Hero Image Script geladen");
        // Tabs wechseln (Upload vs. Galerie)
        function switchCoverTab(tab) {
          const uploadTab = document.getElementById('cover-upload');
          const chooseTab = document.getElementById('cover-choose');
          const tabUploadBtn = document.getElementById('tab-upload');
          const tabChooseBtn = document.getElementById('tab-choose');

          if (!uploadTab || !chooseTab || !tabUploadBtn || !tabChooseBtn) {
            console.warn("Ein oder mehrere Tab-Elemente wurden nicht gefunden.");
            return;
          }

          uploadTab.classList.toggle('hidden', tab !== 'upload');
          chooseTab.classList.toggle('hidden', tab !== 'choose');
          tabUploadBtn.classList.toggle('border-b-2', tab === 'upload');
          tabChooseBtn.classList.toggle('border-b-2', tab === 'choose');
          tabUploadBtn.classList.toggle('text-sky-600', tab === 'upload');
          tabChooseBtn.classList.toggle('text-sky-600', tab === 'choose');
        }

        // Modal öffnen
        const openModalBtn = document.getElementById("openCoverModalBtn");
        const coverModal = document.getElementById("coverModal");

        if (openModalBtn && coverModal) {
          openModalBtn.addEventListener("click", () => {
            console.log("Hero Image btn pressed");
            coverModal.classList.remove("hidden");
          });
        } else {
          console.warn("Modal oder Button zum Öffnen nicht gefunden.");
        }

        // Modal schließen
        window.closeCoverModal = () => {
          if (coverModal) {
            coverModal.classList.add("hidden");
          }
        };

        // Bild auswählen
        window.selectCover = (path) => {
          const coverInput = document.getElementById('cover');
          const previewImg = document.getElementById('coverPreview');

          if (coverInput && previewImg) {
            coverInput.value = path.split('/').pop(); // ✅ nur der Dateiname
            previewImg.src = path; // für Vorschau aber weiterhin der ganze Pfad
            closeCoverModal();
          }
        };

        // Expose `switchCoverTab` to global scope if needed
        window.switchCoverTab = switchCoverTab;
      });
    </script>
    <script>
      document.addEventListener("DOMContentLoaded", function () {
        const titleInput = document.getElementById("title");
        const folderInput = document.getElementById("foldername");

        function slugify(text) {

          const map = {
          'ä': 'ae',
          'ö': 'oe',
          'ü': 'ue',
          'ß': 'ss',
          'à': 'a',
          'á': 'a',
          'è': 'e',
          'é': 'e',
          'ì': 'i',
          'í': 'i',
          'ò': 'o',
          'ó': 'o',
          'ù': 'u',
          'ú': 'u',
          'ñ': 'n'
        };

          return text
            .toString()
            .toLowerCase()
            .trim()
            .replace(/[äöüßàáèéìíòóùúñ]/g, m => map[m]) // ersetze Umlaute
            .replace(/[^a-z0-9]+/g, '-')   // ersetze Sonderzeichen durch -
            .replace(/^-+|-+$/g, '');      // entferne führende/trailing Bindestriche
        }

        async function checkAndGenerateFolder(slug) {
          const response = await fetch('backend_api/check_foldername.php?base=' + encodeURIComponent(slug));
          const data = await response.json();
          folderInput.value = data.suggested;
        }

        titleInput.addEventListener("input", () => {
          const baseSlug = slugify(titleInput.value);
          if (baseSlug.length > 0) {
            checkAndGenerateFolder(baseSlug);
          } else {
            folderInput.value = '';
          }
        });
      });
    </script>
    <script>
      document.addEventListener("DOMContentLoaded", function () {
        const toggleBtn = document.getElementById("is_published");
        const knob = toggleBtn.querySelector("span");
        const hiddenInput = document.getElementById("is_published_input");

        function updateToggleUI(enabled) {
          toggleBtn.setAttribute("aria-checked", enabled ? "true" : "false");
          toggleBtn.classList.toggle("bg-sky-600", enabled);
          toggleBtn.classList.toggle("bg-gray-400", !enabled);
          knob.classList.toggle("translate-x-5", enabled);
          knob.classList.toggle("translate-x-0", !enabled);

          // Update hidden input so form submission works
          hiddenInput.value = enabled ? "true" : "false";
        }

        // Initialstatus setzen
        const isEnabled = toggleBtn.getAttribute("aria-checked") === "true";
        updateToggleUI(isEnabled);

        // Klickverhalten
        toggleBtn.addEventListener("click", () => {
          const current = toggleBtn.getAttribute("aria-checked") === "true";
          updateToggleUI(!current);
        });
      });
      </script>
      <script src="js/remove_hero_image.js"></script>
      <script>
          // Delete-Button Klick öffnet Modal
          document.getElementById('delete-button').addEventListener('click', function() {
            document.getElementById('deleteModal').classList.remove('hidden');
          });

          // Cancel-Button Klick schließt Modal
          document.getElementById('cancelDelete').addEventListener('click', function() {
            document.getElementById('deleteModal').classList.add('hidden');
          });

          // Confirm-Button Klick ruft direkt dein PHP-Skript auf
          document.getElementById('confirmDelete').addEventListener('click', function() {
            const filename = "<?php echo page['source_path']; ?>";
            window.location.href = `/dashboard/backend_api/delete.php?type=page&filename=${encodeURIComponent(filename)}`;
          });

      </script>

	</body>
</html>
