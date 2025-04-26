<?php
  require_once( __DIR__ . "/../functions/function_backend.php");
  security_checklogin();

  $image_url = $_GET['image'];

  $imageData = getImage($image_url);

    // Daten extrahieren
    $fileName = $imageData['filename'];
    $imagePath = "../userdata/content/images/" . $fileName;
    $title = $imageData['title'];
    $description = $imageData['description'];
    $uploadDate = $imageData['upload_date'];
  
    $rating = $imageData['rating'] ?? 0;

    // Exif-Daten
    $camera = $imageData['exif']['Camera'] ?? 'Unknown';
    $lens = $imageData['exif']['Lens'] ?? 'Unknown';
    $focallength = $imageData['exif']['Focal Length'] ?? 'Unknown';
    $apertureRaw = $imageData['exif']['Aperture'] ?? 'Unknown';
    $shutterSpeedRaw = $imageData['exif']['Shutter Speed'] ?? 'Unknown';
    $iso = $imageData['exif']['ISO'] ?? 'Unknown';
    $dateTaken = $imageData['exif']['Date'] ?? 'Unknown';
  
    // **Aperture formatieren (f/28/10 → f/2.8)**
    $aperture = "Unknown";
    if (preg_match('/f\/(\d+)\/(\d+)/', $apertureRaw, $matches)) {
        $apertureValue = round($matches[1] / $matches[2], 1); // 28/10 → 2.8
        $aperture = "f/" . $apertureValue;
    }else{
      $aperture = $apertureRaw;
    }
  
    // **Shutter Speed formatieren (4/1 → 4s oder 1/250 → 1/250s)**
    $shutterSpeed = "Unknown";
    if (preg_match('/(\d+)\/(\d+)/', $shutterSpeedRaw, $matches)) {
        $numerator = (int)$matches[1];  // Zähler
        $denominator = (int)$matches[2]; // Nenner
        
        if ($numerator >= $denominator) {
            // Belichtungszeit ≥ 1 Sekunde → "4s"
            $shutterSpeed = ($numerator / $denominator) . "s";
        } else {
            // Belichtungszeit < 1 Sekunde → "1/250s"
            $shutterSpeed = "1/" . round($denominator / $numerator) . "s";
        }
    }
  
    // GPS-Daten für OpenStreetMap
    $latitude = $imageData['exif']['GPS']['latitude'] ?? 0;
    $longitude = $imageData['exif']['GPS']['longitude'] ?? 0;
  
    $hasGPS = !is_null($latitude) && !is_null($longitude);
?>

