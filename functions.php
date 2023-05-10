<?php
#functions.php
$crumbs = array(
                "Projects" => "projects.php",
                "Login"   => "login.php",
                "Register" => "register.php",
                "Members" => "members.php",
                    "Member" => "member.php",
                    "Update" => "update.php",
                    "Delete" => "delete.php",
                "Logout"  => "logout.php"
                    #there is a problem here, because these expect get etc
            );


$crumb_links = array(
                     "Login"   => "Projects",
                     "Register" => "Projects",
                     "Members" => "Projects",
                     "Logout"  => "Projects",
                        "Member" => "Members",
                        "Update" => "Members",
                        "Delete" => "Member"
                    );

$logged_in_pages = array(
    "Projects",
    "Members",
    "Logout"
);

$roles = array("user", "admin"); //no need for => true, because we have in_array() Speaking of which, rewrite the other stuff later.
//this works for now, but we need a sql table to store them in, and then we can make a function
//to find out if someone is an admin or not
$admin = array(
    "18" => true
);
$admin_pages = array(
    #$crumbs["Multiplication Project"] => true
);
//should rewrite to use in_array() instead
//oh well
$blocked = array(
    $crumbs["Members"] => true,
    $crumbs["Member"] => true,
    $crumbs["Update"] => true,
    $crumbs["Delete"] => true,
    $crumbs["Create Story"] => true,
    $crumbs["Projects"] => true,
        $crumbs["Chapters"] => true,
        $crumbs["Create Chapter"] => true,
    "updateStory.php" => true,
    "updateReference.php" => true,
    "updateChapter.php" => true,
    "story.php" => true,
    $crumbs["References"] => true
);
function printHeader($titleOfPage, $current_page, $javascript = ""){
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titleOfPage; ?></title>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
    <link href="css/siteStyle.css" rel="stylesheet">

    <script type="text/javascript">
        <?php echo $javascript; ?>
    </script>
</head>
<body>
    <header class="top container-fluid">
        <!-- navbar -->
<?php
    navbar($current_page);
}

function printSite ($url, $sitename){
    echo "<a href='$url'>$sitename</a><br />\n";
}
function nav_a($name, $href, $current_page, $class="", $extra=""){
    if($current_page == $name){
        return "<a class='active $class' href='$href' $extra>$name</a>";
    }else{
        return "<a class='$class' href='$href' $extra>$name</a>";
    }
}
function a($href, $name, $class="", $extra=""){
    return "<a class='$class' href='$href' $extra>$name</a>";
}

function nav_link($name, $href, $current_page, $class="", $extra = ""){
    if($current_page == $name){
        print "<li class='active'><a class='$class' href='$href' $extra>$name</a></li>";
    }else{
        print "<li><a href='$href' class='$class' $extra>$name</a></li>";
    }
}

function navbar($current_page){
    global $crumbs;
    global $crumb_links;
    global $logged_in_pages;
    $current_page_0 = $current_page;

?>
        <nav class="navbar navbar-inverse navbar-fixed-top">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <?php print nav_a("BooklyBook", "index.php", $current_page, "navbar-brand")."\n"; ?>

                </div>
                <div id="navbar" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <?php
                            foreach($crumbs as $page_name => $link){
                                //BooklyBook ones are the ones which are not sub-pages
                                if($crumb_links[$page_name] != "BooklyBook"){
                                    continue;
                                }
                                /*
                                    Skip those pages which should be hidden after logging in or out
                                */
                                if(isLoggedIn()){ //if logged in
                                    if( $page_name == "Register" || $page_name == "Login"){
                                        continue;
                                    }
                                }else{//if not logged in
                                    if( in_array($page_name, $logged_in_pages) ){
                                        continue;
                                    }
                                }
                                nav_link($page_name, $link, $current_page);
                            }
                            if(isLoggedIn()){
                                nav_link("Logged In", "member.php?id=" . $_SESSION["user_id"] . "", "", "text-logged-in");
                            }
                        ?>
                    </ul>
                </div><!--/.nav-collapse -->
            </div>
        </nav>
<?php
}


function getCrumbs($page_name, $crumbs, $crumb_links, $index = ""){
    $crumbsies = array();
    for( $j = $page_name, $div = ""; $j; $j = $crumb_links[$j], $div = "  ->  "){
        array_push($crumbsies, $index . a($crumbs[$j], $j) . $div);  //creates all of the links needed for the breadcrumbs in an associative array
    }
    return $crumbsies;
}


