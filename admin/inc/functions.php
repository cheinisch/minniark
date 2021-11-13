<?php

require 'db-connect.php';
require 'image-cache.php';

function get_essays(){

    $conn = OpenCon();
    $conn->query("SET NAMES 'utf8'");
    $sql = "SELECT * FROM essay;";
    $result = $conn->query($sql);

    $conn->close();

    return $result;

}

function get_albums(){

    $conn = OpenCon();
    $conn->query("SET NAMES 'utf8'");
    $sql = "SELECT * FROM album;";
    $result = $conn->query($sql);

    $conn->close();

    return $result;

}

function get_essay($id){

    $conn = OpenCon();
    $conn->query("SET NAMES 'utf8'");

    $sql = "SELECT * FROM `essay` WHERE `id` = $id;";
    $result = $conn->query($sql);

    $conn->close();

    return $result;

}

function trunc($phrase, $max_words) {
    $phrase_array = explode(' ',$phrase);
    if(count($phrase_array) > $max_words && $max_words > 0)
       $phrase = implode(' ',array_slice($phrase_array, 0, $max_words)).'...';
    return $phrase;
 }

 function login($user, $password)
 {

    $conn = OpenCon();
    $conn->query("SET NAMES 'utf8'");

    $sql = "SELECT `admin_mail`, `admin_passwd` FROM `config`;";
    $result = $conn->query($sql);

    $conn->close();

    $userdata = $result->fetch_assoc();

    if($user == $userdata["admin_mail"])
    {
        if(password_verify($password,$userdata["admin_passwd"]))
        {
            return true;
        }else{
            return false;
        }
    }else{
        return false;
    }

    return false;

 }

 function userid()
 {

    $conn = OpenCon();
    $conn->query("SET NAMES 'utf8'");

    $sql = "SELECT `admin_mail`, `admin_passwd` FROM `config`;";
    $result = $conn->query($sql);

    $conn->close();

    $userdata = $result->fetch_assoc();

    return $userdata["admin_mail"];
    

 }

 function get_userdata()
 {
    $conn = OpenCon();
    $conn->query("SET NAMES 'utf8'");

    $sql = "SELECT `admin_mail`, `admin_user` FROM `config`;";
    $result = $conn->query($sql);

    $conn->close();

    $userdata = $result->fetch_assoc();

    return $userdata; 
 }

 function set_userdata($username, $usermail, $userpasswd, $current_user)
 {

    if(!empty($username))
    {

        echo $username." ".$current_user;
        $conn = OpenCon();
        $conn->query("SET NAMES 'utf8'");
        $sql = "UPDATE `config` SET `admin_user` = '$username' WHERE `config`.`admin_user` = '$current_user';";
        echo $sql;
        $conn->query($sql);
        $conn->close();
    }

    if(!empty($usermail))
    {

        $conn = OpenCon();
        $conn->query("SET NAMES 'utf8'");
        $sql = "UPDATE `config` SET `admin_mail` = '$usermail' WHERE `config`.`admin_user` = '$current_user';";
        $conn->query($sql);
        $conn->close();
    }

    if(!empty($userpasswd))
    {
        $options = [
            'cost' => 11,
        ];

        $hash = password_hash($userpasswd, PASSWORD_BCRYPT, $options);

        $conn = OpenCon();
        $conn->query("SET NAMES 'utf8'");
        $sql = "UPDATE `config` SET `admin_passwd` = '$hash' WHERE `config`.`admin_user` = '$current_user';";
        $conn->query($sql);
        $conn->close();
    }
 }

 function get_sitedata()
 {
    $conn = OpenCon();
    $conn->query("SET NAMES 'utf8'");

    $sql = "SELECT * FROM `config`;";
    $result = $conn->query($sql);

    $conn->close();

    $sitedata = $result->fetch_assoc();

    return $sitedata; 
 }

 function set_sitedata($sitename, $sitetitle, $sitetagline, $sitecopyright, $sitekeywords, $current_user)
 {

    if(!empty($sitename))
    {
        $conn = OpenCon();
        $conn->query("SET NAMES 'utf8'");
        $sql = "UPDATE `config` SET `site-name` = '$sitename' WHERE `config`.`admin_user` = '$current_user';";
        echo $sql;
        $conn->query($sql);
        $conn->close();
    }

    if(!empty($sitetitle))
    {
        $conn = OpenCon();
        $conn->query("SET NAMES 'utf8'");
        $sql = "UPDATE `config` SET `site-title` = '$sitetitle' WHERE `config`.`admin_user` = '$current_user';";
        echo $sql;
        $conn->query($sql);
        $conn->close();
    }

    if(!empty($sitetagline))
    {
        $conn = OpenCon();
        $conn->query("SET NAMES 'utf8'");
        $sql = "UPDATE `config` SET `site-tagline` = '$sitetagline' WHERE `config`.`admin_user` = '$current_user';";
        echo $sql;
        $conn->query($sql);
        $conn->close();
    }

    if(!empty($sitecopyright))
    {
        $conn = OpenCon();
        $conn->query("SET NAMES 'utf8'");
        $sql = "UPDATE `config` SET `site-copyright` = '$sitecopyright' WHERE `config`.`admin_user` = '$current_user';";
        echo $sql;
        $conn->query($sql);
        $conn->close();
    }

    if(!empty($sitekeywords))
    {
        $conn = OpenCon();
        $conn->query("SET NAMES 'utf8'");
        $sql = "UPDATE `config` SET `site-keywords` = '$sitekeywords' WHERE `config`.`admin_user` = '$current_user';";
        echo $sql;
        $conn->query($sql);
        $conn->close();
    }

 }

 function get_album_images()
 {
        $id = $_GET['id'];

        $conn = OpenCon();
    $conn->query("SET NAMES 'utf8'");

    $sql = "SELECT * FROM `content` where `content-album-id` = $id;";
    
    $result = $conn->query($sql) or die($conn->error);
    $conn->close();

    return $result;
 }


 function recreate_cache()
 {
    echo getcwd() . "\n";

    $files = glob("..\\storage\\images\\cache\\*"); // get all file names
    foreach($files as $file){ // iterate files
        if(is_file($file)) {
            unlink($file); // delete file
        }
    }

    if ($handle = opendir("..\\storage\\images\\original\\")) {

        while (false !== ($entry = readdir($handle))) {
    
            if ($entry != "." && $entry != "..") {
    
                echo "$entry\n";
                createimage('..\\storage\\images\\original\\'.$entry,'..\\storage\\images\\cache\\thumb_'.$entry, 200);

                createimage('..\\storage\\images\\original\\'.$entry,'..\\storage\\images\\cache\\medium_'.$entry, 600);

                createimage('..\\storage\\images\\original\\'.$entry,'..\\storage\\images\\cache\\large_'.$entry, 1024);
            }
        }
    
        closedir($handle);
    }



 }


 function pcs_admin_albums_item($item)
 {
 
     $albumlist = get_albums();
 
     while($row = $albumlist->fetch_assoc())
             {
 
         $vars = array(
             '{{date}}'       => date('F j, Y', $row["album_date"]),
             '{{text}}'        => $row["album_title"],
             '{{image.thumbnail}}' => admin_ip_get_album_thumbnail($row["id"]),
             '{{image.medium}}' => admin_ip_get_album_medium($row["id"]),
             '{{album_url}}' => "admin.php?page=album-edit&id=".$row["id"]
           );
 
         echo strtr($item, $vars);
     }
 }

 function admin_ip_get_album_thumbnail($albumid)
{
    $conn = OpenCon();
    $conn->query("SET NAMES 'utf8'");

    $sql = "SELECT * FROM `content` where `content-album-id` = $albumid and `content-album-id-title` = 1;";
    
    $result = $conn->query($sql) or die($conn->error);
    $conn->close();

    $image = $result->fetch_assoc();

    if(!isset($image["content-filename"]))
    {
        return "../storage/images/error_images/fff.png";    
    }

    return "../storage/images/cache/thumb_".$image["content-filename"];
}

