<?php
    require "LH_Library.php";
    require "setup.php";
    require "db.php";

    $increment = $_POST["increment"];
    $pieces    = $_POST["pieces"    ];

    $piece_tbl  = new Table("piece", $conn);

    if(!$pieces || !$increment){
        echo "ERROR!";
        die();
    }

    $res = $piece_tbl->addDaysToDate("id", $pieces, "due_date", $increment);

    if ($conn->error) {
        try {
            throw new Exception("MySQL error $conn->error <br> Query:<br> $query", $conn->errno);
        } catch(Exception $e ) {
            echo "Error No: ".$e->getCode(). " - ". $e->getMessage() . "<br >";
            echo nl2br($e->getTraceAsString());
        }
    }else{
        echo "Date shifted by $increment days.";
    }
?>
