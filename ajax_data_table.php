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

if($_POST['inactive'] == "true"){
    $inactive = "AND complete = 1";
}else{
    $inactive = "AND complete = 0";
}
$pieces = $piece_tbl->getAllAssoc( array("project_id", $project_id), "$inactive ORDER BY due_date ASC" );

for($i = 0; $i < count($pieces); $i++){
    $pieces[$i]['delete_link'] = "<input id='delete$i' type='submit' value='Delete' onclick='deletePiece(" . $pieces[$i]['id'] . ");' />";    //. ", \"delete$i\" )' />";
    $pieces[$i]['update_link'] = "<a href='updatePiece.php?id=" . $pieces[$i]['id'] . "&project_id=$project_id' >Update</a>" ;
    $pieces[$i]['complete'] = $pieces[$i]['complete'] ? "Done" : '<input type="checkbox" name="' . $pieces[$i]['id'] .  '" >';
}
array_unshift($pieces, array(
    "title"       => "Title",
    "description" => "Description",
    "due_date"    => "Due Date",
    "complete"    => "Status",
    'delete_link' => "Delete",
    'update_link' => "Update"

));

printHtmlTableAssoc4($pieces, array("title", "description", "due_date", "complete", 'delete_link', 'update_link'), "", "data-table");

?>