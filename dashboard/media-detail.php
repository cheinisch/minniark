<?php
  require_once( __DIR__ . "/../functions/function_backend.php");
  require_once __DIR__ . '/../app/autoload.php';
  security_checklogin();

  $image_url = $_GET['image'];

  $imageData = getImage($image_url);

  // Lizenzmanager
  $lm = new LicenseManager(dirname(__DIR__));
  $licensed = $lm->isLicensed();

    // Daten extrahieren
    $fileName = $imageData['filename'];
    $imagePath = "../userdata/content/images/" . $fileName."?v=".time();
    $title = $imageData['title'];
    $description = $imageData['description'];
    $uploadDate = $imageData['upload_date'] ?? null;
  
    $rating = $imageData['rating'] ?? 0;

    // Exif-Daten
    $camera = $imageData['exif']['Camera'] ?? 'Unknown';
    $lens = $imageData['exif']['Lens'] ?? 'Unknown';
    $focallength = $imageData['exif']['Focal Length'] ?? 'Unknown';
    $apertureRaw = $imageData['exif']['Aperture'] ?? 'Unknown';
    $shutterSpeedRaw = $imageData['exif']['Shutter Speed'] ?? 'Unknown';
    $iso = $imageData['exif']['ISO'] ?? 'Unknown';
    $dateTaken = $imageData['exif']['Date'] ?? 'Unknown';
  
    // Name Fix
    $camera = str_replace('Canon Canon', 'Canon',$camera);

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

    // Tags auslesen

    // ---- Tags vorbereiten ----
    $tags = $imageData['tags'] ?? [];  // falls im YAML vorhanden
    if (!is_array($tags)) {
        // falls als String gespeichert → in Array umwandeln
        $tags = array_map('trim', explode(',', $tags));
    }
?>

<!DOCTYPE html>
<html lang="<?php echo get_language(); ?>">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Images - <?php echo get_sitename(); ?></title>
		<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
		<!--<script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>-->
		<link
  rel="stylesheet"
  href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
/>
<script
  src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js">
</script>
	</head>
	<body class="bg-white dark:bg-black">
		<!-- Modale -->
		<!-- Image Text Edit Modal -->
		<div id="editImageTextModal" aria-labelledby="image-text-dialog"
			class="hidden fixed inset-0 size-auto max-h-none max-w-none overflow-y-auto bg-transparent backdrop:bg-transparent z-50">

		<!-- Backdrop -->
		<div class="fixed inset-0 bg-gray-500/75 transition-opacity data-closed:opacity-0 data-enter:duration-300 data-enter:ease-out data-leave:duration-200 data-leave:ease-in dark:bg-gray-900/50"></div>

		<!-- Panel -->
		<div tabindex="0" class="flex min-h-full items-end justify-center p-4 text-center focus:outline-none sm:items-center sm:p-0">
			<div class="relative transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all
						data-closed:translate-y-4 data-closed:opacity-0 data-enter:duration-300 data-enter:ease-out data-leave:duration-200 data-leave:ease-in
						sm:my-8 sm:w-full max-w-4xl sm:p-6 data-closed:sm:translate-y-0 data-closed:sm:scale-95 dark:bg-black dark:outline dark:-outline-offset-1 dark:outline-white/10">

			<!-- Close (X) -->
			<div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
				<button type="button" id="close_edit_image_text"
						class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-2 focus:outline-offset-2 focus:outline-cyan-600 dark:bg-black dark:hover:text-gray-300 dark:focus:outline-white">
				<span class="sr-only">Close</span>
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6">
					<path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
				</svg>
				</button>
			</div>

			<!-- Header -->
			<div class="sm:flex sm:items-start">
				<div class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-amber-100 sm:mx-0 sm:size-10 dark:bg-amber-500/10">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6 text-amber-600 dark:text-amber-400">
					<path d="M4 6h16M4 10h16M4 14h10" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
				</div>
				<div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
				<h3 id="image-text-dialog" class="text-base font-semibold text-gray-900 dark:text-white">
					<?php echo languageString('image.text.edit.title') ?: 'Edit image text'; ?>
				</h3>
				<div class="mt-2">
					<p class="text-sm text-gray-500 dark:text-gray-400">
					<?php echo languageString('image.text.edit.subtitle') ?: 'Change title and description.'; ?>
					</p>
				</div>
				</div>
			</div>

			<!-- Content: Labels oben -->
			<div class="mt-4">
				<form class="space-y-4" id="image-text-form" onsubmit="return false;">
				<div>
					<label for="image-title-input" class="block text-xs font-medium text-gray-700 dark:text-gray-300">
					<?php echo languageString('image.title') ?: 'Title'; ?>
					</label>
					<input id="image-title-input" data-key="title" type="text"
						value="<?php echo nl2br(htmlspecialchars($title)); ?>"
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
									dark:bg-black dark:text-white dark:border-white/10 dark:placeholder-white/30"><?php echo nl2br(htmlspecialchars($description)); ?></textarea>
				</div>
				</form>
			</div>

			<!-- Footer: Buttons -->
			<div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
				<!-- Save -->
				<button type="button" id="save_image_text"
						class="inline-flex w-full justify-center rounded-md bg-black px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-black/90 sm:ml-3 sm:w-auto dark:bg-white dark:text-black dark:hover:bg-white/90">
				<?php echo languageString('general.save') ?: 'Save'; ?>
				</button>

				<!-- Cancel -->
				<button type="button" id="cancel_image_text"
						class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs inset-ring-1 inset-ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto dark:bg-white/10 dark:text-white dark:shadow-none dark:inset-ring-white/5 dark:hover:bg-white/20">
				<?php echo languageString('general.cancel') ?: 'Cancel'; ?>
				</button>
			</div>

			</div>
		</div>
		</div>
		<!-- /Image Text Edit Modal -->

		 <!-- EXIF Edit Modal (Labels oben) -->