function printCrumbs2($array, $indent = ""){

    echo           '        <div class="crumbs">'. "\n";
    echo $indent . '            <div class="container bg-dark padding-top-2eems">'. "\n";
    echo $indent . '                <div class="row" >       '. "\n";
    echo $indent . '                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-light2">' . "\n";
    echo $indent . '                        <div class="text-center">' . "\n";
    echo $indent . '                            <div id="breadcrumb" class="text-middle">' . "\n";

    for($i = count($array) - 1; $i >= 0; $i--){
        print $indent . tabs(8) . $array[$i] . "\n";//this might work, depending on whether or not an array passed in without reference is treated as a ton of individual elements or not.
    }
    echo $indent . '                            </div>' . "\n";
    echo $indent . '                         </div>' . "\n";
    echo $indent . '                    </div>' . "\n";
    echo $indent . '                </div>' . "\n";
    echo $indent . '            </div>' . "\n";
    echo $indent . '        </div>' . "\n";
}
function printCrumbs($array, $indent = ""){

    echo '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-light2">' . "\n";
    echo $indent . '    <div class="text-center">' . "\n";
    echo $indent . '        <div id="breadcrumb" class="text-middle">' . "\n";

    for($i = count($array) - 1; $i >= 0; $i--){
        print $indent . tabs(3) . $array[$i] . "\n";//this might work, depending on whether or not an array passed in without reference is treated as a ton of individual elements or not.
    }
    echo $indent . '        </div>' . "\n";
    echo $indent . '    </div>' . "\n";
    echo $indent . '</div>' . "\n";

    if($_SESSION["FLASH"]){ ?>
        <!-- Main document -->
        <div class="main">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 col-sm-12 col-xs-12 bg-dark">
                        <div id="flash" class="">
                            <?php
                                echo "<p id='flash-text'>" . $_SESSION["FLASH"] . "</p>\n";
                                unset($_SESSION["FLASH"]);
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<?php
    }
}

function getCrumbs0($page_name, $crumbs, $crumb_links, $index = ""){
    $crumbsies = array();
    for( $j = $page_name; $j; $j = $crumb_links[$j] ){
        array_push($crumbsies, $index . a($crumbs[$j], $j) . "  ->  ");  //creates all of the links needed for the breadcrumbs in an associative array
    }

    print_r($crumbsies);
    return $crumbsies;
}


function printCrumbs0($array){
    for($i = count($array) - 1; $i >= 0; $i--){
        print $array[$i] . "\n";//this might work, depending on whether or not an array passed in without reference is treated as a ton of individual elements or not.
    }
}

function connectDB(){
    $conn = mysqli_connect("localhost", "librarian", "62561origami!", "bookly_book");

    if (!$conn) {
        echo "Database connection failed: " . mysqli_connect_error();
    }
    return $conn;
}

//Global-level variables for determining information about errors and such
/*
    REWRITE To use references instead. Then you don't need to worry about returning anything.
    Also I suspect that $valid_post doesn't actually change when you use this,
    so you probably avoided using it after calling this anyway, so it probably isn't needed.
    Double check though.
*/
function checkUserForm($isUpdatePage = false, $valid_post, $errors, $display_errors){ //for changing user details
    global $roles;
    if(empty($valid_post)){
        #echo "WHAAAT THEEE EMPTY POST!\n";
    }
    if($valid_post){
        if( !preg_match("/^[a-zA-Z]{3,}$/", $_POST['first_name'])){
            $valid_post = false;
            $errors .= "<p class='error-text' >Your first name is too short, or you used incorrect characters. Must be 3 characters long or greater, and only letters</p>\n";
            $display_errors['first_name'] = true;
        }
        if( !preg_match("/^[a-zA-Z]{3,}$/", $_POST['last_name'])){
            $valid_post = false;
            $errors .= "<p class='error-text' >Your last name is too short, or you used incorrect characters. Must be 3 characters long or greater, and only letters</p>\n";
            $display_errors['last_name'] = true;
        }
        //password match error
        if(empty($_POST['password']) && !$isUpdatePage){
            $valid_post = false;
            $errors .= "<p class='error-text' >Please enter a password!!</p>\n";
            $display_errors['password'] = true;
        }
        if(($_POST['password'] != $_POST['password2'])){
            $valid_post = false;
            $errors .= "<p class='error-text' >The passwords you entered do not match!!</p>\n";
            $display_errors['password2'] = true;
        }
        if( (empty($_POST['password']) || empty($_POST['password2'])) && !$isUpdatePage ){
            $valid_post = false;
            $errors .= "<p class='error-text' >You didn't enter, or confirm, your password!</p>\n";
            $display_errors['password2'] = true;
        }
        if( !preg_match("/^[\w]{3,}$/", $_POST['username'])){
            $valid_post = false;
            $errors .= "<p class='error-text' >Your username is too short, or you used incorrect characters. Must be 3 characters long or greater, and only letters, numbers, or underscore</p>\n";
            $display_errors['username'] = true;
        }
        if( !preg_match("/^[\w]+@[\w]+[.][a-zA-Z]{3}$/", $_POST['email'])){
            $valid_post = false;
            $errors .= "<p class='error-text' >Your email is not in an accepted format</p>\n";
            $display_errors['email'] = true;
        }
        if( isAdmin() && !in_array($_POST['role'], $roles) && $isUpdatePage ){
            $valid_post = false;
            $errors .= "<p class='error-text' >Somehow you entered in a user role that doesn't exist." . $_POST['role'] . "</p>\n";
            $display_errors['role'] = true;
        }
    }
    return $errors;

}

