                <nav class="flex flex-1 flex-col pt-5 px-15 text-gray-600 text-base font-medium" aria-label="Sidebar">
                  <ul role="list" class="-mx-2 space-y-1">
                    <li>Overview</li>
                    <ul class="px-5">
                      <li><a href="dashboard.php" class="<?php if($settingspage != "dashboard"){ ?> text-gray-400 hover:<?php } ?>text-sky-400">Dashboard</a></li>
                    </ul>
                    <?php
                    
                      if($_SESSION['admin'])
                      {
                    ?>
                    <li>Settings</li>
                    <ul class="px-5">
                      <li><a href="dashboard-user.php" class="<?php if($settingspage != "user"){ ?> text-gray-400 hover:<?php } ?>text-sky-400">User Settings</a></li>
                      <li><a href="dashboard-system.php" class="<?php if($settingspage != "system"){ ?> text-gray-400 hover:<?php } ?>text-sky-400">System Settings</a></li>
                      <li><a href="dashboard-theme.php" class="<?php if($settingspage != "theme"){ ?> text-gray-400 hover:<?php } ?>text-sky-400">Theme Settings</a></li>
                      <li><a href="dashboard-welcomepage.php" class="<?php if($settingspage != "welcomepage"){ ?> text-gray-400 hover:<?php } ?>text-sky-400">Start Page</a></li>
                      <li><a href="dashboard-plugin.php" class="<?php if($settingspage != "plugin"){ ?> text-gray-400 hover:<?php } ?>text-sky-400">Plugins</a></li>
                      <li><a href="dashboard-export_import.php" class="<?php if($settingspage != "export"){ ?> text-gray-400 hover:<?php } ?>text-sky-400">Export / Import</a></li>
                    </ul>
                    <?php
                      }
                    ?>
                  </ul>
                </nav>