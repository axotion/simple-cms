<?php
include("main.php");
if (!isset($_SESSION['logged'])) {
   header("Location:index.php");
   exit(0);
}
//admin
$obj = new db_connect('blog');
$user = $_SESSION['logged'];
$u_status = $_SESSION['status'];
$pane = new panel();

    if ($u_status == 0) {
        echo 'Admin <br>';
        echo '<a href=logout.php> Logout</a>';
        $pane->generate_add_post_view("form","title","content","file","submit");
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $pane->add_post($_POST['title'], $_POST['content'], $_FILES['file']['tmp_name'],$_FILES['file']['name'],$_FILES['file']['error']);
        }
   }
   // mod
    if ($u_status == 1) {
//mod funcs
       echo 'Mod';
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>dev</title>


</head>
<body>

</body>
</html>