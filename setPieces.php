<?php
    require "LH_Library.php";
    require "db.php";

    $pieces = $_POST["pieces"];
    $piece_tbl = new Table("piece", $conn);
    $pairs = Array(
        "complete" => 1
    );
    $piece_tbl->setAll($pairs, "id", $pieces);
    if ($conn->error) {
        try {
            throw new Exception("MySQL error $conn->error <br> Query:<br> $query", $conn->errno);
        } catch(Exception $e ) {
            echo "Error No: ".$e->getCode(). " - ". $e->getMessage() . "<br >";
            echo nl2br($e->getTraceAsString());
        }
    }
?>