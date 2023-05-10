<?php
require_once("header.php");
$con = connectDB();
$valid_post = ($_SERVER["REQUEST_METHOD"] == "POST") ? true : false;
$errors = "";
$user;
$display_errors = array(
    'password' => false,
    'username' => false
);

//need to do custom error checking because this page too is different
$errors = checkLoginForm($con, $SALT, $valid_post, $errors, $display_errors);
if($errors){
    $valid_post = false;
}
//the checkLoginForm function does all the validation we need
//wait nvm, we need to grab the password and etc because it is not returned from checkLoginForm
if($valid_post){
    $password = sha1($SALT . $_POST['password']);
    $username = $_POST['username'];
    $sql    = "SELECT * FROM users WHERE password='" . $password. "' AND username='" . $username . "'";
    $result = mysqli_query($con, $sql);
    if(!$result){
        echo mysqli_error();
        exit();
    }
    $user = mysqli_fetch_assoc($result);
}


if($valid_post){
    //redirect now that they are logged in
    //with message about the fact they are logged in now
    login($user['user_id'], $user['username'], $user['role']);

    $url = isset($_SESSION['protected_page']) ? $_SESSION['protected_page'] : "members.php";
    unset($_SESSION['protected_page']);
    if(!empty($_SESSION["pg_get"]) ){
        $pg_get = $_SESSION["pg_get"];
        unset($_SESSION["pg_get"]);
        redirectUser($url . "?" . $pg_get, "Welcome " . $user['username'] . ", we've missed you.");
    }
    redirectUser($url, "Welcome " . $user['username'] . ", we've missed you.");
}
printHeader("Login", "Login");
?>
        <div class="footer">
            <div class="container bg-dark padding-bottom-10eems">
                <div class="row" >
                    <div id="qresult" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-light2" >
                        <div id="main-row-div-1-multiplication" class="text-center">
                            <div class="text-middle">
<?php
    if(!$valid_post && !$errors){
        print "<h1>Welcome, please log-in</h1>\n";
    }else {
        print "<h1>Incorrect username or password</h1>\n";
    }
    if($errors){
        print "<div class='text-error-border'><br />\n";
        print_r($errors);
        print "\n</div>";
    }
?>
                            </div>
                        </div>
                    </div>
                    <?php printCrumbs(getCrumbs("Login", $crumbs, $crumb_links), tabs(5)); ?>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-dark php-section">
                        <br />
                        <form class="text-center login-form-bg" action="login.php" method="post">
                            <br /><?php
                                $username  = $_POST['username'];
                                $password  = $_POST['password']; #sha1 is one way, so there is no point in showing the old password
                            ?>
                            <span class=<?php ($display_errors['username']) ? print '"error-field"': print '""';?> >Username:</span> <input class=<?php ($display_errors['username']) ? print '"error-field"': print '""';?> type='text' name='username' value=<?php print '"' . $username . '"'; ?> /> <br /> <br />
                            <span class=<?php ($display_errors['password']) ? print '"error-field"': print '""';?> >Password:</span> <input class=<?php ($display_errors['password']) ? print '"error-field"': print '""';?> type='password' name='password' value=<?php print '"' . $password . '"'; ?> /> <br /> <br />
                            <input type="submit" value="Submit" /><br />
                        </form>
                    </div>
                </div>
            </div>
        </div>

<?php
    require_once("foot.php");
?>