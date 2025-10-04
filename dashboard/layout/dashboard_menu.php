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
										<span class="flex-1 text-left"><?php echo languageString('dashboard.nav.dashboard'); ?></span>
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
											<?php echo languageString('dashboard.nav.overview'); ?>
										</a>
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
										<span class="flex-1 text-left"><?php echo languageString('dashboard.nav.settings'); ?></span>
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
											<?php echo languageString('dashboard.nav.system'); ?>
										</a>
										</li>
										<li>
										<a href="dashboard-theme.php"
											id="add-collection"
											class="group flex items-center rounded-md px-1 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white">
											<?php echo languageString('dashboard.nav.theme'); ?>
										</a>
										</li>
										<li>
										<a href="dashboard-welcomepage.php"
											id="add-collection"
											class="group flex items-center rounded-md px-1 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white">
											<?php echo languageString('dashboard.nav.welcome'); ?>
										</a>
										</li>
										<li>
										<a href="dashboard-plugin.php"
											id="add-collection"
											class="group flex items-center rounded-md px-1 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white">
											<?php echo languageString('dashboard.nav.plugin'); ?>
										</a>
										</li>
										<li>
										<a href="dashboard-user.php"
											id="add-collection"
											class="group flex items-center rounded-md px-1 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white">
											<?php echo languageString('dashboard.nav.user'); ?>
										</a>
										</li>
										<li>
										<a href="dashboard-menu.php"
											id="add-collection"
											class="group flex items-center rounded-md px-1 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white">
											<?php echo languageString('dashboard.nav.nav'); ?>
										</a>
										</li>
										<li>
										<a href="dashboard-export_import.php"
											id="add-collection"
											class="group flex items-center rounded-md px-1 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white">
											<?php echo languageString('dashboard.nav.export_import'); ?>
										</a>
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
										<span class="flex-1 text-left"><?php echo languageString('dashboard.nav.personal'); ?></span>
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
											<?php echo languageString('dashboard.nav.personal'); ?>
										</a>
										</li>
									</ul>
								</li>
								<!-- Dashboard 2 mit Dropdown -->
								<?php
									if(license_isActive())
									{
									?>
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
										<span class="flex-1 text-left"><?php echo languageString('dashboard.nav.additionalmenu'); ?></span>
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
											<?php echo languageString('dashboard.nav.ai'); ?>
										</a>
										</li>
									</ul>
								</li>
								<?php
									}
									?>
							</ul>
						</li>
					</ul>