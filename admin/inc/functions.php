<?php

require 'db-connect.php';

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


 // Begin public functions

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

$text = 'testtext mit ein bisschen länge';

    $albumlist = get_albums();

    while($row = $albumlist->fetch_assoc())
            {

        $vars = array(
            '{{date}}'       => date('F j, Y', $row["album_date"]),
            '{{text}}'        => $row["album_text"],
            '{{thumbnail}}' => 'someothertext'
          );

        echo strtr($item, $vars);
    }
}

function ip_get_author()
{
    $author = get_userdata();

    return $author["admin_user"];
}

?>