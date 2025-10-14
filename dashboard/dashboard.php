<!doctype html>
<html lang="en-US">
   <head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <title>nav.dashboard - Minniark Demo</title>
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
                        <img src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=indigo&shade=500" alt="Your Company" class="h-8 w-auto" />
                     </div>
                     <nav class="relative flex flex-1 flex-col">
                        <ul role="list" class="flex flex-1 flex-col gap-y-7">
                           <li>
                              <ul role="list" class="-mx-2 space-y-1">
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
                                       <span class="flex-1 text-left">dashboard.nav.dashboard</span>
                                       <!-- Chevron -->
                                       <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"
                                          class="size-5 shrink-0 transition-transform duration-200" data-chevron>
                                          <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.25a.75.75 0 0 1-1.06 0L5.25 8.29a.75.75 0 0 1-.02-1.08z" clip-rule="evenodd" />
                                       </svg>
                                    </button>
                                    <!-- Unterpunkte -->
                                    <ul id="submenu-albums" class="mt-1 space-y-1 hidden">
                                       <li>
                                          <a href="dashboard.php"
                                             id="add-album"
                                             class="group flex items-center rounded-md px-1 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white">
                                          dashboard.nav.overview										</a>
                                       </li>
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
                                       <span class="flex-1 text-left">dashboard.nav.settings</span>
                                       <!-- Chevron -->
                                       <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"
                                          class="size-5 shrink-0 transition-transform duration-200" data-chevron>
                                          <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.25a.75.75 0 0 1-1.06 0L5.25 8.29a.75.75 0 0 1-.02-1.08z" clip-rule="evenodd" />
                                       </svg>
                                    </button>
                                    <!-- Unterpunkte -->
                                    <ul id="submenu-collection" class="mt-1 space-y-1 hidden">
                                       <li>
                                          <a href="dashboard-system.php"
                                             id="add-collection"
                                             class="group flex items-center rounded-md px-1 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white">
                                          dashboard.nav.system										</a>
                                       </li>
                                       <li>
                                          <a href="dashboard-theme.php"
                                             id="add-collection"
                                             class="group flex items-center rounded-md px-1 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white">
                                          dashboard.nav.theme										</a>
                                       </li>
                                       <li>
                                          <a href="dashboard-welcomepage.php"
                                             id="add-collection"
                                             class="group flex items-center rounded-md px-1 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white">
                                          dashboard.nav.welcome										</a>
                                       </li>
                                       <li>
                                          <a href="dashboard-plugin.php"
                                             id="add-collection"
                                             class="group flex items-center rounded-md px-1 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white">
                                          dashboard.nav.plugin										</a>
                                       </li>
                                       <li>
                                          <a href="dashboard-user.php"
                                             id="add-collection"
                                             class="group flex items-center rounded-md px-1 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white">
                                          dashboard.nav.user										</a>
                                       </li>
                                       <li>
                                          <a href="dashboard-menu.php"
                                             id="add-collection"
                                             class="group flex items-center rounded-md px-1 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white">
                                          dashboard.nav.nav										</a>
                                       </li>
                                       <li>
                                          <a href="dashboard-export_import.php"
                                             id="add-collection"
                                             class="group flex items-center rounded-md px-1 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white">
                                          dashboard.nav.export_import										</a>
                                       </li>
                                       <li>
                                          <a href="dashboard-update.php"
                                             id="add-collection"
                                             class="group flex items-center rounded-md px-1 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white">
                                          dashboard.nav.update										</a>
                                       </li>
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
                                       <span class="flex-1 text-left">dashboard.nav.personal</span>
                                       <!-- Chevron -->
                                       <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"
                                          class="size-5 shrink-0 transition-transform duration-200" data-chevron>
                                          <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.25a.75.75 0 0 1-1.06 0L5.25 8.29a.75.75 0 0 1-.02-1.08z" clip-rule="evenodd" />
                                       </svg>
                                    </button>
                                    <!-- Unterpunkte -->
                                    <ul id="submenu-collection" class="mt-1 space-y-1 hidden">
                                       <li>
                                          <a href="dashboard-personal.php"
                                             id="add-collection"
                                             class="group flex items-center rounded-md px-1 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white">
                                          dashboard.nav.personal										</a>
                                       </li>
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
                                       <span class="flex-1 text-left">dashboard.nav.additionalmenu</span>
                                       <!-- Chevron -->
                                       <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"
                                          class="size-5 shrink-0 transition-transform duration-200" data-chevron>
                                          <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.25a.75.75 0 0 1-1.06 0L5.25 8.29a.75.75 0 0 1-.02-1.08z" clip-rule="evenodd" />
                                       </svg>
                                    </button>
                                    <!-- Unterpunkte -->
                                    <ul id="submenu-collection" class="mt-1 space-y-1 hidden">
                                       <li>
                                          <a href="dashboard-ai.php"
                                             id="add-collection"
                                             class="group flex items-center rounded-md px-1 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white">
                                          dashboard.nav.ai										</a>
                                       </li>
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
                              <span class="flex-1 text-left">dashboard.nav.dashboard</span>
                              <!-- Chevron -->
                              <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"
                                 class="size-5 shrink-0 transition-transform duration-200" data-chevron>
                                 <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.25a.75.75 0 0 1-1.06 0L5.25 8.29a.75.75 0 0 1-.02-1.08z" clip-rule="evenodd" />
                              </svg>
                           </button>
                           <!-- Unterpunkte -->
                           <ul id="submenu-albums" class="mt-1 space-y-1 hidden">
                              <li>
                                 <a href="dashboard.php"
                                    id="add-album"
                                    class="group flex items-center rounded-md px-1 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white">
                                 dashboard.nav.overview										</a>
                              </li>
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
                              <span class="flex-1 text-left">dashboard.nav.settings</span>
                              <!-- Chevron -->
                              <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"
                                 class="size-5 shrink-0 transition-transform duration-200" data-chevron>
                                 <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.25a.75.75 0 0 1-1.06 0L5.25 8.29a.75.75 0 0 1-.02-1.08z" clip-rule="evenodd" />
                              </svg>
                           </button>
                           <!-- Unterpunkte -->
                           <ul id="submenu-collection" class="mt-1 space-y-1 hidden">
                              <li>
                                 <a href="dashboard-system.php"
                                    id="add-collection"
                                    class="group flex items-center rounded-md px-1 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white">
                                 dashboard.nav.system										</a>
                              </li>
                              <li>
                                 <a href="dashboard-theme.php"
                                    id="add-collection"
                                    class="group flex items-center rounded-md px-1 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white">
                                 dashboard.nav.theme										</a>
                              </li>
                              <li>
                                 <a href="dashboard-welcomepage.php"
                                    id="add-collection"
                                    class="group flex items-center rounded-md px-1 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white">
                                 dashboard.nav.welcome										</a>
                              </li>
                              <li>
                                 <a href="dashboard-plugin.php"
                                    id="add-collection"
                                    class="group flex items-center rounded-md px-1 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white">
                                 dashboard.nav.plugin										</a>
                              </li>
                              <li>
                                 <a href="dashboard-user.php"
                                    id="add-collection"
                                    class="group flex items-center rounded-md px-1 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white">
                                 dashboard.nav.user										</a>
                              </li>
                              <li>
                                 <a href="dashboard-menu.php"
                                    id="add-collection"
                                    class="group flex items-center rounded-md px-1 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white">
                                 dashboard.nav.nav										</a>
                              </li>
                              <li>
                                 <a href="dashboard-export_import.php"
                                    id="add-collection"
                                    class="group flex items-center rounded-md px-1 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white">
                                 dashboard.nav.export_import										</a>
                              </li>
                              <li>
                                 <a href="dashboard-update.php"
                                    id="add-collection"
                                    class="group flex items-center rounded-md px-1 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white">
                                 dashboard.nav.update										</a>
                              </li>
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
                              <span class="flex-1 text-left">dashboard.nav.personal</span>
                              <!-- Chevron -->
                              <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"
                                 class="size-5 shrink-0 transition-transform duration-200" data-chevron>
                                 <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.25a.75.75 0 0 1-1.06 0L5.25 8.29a.75.75 0 0 1-.02-1.08z" clip-rule="evenodd" />
                              </svg>
                           </button>
                           <!-- Unterpunkte -->
                           <ul id="submenu-collection" class="mt-1 space-y-1 hidden">
                              <li>
                                 <a href="dashboard-personal.php"
                                    id="add-collection"
                                    class="group flex items-center rounded-md px-1 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white">
                                 dashboard.nav.personal										</a>
                              </li>
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
                              <span class="flex-1 text-left">dashboard.nav.additionalmenu</span>
                              <!-- Chevron -->
                              <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"
                                 class="size-5 shrink-0 transition-transform duration-200" data-chevron>
                                 <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.25a.75.75 0 0 1-1.06 0L5.25 8.29a.75.75 0 0 1-.02-1.08z" clip-rule="evenodd" />
                              </svg>
                           </button>
                           <!-- Unterpunkte -->
                           <ul id="submenu-collection" class="mt-1 space-y-1 hidden">
                              <li>
                                 <a href="dashboard-ai.php"
                                    id="add-collection"
                                    class="group flex items-center rounded-md px-1 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white">
                                 dashboard.nav.ai										</a>
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
                  <a href="dashboard.php"
                     class="inline-flex items-center justify-start mx-2 py-2 border-b-2 border-gray-800 dark:border-gray-400 rounded-none
                     no-underline text-base font-normal leading-tight appearance-none">
                  nav.dashboard							</a>
                  <a href="media.php"
                     class="inline-flex items-center justify-start mx-2 py-2 border-b border-gray-800 dark:border-gray-400 rounded-none
                     no-underline text-base font-normal leading-tight appearance-none">
                  nav.images							</a>
                  <a href="blog.php"
                     class="inline-flex items-center justify-start mx-2 py-2 border-b border-gray-800 dark:border-gray-400 rounded-none
                     no-underline text-base font-normal leading-tight appearance-none">
                  nav.blogposts							</a>
                  <a href="pages.php"
                     class="inline-flex items-center justify-start mx-2 py-2 border-b border-gray-800 dark:border-gray-400 rounded-none
                     no-underline text-base font-normal leading-tight appearance-none">
                  nav.pages							</a>
               </div>
            </div>
            <div class="flex items-center gap-x-4 lg:gap-x-6">
               <button type="button" 
                  id="notifBtn"
                  class="-m-2.5 p-2.5 text-gray-800 hover:text-gray-500 dark:text-gray-400 dark:hover:text-gray-300">
                  <span class="sr-only">View notifications</span>
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6">
                     <path d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
               </button>
               <!-- Dropdown wird per JS eingefügt -->
               <div id="notifMenu" hidden></div>
               <!-- Separator -->
               <div aria-hidden="true" class="hidden lg:block lg:h-6 lg:w-px lg:bg-white dark:bg-black/10 dark:lg:bg-gray-100/10"></div>
               <!-- Profile dropdown -->
               <div data-dropdown class="relative">
                  <button type="button" class="relative flex items-center"
                     aria-haspopup="menu" aria-expanded="false" data-trigger>
                     <span class="absolute -inset-1.5"></span>
                     <span class="sr-only">Open user menu</span>
                     <img src="https://www.gravatar.com/avatar/b596ec4cc497d5338897010ab616ed3e?s=160&d=http%3A%2F%2Fminniark.heimfisch.me%2Fdashboard%2Fimg%2Favatar.png" alt=""
                        class="size-8 rounded-full bg-gray-50 outline -outline-offset-1 outline-black/5 dark:bg-gray-800 dark:outline-white/10" />
                     <span class="hidden lg:flex lg:items-center">
                        <span aria-hidden="true" class="ml-4 text-sm/6 font-semibold text-gray-900 dark:text-white">
                        cheinisch								</span>
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
                     nav.your_profile								</a>
                     <a href="login.php?logout=true"
                        class="block px-3 py-1 text-sm/6 text-gray-900 hover:bg-gray-50 focus:outline-hidden dark:text-white dark:hover:bg-white/5"
                        role="menuitem">
                     nav.sign_out								</a>
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
                  class="inline-flex items-center  py-2 border-b-2 border-gray-800 dark:border-gray-400 rounded-none
                  no-underline text-base font-normal leading-tight appearance-none">
               nav.dashboard					</a>
               <a href="media.php"
                  class="inline-flex items-center py-2 border-b border-gray-800 dark:border-gray-400 rounded-none
                  no-underline text-base font-normal leading-tight appearance-none">
               nav.images					</a>
               <a href="blog.php"
                  class="inline-flex items-center py-2 border-b border-gray-800 dark:border-gray-400 rounded-none
                  no-underline text-base font-normal leading-tight appearance-none">
               nav.blogposts					</a>
               <a href="pages.php"
                  class="inline-flex items-center py-2 border-b border-gray-800 dark:border-gray-400 rounded-none
                  no-underline text-base font-normal leading-tight appearance-none">
               nav.pages					</a>
            </nav>
         </div>
      </div>
      <main class="py-10 bg-white dark:bg-black">
         <div class="px-4 sm:px-6 lg:px-8 text-black dark:text-white">
            <!-- Toolbar -->
            <div class="mb-4 flex items-center justify-end">
               <a href="backend_api/cache.php"
                  class="text-xs px-2 py-1 rounded border border-black/10 hover:bg-black/5 dark:border-white/10 dark:hover:bg-white/10">
               dashboard.clear_cache      </a>
            </div>
            <!-- System + News: gleiche Card-Optik wie Blogliste -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
               <!-- System Information -->
               <section class="rounded-sm border border-black/10 dark:border-white/10 bg-white dark:bg-black/40 shadow-xs">
                  <header class="px-4 py-3 border-b border-black/10 dark:border-white/10">
                     <h3 class="text-sm font-semibold">dashboard.systeminformation.title</h3>
                  </header>
                  <div class="p-4 space-y-6">
                     <!-- Storage -->
                     <div>
                        <h4 class="text-sm font-semibold">dashboard.systeminformation.storage</h4>
                        <ul class="mt-2 text-sm text-black/80 dark:text-gray-300">
                           <li class="mb-1">
                              Used storage: 62 MB
                              <ul class="list-disc ml-5 mt-1 space-y-0.5">
                                 <li>Image storage: 16 MB</li>
                                 <li>Cache storage: 33.4 MB</li>
                                 <li>System and Backup: 12.6 MB</li>
                              </ul>
                           </li>
                           <li>Free storage: 6703.8 MB</li>
                        </ul>
                     </div>
                     <!-- Versions -->
                     <div>
                        <h4 class="text-sm font-semibold">dashboard.systeminformation.version</h4>
                        <div class="mt-2 overflow-x-auto">
                           <table class="w-full text-sm">
                              <thead class="text-left text-black/60 dark:text-gray-400">
                                 <tr class="border-b border-black/10 dark:border-white/10">
                                    <th class="py-2 pr-4">dashboard.systeminformation.component</th>
                                    <th class="py-2">dashboard.systeminformation.component-version</th>
                                 </tr>
                              </thead>
                              <tbody class="text-black/80 dark:text-gray-300 divide-y divide-black/10 dark:divide-white/10">
                                 <tr>
                                    <td class="py-2 pr-4">Minniark</td>
                                    <td class="py-2">2025.7.1</td>
                                 </tr>
                                 <tr>
                                    <td class="py-2 pr-4">dashboard.systeminformation.operation_system</td>
                                    <td class="py-2">Linux</td>
                                 </tr>
                                 <tr>
                                    <td class="py-2 pr-4">PHP</td>
                                    <td class="py-2">8.2.29</td>
                                 </tr>
                                 <tr>
                                    <td class="py-2 pr-4">dashboard.systeminformation.webserver</td>
                                    <td class="py-2">nginx/1.22.1</td>
                                 </tr>
                              </tbody>
                           </table>
                        </div>
                     </div>
                  </div>
               </section>
               <!-- News / Updates -->
               <section class="rounded-sm border border-black/10 dark:border-white/10 bg-white dark:bg-black/40 shadow-xs">
                  <header class="px-4 py-3 border-b border-black/10 dark:border-white/10">
                     <h3 class="text-sm font-semibold">dashboard.systeminformation.news</h3>
                  </header>
                  <div class="divide-y divide-black/10 dark:divide-white/10">
                     <article class="p-4">
                        <div class="flex items-start justify-between gap-4">
                           <a href="https://minniark.app/blog/august-updates"
                              class="text-base font-semibold hover:underline">
                           August Updates                </a>
                        </div>
                        <div class="mt-1 text-xs text-black/60 dark:text-gray-400">
                           <time>17. Aug 2025</time>
                        </div>
                        <p class="mt-2 text-sm text-black/80 dark:text-gray-300">
                           Minniark continues to evolve with new features, improvements, and stability fixes. Below are the highlights and changelogs for the latest updates in August 2025.
                           Changes
                           These are the changes for august
                           AI Text generation
                           Added automatic AI-based d…              
                        </p>
                     </article>
                     <article class="p-4">
                        <div class="flex items-start justify-between gap-4">
                           <a href="https://minniark.app/blog/july-updates"
                              class="text-base font-semibold hover:underline">
                           July Updates                </a>
                        </div>
                        <div class="mt-1 text-xs text-black/60 dark:text-gray-400">
                           <time>30. Jul 2025</time>
                        </div>
                        <p class="mt-2 text-sm text-black/80 dark:text-gray-300">
                           Minniark continues to evolve with new features, improvements, and stability fixes. Below are the highlights and changelogs for the latest updates in July 2025.
                           Changes
                           These are the changes for june
                           Template Installation and Updates
                           You can now inst…              
                        </p>
                     </article>
                     <article class="p-4">
                        <div class="flex items-start justify-between gap-4">
                           <a href="https://minniark.app/blog/june-updates"
                              class="text-base font-semibold hover:underline">
                           June Updates                </a>
                        </div>
                        <div class="mt-1 text-xs text-black/60 dark:text-gray-400">
                           <time>27. Jun 2025</time>
                        </div>
                        <p class="mt-2 text-sm text-black/80 dark:text-gray-300">
                           There are some changes in june. The biggest change is the change from php files to yml and md (markdown). The second change is, you can create a custom navigation. And there a lot of bug fixes.
                           Changes
                           These are the changes for june
                           Custom Navigatio…              
                        </p>
                     </article>
                  </div>
               </section>
            </div>
            <!-- Hinweis -->
            <div class="mt-4">
               <div class="rounded-sm border border-black/10 dark:border-white/10 bg-sky-700 text-white px-3 py-2 shadow-xs">
                  dashboard.systeminformation.bugreport        
               </div>
            </div>
         </div>
      </main>
      <script src="js/navbar.js"></script>
      <script src="js/tailwind.js"></script>
      <script src="js/profile_settings.js"></script>
      <!--<script>
         document.getElementById('location').addEventListener('change', function () {
             const url = this.value;
             window.location.href = url; // Weiterleitung zur gewählten URL
         });
         </script>-->
      <script src="js/notify.js"></script>
   </body>
</html>