function checkStoryForm(&$valid_post, &$errors, &$display_errors){
    if($valid_post){
        if( !preg_match("/^[\w\s\d()]{0,}$/", $_POST['title'])){
            $valid_post = false;
            $errors .= "<p class='error-text' >Your title has unsupported characters in it. We only support letters, spaces, numbers, and parenthesis.</p>\n";
            $display_errors['title'] = true;
        }
        if( !preg_match("/^[a-z\sA-Z.,;!?()!@#$%^&*{}\|]{0,}$/", $_POST['synopsis'])){
            $valid_post = false;
            $errors .= "<p class='error-text' >Your synopsis has unsupported characters in it. We only support letters, spaces, numbers, and any of these: .,!?()!;@#$%^&*{}\| .</p>\n";
            $display_errors['synopsis'] = true;

        }
    }
    #return $errors; #this isn't necessary because it modifies it directly because its passed in by reference
}
/*
    Modify to user references instead
    then modify the actual page that uses it too.
    no need for $errors = blah blah blah
    just checkLoginForm(values, values, values);
*/
function checkLoginForm($mysqli, $SALT, $valid_post, $errors, $display_errors){
    $password = mysqli_real_escape_string($mysqli, sha1($SALT . $_POST['password']));
    $username = $_POST['username'];
    if(is_null($valid_post)){
        echo "WHAAAT THEEE EMPTY POST!\n";
    }
    if($valid_post){
        $sql    = "SELECT * FROM users WHERE password='" . $password. "' AND username='" . $username . "'";
        $result = mysqli_query($mysqli, $sql);
        if(empty($password)){
            $valid_post = false;
            $errors .= "<p class='error-text' >Please enter a password!!</p>\n";
            $display_errors['password'] = true;
        }
        if($result->num_rows != 1){
            $valid_post = false;
            $errors .= "<p class='error-text' >Your username or password was incorrect</p>\n";
            $display_errors['password'] = true;
            $display_errors['username'] = true;
        }
        if(!preg_match("/^[\w]{3,}$/", $username) || empty($username)){
            $valid_post = false;
            $errors .= "<p class='error-text' >Your username is too short, or you used incorrect characters. Must be 3 characters long or greater, and only letters, numbers, or underscore</p>\n";
            $display_errors['username'] = true;
        }
    }
    return $errors;

}
function checkChapterForm(&$valid_post, &$errors, &$display_errors){
    if($valid_post){
        if( !preg_match("/^[\w\s\d()]{0,}$/", $_POST['title'])){
            $valid_post = false;
            $errors .= "<p class='error-text' >Your title has unsupported characters in it. We only support letters, spaces, numbers, and parenthesis.</p>\n";
            $display_errors['title'] = true;
        }
        if( !preg_match('/^[a-zA-Z\s\'.,!?@#$%^&*()\\-+"]{0,}$/', $_POST['content']) ){
            $valid_post = false;
            $errors .= "<p class='error-text' >Your content has unsupported characters in it. We only support letters, spaces, numbers, and parenthesis and .,!?@#$%^&*()[]\-+.</p>\n";
            $display_errors['content'] = true;
        }
    }
}
//doesn't use $valid_post because uses ajax and stuff
function checkReferenceForm(&$valid_post, &$errors, &$display_errors){
    if( !preg_match("/^[a-zA-Z\s0-9!.?]{0,}$/", $_POST['name'])){
//        $valid_post = false;
        $errors .= "<p class='error-text' >Your reference name has unsupported characters in it. We only support letters, spaces, numbers, and !.? </p>\n";
        $display_errors['name'] = true;
    }
    if( !preg_match("/^[a-zA-Z\s0-9!,.?]{0,}$/", $_POST['description']) ){
//        $valid_post = false;
        $errors .= "<p class='error-text' >Your description has unsupported characters in it. We only support letters, spaces, numbers, and !.? </p>\n";
        $display_errors['description'] = true;
    }
}
function getErrorMsgBox($msg){
    return '<div class="container">
    <div class="row">
        <div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 col-sm-12 col-xs-12 bg-dark">
            <div id="flash" class="">
                <div id="flash-text">' . $msg . '</div>
            </div>
        </div>
    </div>';

}

