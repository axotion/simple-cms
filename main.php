<?php

/**
 * Copyright (c) <year> <copyright holders>

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
 */


session_start();

interface panel_system{
    public function add_post($title,$content,$tmp_file,$name_file,$file_error);
    public function edit_post($id);
    public function delete_post($id);
    public function add_mod($id); //moderator
    public function del_mod($id); //moderator
    public function change_password($id,$password); //admin
    public function show_stats(); //like views and comments
}
class db_connect{
    public $db = null;

    /**
     * db_connect constructor.
     * @param $db
     */
    function __construct($db)
    {
        $this->db=$db;
        try {
            $this->db = new PDO('mysql:host=192.168.1.100;dbname='.$this->db.';', 'root', 'pkpMrsXulljosMU3');
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "There was a problem with DB";
            echo $e->getMessage();
        }
    }
}
class blog_edit
{
    private $if_content=false;
    private $res = null;
    private $ppage = 0;
    private $db;

    /**
     * blog_edit constructor.
     * @param $db
     */
    function __construct($db)
    {
        $_GET['page'] = $_GET['page'] ?? 1;
        $this->db = (new db_connect('blog'))->db;
    }

    /**
     * @param $ppage
     * @return int
     */
    function show_all_posts($ppage): int
    {
        $this->ppage = $ppage;
        $n_of_pages=0;
        $act_page = (int)$_GET['page'] ?? 1;
        if($act_page <= 0){
            header("Location:404.php");
            exit(0);
        }
        $sql = "SELECT * FROM posts ORDER BY id DESC LIMIT " . ((($act_page-1) * (int)$this->ppage) . ", " . (int)$this->ppage);
        $get_posts = $this->db->prepare($sql);
        $get_posts->execute();
        echo "<div id='line'> <br>";
        if($_GET['page'] >= 1){
        foreach ($get_posts as $this->res) {

            echo strip_tags("<p id='title'>".
                $this->res['title'] .
                "<p id='content'>" .
                substr($this->res['content'],0, 49).
                "...". "<br>".
                "<a href='post.php?post=".$this->res['id']."' id='post-more' target='_blank'>Read more</a>"."<br>" .
                $this->res['date'] .
                "<p id='date'>", '<p><br><a><img><b>');
            $n_of_pages++;
        }
        }
        echo "</div>";
        if(($n_of_pages % $this->ppage) == 0){
            $this->if_content=true;
        }
        else{
            $this->if_content=false;
        }
        return $n_of_pages/$this->ppage;
    }
    //paginator
    /**
     * @param $max_n
     * @return int
     */
    function nav_arrows($max_n){
        //print $max_n (max numbers) on page (1 2 3...last)
        $t1=0;
        try {
            $c_page = ($this->db->query('SELECT * FROM posts')->rowCount() ) / $this->ppage;
            $tmp = $c_page;
            echo "<p id='nupage'>";
            while($tmp > 0) {
               $t1++;
               echo "<a href='index.php?page=" . $t1 . "' id='npage'>" . $t1 . " </a>";
               $tmp--;
           }

           if($t1 > $max_n) {
               echo "... <a href='index.php?page=" . ($t1+1) . "' id='npage'>" . ($t1+1) . " </a>";
           }
           echo "</p>";
        }
        catch (Exception $e){
            echo $e->getMessage();
        }
        //It shows button next and prev.

        //shows next only if content (text) return something and page id is different from $t1 (last page)
        if($this->if_content == true AND $_GET['page'] < $t1 AND $t1 = 1 AND $this->db->query('SELECT * FROM posts')->rowCount() >= 6){
            echo "<a href='"."index.php?page=".(((int)$_GET['page']+1))."' id='page-next' >Next</a> ";
        }

        //shows prev button only if content (text) return something and page id is higher or equal than 2
        if(!empty($this->res['content']) AND (int)$_GET['page'] >= 2){
            echo "<a href='"."index.php?page=".(((int)$_GET['page']-1))."' id='page-prev' >Previous</a> ";
        }
        if(empty($this->res['date'] AND $this->res != null)){
            header("location:404.php");
        }
        return $t1; //all posts ID's in DB
    }
    /**
     * @param $id
     * @return int
     */
    function read_all($id) : int
    {
        $sql = "SELECT * FROM posts WHERE id=:id";
        $get_post= $this->db->prepare($sql);
        $get_post->bindParam(':id',$id,PDO::PARAM_INT);
        $get_post->execute();
        if( $get_post->rowCount() == 0){
            header("location:404.php");
            exit(0);
        }
        foreach ($get_post as $this->res ){
            echo strip_tags("<p id='title'>".
                $this->res['title'] .
                "<p id='content'>" .
                $this->res['content']."<br>".
                $this->res['date'] .
                "<p id='date'>", '<p><br><<a><img><b>');
        }
        return $get_post->rowCount() ?? 0;
    }

