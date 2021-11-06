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
    <p class="blog-post-meta"><?php echo $row["publish_date"]; ?> by <a href="#">Mark</a></p>
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

function pcs_get_main_menu()
{
    ?>
    <li><a href="#">Timeline</a></li>
    <li><a href="index.php?content=albums" <?php if (isset($_GET['content'])){if($_GET['content'] == 'albums'){ echo 'class="active"'; }}?>>Albums</a></li>
        <li><a href="index.php?content=essays" <?php if (isset($_GET['content'])){if($_GET['content'] == 'essay' || $_GET['content'] == 'essays'){ echo 'class="active"'; }}?>>Essays</a></li>
        <li><a href="#">Page</a></li>
        <?php
}

function pcs_albums_item($item)
{

$text = 'testtext mit ein bisschen länge';

    for($i = 0; $i < 4; $i++)
    {

        $vars = array(
            '{{date}}'       => '1. August',
            '{{text}}'        => $text,
            '{{thumbnail}}' => 'someothertext'
          );

        echo strtr($item, $vars);
    }
}

function pcs_start_loop()
{

}

function pcs_end_loop()
{

}

?>