function createUserForm($default, $action){
?>
    <form class="text-center login-form-bg" action="<?php echo $action ?>" method="post">
        <br />
        <span class=<?php ($display_errors['first_name']) ? print '"error-field"': print '""';?> >First Name:</span> <input class=<?php ($display_errors['first_name']) ? print '"error-field"': print '""';?> type='text' name='first_name' value=<?php print '"' . $default['first_name'] . '"'; ?> /> <br /> <br />
        <span class=<?php ($display_errors['last_name']) ? print '"error-field"': print '""';?> >Last Name:</span> <input class=<?php ($display_errors['last_name']) ? print '"error-field"': print '""';?> type='text' name='last_name' value=<?php print '"' . $default['last_name'] . '"'; ?> /> <br /> <br />
        <span class=<?php ($display_errors['username']) ? print '"error-field"': print '""';?> >Username:</span> <input class=<?php ($display_errors['username']) ? print '"error-field"': print '""';?> type='text' name='username' value=<?php print '"' . $default['username'] . '"'; ?> /> <br /> <br />
        <span class=<?php ($display_errors['email']) ? print '"error-field"': print '""';?> >Email:</span> <input class=<?php ($display_errors['email']) ? print '"error-field"': print '""';?> type='text' name='email' value=<?php print '"' . $default['email'] . '"'; ?> /> <br /> <br />
        <span class=<?php ($display_errors['password']) ? print '"error-field"': print '""';?> >Password:</span> <input class=<?php ($display_errors['password']) ? print '"error-field"': print '""';?> type='password' name='password' value=<?php print '"' . $default['password'] . '"'; ?> /> <br /> <br />
        <span class=<?php ($display_errors['password2']) ? print '"error-field"': print '""';?> >Confirm Pass:</span> <input class=<?php ($display_errors['password2']) ? print '"error-field"': print '""';?> type='password' name='password2' value=<?php print '"' . $default['password2'] . '"'; ?> /> <br /> <br />
        <input type="submit" value="Submit" /><br />
    </form>
<?php
}

/*
function createUser($values){
    global $SALT;
    $sql_query = "INSERT INTO users (first_name, last_name, username, email, password, date_created, role) VALUES (" .
        "'" . mysql_real_escape_string($values['first_name']) . "', '" .
              mysql_real_escape_string($values['last_name'])  . "', '" .
              mysql_real_escape_string($values['username'])   . "', '" .
              mysql_real_escape_string($values['email'])      . "', '" .
              (sha1( $SALT . $values['password']))   . "', '" .
              mysql_real_escape_string(date("Y/m/d"))       . "'," .
              "'user'"
    . ")";
    $result = mysql_query($sql_query);
    if(!mysql_ping()){
        print "<br /> MYSQL ERROR" . mysql_error() . "<br />\n";

    }

}
*/

function createUser($mysqli, $values){
    global $SALT;
    $sql_query = "INSERT INTO users (first_name, last_name, username, email, password, date_created, role) VALUES (" .
        "'" . mysqli_real_escape_string($mysqli, $values['first_name']) . "', '" .
              mysqli_real_escape_string($mysqli, $values['last_name'])  . "', '" .
              mysqli_real_escape_string($mysqli, $values['username'])   . "', '" .
              mysqli_real_escape_string($mysqli, $values['email'])      . "', '" .
              (sha1( $SALT . $values['password']))   . "', '" .
              mysqli_real_escape_string($mysqli, date("Y/m/d"))       . "'," .
              "'user'"
    . ")";
    $result = mysqli_query($mysqli, $sql_query);
    if (!$result) {
      echo "Database connection failed (within createUser): " . mysqli_error($mysqli);
      #exit();
    }
}


function getUser($mysqli, $user_id){
    $sql_query = "SELECT * FROM users WHERE user_id='" . $_GET['id'] . "'";
    $results = mysqli_query($mysqli, $sql_query);
    if(!$results || ($results->num_rows != 1) ){
        return false;
    }
    $member = mysqli_fetch_assoc($results);
    return $member;
}