<div id="editExifModal" aria-labelledby="exif-edit-dialog"
     class="hidden fixed inset-0 size-auto max-h-none max-w-none overflow-y-auto bg-transparent backdrop:bg-transparent z-50">

  <!-- Backdrop -->
  <div class="fixed inset-0 bg-gray-500/75 transition-opacity data-closed:opacity-0 data-enter:duration-300 data-enter:ease-out data-leave:duration-200 data-leave:ease-in dark:bg-gray-900/50"></div>

  <!-- Panel -->
  <div tabindex="0" class="flex min-h-full items-end justify-center p-4 text-center focus:outline-none sm:items-center sm:p-0">
    <div class="relative transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all
                data-closed:translate-y-4 data-closed:opacity-0 data-enter:duration-300 data-enter:ease-out data-leave:duration-200 data-leave:ease-in
                sm:my-8 sm:w-full sm:max-w-lg sm:p-6 data-closed:sm:translate-y-0 data-closed:sm:scale-95 dark:bg-black dark:outline dark:-outline-offset-1 dark:outline-white/10">

      <!-- Close (X) -->
      <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
        <button type="button" id="editExifClose"
                class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-2 focus:outline-offset-2 focus:outline-cyan-600 dark:bg-black dark:hover:text-gray-300 dark:focus:outline-white">
          <span class="sr-only">Close</span>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6">
            <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </button>
      </div>

      <!-- Header -->
      <div class="sm:flex sm:items-start">
        <div class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-blue-100 sm:mx-0 sm:size-10 dark:bg-blue-500/10">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6 text-blue-600 dark:text-blue-400">
            <path d="M3 7h18M5 7l1.5 12.5a2 2 0 0 0 2 1.5h7a2 2 0 0 0 2-1.5L19 7M9 7V5a3 3 0 0 1 6 0v2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
          <h3 id="exif-edit-dialog" class="text-base font-semibold text-gray-900 dark:text-white">
            <?php echo languageString('exif.edit.title') ?: 'Edit EXIF'; ?>
          </h3>
          <div class="mt-2">
            <p class="text-sm text-gray-500 dark:text-gray-400">
              <?php echo languageString('exif.edit.subtitle') ?: 'Adjust metadata and save.'; ?>
            </p>
          </div>
        </div>
      </div>

      <!-- Content: Labels oben + Inputs -->
      <div class="mt-4">
        <form class="space-y-4" id="exif-edit-form" onsubmit="return false;">
          <!-- EXIF -->
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
                     value="<?php echo htmlspecialchars($aperture); ?>"
                     class="mt-1 block w-full rounded border border-black/10 px-2 py-1 text-sm
                            text-gray-900 focus:outline-none focus:ring-2 focus:ring-cyan-500/40
                            dark:bg-black dark:text-white dark:border-white/10" />
            </div>

            <div>
              <label for="exif-shutter-input" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Shutter Speed</label>
              <input id="exif-shutter-input" data-key="shutter_speed" type="text"
                     value="<?php echo htmlspecialchars($shutterSpeed); ?>"
                     class="mt-1 block w-full rounded border border-black/10 px-2 py-1 text-sm
                            text-gray-900 focus:outline-none focus:ring-2 focus:ring-cyan-500/40
                            dark:bg-black dark:text-white dark:border-white/10" />
            </div>

            <div>
              <label for="exif-iso-input" class="block text-xs font-medium text-gray-700 dark:text-gray-300">ISO</label>
              <input id="exif-iso-input" data-key="iso" type="text"
                     value="<?php echo htmlspecialchars($iso); ?>"
                     class="mt-1 block w-full rounded border border-black/10 px-2 py-1 text-sm
                            text-gray-900 focus:outline-none focus:ring-2 focus:ring-cyan-500/40
                            dark:bg-black dark:text-white dark:border-white/10" />
            </div>

            <div>
              <label for="exif-focal-input" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Focal Length</label>
              <input id="exif-focal-input" data-key="focal_length" type="text"
                     value="<?php echo htmlspecialchars($focallength); ?>"
                     class="mt-1 block w-full rounded border border-black/10 px-2 py-1 text-sm
                            text-gray-900 focus:outline-none focus:ring-2 focus:ring-cyan-500/40
                            dark:bg-black dark:text-white dark:border-white/10" />
            </div>

            <div>
              <label for="exif-date-input" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Date</label>
              <input id="exif-date-input" data-key="date" type="text"
                     value="<?php echo htmlspecialchars($dateTaken); ?>"
                     class="mt-1 block w-full rounded border border-black/10 px-2 py-1 text-sm
                            text-gray-900 focus:outline-none focus:ring-2 focus:ring-cyan-500/40
                            dark:bg-black dark:text-white dark:border-white/10" />
            </div>
          </div>

          <!-- GPS -->
          <div class="grid grid-cols-1 gap-4">
            <div>
              <label for="exif-lat-input" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Lat</label>
              <input id="exif-lat-input" data-key="lat" type="text"
                     value="<?php echo htmlspecialchars($latitude); ?>"
                     class="mt-1 block w-full rounded border border-black/10 px-2 py-1 text-sm
                            text-gray-900 focus:outline-none focus:ring-2 focus:ring-cyan-500/40
                            dark:bg-black dark:text-white dark:border-white/10" />
            </div>
            <div>
              <label for="exif-lon-input" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Lon</label>
              <input id="exif-lon-input" data-key="lon" type="text"
                     value="<?php echo htmlspecialchars($longitude); ?>"
                     class="mt-1 block w-full rounded border border-black/10 px-2 py-1 text-sm
                            text-gray-900 focus:outline-none focus:ring-2 focus:ring-cyan-500/40
                            dark:bg-black dark:text-white dark:border-white/10" />
            </div>
          </div>
        </form>
      </div>

      <!-- Footer: Buttons -->
	  <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
		<button type="button" id="save_metadata" command="close" commandfor="deleteImageModal" class="inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs inset-ring-1 inset-ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto dark:bg-white/10 dark:text-white dark:shadow-none dark:inset-ring-white/5 dark:hover:bg-white/20">Save</button>		
		<button type="button" id="update-exif" command="close" commandfor="deleteImageModal" class="mx-2 mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs inset-ring-1 inset-ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto dark:bg-white/10 dark:text-white dark:shadow-none dark:inset-ring-white/5 dark:hover:bg-white/20">Sync</button>
		<button type="button" id="cancel_metadata" command="close" commandfor="deleteImageModal" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-red-500 sm:ml-3 sm:w-auto dark:bg-red-500 dark:shadow-none dark:hover:bg-red-400"><?php echo languageString('general.cancel'); ?></button>
	</div>

    </div>
  </div>
