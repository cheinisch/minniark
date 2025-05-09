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
  $descriptionHtml = $Parsedown->text($albumdata['Description']);

  $cacheImage = get_cacheimage($albumdata['HeadImage'],"l");

  $headimage = null;

  if($albumdata['HeadImage'] != null || $albumdata['HeadImage'] != '')
  {
    $headimage = "../cache/images/".$cacheImage;
  }else{
    $headimage = "img/placeholder.png";
  }

?>

<!DOCTYPE html>
<html lang="<?php echo get_language(); ?>">
    <head>      
      <meta charset="UTF-8">        
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Images - <?php echo get_sitename(); ?></title>

      <!-- Tailwind CSS -->
      <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
      <link rel="stylesheet" href="../lib/simplemde/simplemde.min.css">
      <link rel="stylesheet" href="css/additional.css">
      <style>
        :root {
          --img-max-width: 250px;
        }

        @media (min-width: 768px) {
          .dynamic-image-width {
            max-width: var(--img-max-width);
          }
        }

        @media (max-width: 767px) {
          .dynamic-image-width {
            max-width: 100% !important;
          }
        }
      </style>
    </head>
    <body class="min-h-screen flex flex-col">
      <!-- Add Image to Album Modal-->
      <div id="addToAlbumImageModal" class="hidden relative z-10" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500/75 transition-opacity" aria-hidden="true"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
          <div class="flex min-h-full items-stretch justify-center text-center md:items-center md:px-2 lg:px-4">
            <div class="flex w-full transform text-left text-base transition md:my-8 md:max-w-4xl md:px-4 lg:max-w-5xl">
              <div class="relative flex w-full flex-col items-start overflow-hidden bg-white px-4 pt-14 pb-8 shadow-2xl sm:px-6 sm:pt-8 md:p-6 lg:p-8">
                <!-- Close Button -->
                <button type="button" id="closeAddToAlbumImageModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 sm:top-8 sm:right-6 md:top-6 md:right-6 lg:top-8 lg:right-8">
                  <span class="sr-only">Close</span>
                  <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                  </svg>
                </button>

                <h2 class="text-2xl font-bold text-gray-900 sm:pr-12 mb-4">Add Images to Album</h2>

                <input type="text" id="imageSearchInput" placeholder="Search by image name..." class="w-full mb-4 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-sky-500 text-sm">

                <form id="addImagesForm" method="post" action="backend_api/add_images_to_album.php" class="w-full">
                  <input type="hidden" name="album" value="<?php echo htmlspecialchars($albumTitle); ?>">

                  <div id="imageList" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 max-h-[60vh] overflow-y-auto mb-6">
                    <?php
                    $allImages = getAllUploadedImages(); // Annahme: gibt Dateinamen-Array zurück
                    foreach ($allImages as $img) {
                      echo '
                      <label class="block text-sm text-center cursor-pointer">
                        <input type="checkbox" name="images[]" value="' . htmlspecialchars($img['filename']) . '" class="sr-only peer">
                        <div class="peer-checked:ring-2 peer-checked:ring-sky-500 rounded overflow-hidden border border-gray-300">
                          <img src="../userdata/content/images/' . htmlspecialchars($img['filename']) . '" alt="' . $img['title'] . '" class="object-cover w-full aspect-square">
                        </div>
                        <span class="block mt-1 truncate text-xs">' . $img['title'] . '</span>
                      </label>';
                    }
                    ?>
                  </div>

                  <div class="flex gap-4 justify-end">
                    <button type="button" id="cancelAddToAlbumImage" class="flex-1 flex items-center justify-center border border-transparent bg-rose-500 px-8 py-3 text-base font-medium text-white hover:bg-rose-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-gray-50 focus:outline-none">
                      Cancel
                    </button>
                    <button type="submit" class="flex-1 flex items-center justify-center border border-transparent bg-sky-500 px-8 py-3 text-base font-medium text-white hover:bg-sky-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-50 focus:outline-none">
                      Add Selected
                    </button>
                  </div>
                </form>

              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Confirm Modal -->
      <div id="confirmModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden">
        <div class="fixed inset-0 bg-gray-500/75 transition-opacity" aria-hidden="true"></div>
          <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto">
            <div class="relative w-full max-w-xl mx-auto shadow-lg bg-white p-6">
              <h2 class="text-xl font-semibold text-gray-800">Delete Confirmation</h2>
              <p class="mt-4 text-gray-600">Do you really want to remove this image from album?</p>
              <div class="flex justify-end mt-6 space-x-3">
                <button id="confirmNo" class="px-4 py-2 bg-sky-500 text-white hover:bg-sky-600">Cancel</button>
                <button id="confirmYes" class="px-4 py-2 bg-red-500 text-white hover:bg-red-600">Remove</button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Add Album Modal -->
      <div id="addAlbumModal" class="hidden relative z-10" role="dialog" aria-modal="true">
        
        <div class="fixed inset-0 hidden bg-gray-500/75 transition-opacity md:block" aria-hidden="true"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
          <div class="flex min-h-full items-stretch justify-center text-center md:items-center md:px-2 lg:px-4">

            <div class="flex w-full transform text-left text-base transition md:my-8 md:max-w-2xl md:px-4 lg:max-w-lg">
              <div class="relative flex w-full items-center overflow-hidden bg-white px-4 pt-14 pb-8 shadow-2xl sm:px-6 sm:pt-8 md:p-6 lg:p-8">
                <button type="button" id="closeAlbumModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 sm:top-8 sm:right-6 md:top-6 md:right-6 lg:top-8 lg:right-8">
                  <span class="sr-only">Close</span>
                  <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                  </svg>
                </button>

                <div class="grid w-full grid-cols-1 items-start gap-x-6 gap-y-8 sm:grid-cols-12 lg:gap-x-8">
                  <div class="col-span-12">
                    <h2 class="text-2xl font-bold text-gray-900 sm:pr-12">Create new Album</h2>

                    <section aria-labelledby="information-heading" class="mt-3">
                      <h3 id="information-heading" class="sr-only">Album information</h3>
                      <div class="sm:col-span-3">
                        <label for="album-title" class="block text-2xl text-gray-900">Album Name</label>
                        <div class="mt-2">
                        <input type="text" name="album-title" id="album-title" value="" placeholder="Enter album name" class="block w-full bg-white/5 px-3 py-1.5 text-base text-gray-700 outline-1 -outline-offset-1 outline-gray-500 placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-sky-500">
                        </div>
                      </div>
                      <div class="mt-3">
                        <h4 class="text-xl text-gray-900 sm:pr-12">Set Album description</h4>
                      </div>
                      <div class="mt-6 flex gap-4">
                        <button type="submit" class="flex-1 flex items-center justify-center border border-transparent bg-sky-500 px-8 py-3 text-base font-medium text-white hover:bg-sky-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-50 focus:outline-none">
                        Save
                        </button>
                        <button type="button" class="flex-1 flex items-center justify-center border border-transparent bg-rose-500 px-8 py-3 text-base font-medium text-white hover:bg-rose-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-gray-50 focus:outline-none">
                        Cancel
                        </button>
                      </div>
                    </section>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Add Collection Modal -->
      <div id="addCollectionModal" class="hidden relative z-10" role="dialog" aria-modal="true">
        
        <div class="fixed inset-0 hidden bg-gray-500/75 transition-opacity md:block" aria-hidden="true"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
          <div class="flex min-h-full items-stretch justify-center text-center md:items-center md:px-2 lg:px-4">

            <div class="flex w-full transform text-left text-base transition md:my-8 md:max-w-2xl md:px-4 lg:max-w-lg">
              <div class="relative flex w-full items-center overflow-hidden bg-white px-4 pt-14 pb-8 shadow-2xl sm:px-6 sm:pt-8 md:p-6 lg:p-8">
                <button type="button" id="closeCollectionModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 sm:top-8 sm:right-6 md:top-6 md:right-6 lg:top-8 lg:right-8">
                  <span class="sr-only">Close</span>
                  <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                  </svg>
                </button>

                <div class="grid w-full grid-cols-1 items-start gap-x-6 gap-y-8 sm:grid-cols-12 lg:gap-x-8">
                  <div class="col-span-12">
                    <h2 class="text-2xl font-bold text-gray-900 sm:pr-12">Create new Collection</h2>

                    <section aria-labelledby="information-heading" class="mt-3">
                      <h3 id="information-heading" class="sr-only">Collection information</h3>
                      <div class="sm:col-span-3">
                        <label for="album-title" class="block text-2xl text-gray-900">Collection Name</label>
                        <div class="mt-2">
                        <input type="text" name="album-title" id="album-title" value="" placeholder="Enter album name" class="block w-full bg-white/5 px-3 py-1.5 text-base text-gray-700 outline-1 -outline-offset-1 outline-gray-500 placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-sky-500">
                        </div>
                      </div>
                      <div class="mt-3">
                        <h4 class="text-xl text-gray-900 sm:pr-12">Set Collection description</h4>
                      </div>
                      <div class="mt-6 flex gap-4">
                        <button type="submit" class="flex-1 flex items-center justify-center border border-transparent bg-sky-500 px-8 py-3 text-base font-medium text-white hover:bg-sky-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-50 focus:outline-none">
                        Save
                        </button>
                        <button type="button" class="flex-1 flex items-center justify-center border border-transparent bg-rose-500 px-8 py-3 text-base font-medium text-white hover:bg-rose-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-gray-50 focus:outline-none">
                        Cancel
                        </button>
                      </div>
                    </section>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>      
      <!-- Upload Modal -->
      <div id="uploadModal" class="relative z-50 hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500/75 transition-opacity" aria-hidden="true"></div>
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto">
          <div class="relative w-full max-w-xl mx-auto rounded-lg shadow-lg bg-white p-6">
            <div class="flex items-center justify-between mb-4">
              <h2 class="text-xl font-semibold text-gray-800">Neues Medium hochladen</h2>
              <button id="closeUpload" class="text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
            <div class="border border-gray-200 rounded-lg p-6">
              <label for="file-upload" class="block text-sm font-medium text-gray-700">Upload File</label>
              <div id="uploadBox" class="mt-2 flex justify-center rounded-lg border-2 border-dashed border-gray-300 p-6 cursor-pointer">
                <div class="text-center">
                  <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5V18a2 2 0 002 2h14a2 2 0 002-2v-1.5M7.5 11.5L12 7m0 0l4.5 4.5M12 7v10" />
                  </svg>
                  <p class="mt-2 text-sm text-gray-600">Click or drop file here</p>
                  <p class="mt-1 text-xs text-gray-500">PNG, JPG up to <?php echo get_uploadsize(); ?></p>
                </div>
              </div>
              <input id="fileInput" type="file" class="hidden" multiple>
              <div id="progressContainer" class="mt-4 w-full bg-gray-200 rounded-full h-2.5">
                <div id="progressBar" class="bg-blue-600 h-2.5 rounded-full text-xs text-center text-white"></div>
              </div>
              <div id="messageBox" class="mt-2 text-sm"></div>
            </div>
          </div>
        </div>
      </div>
        <!-- Normal Layout -->
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
                        <!-- Current: "border-indigo-500 text-gray-900", Default: "border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700" -->
                        <a href="dashboard.php" class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-base font-medium text-gray-600 dark:text-gray-300 hover:border-sky-400 hover:text-sky-400">Dashboard</a>
                        <a href="media.php" class="inline-flex items-center border-b-2 border-sky-400 px-1 pt-1 text-base font-medium text-sky-400">Images</a>
                        <a href="blog.php" class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-base font-medium text-gray-600 dark:text-gray-300 hover:border-sky-400 hover:text-sky-400">Blogposts</a>
                        <a href="pages.php" class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-base font-medium text-gray-600 dark:text-gray-300 hover:border-sky-400 hover:text-sky-400">Pages</a>
                      </div>
                    </div>
                    <div class="flex items-center">
                         <div class="shrink-0">
                        <button type="button" id="addImagetoAlbumBtn" class="relative inline-flex items-center gap-x-1.5  bg-sky-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-sky-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600">
                          <svg class="-ml-0.5 size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                            <path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
                          </svg>
                          Add Image to Album
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
                    <!-- Current: "bg-indigo-50 border-indigo-500 text-indigo-700", Default: "border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700" -->
                    <a href="dashboard.php" class="block border-l-4 border-transparent py-2 pr-4 pl-3 text-base font-medium text-gray-600 dark:text-gray-300 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-700 sm:pr-6 sm:pl-5">Dashboard</a>
                    <a href="media.php" class="block border-l-4 border-sky-400 py-2 pr-4 pl-3 text-base font-medium text-sky-400 sm:pr-6 sm:pl-5">Images</a>
                    <a href="blog.php" class="block border-l-4 border-transparent py-2 pr-4 pl-3 text-base font-medium text-gray-600 dark:text-gray-300 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-700 sm:pr-6 sm:pl-5">Blogposts</a>
                    <a href="pages.php" class="block border-l-4 border-transparent py-2 pr-4 pl-3 text-base font-medium text-gray-600 dark:text-gray-300 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-700 sm:pr-6 sm:pl-5">Pages</a>
                  </div>
                  <div class="border-t border-gray-500 pt-4 pb-3">
                    <div class="mt-3 space-y-1">
                      <a href="#" class="block px-4 py-2 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6">Content</a>
                      <div class="pl-5">
                        <a href="?" class="block px-4 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6">All Photos (<?php count_images(); ?>)</a>
                      </div>
                      <?php get_imageyearlist(true); ?>
                      <a href="#" class="block px-4 py-2 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6">Albums (1)</a>
                      <div class="pl-5">
                        <a href="#" class="block px-4 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6">Album 1</a>
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
          <aside class="hidden md:block max-w-[280px] w-full bg-neutral-200 dark:bg-gray-950 overflow-auto flex-1 text-lg">
            <nav class="flex flex-1 flex-col pt-5 px-15 text-gray-600 text-base font-medium" aria-label="Sidebar">
                <ul role="list" class="-mx-2 space-y-1">
                  <li>Content</li>
                  <ul class="px-5">
                    <li><a href="media.php" class="text-gray-400 hover:text-sky-400">All Photos (<?php count_images(); ?>)</a></li>
                  </ul>
                  <li class="flex items-center gap-1">
                    Albums (<a href="#" id="add-album">add new</a>)
                    <!--<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <line x1="12" y1="5" x2="12" y2="19" />
                      <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>-->
                  </li>
                  <ul class="px-5">
                    <?php 

                      $albums = getAlbumList();

                      foreach($albums as $album)
                      {
                        echo '<li id="'.$album['Name'].'"><a href="album-detail.php?album='.$album['Name'].'" class="text-gray-400 hover:text-sky-400">'.$album['Name'].'</a></li>';
                      }                    
                    ?>
                  </ul>
                  <li class="flex items-center gap-1">
                    Collections (<a href="#" id="add-collection">add new</a>)
                    <!--<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <line x1="12" y1="5" x2="12" y2="19" />
                      <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>-->
                  </li>
                  <ul class="px-5">
                    <li><a href="#" class="text-gray-400 hover:text-sky-400">Album 1</a></li>
                  </ul>                    
                </ul>
              </nav>
          </aside>
          <main class="flex-1 bg-white dark:bg-neutral-900 overflow-auto">
            <div class="flex">
              <!-- Album Information -->
              <div class="min-w-xl px-5 mt-5 mb-5">
                <div>
                  <div class="max-w-xl">
                    <img src="<?php echo $headimage; ?>">
                  </div>
                  <div id="text-show-frame" class="">
                    <div class="pt-5">
                      <h2 id="headline" class="text-3xl pb-5 dark:text-gray-400"><?php echo $albumdata['Name']; ?></h2>
                    </div>
                    <div class="dark:text-gray-400">
                      <?php echo $descriptionHtml; ?>
                    </div>
                  </div>
                  <form action="backend_api/album_update.php" method="post">
                    <div id="text-edit-frame" class="hidden">                    
                      <div class="pt-10">
                        <input type="text" id="album-title-edit" name="album-title-edit" value="<?php echo $albumdata['Name']; ?>" class="border-b focus:border-b-2 focus:border-sky-500 outline-none border-gray-400">
                        <input type="hidden" id="album-current-title" name="album-current-title" value="<?php echo $albumdata['Name']; ?>">
                      </div>
                      <div class="pt-2 bg-white">
                        <textarea id="album-description" name="album-description" class="w-full border-b focus:border-b-2 focus:border-sky-500 outline-none border-gray-400" placeholder="Enter Album description" rows="10"><?php echo $albumdata['Description']; ?></textarea>
                      </div>
                    </div>
                    <div id="button_group" class="space-x-2 mt-2">
                      <div id="normal-group">
                      <button type="button" id="edit_text" class="relative inline-flex items-center gap-x-1.5  bg-sky-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-sky-600">
                        Edit
                      </button>
                      </div>
                      <div id="edit-group" class="hidden">
                      <button type="submit" id="save_edit" class="hidden relative inline-flex items-center gap-x-1.5  bg-sky-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-sky-600">
                        Save
                      </button>
                      <button type="button" id="cancel_edit" class="hidden relative inline-flex items-center gap-x-1.5  bg-gray-300 px-3 py-2 text-sm font-semibold text-gray-800 shadow-xs hover:bg-gray-400">
                        Cancel
                      </button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
              <!-- Content Bilderblock -->
              <div class="px-4 sm:px-6 lg:px-8 mt-5 mb-5 flex flex-wrap gap-4">
                <?php

                  $albumFile = strtolower($albumTitle);
                  renderImageGalleryAlbum($albumFile); // Galerie ausgeben              
                ?>
              </div>
            </div>
          </main>
        </div>

        <script src="../lib/simplemde/simplemde.min.js"></script>
        <script src="js/tailwind.js"></script>
        <script src="js/slider.js"></script>
        <script src="js/album_collection.js"></script>
        <script src="js/album_edit.js"></script>
        <script src="js/image_dropdown.js"></script>
        <script src="js/album_add_image.js"></script>
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
          document.getElementById('confirmNo').addEventListener('click', () => {
            document.getElementById('confirmModal').classList.add('hidden');
            pendingLink = null;
          });

          // Bestätigen → Weiterleitung
          document.getElementById('confirmYes').addEventListener('click', () => {
            if (pendingLink) {
              removeBtn = document.getElementById('confirmYes');
              removeBtn.innerText = 'Removing...';
              window.location.href = pendingLink;
            }
          });
        </script>
    </body>
</html>