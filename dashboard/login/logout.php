<?php
session_start();
session_destroy(); // Sitzung beenden
header("Location: ../index.php");
exit;

?>