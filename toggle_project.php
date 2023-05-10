<?php
require "LH_Library.php";
require "db.php";

/*
    Need to sanitize the data
*/
$proj_id = $_POST["id"];
$status  = $_POST["status"];

$pairs = Array(
    "status" => $status
);
$wheres = Array(
    "id" => $proj_id
);

$project_tbl = new Table("project", $conn);

$res = $project_tbl->update($pairs, $wheres);

if ($conn->error) {
    try {
        throw new Exception("MySQL error $conn->error <br> Query:<br> $query", $conn->errno);
    } catch(Exception $e ) {
        echo "Error No: ".$e->getCode(). " - ". $e->getMessage() . "<br >";
        echo nl2br($e->getTraceAsString());
    }
}
?>