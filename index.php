<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>dev</title>
    <ul id="menu-bar">
        <li><a href="index.php">Home</a></li>
        <li><a href="login.php">Login</a></li>
        <li><a href="test">Contact</a></li>
        <li><a href="test">About</a></li>
    </ul>
    <?php include("main.php");
    $blog = new blog_edit("blog");
   $a = $blog->show_all_posts(5);
   $blog->nav_arrows(15);
    ?>
</head>
<body>

</body>
</html>