<?php
  require_once( __DIR__ . "/../functions/function_backend.php");
  security_checklogin();

  // Prüfen, ob ein bestimmtes Jahr übergeben wurde
  $new = isset($_GET['new']) ? $_GET['new'] : null;
  $edit = isset($_GET['edit']) ? $_GET['edit'] : null;

  if($edit != null)
  {
    $page = read_page($edit);
  }else{
    $page['title'] = null;
    $page['source_path'] = null;
    $page['content'] = null;
    $page['is_published'] = "false";
  }

?>

<!DOCTYPE html>
<html lang="<?php echo get_language(); ?>">
  <head>      
      <meta charset="UTF-8">        
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Page - <?php echo get_sitename(); ?></title>

      <!-- Tailwind CSS -->
      <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

      <!-- EasyMDE CSS -->
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.css">

      <!-- EasyMDE JS -->
      <script src="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.js"></script>
  </head>
  <body class="min-h-screen flex flex-col">
    <!-- delete Modal -->
    <div id="deleteModal" class="hidden relative z-50 " role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500/75 transition-opacity" aria-hidden="true"></div>
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto">
          <div class="relative w-full max-w-xl mx-auto shadow-lg bg-white p-6">
            <h2 class="text-xl font-semibold text-gray-800">Delete Confirmation</h2>
            <p class="mt-4 text-gray-600">Do you really want to delete this post?</p>
            <div class="flex justify-end mt-6 space-x-3">
              <button id="cancelDelete" class="px-4 py-2 bg-sky-500 text-white hover:bg-sky-600">Cancel</button>
              <button id="confirmDelete" class="px-4 py-2 bg-red-500 text-white hover:bg-red-600">Delete</button>
            </div>
          </div>
        </div>
      </div>
    <!-- Modal -->
    <div id="coverModal" class="hidden relative z-10" role="dialog" aria-modal="true">
      <div class="fixed inset-0 bg-gray-500/75 transition-opacity md:block" aria-hidden="true"></div>
      <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center text-center md:px-2 lg:px-4">
          <div class="flex w-full transform text-left text-base transition md:my-8 md:max-w-2xl md:px-4 lg:max-w-lg">
            <div class="relative flex w-full flex-col overflow-hidden bg-white px-4 pt-14 pb-8 shadow-2xl sm:px-6 sm:pt-8 md:p-6 lg:p-8">

              <button type="button" onclick="closeCoverModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 sm:top-8 sm:right-6 md:top-6 md:right-6 lg:top-8 lg:right-8">
                <span class="sr-only">Schließen</span>
                <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
              </button>

              <h2 class="text-2xl font-bold text-gray-900">Coverbild auswählen</h2>

              <div class="flex gap-4 mt-4 border-b pb-2">
                <button onclick="switchCoverTab('upload')" id="tab-upload" class="px-4 py-2 border-b-2 border-sky-600 font-medium text-sky-600">Datei hochladen</button>
                <button onclick="switchCoverTab('choose')" id="tab-choose" class="px-4 py-2 text-gray-600">Aus Galerie</button>
              </div>

              <div id="cover-upload" class="block mt-4">
                <form id="coverUploadForm" enctype="multipart/form-data">
                  <input type="file" name="coverFile" id="coverFile" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100">
                  <button type="submit" class="mt-3 bg-sky-600 text-white px-4 py-2 rounded hover:bg-sky-700">Hochladen</button>
                </form>
              </div>

              <div id="cover-choose" class="hidden mt-6 max-h-[60vh] overflow-y-auto columns-2 md:columns-3 gap-4 space-y-4">
                <?php
                  $imageDir = realpath(__DIR__ . '/../userdata/content/images');
                  $images = array_filter(scandir($imageDir), function($file) use ($imageDir) {
                    return in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']) && is_file($imageDir . DIRECTORY_SEPARATOR . $file);
                  });
                  foreach ($images as $img):
                    $path = "/userdata/content/images/" . urlencode($img);
                    echo "<img src='$path' class='mb-4 rounded shadow cursor-pointer hover:ring-2 hover:ring-sky-500 w-full break-inside-avoid' onclick=\"selectCover('$path')\">";
                  endforeach;
                ?>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
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
                <!-- Current: "border-sky-500 text-gray-900", Default: "border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700" -->
                <a href="dashboard.php" class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-base font-medium text-gray-600 dark:text-gray-300 hover:border-sky-500 hover:text-sky-500">Dashboard</a>
                <a href="media.php" class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-base font-medium text-gray-600 dark:text-gray-300 hover:border-sky-500 hover:text-sky-500">Images</a>
                <a href="blog.php" class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-base font-medium text-gray-600 dark:text-gray-300 hover:border-sky-500 hover:text-sky-500">Blogposts</a>
                <a href="pages.php" class="inline-flex items-center border-b-2 border-sky-500 px-1 pt-1 text-base font-medium text-sky-500">Pages</a>
              </div>
            </div>
            <div class="flex items-center">
              <div class="shrink-0 pr-5">
                  <button type="button" id="delete-button" class="relative inline-flex items-center gap-x-1.5 bg-sky-400 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-sky-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600">
                    <svg class="-ml-0.5 size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                      <path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
                    </svg>
                    Delete Page
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
                  <div class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black/5 focus:outline-hidden hidden" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                    <!-- Active: "bg-gray-100 outline-hidden", Not Active: "" -->
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-0">Your Profile</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-1">Settings</a>
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
            <a href="dashboard.php" class="block border-l-4 border-transparent py-2 pr-4 pl-3 text-base font-medium text-gray-300 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-700 sm:pr-6 sm:pl-5">Dashboard</a>
            <a href="media.php" class="block border-l-4 border-transparent py-2 pr-4 pl-3 text-base font-medium text-gray-300 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-700 sm:pr-6 sm:pl-5">Images</a>
            <a href="blog.php" class="block border-l-4 border-sky-400 py-2 pr-4 pl-3 text-base font-medium text-sky-400 sm:pr-6 sm:pl-5">Blogposts</a>
            <a href="pages.php" class="block border-l-4 border-transparent py-2 pr-4 pl-3 text-base font-medium text-gray-300 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-700 sm:pr-6 sm:pl-5">Pages</a>
          </div>
          <div class="border-t border-gray-500 pt-4 pb-3">
            <div class="mt-3 space-y-1">
              <a href="#" class="block px-4 py-2 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6">Content</a>
              <div class="pl-5">
                <a href="#" class="block px-4 py-2 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6">Content</a>
              </div>
              <a href="#" class="block px-4 py-2 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6">Albums (1)</a>
              <div class="pl-5">
                <a href="#" class="block px-4 py-2 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6">Album 1</a>
              </div>
            </div>
          </div>
          <div class="border-t border-gray-500 pt-4 pb-3">
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
              <a href="login.php?logout=true" class="block px-4 py-2 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6">Sign out</a>
            </div>
          </div>
        </div>
      </nav>              
    </header>
    <div class="flex flex-1">
      <aside class="hidden md:block max-w-[280px] w-full bg-neutral-200 dark:bg-gray-950 overflow-auto flex-1">   
        <nav class="flex flex-1 flex-col pt-5 px-15 text-gray-600 text-base font-medium" aria-label="Sidebar">
            <ul role="list" class="-mx-2 space-y-1">
              <li>Content</li>
              <ul class="px-5">
                <li><a href="pages.php" class="text-gray-400 hover:text-sky-400">All Posts (<?php echo count_posts(); ?>)</a></li>
              </ul>                  
            </ul>
          </nav>
      </aside>
      <main class="flex-1 bg-white dark:bg-neutral-900 p-6 overflow-auto">          
        <form id="pageForm">
          <div class="space-y-12">
            <div class="grid max-w-7xl grid-cols-1 gap-x-8 gap-y-10 px-4 py-16 sm:px-6 md:grid-cols-3 lg:px-8">
              <div>
                <h2 class="text-base/7 font-semibold text-gray-900">Main Content</h2>
                <p class="mt-1 text-sm/6 text-gray-600">This information will be displayed publicly so be careful what you share.</p>
              </div>

              <div class="grid max-w-6xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 md:col-span-2">
                <div class="sm:col-span-4">
                  <label for="title" class="block text-sm/6 font-medium text-gray-900">Title</label>
                  <div class="mt-2">
                    <div class="flex items-center bg-white pl-3 outline-1 -outline-offset-1 outline-gray-300 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-sky-600">
                      <input type="text" name="title" id="title" class="block min-w-0 grow py-1.5 pr-3 pl-1 text-base text-gray-900 placeholder:text-gray-400 focus:outline-none sm:text-sm/6" placeholder="title" value="<?php echo $page['title']; ?>">
                    </div>
                  </div>
                </div>
                <div class="sm:col-span-4">
                  <label for="foldername" class="block text-sm/6 font-medium text-gray-900">Foldername</label>
                  <div class="mt-2">
                    <div class="flex items-center bg-white pl-3 outline-1 -outline-offset-1 outline-gray-300 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-sky-600">
                      <div class="shrink-0 text-base text-gray-500 select-none sm:text-sm/6">/userdata/content/pages/</div>
                        <input type="text" name="foldername" id="foldername" class="block min-w-0 grow py-1.5 pr-3 pl-1 text-base text-gray-900 placeholder:text-gray-400 focus:outline-none sm:text-sm/6" placeholder="foldername" readonly value="<?php echo $page['source_path']; ?>">
                        <input type="hidden" id="original_foldername" name="original_foldername" value="<?php echo $page['source_path']; ?>">
                      </div>
                    </div>
                  </div>
                  <div class="col-span-full">
                    <label for="content" class="block text-sm/6 font-medium text-gray-900">Content</label>
                    <div class="mt-2 bg-white">
                      <textarea name="content" id="content" rows="3" class="block w-full bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-sky-600 sm:text-sm/6"><?php echo $page['content']; ?></textarea>
                    </div>
                  </div>
                  <div class="col-span-full">
                    <!-- Modal Trigger Button + Vorschau mit Dummybild -->
                    <input type="hidden" name="cover" id="cover">
                    <div class="mb-4">
                      <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Hero Image Preview</label>
                      <div class="aspect-w-16 aspect-h-9 w-full max-w-md bg-gray-100 overflow-hidden border border-gray-300">
                        <img id="coverPreview" src="img/placeholder.png" alt="Cover Preview" class="w-full h-full max-h-md object-cover">
                      </div>
                      <button type="button" onclick="openCoverModal()" class="mt-2 inline-block px-4 py-2 bg-sky-500 text-white text-sm hover:bg-sky-600">
                        Select Hero Image
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              <div class="grid max-w-7xl grid-cols-1 gap-x-8 gap-y-10 px-4 py-16 sm:px-6 md:grid-cols-3 lg:px-8">
                <div>
                  <h2 class="text-base/7 font-semibold text-gray-900">Post settings</h2>
                  <p class="mt-1 text-sm/6 text-gray-600">This information will be displayed publicly so be careful what you share.</p>
                </div>

                <div class="grid max-w-6xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 md:col-span-2">
                  <div class="col-span-full">
                    <div class="flex items-center justify-between">
                      <span class="flex grow flex-col">
                        <span class="text-sm/6 font-medium text-gray-900 dark:text-white" id="availability-label">Is published</span>
                        <span class="text-sm text-gray-500" id="availability-description">Change between visible and invisible</span>
                      </span>
                      <!-- Enabled: "bg-indigo-600", Not Enabled: "bg-gray-200" -->
                      <button type="button" id="is_published" class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent bg-gray-400 transition-colors duration-200 ease-in-out focus:ring-2 focus:ring-sky-600 focus:ring-offset-2 focus:outline-hidden" role="switch" aria-checked="<?php echo $page['is_published']; ?>" aria-labelledby="availability-label" aria-describedby="availability-description">
                        <!-- Enabled: "translate-x-5", Not Enabled: "translate-x-0" -->
                        <span aria-hidden="true" class="pointer-events-none inline-block size-5 translate-x-0 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out"></span>
                      </button>
                    </div>
                  </div>

                  <div class="mt-6 flex items-center justify-end gap-x-6">
                    <button type="button" class="bg-rose-500 px-3 py-2 text-sm/6 font-semibold text-gray-900">Cancel</button>
                    <button type="submit" class="bg-sky-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-sky-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600">Save</button>
                  </div>
                </div>
              </div>
          </div>
        </form>
      </main>
    </div>
    
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

        function updateToggleUI(enabled) {
          toggleBtn.setAttribute("aria-checked", enabled ? "true" : "false");
          toggleBtn.classList.toggle("bg-indigo-600", enabled);
          toggleBtn.classList.toggle("bg-gray-400", !enabled);
          knob.classList.toggle("translate-x-5", enabled);
          knob.classList.toggle("translate-x-0", !enabled);
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
      <script src="js/tailwind.js"></script>
      <script src="js/page_save.js"></script>
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