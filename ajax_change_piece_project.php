<?php
    require "LH_Library.php";
    require "setup.php";
    require "db.php";

    $project_id = $_POST["project_id"];
    $pieces     = $_POST["pieces"    ];

    $piece_tbl  = new Table("piece", $conn);

    if(!$pieces || !$project_id){
        echo "ERROR!";
        die();
    }
    $pairs = Array(
        "project_id" => $project_id
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