function redirectUser($destination, $flash){
    $_SESSION["FLASH"] = $flash;
    header("location: $destination");
    exit(); #if you don't exit you will unset the flash message again.
}
function unsetUser(){
    unset($_SESSION['user_id']);
    unset($_SESSION['username']);
    unset($_SESSION['role']);
}
function logout(){
    unsetUser();
}
function login($user_id, $username, $role){
    $_SESSION["user_id"] = $user_id;
    $_SESSION["username"] = $username;
    $_SESSION["role"] = $role;
}
function isLoggedIn(){
    if(isset($_SESSION["user_id"]) ){
        return true;
    }
    return false;
}

function isAdmin(){
    return $_SESSION["role"] == "admin";
}

function adminPage(){
    global $admin_pages;
    if($admin_pages[$_SESSION['protected_page']]){
        return true;
    }
    return false;
}
//this will go into every page I think.
//that way when you log-in eventually, it will redirect you to where you originally
//intended to go.
//WELL no, because this would just redirect you to every page you visited when not logged in
//YOU have to separate the latter half of the function and put it into the log_in page
function authenticate(){
    global $admin_pages;
    global $admin;
    global $blocked;
    global $logged_in_pages;
    //what happens if the split doesn't work, will pop still work? hm idk
    //because we use get for profiles, this doesn't work below. member.php?blah blah doesn't match member.php
    $temp = explode("/", $_SERVER['REQUEST_URI']);
    $page_name = $temp[count($temp) - 1];//will just give us the name of the page "members.php" for example

    //name with get
    $pg_name_get = explode("?", $page_name); //this will always at least have a 0 index.
    $page_name = $pg_name_get[0]; //this makes sure that you will still get bounced even if you put get information after the part

    $pg_get = count($pg_name_get) > 1 ? $pg_name_get[1] : ""; //this get's the get info if there is any
    $pg_get = !empty($_SESSION['pg_get']) ? $_SESSION['pg_get'] : $pg_get; //supposedly should make the get part the same
    //and the 0 index will always be the page name
    $_SESSION['pg_get'] = $pg_get;//stores it. Though we will have to do !empty because "" still is set.


    //^this would work but... wouldn't redirect them to the right spot. (?id=blah blah would be deleted)
    $url = "";
    $msg = "";

    if($blocked[$page_name] || $admin_pages[$page_name] || in_array($page_name, $logged_in_pages) ){
        if( !(isLoggedIn()) ){    //this is wrong. count($temp would have to be greater than 1
            $_SESSION['protected_page'] = $page_name;
            $url = "login.php";
            $msg = "Please log in!";
        }else{
            /*
                Check if logged in
                    if not, try and save where they want to go
                    if they are
                        if they were trying to go somewhere before (isset($_SESSION['protected_page']))
                        Check to see if it is an admin page
                            Check to see if they are an admin
                                if they are an admin, redirect them to the page
                                if they are not, bounce them to the members page
                        If it is not an admin page
                            redirect them to the page

            */
            if(isset($_SESSION['protected_page'])){
                if($admin_pages[$_SESSION['protected_page']]){
                    if(isAdmin()){
                        $url = $_SESSION['protected_page'];
                        $msg = "Thanks for logging in!";
                    }else{
                        $url = "members.php";
                        $msg = "You do not have permission to view " . $_SESSION['protected_page'];
                    }
                }else{
                    $url = $_SESSION['protected_page'];
                    $msg = "Thanks for logging in!";
                }
            //else if you weren't trying to get anywhere before, but now are trying to access an admin page
            }else if($admin_pages[$page_name]){
                //if you are not an admin, get bounced
                if(!(isAdmin())){
                    $url = "members.php";
                    $msg = "You do not have permission to access $page_name";
                }
            }
            unset($_SESSION['protected_page']);
        }

    }else{
        //if you accessing a page which is not protects
        //however we should check if they are logged in or not, and there was somewhere you wanted to go, then go there.
        if(isLoggedIn()){
            //if you are logged in
            //and there was somewhere you were trying to go before
            //then go there and unset the fact that you were trying to do so
            if(isset($_SESSION['protected_page'])){
                $url = $_SESSION['protected_page'];
                $msg = "Thanks for logging in!";
                unset($_SESSION['protected_page']);
            }
        }
    }
    if($msg){
        //the reason we don't have to worry about $pg_get is because login.php handles that for us
        redirectUser($url, $msg);
    }
}


//if they aren't an admin or if they are trying to access a page that isn't their own
//redirect them
function notAdminOrOwnPageBounce($id){
    if (!isAdmin() && $_SESSION["user_id"] != $id ){
        redirectUser("members.php", "Invalid Request");
    }
}





?>