    /**
     * @param $user_id
     * @param $password_id
     * @param $captcha_id
     * @return string
     */
    function login_panel($user_id, $password_id,$captcha_id){

        if(isset($_SESSION['logged'])){
            echo "Already logged in";
        }
        else{
            if($_SERVER['REQUEST_METHOD'] == "POST"){
                if($_SESSION['code'] == (int)$captcha_id){
                    if(!empty($user_id) AND !empty($password_id)) {
                    $user = $user_id;
                    $password = $password_id;
                    $password = (string)md5($password);
                    $sql = "SELECT * FROM users WHERE login =:user";
                    $this->res = $this->db->prepare($sql);
                    $this->res->bindParam(':user', $user, PDO::PARAM_STR, 30);
                    $this->res->execute();

                    if (!is_bool($this->res) AND $this->res->rowCount() == 1) {
                        foreach ($this->res as $rep) {
                            if ($user . $password == $rep['login'] . $rep['password']) {
                                $_SESSION['logged'] = $user;
                                $_SESSION['status'] = rep['status'];
                                header("Location:panel.php");
                            } else {
                                echo "<script>alert('Bad login or password')</script>";

                            }
                        }
                    }
                     else {
                        echo "<script>alert('Bad login or password')</script>";

                    }
                }
                }
                else{
                    echo "<script>alert('Bad captcha')</script>";

                }
            }
        }
        return $_SESSION['logged'] ?? "bad try";
    }
    function logout_panel(){
        $_SESSION = array();
        session_destroy();
        header("Location:index.php");
    }

    /**
     * @return int
     */
    function load_comments(){
        $obj = new db_connect('blog');
        $con = $obj->db->prepare("SELECT * FROM comments WHERE id=:id ORDER BY date DESC");
        $con->bindParam('id', $_GET['post'], PDO::PARAM_INT);
        $con->execute();

        if($con->rowCount() > 0) {


            foreach ($con as $test) {
                echo "<b>" . strip_tags($test['author']) . "</b><br><br>" . strip_tags($test['content']) ."<br>".$test['date']."<br><br><br>";
            }
            echo "</div>";
        }
        return $con->rowCount(); //amount of comments
    }

    /**
     * @param $name_form
     * @param $name_author
     * @param $name_content
     * @param $submit_name
     */
    function generate_comment_add($name_form, $name_author, $name_content, $submit_name){
        echo "
         </div><br><div id='comments'>
        <form name='".$name_form."' method='POST'><br>
        <input type='text' name='".$name_author."' placeholder='author' required ><br>
        <textarea rows='10' cols='100' name='".$name_content."'  pattern='.{51,}' placeholder='text'></textarea><br>
        <img src='captcha.php' /><br>
            <input type=\"text\" name='captcha' placeholder=\"captcha code\" id=\"captcha_text\" required> <br>

      <input type='submit' name='".$submit_name."' >
      </form>
        <br><br><br>
 ";
    }

