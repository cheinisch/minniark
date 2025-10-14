<ul role="list" class="flex flex-1 flex-col gap-y-7">
							
						<li>
							<div class="text-xs/6 font-semibold text-gray-400"><?php echo languageString('page.filter_posts'); ?></div>
							<ul role="list" class="-mx-2 mt-2 space-y-1">
               					 <li>
									<!-- Current: "bg-white/5 text-white", Default: "text-gray-400 hover:text-white hover:bg-white/5" -->
									<a href="?" class="group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold text-gray-400 hover:bg-white/5 hover:text-white">
									<span class="flex size-6 shrink-0 items-center justify-center rounded-lg border border-black dark:border-white/10 bg-white/5 text-[0.625rem] font-medium text-gray-400 group-hover:border-black dark:border-white/20 group-hover:text-white">R</span>
									<span class="truncate"><?php echo languageString('general.reset_filter'); ?></span>
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
										<span class="flex-1 text-left"><?php echo languageString('general.year'); ?></span>
										<!-- Chevron -->
										<svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"
											class="size-5 shrink-0 transition-transform duration-200" data-chevron>
										<path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.25a.75.75 0 0 1-1.06 0L5.25 8.29a.75.75 0 0 1-.02-1.08z" clip-rule="evenodd" />
										</svg>
									</button>

									<!-- Unterpunkte -->
									<ul id="submenu-dashboard-2" class="mt-1 space-y-1 hidden">
									<?php get_pageyearlist(false); ?>
									</ul>
								</li>
							</ul>
						</li>
					</ul>