<?php
  require_once( __DIR__ . "/../functions/function_backend.php");
  security_checklogin();

  // Prüfen, ob ein bestimmtes Jahr übergeben wurde
  $filterYear = isset($_GET['year']) ? $_GET['year'] : null;
  $filterTag = isset($_GET['tag']) ? $_GET['tag'] : null;

?>

<!DOCTYPE html>
<html lang="<?php echo get_language(); ?>">
    <head>      
        <meta charset="UTF-8">        
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Blogposts - <?php echo get_sitename(); ?></title>

        <!-- Tailwind CSS -->
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

        <!-- EasyMDE CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.css">

        <!-- EasyMDE JS -->
        <script src="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.js"></script>
    </head>
    <body class="min-h-screen flex flex-col">
      <!-- Modal -->
      <div id="coverModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center">
        <div class="bg-neutral-200 dark:bg-neutral-800 p-6 w-full max-w-xl relative">
          <button onclick="closeCoverModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700">&times;</button>

          <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">Cover auswählen</h2>

          <!-- Tabs -->
          <div class="flex gap-4 mb-4">
            <button onclick="switchCoverTab('upload')" id="tab-upload" class="px-4 py-2 border-b-2 border-sky-600 font-medium text-sky-600">Datei hochladen</button>
            <button onclick="switchCoverTab('choose')" id="tab-choose" class="px-4 py-2 text-gray-600 dark:text-gray-300">Aus Galerie</button>
          </div>

          <!-- Upload Area -->
          <div id="cover-upload" class="block">
            <input type="file" id="coverFile" name="coverFile" class="mb-4">
          </div>

          <!-- Auswahl aus bestehenden Bildern -->
          <div id="cover-choose" class="hidden max-h-96 overflow-y-auto grid grid-cols-3 gap-4">
            <?php
              $imageDir = realpath(__DIR__ . '/../userdata/content/images');
              $images = array_filter(scandir($imageDir), function($file) use ($imageDir) {
                return in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']) && is_file($imageDir . DIRECTORY_SEPARATOR . $file);
              });
              foreach ($images as $img):
                $path = "/userdata/content/images/" . urlencode($img);
                echo "<img src='$path' class='rounded shadow cursor-pointer hover:ring-2 hover:ring-sky-500' onclick=\"selectCover('$path')\">";
              endforeach;
            ?>
          </div>
        </div>
      </div>
        <header>
          <nav class="bg-neutral-100 dark:bg-gray-950 shadow-sm">
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
                    <a href="dashboard.php" class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-base font-medium text-gray-600 dark:text-gray-300 hover:border-sky-400 hover:text-sky-400">Dashboard</a>
                    <a href="media.php" class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-base font-medium text-gray-600 dark:text-gray-300 hover:border-sky-400 hover:text-sky-400">Images</a>
                    <a href="blog.php" class="inline-flex items-center border-b-2 border-sky-400 px-1 pt-1 text-base font-medium text-sky-400">Blogposts</a>
                    <a href="pages.php" class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-base font-medium text-gray-600 dark:text-gray-300 hover:border-sky-400 hover:text-sky-400">Pages</a>
                  </div>
                </div>
                <div class="flex items-center">
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
          
                      <!--
                        Dropdown menu, show/hide based on menu state.
          
                        Entering: "transition ease-out duration-200"
                          From: "transform opacity-0 scale-95"
                          To: "transform opacity-100 scale-100"
                        Leaving: "transition ease-in duration-75"
                          From: "transform opacity-100 scale-100"
                          To: "transform opacity-0 scale-95"
                      -->
                      <div class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black/5 focus:outline-hidden hidden" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                        <!-- Active: "bg-gray-100 outline-hidden", Not Active: "" -->
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-0">Your Profile</a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-1">Settings</a>
                        <a href="login/logout.php" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-2">Sign out</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          
            <!-- Mobile menu, show/hide based on menu state. -->
            <div class="md:hidden" id="mobile-menu">
              <div class="space-y-1 pt-2 pb-3">
                <!-- Current: "bg-indigo-50 border-indigo-500 text-indigo-700", Default: "border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700" -->
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
                  <a href="login/logout.php" class="block px-4 py-2 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6">Sign out</a>
                </div>
              </div>
            </div>
          </nav>
              
        </header>
        <div class="flex flex-1">
        <div class="flex flex-1">
          <aside class="hidden md:block max-w-[280px] w-full bg-gray-950 overflow-auto flex-1">
              <nav class="flex flex-1 flex-col pt-5 px-15 text-gray-300 text-base font-medium" aria-label="Sidebar">
                <ul role="list" class="-mx-2 space-y-1">
                  <li>Content</li>
                  <ul class="px-5">
                    <li><a href="?" class="text-gray-400 hover:text-sky-400">All Posts (<?php echo count_posts(); ?>)</a></li>
                  </ul>
                  <li>Year</li>
                  <ul class="px-5">
                    <?php get_postyearlist(false); ?>
                  </ul>
                  <li>Tags</li>
                  <ul class="px-5">
                    <?php get_posttaglist(false); ?>
                  </ul>                   
                </ul>
              </nav>
          </aside>
          <main class="flex-1 bg-white dark:bg-neutral-900 p-6 overflow-auto">
         
          <form>
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
            <div class="flex items-center bg-white pl-3 outline-1 -outline-offset-1 outline-gray-300 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-indigo-600">
              <input type="text" name="title" id="title" class="block min-w-0 grow py-1.5 pr-3 pl-1 text-base text-gray-900 placeholder:text-gray-400 focus:outline-none sm:text-sm/6" placeholder="title">
            </div>
          </div>
        </div>
        <div class="sm:col-span-4">
          <label for="foldername" class="block text-sm/6 font-medium text-gray-900">Foldername</label>
          <div class="mt-2">
            <div class="flex items-center bg-white pl-3 outline-1 -outline-offset-1 outline-gray-300 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-indigo-600">
              <div class="shrink-0 text-base text-gray-500 select-none sm:text-sm/6">/userdata/content/essays/</div>
              <input type="text" name="foldername" id="foldername" class="block min-w-0 grow py-1.5 pr-3 pl-1 text-base text-gray-900 placeholder:text-gray-400 focus:outline-none sm:text-sm/6" placeholder="foldername" readonly>
            </div>
          </div>
        </div>

        <div class="col-span-full">
          <label for="content" class="block text-sm/6 font-medium text-gray-900">Content</label>
          <div class="mt-2">
            <textarea name="content" id="content" rows="3" class="block w-full bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-sky-600 sm:text-sm/6"></textarea>
          </div>
        </div>

        <div class="sm:col-span-4">
          <label for="tags" class="block text-sm/6 font-medium text-gray-900">Tags</label>
          <div class="mt-2">
            <div class="flex items-center bg-white pl-3 outline-1 -outline-offset-1 outline-gray-300 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-indigo-600">
              <input type="text" name="tags" id="tags" class="block min-w-0 grow py-1.5 pr-3 pl-1 text-base text-gray-900 placeholder:text-gray-400 focus:outline-none sm:text-sm/6" placeholder="tags">
            </div>
          </div>
        </div>

        <div class="col-span-full">
          <label for="photo" class="block text-sm/6 font-medium text-gray-900">Cover Photo</label>
          <div class="mt-2 flex items-center gap-x-3">
            <svg class="size-12 text-gray-300" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" data-slot="icon">
              <path fill-rule="evenodd" d="M18.685 19.097A9.723 9.723 0 0 0 21.75 12c0-5.385-4.365-9.75-9.75-9.75S2.25 6.615 2.25 12a9.723 9.723 0 0 0 3.065 7.097A9.716 9.716 0 0 0 12 21.75a9.716 9.716 0 0 0 6.685-2.653Zm-12.54-1.285A7.486 7.486 0 0 1 12 15a7.486 7.486 0 0 1 5.855 2.812A8.224 8.224 0 0 1 12 20.25a8.224 8.224 0 0 1-5.855-2.438ZM15.75 9a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" clip-rule="evenodd" />
            </svg>
            <button type="button" onclick="openCoverModal()" class=" bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900 shadow-xs ring-1 ring-gray-300 ring-inset hover:bg-gray-50">Change</button>
          </div>
      </div>
    </div>
  <!-- Coming soon 
    <div class="grid max-w-7xl grid-cols-1 gap-x-8 gap-y-10 px-4 py-16 sm:px-6 md:grid-cols-3 lg:px-8">
      <div>
        <h2 class="text-base/7 font-semibold text-gray-900">Additional Content</h2>
        <p class="mt-1 text-sm/6 text-gray-600">Add an Album or Collection to the post.</p>
      </div>

      <div class="max-w-2xl space-y-10 md:col-span-2">
        <fieldset>
          <legend class="text-sm/6 font-semibold text-gray-900">By email</legend>
          <div class="mt-6 space-y-6">
            <div class="flex gap-3">
              <div class="flex h-6 shrink-0 items-center">
                <div class="group grid size-4 grid-cols-1">
                  <input id="comments" aria-describedby="comments-description" name="comments" type="checkbox" checked class="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 bg-white checked:border-indigo-600 checked:bg-indigo-600 indeterminate:border-indigo-600 indeterminate:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 forced-colors:appearance-auto">
                  <svg class="pointer-events-none col-start-1 row-start-1 size-3.5 self-center justify-self-center stroke-white group-has-disabled:stroke-gray-950/25" viewBox="0 0 14 14" fill="none">
                    <path class="opacity-0 group-has-checked:opacity-100" d="M3 8L6 11L11 3.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <path class="opacity-0 group-has-indeterminate:opacity-100" d="M3 7H11" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                </div>
              </div>
              <div class="text-sm/6">
                <label for="comments" class="font-medium text-gray-900">Comments</label>
                <p id="comments-description" class="text-gray-500">Get notified when someones posts a comment on a posting.</p>
              </div>
            </div>
            <div class="flex gap-3">
              <div class="flex h-6 shrink-0 items-center">
                <div class="group grid size-4 grid-cols-1">
                  <input id="candidates" aria-describedby="candidates-description" name="candidates" type="checkbox" class="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 bg-white checked:border-indigo-600 checked:bg-indigo-600 indeterminate:border-indigo-600 indeterminate:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 forced-colors:appearance-auto">
                  <svg class="pointer-events-none col-start-1 row-start-1 size-3.5 self-center justify-self-center stroke-white group-has-disabled:stroke-gray-950/25" viewBox="0 0 14 14" fill="none">
                    <path class="opacity-0 group-has-checked:opacity-100" d="M3 8L6 11L11 3.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <path class="opacity-0 group-has-indeterminate:opacity-100" d="M3 7H11" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                </div>
              </div>
              <div class="text-sm/6">
                <label for="candidates" class="font-medium text-gray-900">Candidates</label>
                <p id="candidates-description" class="text-gray-500">Get notified when a candidate applies for a job.</p>
              </div>
            </div>
            <div class="flex gap-3">
              <div class="flex h-6 shrink-0 items-center">
                <div class="group grid size-4 grid-cols-1">
                  <input id="offers" aria-describedby="offers-description" name="offers" type="checkbox" class="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 bg-white checked:border-indigo-600 checked:bg-indigo-600 indeterminate:border-indigo-600 indeterminate:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 forced-colors:appearance-auto">
                  <svg class="pointer-events-none col-start-1 row-start-1 size-3.5 self-center justify-self-center stroke-white group-has-disabled:stroke-gray-950/25" viewBox="0 0 14 14" fill="none">
                    <path class="opacity-0 group-has-checked:opacity-100" d="M3 8L6 11L11 3.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <path class="opacity-0 group-has-indeterminate:opacity-100" d="M3 7H11" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                </div>
              </div>
              <div class="text-sm/6">
                <label for="offers" class="font-medium text-gray-900">Offers</label>
                <p id="offers-description" class="text-gray-500">Get notified when a candidate accepts or rejects an offer.</p>
              </div>
            </div>
          </div>
        </fieldset>
      </div>
    </div>-->
  </div>

  <div class="mt-6 flex items-center justify-end gap-x-6">
    <button type="button" class="text-sm/6 font-semibold text-gray-900">Cancel</button>
    <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Save</button>
  </div>
</form>



          </main>
        </div>
        <script src="js/tailwind.js"></script>
        <script>
          document.addEventListener("DOMContentLoaded", function () {
            new EasyMDE({
              element: document.getElementById("content"),
              spellChecker: false,
              autosave: {
                enabled: false
              },
              placeholder: "Please enter you content",
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
  function openCoverModal() {
    document.getElementById('coverModal').classList.remove('hidden');
  }
  function closeCoverModal() {
    document.getElementById('coverModal').classList.add('hidden');
  }
  function switchCoverTab(tab) {
    document.getElementById('cover-upload').classList.toggle('hidden', tab !== 'upload');
    document.getElementById('cover-choose').classList.toggle('hidden', tab !== 'choose');
    document.getElementById('tab-upload').classList.toggle('border-b-2', tab === 'upload');
    document.getElementById('tab-choose').classList.toggle('border-b-2', tab === 'choose');
    document.getElementById('tab-upload').classList.toggle('text-sky-600', tab === 'upload');
    document.getElementById('tab-choose').classList.toggle('text-sky-600', tab === 'choose');
  }
  function selectCover(path) {
    document.getElementById('cover').value = path;
    closeCoverModal();
  }
</script>
    </body>
</html>