<!DOCTYPE html>
<html lang="<?php echo get_language(); ?>">
    <head>      
        <meta charset="UTF-8">        
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Images - <?php echo get_sitename(); ?></title>

        <!-- Tailwind CSS -->
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
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
        <!-- Leaflet CSS -->
        <link
          rel="stylesheet"
          href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        />

        <!-- Leaflet JS -->
        <script
          src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        ></script>
    </head>
    <body class="min-h-screen flex flex-col">
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
            <nav class="bg-gray-950 shadow-sm">
                <div class="mx-auto max-w-12xl px-4 sm:px-6 lg:px-8">
                  <div class="flex h-16 justify-between">
                    <div class="flex">
                      <div class="mr-2 -ml-2 flex items-center md:hidden">
                        <!-- Mobile menu button -->
                        <button type="button" class="relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:ring-2 focus:ring-sky-500 focus:outline-hidden focus:ring-inset" aria-controls="mobile-menu" aria-expanded="false">
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
                        <a href="dashboard.php" class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-gray-300 hover:border-sky-400 hover:text-sky-400">Dashboard</a>
                        <a href="media.php" class="inline-flex items-center border-b-2 border-sky-400 px-1 pt-1 text-sm font-medium text-sky-400">Images</a>
                        <a href="blog.php" class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-gray-300 hover:border-sky-400 hover:text-sky-400">Blogposts</a>
                        <a href="pages.php" class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-gray-300 hover:border-sky-400 hover:text-sky-400">Pages</a>
                      </div>
                    </div>
                    <div class="flex items-center">
                         <div class="shrink-0">
                        <button type="button" class="relative inline-flex items-center gap-x-1.5 rounded-md bg-sky-400 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-sky-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600">
                          <svg class="-ml-0.5 size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                            <path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
                          </svg>
                          Upload new Image
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
                    <a href="media.php" class="block border-l-4 border-sky-400 py-2 pr-4 pl-3 text-base font-medium text-sky-400 sm:pr-6 sm:pl-5">Images</a>
                    <a href="blog.php" class="block border-l-4 border-transparent py-2 pr-4 pl-3 text-base font-medium text-gray-300 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-700 sm:pr-6 sm:pl-5">Blogposts</a>
                    <a href="pages.php" class="block border-l-4 border-transparent py-2 pr-4 pl-3 text-base font-medium text-gray-300 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-700 sm:pr-6 sm:pl-5">Pages</a>
                  </div>
                  <div class="border-t border-gray-500 pt-4 pb-3">
                    <div class="mt-3 space-y-1">
                      <a href="#" class="block px-4 py-2 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6">Modify Image</a>
                      <div class="pl-5">
                        <a href="?" class="block px-4 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6">Rotate left</a>
                        <a href="?" class="block px-4 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6">Rotate right</a>
                        <a href="?" class="block px-4 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6">Flip Horizontal</a>
                        <a href="?" class="block px-4 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6">Flip Vertical</a>
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
          <aside class="hidden md:block max-w-[250px] w-full bg-gray-950 overflow-auto flex-1">
            <nav class="flex flex-1 flex-col pt-5 px-15 text-gray-300 text-sm font-medium" aria-label="Sidebar">
              <ul role="list" class="-mx-2 space-y-1">
                <li>Modify Image</li>
                <ul class="px-5">
                  <li><a href="?" class="text-gray-400 hover:text-sky-400">Rotate left</a></li>
                  <li><a href="?" class="text-gray-400 hover:text-sky-400">Rotate right</a></li>
                  <li><a href="?" class="text-gray-400 hover:text-sky-400">Flip Horizontal</a></li>
                  <li><a href="?" class="text-gray-400 hover:text-sky-400">Flip Vertical</a></li>
                </ul>                 
              </ul>
            </nav>
          </aside>
          <main class="flex-1 bg-neutral-900 overflow-auto">
            <div class="px-4 sm:px-6 lg:px-8 mt-5 mb-5 flex flex-wrap">
              <!-- IMAGE -->
              <div class="max-w-full lg:max-w-[750px] xl:max-w-3/4 2xl:max-w-4/5 4xl:max-w-7/8 mx-auto">
                <img src="<?php echo $imagePath; ?>" class="w-full 2xl:max-w-7xl h-auto border-2 border-gray-300">
                <article class="text-wrap text-gray-200 pt-2">
                  <h2 class="text-xl font-semibold">
                    <span id="image-title"><?php echo htmlspecialchars($title); ?></span>
                    <input type="text" id="edit-title" class="hidden w-full mt-2 p-1 border rounded text-sm" value="<?php echo htmlspecialchars($title); ?>">
                  </h2>

                  <div id="text_container">
                    <p id="editable_text"><?php echo nl2br(htmlspecialchars($description)); ?></p>
                    <textarea id="edit-description" class="hidden w-full mt-2 p-1 border rounded text-sm"><?php echo htmlspecialchars($description); ?></textarea>
                  </div>

                  <div id="button_group" class="space-x-2 mt-2">
                    <button type="button" id="edit_text" class="relative inline-flex items-center gap-x-1.5 rounded-md bg-sky-400 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-sky-600">
                      Edit
                    </button>
                    <button type="button" id="cancel_edit" class="invisible relative inline-flex items-center gap-x-1.5 rounded-md bg-gray-300 px-3 py-2 text-sm font-semibold text-gray-800 shadow-xs hover:bg-gray-400">
                      Cancel
                    </button>
                  </div>
                </article>
              </div>
              <!-- META INFO -->
              <div class="max-w-full xl:max-w-1/5 xl:min-w-1/4 2xl:min-w-1/5 4xl:max-w-1/8 min-w-full pt-2 md:pt-0 pl-0 md:pl-2 ml-auto">
                <div class="bg-white shadow-md rounded-xl p-6 space-y-4 min-h-full">
                  <h2 class="text-xl font-semibold">Metadata</h2>
                  <ul class="divide-y divide-gray-200 text-sm text-gray-700">
                    <li class="flex justify-between py-2">
                      <span class="font-medium">Kamera</span>
                      <span><?php echo $camera; ?></span>
                    </li>
                    <li class="flex justify-between py-2">
                      <span class="font-medium">Lens</span>
                      <span><?php echo $lens; ?></span>
                    </li>
                    <li class="flex justify-between py-2">
                      <span class="font-medium">Aperture</span>
                      <span><?php echo $aperture; ?></span>
                    </li>
                    <li class="flex justify-between py-2">
                      <span class="font-medium">Shutter Speed</span>
                      <span><?php echo $shutterSpeed; ?></span>
                    </li>
                    <li class="flex justify-between py-2">
                      <span class="font-medium">ISO</span>
                      <span><?php echo $iso; ?></span>
                    </li>
                    <li class="flex justify-between py-2">
                      <span class="font-medium">Focal length</span>
                      <span><?php echo $focallength; ?></span>
                    </li>
                    <li class="flex justify-between py-2">
                      <span class="font-medium">Date</span>
                      <span><?php echo $dateTaken; ?></span>
                    </li>
                  </ul>
                  <h2 class="text-xl font-semibold">Informationen</h2>
                  <ul class="divide-y divide-gray-200 text-sm text-gray-700">
                    <li class="flex justify-between items-center py-2">
                      <span class="font-medium">Rating</span>
                      <span id="rating-stars" class="flex space-x-1 text-yellow-400" data-rating="<?php echo htmlspecialchars($rating); ?>" data-filename="<?php echo htmlspecialchars($fileName); ?>">></span>
                    </li>
                    <li class="flex justify-between py-2">
                      <span class="font-medium">Tags</span>
                      <span>coming soon</span>
                    </li>
                  </ul>
                  <h2 class="text-xl font-semibold">GPS Data</h2>
                  <ul class="divide-y divide-gray-200 text-sm text-gray-700">
                    <li class="flex justify-between py-2">
                      <span class="font-medium">Lat:</span>
                      <span><?php echo $latitude; ?></span>
                    </li>
                    <li class="flex justify-between py-2">
                      <span class="font-medium">Lon:</span>
                      <span><?php echo $longitude; ?></span>
                    </li>
                    <li>
                      <div id="map" class="w-full h-48 border-2 border-gray-300"></div>
                    </li>
                  </ul>
                  <div id="button_group_meta" class=" relative flex space-x-2 mt-2">
                    <button type="button" id="edit_metadata" class="inline-block align-bottom w-1/2 inline-flex justify-center items-center gap-x-1.5 rounded-md bg-sky-400 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-sky-600">
                      Edit Metadata
                    </button>
                    <button type="button" id="update-exif" class="inline-block align-bottom w-1/2 inline-flex justify-center items-center gap-x-1.5 rounded-md bg-sky-400 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-sky-600">
                      Sync Metadata
                    </button>
                  </div>
                  <div id="button_group_meta_manual" class="hidden relative flex space-x-2 mt-2">
                    <button type="button" id="save_metadata" class="inline-block align-bottom w-1/2 inline-flex justify-center items-center gap-x-1.5 rounded-md bg-sky-400 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-sky-600">
                      Save
                    </button>
                    <button type="button" id="cancel_metadata" class="inline-block align-bottom w-1/2 inline-flex justify-center items-center gap-x-1.5 rounded-md bg-rose-400 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-rose-600">
                      Cancel
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </main>
        </div>
        <script src="js/tailwind.js"></script>
        <script src="js/image_rating.js"></script>
        <script src="js/edit_text.js"></script>
        <script src="js/sync_exifdata.js?<?=time()?>"></script>
        <script src="js/edit_exifdata.js"></script>
        <script src="js/file_upload.js"></script>
        <script>
          const map = L.map('map').setView([<?php echo $latitude; ?>, <?php echo $longitude; ?>], 12); // Beispielkoordinaten (London)

          L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution:
              '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
          }).addTo(map);

          L.marker([<?php echo $latitude; ?>, <?php echo $longitude; ?>]).addTo(map)
            //.bindPopup('Beispielstandort')
            .openPopup();
        </script>
    </body>
</html>