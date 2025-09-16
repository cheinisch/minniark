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
		<script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>
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
						<div class="relative flex grow flex-col gap-y-5 overflow-y-auto bg-white dark:bg-black px-6 pb-4 ring-1 ring-white/10 dark:before:pointer-events-none dark:before:absolute dark:before:inset-0 dark:before:bg-white dark:bg-black/10">
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
													Dashboard
												</a>
											</li>
											<li>
												<a href="#" class="group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold text-gray-400 hover:bg-white/5 hover:text-white">
													<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 shrink-0">
														<path d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" stroke-linecap="round" stroke-linejoin="round" />
													</svg>
													Team
												</a>
											</li>
											<li>
												<a href="#" class="group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold text-gray-400 hover:bg-white/5 hover:text-white">
													<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 shrink-0">
														<path d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" stroke-linecap="round" stroke-linejoin="round" />
													</svg>
													Projects
												</a>
											</li>
											<li>
												<a href="#" class="group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold text-gray-400 hover:bg-white/5 hover:text-white">
													<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 shrink-0">
														<path d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" stroke-linecap="round" stroke-linejoin="round" />
													</svg>
													Calendar
												</a>
											</li>
											<li>
												<a href="#" class="group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold text-gray-400 hover:bg-white/5 hover:text-white">
													<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 shrink-0">
														<path d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" stroke-linecap="round" stroke-linejoin="round" />
													</svg>
													Documents
												</a>
											</li>
											<li>
												<a href="#" class="group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold text-gray-400 hover:bg-white/5 hover:text-white">
													<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 shrink-0">
														<path d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z" stroke-linecap="round" stroke-linejoin="round" />
														<path d="M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5Z" stroke-linecap="round" stroke-linejoin="round" />
													</svg>
													Reports
												</a>
											</li>
										</ul>
									</li>
									<li>
										<div class="text-xs/6 font-semibold text-gray-400">Your teams</div>
										<ul role="list" class="-mx-2 mt-2 space-y-1">
											<li>
												<!-- Current: "bg-white/5 text-white", Default: "text-gray-400 hover:text-white hover:bg-white/5" -->
												<a href="#" class="group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold text-gray-400 hover:bg-white/5 hover:text-white">
												<span class="flex size-6 shrink-0 items-center justify-center rounded-lg border border-black dark:border-white/10 bg-white/5 text-[0.625rem] font-medium text-gray-400 group-hover:border-black dark:border-white/20 group-hover:text-white">H</span>
												<span class="truncate">Heroicons</span>
												</a>
											</li>
											<li>
												<a href="#" class="group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold text-gray-400 hover:bg-white/5 hover:text-white">
												<span class="flex size-6 shrink-0 items-center justify-center rounded-lg border border-black dark:border-white/10 bg-white/5 text-[0.625rem] font-medium text-gray-400 group-hover:border-black dark:border-white/20 group-hover:text-white">T</span>
												<span class="truncate">Tailwind Labs</span>
												</a>
											</li>
											<li>
												<a href="#" class="group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold text-gray-400 hover:bg-white/5 hover:text-white">
												<span class="flex size-6 shrink-0 items-center justify-center rounded-lg border border-black dark:border-white/10 bg-white/5 text-[0.625rem] font-medium text-gray-400 group-hover:border-black dark:border-white/20 group-hover:text-white">W</span>
												<span class="truncate">Workcation</span>
												</a>
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
										Images
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
                                    <ul id="submenu-dashboard-2" class="mt-1 space-y-1 hidden">
                                        <li>
                                        <a href="#"
                                            id="add-album"
                                            class="group flex items-center rounded-md p-2 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white">
                                            <?php echo languageString('general.add_new'); ?>
                                        </a>
                                        </li>
                                        <?php 

                                        $albums = getAlbumList();

                                        foreach($albums as $album)
                                        {
                                          echo '<li id="'.$album['title'].'">
                                                <a href="album-detail.php?album='.$album['slug'].'" class="group flex items-center rounded-md p-2 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white">'.$album['title'].'</a>
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
                                    <ul id="submenu-dashboard-2" class="mt-1 space-y-1 hidden">
                                        <li>
                                        <a href="#"
                                            id="add-album"
                                            class="group flex items-center rounded-md p-2 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white">
                                            <?php echo languageString('general.add_new'); ?>
                                        </a>
                                        </li>
                                        <?php 

                                        $collections = getCollectionList();

                                        foreach($collections as $collection)
                                        {
                                          echo '<li id="'.$collection['title'].'">
                                                <a href="collection-detail.php?collection='.$collection['slug'].'" class="group flex items-center rounded-md p-2 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white">'.$collection['title'].'</a>
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
									<span class="flex size-6 shrink-0 items-center justify-center rounded-lg border border-black dark:border-white/10 bg-white/5 text-[0.625rem] font-medium text-gray-400 group-hover:border-black dark:border-white/20 group-hover:text-white">H</span>
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
                                        <span class="flex-1 text-left">Rating</span>
                                        <!-- Chevron -->
                                        <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"
                                            class="size-5 shrink-0 transition-transform duration-200" data-chevron>
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.25a.75.75 0 0 1-1.06 0L5.25 8.29a.75.75 0 0 1-.02-1.08z" clip-rule="evenodd" />
                                        </svg>
                                    </button>

                                    <!-- Unterpunkte -->
                                    <ul id="submenu-dashboard-2" class="mt-1 space-y-1 hidden">
                                        <li>
                                        <a href="#"
                                            class="group flex items-center rounded-md p-2 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white">
                                            Übersicht
                                        </a>
                                        </li>
                                        <li>
                                        <a href="#"
                                            class="group flex items-center rounded-md p-2 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white">
                                            Analytics
                                        </a>
                                        </li>
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
                                        <span class="flex-1 text-left">Year</span>
                                        <!-- Chevron -->
                                        <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"
                                            class="size-5 shrink-0 transition-transform duration-200" data-chevron>
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.25a.75.75 0 0 1-1.06 0L5.25 8.29a.75.75 0 0 1-.02-1.08z" clip-rule="evenodd" />
                                        </svg>
                                    </button>

                                    <!-- Unterpunkte -->
                                    <ul id="submenu-dashboard-2" class="mt-1 space-y-1 hidden">
                                        <li>
                                        <a href="#"
                                            class="group flex items-center rounded-md p-2 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white">
                                            Übersicht
                                        </a>
                                        </li>
                                        <li>
                                        <a href="#"
                                            class="group flex items-center rounded-md p-2 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white">
                                            Analytics
                                        </a>
                                        </li>
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
                                        <span class="flex-1 text-left">Tags</span>
                                        <!-- Chevron -->
                                        <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"
                                            class="size-5 shrink-0 transition-transform duration-200" data-chevron>
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.25a.75.75 0 0 1-1.06 0L5.25 8.29a.75.75 0 0 1-.02-1.08z" clip-rule="evenodd" />
                                        </svg>
                                    </button>

                                    <!-- Unterpunkte -->
                                    <ul id="submenu-dashboard-2" class="mt-1 space-y-1 hidden">
                                        <li>
                                        <a href="#"
                                            class="group flex items-center rounded-md p-2 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white">
                                            Übersicht
                                        </a>
                                        </li>
                                        <li>
                                        <a href="#"
                                            class="group flex items-center rounded-md p-2 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white">
                                            Analytics
                                        </a>
                                        </li>
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
						    <a href="#"
								class="inline-flex items-center justify-start mx-2 py-2 border-b hover:border-t border-gray-800 dark:border-gray-400 rounded-none
										no-underline text-base font-normal leading-tight appearance-none">
								Dashboard
							</a>
							<a href="#"
								class="inline-flex items-center justify-start mx-2 py-2 border-b-2 border-gray-800 dark:border-gray-400 rounded-none
										no-underline text-base font-normal leading-tight appearance-none">
								Images
							</a>
							<a href="#"
								class="inline-flex items-center justify-start mx-4 py-2 border-b border-gray-800 dark:border-gray-400 rounded-none
										no-underline text-base font-normal leading-tight appearance-none">
								Blog Posts
							</a>
							<a href="#"
								class="inline-flex items-center justify-start mx-4 py-2 border-b border-gray-800 dark:border-gray-400 rounded-none
										no-underline text-base font-normal leading-tight appearance-none">
								Pages
							</a>
						</div>
					</div>
					<div class="flex items-center gap-x-4 lg:gap-x-6">
						<button type="button"
						class="inline-flex items-center gap-2 -m-2.5 p-2.5 text-gray-800 hover:text-gray-500 dark:text-gray-400 dark:hover:text-gray-300">
							New Image
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-upload" viewBox="0 0 16 16">
								<path d="M8.5 11.5a.5.5 0 0 1-1 0V7.707L6.354 8.854a.5.5 0 1 1-.708-.708l2-2a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 7.707z"/>
  								<path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2M9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
							</svg>
							<span class="sr-only">Neues Bild erstellen</span>
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
						<el-dropdown class="relative">
							<button class="relative flex items-center">
								<span class="absolute -inset-1.5"></span>
								<span class="sr-only">Open user menu</span>
								<img src="<?php echo get_userimage($_SESSION['username']); ?>" alt="" class="size-8 rounded-full bg-gray-50 outline -outline-offset-1 outline-black/5 dark:bg-gray-800 dark:outline-white/10" />
								<span class="hidden lg:flex lg:items-center">
									<span aria-hidden="true" class="ml-4 text-sm/6 font-semibold text-gray-900 dark:text-white"><?php echo $_SESSION['username']; ?></span>
									<svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="ml-2 size-5 text-gray-400 dark:text-gray-500">
										<path d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" fill-rule="evenodd" />
									</svg>
								</span>
							</button>
							<el-menu anchor="bottom end" popover class="w-32 origin-top-right rounded-md py-2 shadow-lg outline outline-gray-900/5 transition transition-discrete [--anchor-gap:--spacing(2.5)] data-closed:scale-95 data-closed:transform data-closed:opacity-0 data-enter:duration-100 data-enter:ease-out data-leave:duration-75 data-leave:ease-in bg-white dark:bg-black dark:shadow-none dark:-outline-offset-1 dark:outline-white/10">
								<a href="dashboard-personal.php" class="block px-3 py-1 text-sm/6 text-gray-900 focus:bg-gray-50 focus:outline-hidden dark:text-white dark:focus:bg-white/5"><?php echo languageString('nav.your_profile'); ?></a>
								<a href="login.php?logout=true" class="block px-3 py-1 text-sm/6 text-gray-900 focus:bg-gray-50 focus:outline-hidden dark:text-white dark:focus:bg-white/5"><?php echo languageString('nav.sign_out'); ?></a>
							</el-menu>
						</el-dropdown>
					</div>
				</div>
			</div>
			<!-- Zweite Leiste: nur auf sm sichtbar -->
			<div class="sm:block md:hidden border-b border-gray-600 dark:border-gray-200 bg-white bg-white dark:bg-black dark:border-black dark:border-white/10">
				<div class="px-4 sm:px-6 lg:px-8 text-black dark:text-white">
					<nav class="flex gap-2 justify-center">
					<a href="#"
						class="inline-flex items-center  py-2 border-b hover:border-t border-gray-800 dark:border-gray-400 rounded-none
								no-underline text-base font-normal leading-tight appearance-none">
						Dashboard
					</a>
					<a href="#"
						class="inline-flex items-center py-2 border-b-2 border-gray-800 dark:border-gray-400 rounded-none
								no-underline text-base font-normal leading-tight appearance-none">
						Images
					</a>
					<a href="#"
						class="inline-flex items-center py-2 border-b border-gray-800 dark:border-gray-400 rounded-none
								no-underline text-base font-normal leading-tight appearance-none">
						Pages
					</a>
					<a href="#"
						class="inline-flex items-center py-2 border-b border-gray-800 dark:border-gray-400 rounded-none
								no-underline text-base font-normal leading-tight appearance-none">
						Posts
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
    <!-- Kleines Toggle-Script -->
    <script>
    (function () {
      function getTarget(btn) {
        const sel = btn.getAttribute('data-collapse-target');
        if (!sel || sel === 'next') {
          let n = btn.nextElementSibling;
          while (n && n.nodeType !== 1) n = n.nextElementSibling;
          return n;
        }
        return document.querySelector(sel);
      }

      document.addEventListener('click', (e) => {
        // Globales Toggle für alle Dropdowns
        const btn = e.target.closest('[data-collapse-target]');
        if (btn) {
          const panel = getTarget(btn);
          if (panel) {
            const expanded = btn.getAttribute('aria-expanded') === 'true';
            btn.setAttribute('aria-expanded', String(!expanded));
            panel.classList.toggle('hidden', expanded);
            const chev = btn.querySelector('[data-chevron]');
            if (chev) chev.classList.toggle('rotate-180', !expanded);
          }
        }

        // Outside-Click nur für die Bilderliste
        const container = document.getElementById('image-list');
        if (!container) return;

        container.querySelectorAll('[data-collapse-target][aria-expanded="true"]').forEach((openBtn) => {
          const panel = getTarget(openBtn);
          if (!panel) return;
          const clickInBtn = openBtn.contains(e.target);
          const clickInPanel = panel.contains(e.target);
          if (!clickInBtn && !clickInPanel) {
            openBtn.setAttribute('aria-expanded', 'false');
            panel.classList.add('hidden');
            const chev = openBtn.querySelector('[data-chevron]');
            if (chev) chev.classList.remove('rotate-180');
          }
        });
      });
    })();
    </script>



	</body>
</html>
