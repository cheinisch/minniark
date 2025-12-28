<?php
require_once(__DIR__ . "/../functions/function_backend.php");
require_once __DIR__ . '/../app/autoload.php';
security_checklogin();

$image_url = $_GET['image'] ?? '';
$imageData = getImage($image_url);

// Lizenzmanager
$lm = new LicenseManager(dirname(__DIR__), 'https://api.minniark.com/v1/data/creem');
$licensed = $lm->isLicensed();

// Daten extrahieren
$fileName    = $imageData['filename'] ?? '';
$imagePath   = "../userdata/content/images/" . $fileName . "?v=" . time();
$title       = $imageData['title'] ?? '';
$description = $imageData['description'] ?? '';
$uploadDate  = $imageData['upload_date'] ?? null;

$rating = $imageData['rating'] ?? 0;

// Exif-Daten
$camera         = $imageData['exif']['Camera'] ?? 'Unknown';
$lens           = $imageData['exif']['Lens'] ?? 'Unknown';
$focallength    = $imageData['exif']['Focal Length'] ?? 'Unknown';
$apertureRaw    = $imageData['exif']['Aperture'] ?? 'Unknown';
$shutterSpeedRaw= $imageData['exif']['Shutter Speed'] ?? 'Unknown';
$iso            = $imageData['exif']['ISO'] ?? 'Unknown';
$dateTaken      = $imageData['exif']['Date'] ?? 'Unknown';

// Name Fix
$camera = str_replace('Canon Canon', 'Canon', $camera);

// Aperture formatieren (f/28/10 → f/2.8)
$aperture = "Unknown";
if (is_string($apertureRaw) && preg_match('/f\/(\d+)\/(\d+)/', $apertureRaw, $matches)) {
    $apertureValue = round(((int)$matches[1]) / max(1, (int)$matches[2]), 1);
    $aperture = "f/" . $apertureValue;
} else {
    $aperture = $apertureRaw;
}

// Shutter Speed formatieren (4/1 → 4s oder 1/250 → 1/250s)
$shutterSpeed = "Unknown";
if (is_string($shutterSpeedRaw) && preg_match('/(\d+)\/(\d+)/', $shutterSpeedRaw, $matches)) {
    $numerator = (int)$matches[1];
    $denominator = max(1, (int)$matches[2]);

    if ($numerator >= $denominator) {
        $shutterSpeed = ($numerator / $denominator) . "s";
    } else {
        $shutterSpeed = "1/" . round($denominator / max(1, $numerator)) . "s";
    }
} else {
    $shutterSpeed = $shutterSpeedRaw;
}

// GPS-Daten
$latitude  = $imageData['exif']['GPS']['latitude'] ?? null;
$longitude = $imageData['exif']['GPS']['longitude'] ?? null;
$hasGPS = is_numeric($latitude) && is_numeric($longitude) && !is_null($latitude) && !is_null($longitude);

// Tags
$tags = $imageData['tags'] ?? [];
if (!is_array($tags)) {
    $tags = array_filter(array_map('trim', explode(',', (string)$tags)));
}
?>
<!DOCTYPE html>
<html lang="<?php echo get_language(); ?>">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Images - <?php echo htmlspecialchars(get_sitename()); ?></title>

  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <!-- Tailwind Plus Elements (für <el-dialog>, command/commandfor, Backdrop etc.) -->
  <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>

  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <meta name="image-file" content="<?php echo htmlspecialchars($image_url); ?>">
</head>

