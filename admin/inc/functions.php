<?php

require 'db-connect.php';

function get_essays(){

    $conn = OpenCon();

$sql = "SELECT * FROM essay";
$result = $conn->query($sql);

$conn->close();

return $result;

}


?>