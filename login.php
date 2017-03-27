<!DOCTYPE html>
<html lang="en" xmlns:display="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>dev</title>
<?php
include("main.php");
$a = new blog_edit('blog');
if(isset($_SESSION['logged'])){
           header("Location:panel.php");
    }
if($_SERVER['REQUEST_METHOD'] == "POST" AND isset($_POST['login'])  AND isset($_POST['password']) AND isset($_POST['captcha'])) {
    $a->login_panel($_POST['login'], $_POST['password'], $_POST['captcha']);
}
    ?>
</head>
<body>

<form method="post" name="log-id" action="<?php $_SERVER['PHP_SELF']?>"  id = 'login_form' style="margin-top: 20%">
    <img src="login.png" height=5% width=5% style="display: block; margin: auto";  /><br>
    <input type="text" name="login" placeholder="user" id = 'login'required><br>
    <input type="password" name="password" id = 'password' placeholder="password" required><br>
    <img src="captcha.php" id="captcha"/>   <br>
    <input type="text" name="captcha" placeholder="captcha code" id="captcha_text" required> <br>
    <input type="submit" value="login" name="login_button"  id = 'l_button'>


</form>

</body>
</html>