<body class="bg-white dark:bg-black">
  <div>
    <!-- Topbar -->
    <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-black/10 dark:border-white/10 bg-white dark:bg-black px-4 shadow-xs sm:gap-x-6 sm:px-6 lg:px-8">
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
          <div data-dropdown class="relative">
            <button type="button" class="relative flex items-center" aria-haspopup="menu" aria-expanded="false" data-trigger>
              <span class="absolute -inset-1.5"></span>
              <span class="sr-only">Open user menu</span>
              <img
                src="<?php echo htmlspecialchars(get_userimage($_SESSION['username'] ?? '')); ?>"
                alt=""
                class="size-8 rounded-full bg-gray-50 outline -outline-offset-1 outline-black/5 dark:bg-gray-800 dark:outline-white/10" />
              <span class="hidden lg:flex lg:items-center">
                <span aria-hidden="true" class="ml-4 text-sm/6 font-semibold text-gray-900 dark:text-white">
                  <?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?>
                </span>
                <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="ml-2 size-5 text-gray-400 dark:text-gray-500">
                  <path d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" />
                </svg>
              </span>
            </button>

            <div data-menu hidden role="menu"
              class="w-32 origin-top-right rounded-md py-2 shadow-lg outline outline-gray-900/5 transition transition-discrete
                     data-closed:scale-95 data-closed:transform data-closed:opacity-0
                     data-enter:duration-100 data-enter:ease-out data-leave:duration-75 data-leave:ease-in
                     bg-white dark:bg-black dark:shadow-none dark:-outline-offset-1 dark:outline-white/10">
              <a href="dashboard-personal.php"
                 class="block px-3 py-1 text-sm/6 text-gray-900 hover:bg-gray-50 focus:outline-hidden dark:text-white dark:hover:bg-white/5"
                 role="menuitem"><?php echo languageString('nav.your_profile'); ?></a>
              <a href="login.php?logout=true"
                 class="block px-3 py-1 text-sm/6 text-gray-900 hover:bg-gray-50 focus:outline-hidden dark:text-white dark:hover:bg-white/5"
                 role="menuitem"><?php echo languageString('nav.sign_out'); ?></a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Mobile nav -->
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

    <main class="bg-white dark:bg-black text-black dark:text-white">
      <div class="w-full mx-auto px-4 sm:px-6 lg:px-8 py-2 grid grid-cols-1 md:grid-cols-[minmax(0,1fr)_22rem] gap-8">
        <!-- IMAGE -->
        <article class="lg:w-full">
          <figure class="w-full mx-auto max-w-6xl">
            <img
              id="image"
              src="<?php echo htmlspecialchars($imagePath); ?>"
              alt="<?php echo htmlspecialchars($title ?: 'Image'); ?>"
              class="w-full h-auto rounded-sm border border-black/10 dark:border-white/10 shadow-xs"
            />
            <figcaption class="mt-4">
              <div class="flex items-center justify-between gap-2">
                <h2 id="title" class="text-xl font-semibold">
                  <?php echo htmlspecialchars($title); ?>
                </h2>

                <div class="flex items-center gap-2 shrink-0">
                  <!-- WICHTIG: so wie beim EXIF Modal -> command show-modal -->
                  <button
                    type="button"
                    id="edit_text"
                    command="show-modal"
                    commandfor="editImageTextModal"
                    class="text-xs px-2 py-1 rounded border border-black/10 dark:border-white/10 hover:bg-black/5 dark:hover:bg-white/10">
                    <?php echo languageString('general.edit'); ?>
                  </button>

                  <?php if ($licensed && isAI_active()) { ?>
                    <button
                      type="button"
                      id="generate_text"
                      command="show-modal"
                      commandfor="confirmAiModal"
                      class="text-xs px-2 py-1 rounded border border-black/10 dark:border-white/10 hover:bg-black/5 dark:hover:bg-white/10 whitespace-nowrap">
                      <?php echo languageString('image.generate_ai_text'); ?>
                    </button>
                  <?php } ?>
                </div>
              </div>

              <p id="description" class="mt-2 text-sm leading-6 text-black/80 dark:text-gray-300">
                <?php echo nl2br(htmlspecialchars($description)); ?>
              </p>
            </figcaption>
          </figure>
        </article>

        <!-- META / EXIF -->
        <div class="w-full md:w-92 text-black dark:text-white">
          <section class="rounded-sm border border-black/10 dark:border-white/10 bg-white dark:bg-black/40 shadow-xs mb-2">
            <header class="px-4 py-3 border-b border-black/10 dark:border-white/10">
              <h3 class="text-sm font-semibold">Metadata</h3>
            </header>

            <div class="p-4">
              <dl class="text-sm text-black/80 dark:text-gray-300 divide-y divide-black/10 dark:divide-white/10">
                <div class="py-2">
                  <div class="flex items-start justify-between gap-4">
                    <dt class="font-medium shrink-0"><?php echo languageString('general.tags'); ?></dt>

                    <dd class="flex-1">
                      <?php if (!empty($tags)) : ?>
                        <ul class="ml-0 flex flex-wrap gap-2" id="tag-list">
                          <?php foreach ($tags as $tag) : ?>
                            <li>
                              <span class="group inline-flex items-center gap-1 rounded-full border border-cyan-600/70 bg-cyan-600 text-white px-2 py-0.5 text-xs">
                                <svg viewBox="0 0 24 24" aria-hidden="true" class="size-3 opacity-80 fill-current">
                                  <path d="M21 11.5 12.5 3a1.5 1.5 0 0 0-1.06-.44H5A2 2 0 0 0 3 4.5v6.44c0 .4.16.78.44 1.06L12 21a1.5 1.5 0 0 0 2.12 0l6.88-6.88a1.5 1.5 0 0 0 0-2.12ZM7.75 8.25a1.25 1.25 0 1 1 0-2.5 1.25 1.25 0 0 1 0 2.5Z" />
                                </svg>

                                <span class="truncate max-w-[11rem]"><?php echo htmlspecialchars($tag); ?></span>

                                <a
                                  href="backend_api/remove_tags.php?type=image&file=<?php echo urlencode($image_url); ?>&tag=<?php echo urlencode($tag); ?>"
                                  class="-mr-1 inline-flex items-center rounded-full p-0.5 hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-white/30"
                                  aria-label="Tag entfernen: <?php echo htmlspecialchars($tag); ?>"
                                  title="Entfernen">
                                  <svg viewBox="0 0 24 24" class="size-3 transition group-hover:text-red-200" fill="currentColor" aria-hidden="true">
                                    <path d="M6 18 18 6m0 12L6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none" />
                                  </svg>
                                </a>
                              </span>
                            </li>
                          <?php endforeach; ?>
                        </ul>
                      <?php else : ?>
                        <span class="text-black/60 dark:text-gray-400"><?php echo languageString('general.no_tags'); ?></span>
                      <?php endif; ?>
                    </dd>
                  </div>

                  <form action="backend_api/add_tags.php?type=image&file=<?php echo urlencode($image_url); ?>" method="post" class="mt-3 flex items-center gap-2">
                    <input
                      type="text"
                      name="tag"
                      required
                      class="flex-1 rounded border border-black/10 dark:border-white/10 bg-white dark:bg-black px-3 py-2 text-sm placeholder-black/50 dark:placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-cyan-500/40 focus:border-cyan-500"
                      placeholder="<?php echo languageString('general.tags_add'); ?>" />
                  </form>
                </div>

                <div class="py-2 flex items-center justify-between gap-4">
                  <dt class="font-medium"><?php echo languageString('image.rating'); ?></dt>
                  <dd>
                    <span id="rating-stars" class="flex space-x-1 text-yellow-400"
                      data-rating="<?php echo htmlspecialchars((string)$rating); ?>"
                      data-filename="<?php echo htmlspecialchars($fileName); ?>"></span>
                  </dd>
                </div>
              </dl>
            </div>
          </section>

          <section class="rounded-sm border border-black/10 dark:border-white/10 bg-white dark:bg-black/40 shadow-xs">
            <header class="px-4 py-3 border-b border-black/10 dark:border-white/10">
              <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold">EXIF</h3>
                <div class="flex items-center gap-2">
                  <!-- WICHTIG: wie beim EXIF Modal -> command show-modal -->
                  <button id="edit-exif" type="button" command="show-modal" commandfor="editExifModal"
                    class="text-xs px-2 py-1 rounded border border-black/10 dark:border-white/10 hover:bg-black/5 dark:hover:bg-white/10">
                    Edit
                  </button>
                </div>
              </div>
            </header>

            <div class="p-4">
              <dl class="text-sm text-black/80 dark:text-gray-300 divide-y divide-black/10 dark:divide-white/10">
                <div class="py-2 flex justify-between gap-4"><dt class="font-medium"><?php echo languageString('exif.camera'); ?></dt><dd id="exif-camera"><?php echo htmlspecialchars($camera); ?></dd></div>
                <div class="py-2 flex justify-between gap-4"><dt class="font-medium"><?php echo languageString('exif.lens'); ?></dt><dd id="exif-lens"><?php echo htmlspecialchars($lens); ?></dd></div>
                <div class="py-2 flex justify-between gap-4"><dt class="font-medium"><?php echo languageString('exif.aperture'); ?></dt><dd id="exif-aperture"><?php echo htmlspecialchars((string)$aperture); ?></dd></div>
                <div class="py-2 flex justify-between gap-4"><dt class="font-medium"><?php echo languageString('exif.shutter_speed'); ?></dt><dd id="exif-shutter"><?php echo htmlspecialchars((string)$shutterSpeed); ?></dd></div>
                <div class="py-2 flex justify-between gap-4"><dt class="font-medium"><?php echo languageString('exif.iso'); ?></dt><dd id="exif-iso"><?php echo htmlspecialchars((string)$iso); ?></dd></div>
                <div class="py-2 flex justify-between gap-4"><dt class="font-medium"><?php echo languageString('exif.focal_length'); ?></dt><dd id="exif-focal"><?php echo htmlspecialchars((string)$focallength); ?></dd></div>
                <div class="py-2 flex justify-between gap-4"><dt class="font-medium"><?php echo languageString('exif.date'); ?></dt><dd id="exif-date"><?php echo htmlspecialchars((string)$dateTaken); ?></dd></div>

                <div class="py-2">
                  <div class="flex items-center justify-between">
                    <dt class="font-medium"><?php echo languageString('exif.gps.label'); ?></dt>
                    <div class="flex items-center gap-2">
                      <button id="copy-gps" type="button"
                        class="text-xs px-2 py-1 rounded border border-black/10 dark:border-white/10 hover:bg-black/5 dark:hover:bg-white/10">
                        <?php echo languageString('general.copy'); ?>
                      </button>
                    </div>
                  </div>

                  <dd class="mt-1 text-xs">
                    <span id="exif-lat"><?php echo htmlspecialchars((string)($latitude ?? 0)); ?></span>
                    <span> // </span>
                    <span id="exif-lon"><?php echo htmlspecialchars((string)($longitude ?? 0)); ?></span>
                  </dd>

                  <div id="map" class="mt-3 h-40 rounded border border-black/10 dark:border-white/10 flex items-center justify-center text-xs text-black/60 dark:text-gray-400">
                    <?php if ($hasGPS) : ?>
                      Map loading...
                    <?php else : ?>
                      No GPS data
                    <?php endif; ?>
                  </div>
                </div>
              </dl>
            </div>
          </section>
        </div>
      </div>
    </main>
  </div>

  <!-- ===================== MODALS (alle wie EXIF: <dialog> + command/commandfor) ===================== -->

  <!-- Image Text Edit Modal -->
  <el-dialog>
    <dialog id="editImageTextModal" class="backdrop:bg-transparent">
      <el-dialog-backdrop class="fixed inset-0 bg-black/50 backdrop-blur-[2px] transition-opacity duration-200 ease-out data-closed:opacity-0"></el-dialog-backdrop>

      <div tabindex="0" class="fixed inset-0 flex items-end justify-center p-4 text-center focus:outline-none sm:items-center sm:p-0">
        <el-dialog-panel
          class="relative w-full transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all
                 data-closed:translate-y-4 data-closed:opacity-0
                 data-enter:duration-300 data-enter:ease-out
                 data-leave:duration-200 data-leave:ease-in
                 sm:my-8 sm:w-full sm:max-w-none md:max-w-3xl sm:p-6
                 data-closed:sm:translate-y-0 data-closed:sm:scale-95
                 dark:bg-black dark:outline dark:-outline-offset-1 dark:outline-white/10">

          <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
            <button type="button" command="close" commandfor="editImageTextModal"
              class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-2 focus:outline-offset-2 focus:outline-cyan-600
                     dark:bg-black dark:hover:text-gray-300 dark:focus:outline-white">
              <span class="sr-only">Close</span>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6">
                <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </button>
          </div>

          <div class="sm:flex sm:items-start">
            <div class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-amber-100 sm:mx-0 sm:size-10 dark:bg-amber-500/10">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6 text-amber-600 dark:text-amber-400">
                <path d="M4 6h16M4 10h16M4 14h10" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </div>

            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
              <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                <?php echo languageString('image.text.edit.title') ?: 'Edit image text'; ?>
              </h3>
              <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                <?php echo languageString('image.text.edit.subtitle') ?: 'Change title and description.'; ?>
              </p>
            </div>
          </div>

          <div class="mt-4">
            <form class="space-y-4" id="image-text-form" onsubmit="return false;">
              <div>
                <label for="image-title-input" class="block text-xs font-medium text-gray-700 dark:text-gray-300">
                  <?php echo languageString('image.title') ?: 'Title'; ?>
                </label>
                <input id="image-title-input" data-key="title" type="text"
                  value="<?php echo htmlspecialchars($title); ?>"
                  class="mt-1 block w-full rounded border border-black/10 px-2 py-2 text-sm
                         text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-cyan-500/40
                         dark:bg-black dark:text-white dark:border-white/10 dark:placeholder-white/30" />
              </div>

              <div>
                <label for="image-description-input" class="block text-xs font-medium text-gray-700 dark:text-gray-300">
                  <?php echo languageString('image.description') ?: 'Description'; ?>
                </label>
                <textarea id="image-description-input" data-key="description" rows="6"
                  class="mt-1 block w-full rounded border border-black/10 px-2 py-2 text-sm leading-6
                         text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-cyan-500/40
                         dark:bg-black dark:text-white dark:border-white/10 dark:placeholder-white/30"><?php echo htmlspecialchars($description); ?></textarea>
              </div>
            </form>
          </div>

          <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
            <button type="button" id="save_image_text"
              class="inline-flex w-full justify-center rounded-md bg-black px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-black/90
                     sm:ml-3 sm:w-auto dark:bg-white dark:text-black dark:hover:bg-white/90">
              <?php echo languageString('general.save') ?: 'Save'; ?>
            </button>

            <button type="button" command="close" commandfor="editImageTextModal"
              class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs
                     inset-ring-1 inset-ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto
                     dark:bg-white/10 dark:text-white dark:shadow-none dark:inset-ring-white/5 dark:hover:bg-white/20">
              <?php echo languageString('general.cancel') ?: 'Cancel'; ?>
            </button>
          </div>

        </el-dialog-panel>
      </div>
    </dialog>
  </el-dialog>

  <!-- EXIF Edit Modal -->
  <el-dialog>
    <dialog id="editExifModal" class="backdrop:bg-transparent">
      <el-dialog-backdrop class="fixed inset-0 bg-black/50 backdrop-blur-[2px] transition-opacity duration-200 ease-out data-closed:opacity-0"></el-dialog-backdrop>

      <div tabindex="0" class="fixed inset-0 flex items-end justify-center p-4 text-center focus:outline-none sm:items-center sm:p-0">
        <el-dialog-panel
          class="relative w-full transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all
                 data-closed:translate-y-4 data-closed:opacity-0
                 data-enter:duration-300 data-enter:ease-out
                 data-leave:duration-200 data-leave:ease-in
                 sm:my-8 sm:w-full sm:max-w-lg sm:p-6
                 data-closed:sm:translate-y-0 data-closed:sm:scale-95
                 dark:bg-black dark:outline dark:-outline-offset-1 dark:outline-white/10">

          <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
            <button type="button" command="close" commandfor="editExifModal"
              class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-2 focus:outline-offset-2 focus:outline-cyan-600
                     dark:bg-black dark:hover:text-gray-300 dark:focus:outline-white">
              <span class="sr-only">Close</span>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6">
                <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </button>
          </div>

          <div class="sm:flex sm:items-start">
            <div class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-blue-100 sm:mx-0 sm:size-10 dark:bg-blue-500/10">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6 text-blue-600 dark:text-blue-400">
                <path d="M3 7h18M5 7l1.5 12.5a2 2 0 0 0 2 1.5h7a2 2 0 0 0 2-1.5L19 7M9 7V5a3 3 0 0 1 6 0v2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </div>
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
              <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                <?php echo languageString('exif.edit.title') ?: 'Edit EXIF'; ?>
              </h3>
              <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                <?php echo languageString('exif.edit.subtitle') ?: 'Adjust metadata and save.'; ?>
              </p>
            </div>
          </div>

          <div class="mt-4">
            <form class="space-y-4" id="exif-edit-form" onsubmit="return false;">
              <div class="grid grid-cols-1 gap-4">
                <div>
                  <label for="exif-camera-input" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Camera</label>
                  <input id="exif-camera-input" data-key="camera" type="text"
                         value="<?php echo htmlspecialchars($camera); ?>"
                         class="mt-1 block w-full rounded border border-black/10 px-2 py-1 text-sm
                                text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-cyan-500/40
                                dark:bg-black dark:text-white dark:border-white/10 dark:placeholder-white/30" />
                </div>

                <div>
                  <label for="exif-lens-input" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Lens</label>
                  <input id="exif-lens-input" data-key="lens" type="text"
                         value="<?php echo htmlspecialchars($lens); ?>"
                         class="mt-1 block w-full rounded border border-black/10 px-2 py-1 text-sm
                                text-gray-900 focus:outline-none focus:ring-2 focus:ring-cyan-500/40
                                dark:bg-black dark:text-white dark:border-white/10" />
                </div>

                <div>
                  <label for="exif-aperture-input" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Aperture</label>
                  <input id="exif-aperture-input" data-key="aperture" type="text"
                         value="<?php echo htmlspecialchars((string)$aperture); ?>"
                         class="mt-1 block w-full rounded border border-black/10 px-2 py-1 text-sm
                                text-gray-900 focus:outline-none focus:ring-2 focus:ring-cyan-500/40
                                dark:bg-black dark:text-white dark:border-white/10" />
                </div>

                <div>
                  <label for="exif-shutter-input" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Shutter Speed</label>
                  <input id="exif-shutter-input" data-key="shutter_speed" type="text"
                         value="<?php echo htmlspecialchars((string)$shutterSpeed); ?>"
                         class="mt-1 block w-full rounded border border-black/10 px-2 py-1 text-sm
                                text-gray-900 focus:outline-none focus:ring-2 focus:ring-cyan-500/40
                                dark:bg-black dark:text-white dark:border-white/10" />
                </div>

                <div>
                  <label for="exif-iso-input" class="block text-xs font-medium text-gray-700 dark:text-gray-300">ISO</label>
                  <input id="exif-iso-input" data-key="iso" type="text"
                         value="<?php echo htmlspecialchars((string)$iso); ?>"
                         class="mt-1 block w-full rounded border border-black/10 px-2 py-1 text-sm
                                text-gray-900 focus:outline-none focus:ring-2 focus:ring-cyan-500/40
                                dark:bg-black dark:text-white dark:border-white/10" />
                </div>

                <div>
                  <label for="exif-focal-input" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Focal Length</label>
                  <input id="exif-focal-input" data-key="focal_length" type="text"
                         value="<?php echo htmlspecialchars((string)$focallength); ?>"
                         class="mt-1 block w-full rounded border border-black/10 px-2 py-1 text-sm
                                text-gray-900 focus:outline-none focus:ring-2 focus:ring-cyan-500/40
                                dark:bg-black dark:text-white dark:border-white/10" />
                </div>

                <div>
                  <label for="exif-date-input" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Date</label>
                  <input id="exif-date-input" data-key="date" type="text"
                         value="<?php echo htmlspecialchars((string)$dateTaken); ?>"
                         class="mt-1 block w-full rounded border border-black/10 px-2 py-1 text-sm
                                text-gray-900 focus:outline-none focus:ring-2 focus:ring-cyan-500/40
                                dark:bg-black dark:text-white dark:border-white/10" />
                </div>
              </div>

              <div class="grid grid-cols-1 gap-4">
                <div>
                  <label for="exif-lat-input" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Lat</label>
                  <input id="exif-lat-input" data-key="lat" type="text"
                         value="<?php echo htmlspecialchars((string)($latitude ?? 0)); ?>"
                         class="mt-1 block w-full rounded border border-black/10 px-2 py-1 text-sm
                                text-gray-900 focus:outline-none focus:ring-2 focus:ring-cyan-500/40
                                dark:bg-black dark:text-white dark:border-white/10" />
                </div>
                <div>
                  <label for="exif-lon-input" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Lon</label>
                  <input id="exif-lon-input" data-key="lon" type="text"
                         value="<?php echo htmlspecialchars((string)($longitude ?? 0)); ?>"
                         class="mt-1 block w-full rounded border border-black/10 px-2 py-1 text-sm
                                text-gray-900 focus:outline-none focus:ring-2 focus:ring-cyan-500/40
                                dark:bg-black dark:text-white dark:border-white/10" />
                </div>
              </div>
            </form>
          </div>

          <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
            <button type="button" id="save_metadata"
              class="inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs
                     inset-ring-1 inset-ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto
                     dark:bg-white/10 dark:text-white dark:shadow-none dark:inset-ring-white/5 dark:hover:bg-white/20">
              Save
            </button>

            <button type="button" id="update-exif"
              class="mx-2 mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs
                     inset-ring-1 inset-ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto
                     dark:bg-white/10 dark:text-white dark:shadow-none dark:inset-ring-white/5 dark:hover:bg-white/20">
              Sync
            </button>

            <button type="button" command="close" commandfor="editExifModal"
              class="mt-3 inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-red-500
                     sm:ml-3 sm:mt-0 sm:w-auto dark:bg-red-500 dark:hover:bg-red-400">
              <?php echo languageString('general.cancel'); ?>
            </button>
          </div>

        </el-dialog-panel>
      </div>
    </dialog>
  </el-dialog>

  <!-- AI Confirm Modal -->
  <el-dialog>
    <dialog id="confirmAiModal" class="backdrop:bg-transparent">
      <el-dialog-backdrop class="fixed inset-0 bg-black/50 backdrop-blur-[2px] transition-opacity duration-200 ease-out data-closed:opacity-0"></el-dialog-backdrop>

      <div tabindex="0" class="fixed inset-0 flex items-end justify-center p-4 text-center focus:outline-none sm:items-center sm:p-0">
        <el-dialog-panel
          class="relative w-full transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all
                 data-closed:translate-y-4 data-closed:opacity-0
                 data-enter:duration-300 data-enter:ease-out
                 data-leave:duration-200 data-leave:ease-in
                 sm:my-8 sm:w-full sm:max-w-lg sm:p-6
                 data-closed:sm:translate-y-0 data-closed:sm:scale-95
                 dark:bg-black dark:outline dark:-outline-offset-1 dark:outline-white/10">

          <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
            <button type="button" command="close" commandfor="confirmAiModal"
              class="rounded-md bg-white text-gray-400 hover:text-gray-500
                     focus:outline-2 focus:outline-offset-2 focus:outline-cyan-600
                     dark:bg-black dark:hover:text-gray-300 dark:focus:outline-white">
              <span class="sr-only">Close</span>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6">
                <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </button>
          </div>

          <div class="sm:flex sm:items-start">
            <div class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:size-10 dark:bg-red-500/10">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6 text-red-600 dark:text-red-400">
                <path d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71
                         c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378
                         c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126Z
                         M12 15.75h.007v.008H12v-.008Z"
                      stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </div>

            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
              <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                <?php echo languageString('image.generate.title'); ?>
              </h3>
              <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                <?php echo languageString('image.generate.text'); ?>
              </p>
            </div>
          </div>

          <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
            <button type="button" command="close" commandfor="confirmAiModal"
              class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-red-500
                     sm:ml-3 sm:w-auto dark:bg-red-500 dark:hover:bg-red-400">
              <?php echo languageString('general.cancel'); ?>
            </button>

            <a href="backend_api/ai_img_text.php?file=<?php echo urlencode($image_url); ?>"
               class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs inset-ring-1 inset-ring-gray-300
                      hover:bg-gray-50 sm:mt-0 sm:w-auto
                      dark:bg-white/10 dark:text-white dark:inset-ring-white/5 dark:hover:bg-white/20">
              <?php echo languageString('image.generate.btn'); ?>
            </a>
          </div>

        </el-dialog-panel>
      </div>
    </dialog>
  </el-dialog>

  <!-- Scripts -->
  <script src="js/tailwind.js"></script>
  <script src="js/profile_settings.js"></script>
  <script src="js/image_rating.js"></script>
  <script src="js/media-detail.js"></script>

  <?php if ($hasGPS) : ?>
  <script>
    (function () {
      const lat = <?php echo (float)$latitude; ?>;
      const lon = <?php echo (float)$longitude; ?>;

      const el = document.getElementById('map');
      if (!el) return;

      const map = L.map('map').setView([lat, lon], 12);

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
      }).addTo(map);

      L.marker([lat, lon]).addTo(map);
    })();
  </script>
  <?php endif; ?>
</body>
</html>
