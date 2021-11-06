<?php

include "admin/inc/functions.php";

$theme = pcs_get_theme_path();

include $theme."header.php";

if (isset($_GET['content']))
{
    if($_GET['content'] == 'essays')
    {
        //include $theme."essay.php";
        pcs_get_essays();
    }elseif($_GET['content'] == 'essay')
    {
        include $theme."essay.php";
        //pcs_get_essays();
    }elseif($_GET['content'] == 'albums')
    {
        include $theme."albums.php";
        //pcs_get_albums
    }
    elseif($_GET['content'] == 'album')
    {
        include $theme."album.php";
    }
}else{
    include $theme."timeline.php";
}
include $theme."footer.php";

?>