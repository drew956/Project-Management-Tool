<?php
    /*
        deletePiece.php
        Deletes pieces. That's about it.
    */

    require_once "db.php";
    $piece_tbl = new Table("piece", $conn);

    $id = $_POST['id'];

    if( !($result = $piece_tbl->delete(array("id", $id))) ){
        echo mysqli_error($conn); //this will have to be updated on a per-case basis once we
        die;
        //switch to mysqli. Tsk tsk No is good.
    }
    echo "Delete was successful.";
?>