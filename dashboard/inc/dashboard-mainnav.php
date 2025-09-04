<a href="dashboard-personal.php" class="block px-4 text-base font-medium text-sky-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6"><?php echo languageString('dashboard_nav.personalsettings'); ?></a>
<?php
                    
    if($_SESSION['admin'])
    {
        ?>
        <a href="dashboard-user.php" class="block px-4 text-base font-medium text-sky-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6"><?php echo languageString('dashboard_nav.usersettings'); ?></a>
        <a href="dashboard-system.php" class="block px-4 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6"><?php echo languageString('dashboard_nav.systemsettings'); ?></a>
        <a href="dashboard-theme.php" class="block px-4 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6"><?php echo languageString('dashboard_nav.themesettings'); ?></a>
        <a href="dashboard-welcomepage.php" class="block px-4 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6"><?php echo languageString('dashboard_nav.startpage'); ?></a>
        <a href="dashboard-menu.php" class="block px-4 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6"><?php echo languageString('dashboard_nav.navigation'); ?></a>
        <a href="dashboard-plugin.php" class="block px-4 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6"><?php echo languageString('dashboard_nav.plugins'); ?></a>
        <a href="dashboard-export_import.php" class="block px-4 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6"><?php echo languageString('dashboard_nav.import_export'); ?></a>
        <?php
        if(license_isActive())
        {
        ?>
        <a href="dashboard-ki.php" class="block px-4 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6"><?php echo languageString('dashboard_nav.ai'); ?></a>
        <?php
        }
    }
    