function admin_ip_get_album_medium($albumid)
{
    $conn = OpenCon();
    $conn->query("SET NAMES 'utf8'");

    $sql = "SELECT * FROM `content` where `content-album-id` = $albumid and `content-album-id-title` = 1;";
    
    $result = $conn->query($sql) or die($conn->error);
    $conn->close();

    $image = $result->fetch_assoc();

    if(!isset($image["content-filename"]))
    {
        return "../storage/images/error_images/fff.png";    
    }

    return "../storage/images/cache/mediun_".$image["content-filename"];
}


 // Begin public functions
 // These Functions are in the API

function pcs_get_theme_path()
{
    $conn = OpenCon();
    $conn->query("SET NAMES 'utf8'");

    $sql = "SELECT `theme` FROM `config`;";
    $result = $conn->query($sql);

    $conn->close();

    $theme = $result->fetch_assoc();
    $theme = "storage/themes/".$theme["theme"]."/";
    return $theme;
}


function pcs_get_page_title()
{
    $conn = OpenCon();
    $conn->query("SET NAMES 'utf8'");

    $sql = "SELECT `site-title` FROM `config`;";
    $result = $conn->query($sql);

    $conn->close();

    $title = $result->fetch_assoc();

    return $title["site-title"];
}

function pcs_get_essays()
{

    $essaylist = get_essays();
            while($row = $essaylist->fetch_assoc())
            {

    ?>
    <article class="blog-post">
    <h2 class="blog-post-title"><a href="index.php?content=essay&id=<?php echo $row["id"]; ?>"><?php echo $row["essay_title"]; ?></a></h2>
    <p class="blog-post-meta"><?php echo $row["publish_date"]; ?> by <?php echo ip_get_author(); ?></p>
    <?php echo trunc($row["essay_text"],200); ?>
    </article>
        <?php
            }
}