</div>
<!-- /EXIF Edit Modal -->


		<!-- ai text modal -->
		<div id="confirmAiModal" aria-labelledby="ai-dialog" class="hidden fixed inset-0 size-auto max-h-none max-w-none overflow-y-auto bg-transparent backdrop:bg-transparent z-50">
			<div class="fixed inset-0 bg-gray-500/75 transition-opacity data-closed:opacity-0 data-enter:duration-300 data-enter:ease-out data-leave:duration-200 data-leave:ease-in dark:bg-gray-900/50"></div>

			<div tabindex="0" class="flex min-h-full items-end justify-center p-4 text-center focus:outline-none sm:items-center sm:p-0">
				<div class="relative transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all data-closed:translate-y-4 data-closed:opacity-0 data-enter:duration-300 data-enter:ease-out data-leave:duration-200 data-leave:ease-in sm:my-8 sm:w-full sm:max-w-lg sm:p-6 data-closed:sm:translate-y-0 data-closed:sm:scale-95 dark:bg-black dark:outline dark:-outline-offset-1 dark:outline-white/10">
					<div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
					<button type="button" id="aiTextClose" command="close" commandfor="deleteImageModal" class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-2 focus:outline-offset-2 focus:outline-cyan-600 dark:bg-black dark:hover:text-gray-300 dark:focus:outline-white">
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
						<h3 id="dialog-title" class="text-base font-semibold text-gray-900 dark:text-white"><?php echo languageString('image.generate.title'); ?></h3>
						<div class="mt-2">
						<p class="text-sm text-gray-500 dark:text-gray-400"><?php echo languageString('image.generate.text'); ?></p>
						</div>
					</div>
					</div>
					<div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
						<button type="button" id="confirmNo" command="close" commandfor="deleteImageModal" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-red-500 sm:ml-3 sm:w-auto dark:bg-red-500 dark:shadow-none dark:hover:bg-red-400"><?php echo languageString('general.cancel'); ?></button>
						<a href="backend_api/ai_img_text.php?file=<?php echo $image_url; ?> id="confir,Yes" command="close" commandfor="deleteImageModal" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs inset-ring-1 inset-ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto dark:bg-white/10 dark:text-white dark:shadow-none dark:inset-ring-white/5 dark:hover:bg-white/20"><?php echo languageString('image.generate.btn'); ?></a>
					</div>
				</div>
			</div>
		</div>
		<!-- ai text modal ende  -->  
		<!-- Sidebar --
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
						<!-- Sidebar component, swap this element with another sidebar if you like --
						<div class="relative flex grow flex-col gap-y-5 overflow-y-auto bg-white dark:bg-black px-6 pb-4 ring-1 ring-white/10 dark:before:pointer-events-none dark:before:absolute dark:before:inset-0 dark:before:bg-black/10">
							<div class="relative flex h-16 shrink-0 items-center">
								<img src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=cyan&shade=500" alt="Your Company" class="h-8 w-auto" />
							</div>
							<nav class="relative flex flex-1 flex-col">
								<?php # include (__DIR__.'/layout/media_menu.php'); ?>
							</nav>
						</div>
					</el-dialog-panel>
				</div>
			</dialog>
		</el-dialog>
		<!-- Static sidebar for desktop --
		<div class="hidden bg-white dark:bg-black ring-1 ring-black/10 dark:ring-white/10 lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
			<!-- Sidebar component, swap this element with another sidebar if you like --
			<div class="flex grow flex-col gap-y-5 overflow-y-auto bg-white dark:bg-black/10 px-6 pb-4">
				<div class="flex h-16 shrink-0 items-center">
					<img src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=cyan&shade=500" alt="Your Company" class="h-8 w-auto" />
				</div>
				<nav class="flex flex-1 flex-col">
					<?php # include (__DIR__.'/layout/media_menu.php'); ?>
				</nav>
			</div>
		</div>--
		<div class="lg:pl-72">-->
		<div>
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
								class="inline-flex items-center justify-start mx-2 py-2 border-b border-gray-800 dark:border-gray-400 rounded-none
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
			<main class="bg-white dark:bg-black text-black dark:text-white">
				<div class="w-full mx-auto px-4 sm:px-6 lg:px-8 py-2 grid grid-cols-1 md:grid-cols-[minmax(0,1fr)_22rem] gap-8">
					<!-- IMAGE -->
					<article class="lg:w-full">
						<!-- Bild -->
						<figure class="w-full mx-auto max-w-6xl">
							<img
							id="image"
							src=<?php echo $imagePath; ?>
							alt="Verrazzano-Narrows Bridge, New York"
							class="w-full h-auto rounded-sm border border-black/10 dark:border-white/10 shadow-xs"
							/>
							<figcaption class="mt-4">
								<div class="flex items-center justify-between gap-2">
									<h2 id="title" class="text-xl font-semibold">
									<?php echo htmlspecialchars($title); ?>
									</h2>

									<div class="flex items-center gap-2 shrink-0">
										<button type="button" id="edit_text"
											class="text-xs px-2 py-1 rounded border border-black/10 dark:border-white/10 hover:bg-black/5 dark:hover:bg-white/10">
											<?php echo languageString('general.edit'); ?>
										</button>
										<?php if($licensed && isAI_active()) { ?>
										<button type="button" id="generate_text"
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
										<?php if (!empty($tags)): ?>
										<ul class="ml-0 flex flex-wrap gap-2" id="tag-list">
											<?php foreach ($tags as $tag): ?>
											<li>
												<span class="group inline-flex items-center gap-1 rounded-full border border-cyan-600/70 bg-cyan-600 text-white px-2 py-0.5 text-xs">
												<!-- Tag-Icon (dezent) -->
												<svg viewBox="0 0 24 24" aria-hidden="true" class="size-3 opacity-80 fill-current">
													<path d="M21 11.5 12.5 3a1.5 1.5 0 0 0-1.06-.44H5A2 2 0 0 0 3 4.5v6.44c0 .4.16.78.44 1.06L12 21a1.5 1.5 0 0 0 2.12 0l6.88-6.88a1.5 1.5 0 0 0 0-2.12ZM7.75 8.25a1.25 1.25 0 1 1 0-2.5 1.25 1.25 0 0 1 0 2.5Z"/>
												</svg>
												<span class="truncate max-w-[11rem]"><?= htmlspecialchars($tag) ?></span>

												<!-- Entfernen -->
												<a
													href="backend_api/remove_tags.php?type=image&file=<?= urlencode($image_url) ?>&tag=<?= urlencode($tag) ?>"
													class="-mr-1 inline-flex items-center rounded-full p-0.5 hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-white/30"
													aria-label="Tag entfernen: <?= htmlspecialchars($tag) ?>"
													title="Entfernen"
												>
													<svg viewBox="0 0 24 24" class="size-3 transition group-hover:text-red-200" fill="currentColor" aria-hidden="true">
													<path d="M6 18 18 6m0 12L6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
													</svg>
												</a>
												</span>
											</li>
											<?php endforeach; ?>
										</ul>
										<?php else: ?>
										<span class="text-black/60 dark:text-gray-400"><?php echo languageString('general.no_tags'); ?></span>
										<?php endif; ?>
									</dd>
									</div>

									<!-- Tag hinzufügen -->
									<form action="backend_api/add_tags.php?type=image&file=<?= urlencode($image_url); ?>" method="post" class="mt-3 flex items-center gap-2">
									<input
										type="text"
										name="tag"
										required
										class="flex-1 rounded border border-black/10 dark:border-white/10 bg-white dark:bg-black px-3 py-2 text-sm placeholder-black/50 dark:placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-cyan-500/40 focus:border-cyan-500"
										placeholder="<?php echo languageString('general.tags_add'); ?>"
									/>
									</form>
								</div>
								<div class="py-2 flex items-center justify-between gap-4">
								<dt class="font-medium"><?php echo languageString('image.rating'); ?></dt>
								<dd>
									<span id="rating-stars" class="flex space-x-1 text-yellow-400" data-rating="<?php echo htmlspecialchars($rating); ?>" data-filename="<?php echo htmlspecialchars($fileName); ?>"></span>
									
								</dd>
								</div>
							</dl>
							</div>
						</section>
						<section class="rounded-sm border border-black/10 dark:border-white/10 bg-white dark:bg-black/40 shadow-xs">
						<header class="px-4 py-3 border-b border-black/10 dark:border-white/10">
						<div class="flex items-center justify-between">
							<dt class="font-sm"><h3 class="text-sm font-semibold">EXIF</h3></dt>
							<div class="flex items-center gap-2">
							<button id="edit-exif" class="text-xs px-2 py-1 rounded border border-black/10 dark:border-white/10 hover:bg-black/5 dark:hover:bg-white/10">Edit</button>
							</div>
						</div>
						</header>
						<div class="p-4">
						<dl class="text-sm text-black/80 dark:text-gray-300 divide-y divide-black/10 dark:divide-white/10">
							<div class="py-2 flex justify-between gap-4"><dt class="font-medium"><?php echo languageString('exif.camera'); ?></dt><dd id="exif-camera"><?php echo $camera; ?></dd></div>
							<div class="py-2 flex justify-between gap-4"><dt class="font-medium"><?php echo languageString('exif.lens'); ?></dt><dd id="exif-lens"><?php echo $lens; ?></dd></div>
							<div class="py-2 flex justify-between gap-4"><dt class="font-medium"><?php echo languageString('exif.aperture'); ?></dt><dd id="exif-aperture"><?php echo $aperture; ?></dd></div>
							<div class="py-2 flex justify-between gap-4"><dt class="font-medium"><?php echo languageString('exif.shutter_speed'); ?></dt><dd id="exif-shutter"><?php echo $shutterSpeed; ?></dd></div>
							<div class="py-2 flex justify-between gap-4"><dt class="font-medium"><?php echo languageString('exif.iso'); ?></dt><dd id="exif-iso"><?php echo $iso; ?></dd></div>
							<div class="py-2 flex justify-between gap-4"><dt class="font-medium"><?php echo languageString('exif.focal_length'); ?></dt><dd id="exif-focal"><?php echo $focallength; ?></dd></div>
							<div class="py-2 flex justify-between gap-4"><dt class="font-medium"><?php echo languageString('exif.date'); ?></dt><dd id="exif-date"><?php echo $dateTaken; ?></dd></div>
							<div class="py-2">
							<div class="flex items-center justify-between">
								<dt class="font-medium"><?php echo languageString('exif.gps.label'); ?></dt>
								<div class="flex items-center gap-2">
								<button id="copy-gps" type="button" class="text-xs px-2 py-1 rounded border border-black/10 dark:border-white/10 hover:bg-black/5 dark:hover:bg-white/10"><?php echo languageString('general.copy'); ?></button>
								</div>
							</div>
							<dd class="mt-1 text-xs">
								<span id="exif-lat"><?php echo $latitude; ?></span>
								<span> // </span>
								<span id="exif-lon"><?php echo $longitude; ?></span>
							</dd>
							<div id="map" class="mt-3 h-40 rounded border border-black/10 dark:border-white/10 flex items-center justify-center text-xs text-black/60 dark:text-gray-400">
								Map placeholder (<?php echo $latitude; ?>, <?php echo $longitude; ?>)
							</div>
							</div>
						</dl>
						</div>
					</section>
					</div>
				</div>
			</main>
		</div>
		
		<!-- <script src="js/navbar.js"></script> -->
		<script src="js/tailwind.js"></script>
		<script src="js/profile_settings.js"></script>
		<script src="js/image_rating.js"></script>
		<script src="js/media-detail.js"></script>
		<script>
          const map = L.map('map').setView([<?php echo $latitude; ?>, <?php echo $longitude; ?>], 12);

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
