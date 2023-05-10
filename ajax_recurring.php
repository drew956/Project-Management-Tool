<?php
require "LH_Library.php";
require "setup.php"; //not sure if this is a good idea for session-related stuff, but oh well.
require "db.php";

$project_id = $_POST['project_id'];
if(!$project_id){
    print_r($_POST);
    print_r($_GET);
    echo "Something went wrong";
    die();
}

$piece_tbl = new Table("piece", $conn);
$start_date = $_POST["start_date"];
$end_date   = $_POST["end_date"];
$interval   = $_POST["interval"];
$start    = new DateTime($start_date);
$end      = new DateTime($end_date . " +1 day");
$interval = new DateInterval($interval);
$period   = new DatePeriod($start, $interval, $end);
$days    = array();
$headers = array();

foreach($period as $date){
    $_POST['due_date'] =     $date->format("Y-m-d");
    $piece_tbl->insert_prepared(Array("title" , "description" , "due_date" , "project_id"), $_POST);
    if ($conn->error) {
        try {
            throw new Exception("MySQL error $conn->error <br> Query:<br> $query", $conn->errno);
        } catch(Exception $e ) {
            echo "Error No: ".$e->getCode(). " - ". $e->getMessage() . "<br >";
            echo nl2br($e->getTraceAsString());
        }
        die();
    }
}
echo "Inserted Tasks.";
?>