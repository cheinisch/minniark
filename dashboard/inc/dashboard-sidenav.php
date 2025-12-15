                <nav class="flex flex-1 flex-col pt-5 px-15 text-gray-600 text-base font-medium" aria-label="Sidebar">
                  <ul role="list" class="-mx-2 space-y-1">
                    <li><?php echo languageString('dashboard_nav.overview'); ?></li>
                    <ul class="px-5">
                      <li><a href="dashboard.php" class="<?php if($settingspage != "dashboard"){ ?> text-gray-400 hover:<?php } ?>text-cyan-400"><?php echo languageString('dashboard_nav.dashboard'); ?></a></li>
                    </ul>
                    
                    <li><?php echo languageString('dashboard_nav.settings'); ?></li>
                    <ul class="px-5">
                      <li><a href="dashboard-personal.php" class="<?php if($settingspage != "personal"){ ?> text-gray-400 hover:<?php } ?>text-cyan-400"><?php echo languageString('dashboard_nav.personalsettings'); ?></a></li>
                      <?php
                    
                      if($_SESSION['admin'])
                      {
                        ?>
                        <li><a href="dashboard-user.php" class="<?php if($settingspage != "user"){ ?> text-gray-400 hover:<?php } ?>text-cyan-400"><?php echo languageString('dashboard_nav.usersettings'); ?></a></li>
                        <li><a href="dashboard-system.php" class="<?php if($settingspage != "system"){ ?> text-gray-400 hover:<?php } ?>text-cyan-400"><?php echo languageString('dashboard_nav.systemsettings'); ?></a></li>
                        <li><a href="dashboard-theme.php" class="<?php if($settingspage != "theme"){ ?> text-gray-400 hover:<?php } ?>text-cyan-400"><?php echo languageString('dashboard_nav.themesettings'); ?></a></li>
                        <li><a href="dashboard-welcomepage.php" class="<?php if($settingspage != "welcomepage"){ ?> text-gray-400 hover:<?php } ?>text-cyan-400"><?php echo languageString('dashboard_nav.startpage'); ?></a></li>
                        <li><a href="dashboard-menu.php" class="<?php if($settingspage != "menu"){ ?> text-gray-400 hover:<?php } ?>text-cyan-400"><?php echo languageString('dashboard_nav.navigation'); ?></a></li>
                        <li><a href="dashboard-plugin.php" class="<?php if($settingspage != "plugin"){ ?> text-gray-400 hover:<?php } ?>text-cyan-400"><?php echo languageString('dashboard_nav.plugins'); ?></a></li>
                        <li><a href="dashboard-export_import.php" class="<?php if($settingspage != "export"){ ?> text-gray-400 hover:<?php } ?>text-cyan-400"><?php echo languageString('dashboard_nav.import_export'); ?></a></li>
                        <?php
                        if(license_isActive())
                        {
                        ?>
                          <li><a href="dashboard-ki.php" class="<?php if($settingspage != "ki"){ ?> text-gray-400 hover:<?php } ?>text-cyan-400"><?php echo languageString('dashboard_nav.ai'); ?></a></li>
                        <?php
                        }
                        ?>
                      <?php
                      }
                      ?>
                    </ul>
                    
                  </ul>
                </nav>