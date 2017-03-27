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
</head>
<body>
    <?php include("main.php");
    echo "<div id='line'> <br>";
    if(isset($_GET['post'])){
        $id = $_GET['post'];
        $blog = new blog_edit('blog');
        $blog->read_all($id);
        $blog->generate_comment_add("form","title","content","submit");
        if($_SERVER['REQUEST_METHOD'] == "POST") {

            if (!empty($_POST['title']) AND !empty($_POST['content']) AND !empty($_POST['captcha'])) {

                $blog->comment_add($_POST['title'], $_POST['content'], $_POST['captcha']);
            }
            else{
                echo "Fill all inputs <br>";

            }
        }

        $blog->load_comments();
    }
    else{
        header('Location:index.php');
    }
    echo "</div> <br>";
    ?>



</body>
</html>