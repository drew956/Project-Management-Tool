<?php
    /*
        deleteIdea.php
        Deletes ideas. That's about it.
    */

    require_once "db.php";
    $idea_tbl = new Table("idea", $conn);

    $id = $_POST['id'];

    if( !($result = $idea_tbl->delete(array("id", $id))) ){
        echo mysqli_error($conn); //this will have to be updated on a per-case basis once we
        die;
        //switch to mysqli. Tsk tsk No is good.
    }
    echo "Delete was successful.";
?>