function pcs_get_essay_title()
{

    $id = $_GET['id'];

    $conn = OpenCon();
    $conn->query("SET NAMES 'utf8'");

    $sql = "SELECT * FROM `essay` where `id` = $id;";
    $result = $conn->query($sql);

    $conn->close();

    $title = $result->fetch_assoc();

    return $title["essay_title"];
}

function pcs_get_essay_text()
{

    $id = $_GET['id'];

    $conn = OpenCon();
    $conn->query("SET NAMES 'utf8'");

    $sql = "SELECT * FROM `essay` where `id` = $id;";
    $result = $conn->query($sql);

    $conn->close();

    $title = $result->fetch_assoc();

    return $title["essay_text"];
}

function pcs_get_essay_date()
{

    $id = $_GET['id'];

    $conn = OpenCon();
    $conn->query("SET NAMES 'utf8'");

    $sql = "SELECT * FROM `essay` where `id` = $id;";
    $result = $conn->query($sql);

    $conn->close();

    $title = $result->fetch_assoc();

    return $title["publish_date"];
}

function pcs_get_main_menu($list_item, $active_list_item)
{
    $menu[] = array('type' => 'albums', 
    'title'   => "Albums");

    $menu[] = array('type' => 'essays', 
    'title'   => "Essays");

    for($i = 0;$i < count($menu);$i ++)
    {

        $vars = array(
            '{{type}}'       => $menu[$i]["type"],
            '{{title}}'        => $menu[$i]["title"]
          );

          if(isset($_GET['content']))
          {
          if($_GET['content'] == $menu[$i]["type"])
          {
            echo strtr($active_list_item, $vars);
          }else{
        echo strtr($list_item, $vars);
          }
        }else{
            echo strtr($list_item, $vars);
        }
    }
}


function pcs_albums_item($item)
{

    $albumlist = get_albums();

    while($row = $albumlist->fetch_assoc())
            {

        $vars = array(
            '{{date}}'       => date('F j, Y', $row["album_date"]),
            '{{text}}'        => $row["album_title"],
            '{{image.thumbnail}}' => ip_get_album_thumbnail($row["id"]),
            '{{image.medium}}' => ip_get_album_medium($row["id"]),
            '{{image.large}}' => ip_get_album_large($row["id"]),
            '{{image.original}}' => ip_get_album_original($row["id"]),
            '{{album_url}}' => "index.php?content=album&id=".$row["id"]
          );

        echo strtr($item, $vars);
    }
}

function ip_get_author()
{
    $author = get_userdata();

    return $author["admin_user"];
}

function ip_get_sitename()
{
    $conn = OpenCon();
    $conn->query("SET NAMES 'utf8'");

    $sql = "SELECT `site-name` FROM `config`;";
    $result = $conn->query($sql);

    $conn->close();

    $title = $result->fetch_assoc();

    return $title["site-name"];
}

function ip_get_sitetitle()
{
    $conn = OpenCon();
    $conn->query("SET NAMES 'utf8'");

    $sql = "SELECT `site-title` FROM `config`;";
    $result = $conn->query($sql);

    $conn->close();

    $title = $result->fetch_assoc();

    return $title["site-title"];
}

function ip_get_sitetagline()
{
    $conn = OpenCon();
    $conn->query("SET NAMES 'utf8'");

    $sql = "SELECT `site-tagline` FROM `config`;";
    $result = $conn->query($sql);

    $conn->close();

    $title = $result->fetch_assoc();

    return $title["site-tagline"];
}

function ip_get_sitecopyright()
{
    $conn = OpenCon();
    $conn->query("SET NAMES 'utf8'");

    $sql = "SELECT `site-copyright` FROM `config`;";
    $result = $conn->query($sql);

    $conn->close();

    $title = $result->fetch_assoc();

    return $title["site-copyright"];
}

function ip_get_sitekeywords()
{
    $conn = OpenCon();
    $conn->query("SET NAMES 'utf8'");

    $sql = "SELECT `site-keywords` FROM `config`;";
    $result = $conn->query($sql);

    $conn->close();

    $title = $result->fetch_assoc();

    return $title["site-keywords"];
}

function ip_get_album_thumbnail($albumid)
{
    $conn = OpenCon();
    $conn->query("SET NAMES 'utf8'");

    $sql = "SELECT * FROM `content` where `content-album-id` = $albumid and `content-album-id-title` = 1;";
    
    $result = $conn->query($sql) or die($conn->error);
    $conn->close();

    $image = $result->fetch_assoc();

    if(!isset($image["content-filename"]))
    {
        return "storage/images/error_images/fff.png";    
    }

    return "storage/images/cache/thumb_".$image["content-filename"];
}