    /**
     * @param $name_author
     * @param $name_content
     * @param $captcha
     */
    function comment_add($name_author, $name_content,$captcha){

        if($_SESSION['code'] == (int)$captcha){
            if(!empty($name_content) AND !empty($name_author)) {
                $a_date=  date("d.m.Y");
                $obj = new db_connect('blog');
                $sql = "INSERT INTO comments(id,author,content,date) VALUES (:id,:author,:content, '$a_date')";
                $con = $obj->db->prepare($sql);
                $con->bindParam('author', $name_author, PDO::PARAM_STR);
                $con->bindParam('content', $name_content, PDO::PARAM_STR);
                $con->bindParam('id', $_GET['post'], PDO::PARAM_INT);
                try{
                    $this->res =$con->execute();
                }
                catch (PDOException $e){
                  echo $e->getMessage();
                }
                if (!is_bool($this->res)) {
                    echo 'Successful';
                }
            }
            }
            else{
                echo "<script>alert('Bad captcha')</script>";
            }
    }
}
class panel implements panel_system {
private $if_added=null;
private $s_image = null;
private $content = null;
private $title = null;
private $c_date;
private $img_dir = "files/images";
private $img_seed=null;
    /**
     * @param $title
     * @param $content
     * @param $tmp_file
     * @param $name_file
     * @param $file_error
     * @return bool|null|PDOStatement
     */
    public function add_post($title, $content, $tmp_file, $name_file, $file_error)
    {
        $this->title = $title;
        $this->content = $content;
        $this->c_date = date("d.m.Y");
        $db = new db_connect('blog');

        if(!empty($tmp_file AND isset($tmp_file)) ){
            switch ($file_error){
                case 1: {echo 'File is too large'; break;}
                case 2: {echo 'File is too large'; break;}
                case 3: {echo 'Something went wrong :( <br> Try again.'; break;} //The uploaded file was only partially uploaded
                case 4: {echo 'Something went wrong :( <br> Try again.'; break;} //No file was uploaded
                case 7: {echo 'I/O error :( <br> Try again.'; break;} //Can't write to disk
                default: {echo 'Something went wrong :( <br> Try again.';
                    break;}
            }
            if(getimagesize($tmp_file) != 0){
                $this->s_image = $tmp_file;
                $this->img_seed=rand(1,99999999);
                if(move_uploaded_file($tmp_file, $this->img_dir."/".$this->img_seed.".".pathinfo($name_file, PATHINFO_EXTENSION))) {
                    $this->content = $content . "<br><br><img src='" . $this->img_dir . "/" . $this->img_seed . ".".pathinfo($name_file, PATHINFO_EXTENSION)."' id='post-image'/>";
                }
                else{
                    echo "Something went wrong :(";
                }
                }
        }
        if(is_string($title) AND is_string($content)) {
            $c_count = strlen($this->content);
            if ($c_count < 51) {
                echo "<div id='err-word'><script>alert('You need to write more words')</script></div>";
            } else {
                $this->content = str_replace("'", "", $this->content);
                $sql = "INSERT INTO posts (title,content,date) VALUES (:title,:content,:data)";
                $this->if_added = $db->db->prepare($sql);
                $this->if_added->bindParam(':title', $this->title, PDO::PARAM_STR);
                $this->if_added->bindParam(':content', $this->content, PDO::PARAM_STR);
                $this->if_added->bindParam(':data', $this->c_date, PDO::PARAM_STR);
               try {
                   $this->if_added->execute();
               }
               catch (PDOException $e){
                   echo "<script>alert('Something went wrong :(')</script> ";
               }
                if(!is_bool($this->if_added)) {
                    echo "<script>alert('Successful')</script> ";
                }

            }
        }
    return $this->if_added;
    }

    /**
     * @param $name_form
     * @param $name_title
     * @param $name_content
     * @param $name_file
     * @param $submit_name
     */
    public function generate_add_post_view($name_form, $name_title, $name_content, $name_file, $submit_name){
        echo "
        <form name='".$name_form."' method='POST' enctype='multipart/form-data'><br>
        <input type='text' name='".$name_title."' placeholder='title'><br>
        Text (Minimum characters = 51) <br>
        <textarea rows='10' cols='100' name='".$name_content."' pattern='.{51,}' placeholder='text'></textarea><br>
        <input type='file' name=' ".$name_file."'><br>
        <input type='submit' name='".$submit_name."'>
        ";
    }

    public function edit_post($id)
    {
        // TODO: Implement edit_post() method.
    }

    public function delete_post($id)
    {
        // TODO: Implement delete_post() method.
    }

    public function add_mod($id)
    {
        // TODO: Implement add_mod() method.
    }

    public function del_mod($id)
    {
        // TODO: Implement del_mod() method.
    }

    public function change_password($id, $password)
    {
        // TODO: Implement change_password() method.
    }

    public function show_stats()
    {
        // TODO: Implement show_stats() method.
    }
}
