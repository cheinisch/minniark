<?php
                    
    if($_SESSION['admin'])
    {
?>
<a href="dashboard-user.php" class="block px-4 text-base font-medium text-sky-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6">User Settings</a>
<a href="dashboard-system.php" class="block px-4 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6">System Settings</a>
<a href="dashboard-theme.php" class="block px-4 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6">Theme Settings</a>
<a href="dashboard-welcomepage.php" class="block px-4 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6">Start Page</a>
<a href="dashboard-menu.php" class="block px-4 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6">Navigation</a>
<a href="dashboard-plugin.php" class="block px-4 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6">Plugins</a>
<a href="dashboard-export_import.php" class="block px-4 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6">Export / Import</a>
<?php
    }
?>