function ip_get_album_medium($albumid)
{
    $conn = OpenCon();
    $conn->query("SET NAMES 'utf8'");

    $sql = "SELECT * FROM `content` where `content-album-id` = $albumid and `content-album-id-title` = 1;";
    
    $result = $conn->query($sql) or die($conn->error);
    $conn->close();

    $image = $result->fetch_assoc();

    if(!isset($image["content-filename"]))
    {
        return "storage/images/error_images/fff.png";    
    }

    return "storage/images/cache/mediun_".$image["content-filename"];
}

function ip_get_album_large($albumid)
{
    $conn = OpenCon();
    $conn->query("SET NAMES 'utf8'");

    $sql = "SELECT * FROM `content` where `content-album-id` = $albumid and `content-album-id-title` = 1;";
    
    $result = $conn->query($sql) or die($conn->error);
    $conn->close();

    $image = $result->fetch_assoc();

    if(!isset($image["content-filename"]))
    {
        return "storage/images/error_images/fff.png";    
    }

    return "storage/images/cache/large_".$image["content-filename"];
}

function ip_get_album_original($albumid)
{
    $conn = OpenCon();
    $conn->query("SET NAMES 'utf8'");

    $sql = "SELECT * FROM `content` where `content-album-id` = $albumid and `content-album-id-title` = 1;";
    
    $result = $conn->query($sql) or die($conn->error);
    $conn->close();

    $image = $result->fetch_assoc();

    if(!isset($image["content-filename"]))
    {
        return "storage/images/error_images/fff.png";    
    }

    return "storage/images/original/".$image["content-filename"];
}

function ip_get_album_description()
{
    $albumid = $_GET["id"];

    $conn = OpenCon();
    $conn->query("SET NAMES 'utf8'");

    $sql = "SELECT * FROM `album` where `id` = $albumid;";
    
    $result = $conn->query($sql) or die($conn->error);
    $conn->close();

    $album = $result->fetch_assoc();

    return $album["album_text"];
}

function ip_get_album_title()
{
    $albumid = $_GET["id"];

    $conn = OpenCon();
    $conn->query("SET NAMES 'utf8'");

    $sql = "SELECT * FROM `album` where `id` = $albumid;";
    
    $result = $conn->query($sql) or die($conn->error);
    $conn->close();

    $album = $result->fetch_assoc();

    return $album["album_title"];
}

function ip_get_album_images($layout)
{

    
    $imagelist = get_album_images();

    while($row = $imagelist->fetch_assoc())
            {

        $vars = array(
            '{{image.thumbnail}}' => "storage/images/cache/thumb_".$row["content-filename"],
            '{{image.medium}}' => "storage/images/cache/medium_".$row["content-filename"],
            '{{image.large}}' => "storage/images/cache/large_".$row["content-filename"],
            '{{image.original}}' => "storage/images/original/".$row["content-filename"],
            '{{image-id}}' => "index.php?content=single-image&id=".$row["id"]
          );

        echo strtr($layout, $vars);
    }

}

function ip_get_image($size)
{
    $id = $_GET['id'];

    $conn = OpenCon();
    $conn->query("SET NAMES 'utf8'");

    $sql = "SELECT * FROM `content` where `id` = $id;";
    
    $result = $conn->query($sql) or die($conn->error);
    $conn->close();

    $image = $result->fetch_assoc();

    if(!isset($image["content-filename"]))
    {
        return "storage/images/error_images/fff.png";    
        
    }
    if($size == "thumbnail")
    {
        return "storage/images/cache/thumb_".$image["content-filename"];
    }elseif($size == "medium")
    {
        return "storage/images/cache/medium_".$image["content-filename"];
    }elseif($size == "large")
    {
        return "storage/images/cache/large_".$image["content-filename"];
    }elseif($size == "original")
    {
        return "storage/images/original/".$image["content-filename"];
    }

    return null;
}

function ip_get_image_title()
{
    $id = $_GET['id'];

    $conn = OpenCon();
    $conn->query("SET NAMES 'utf8'");

    $sql = "SELECT * FROM `content` where `id` = $id;";
    
    $result = $conn->query($sql) or die($conn->error);
    $conn->close();

    $image = $result->fetch_assoc();

    return $image["content-name"];
}

function ip_get_image_text()
{
    $id = $_GET['id'];

    $conn = OpenCon();
    $conn->query("SET NAMES 'utf8'");

    $sql = "SELECT * FROM `content` where `id` = $id;";
    
    $result = $conn->query($sql) or die($conn->error);
    $conn->close();

    $image = $result->fetch_assoc();

    return $image["content-text"];
}

?>