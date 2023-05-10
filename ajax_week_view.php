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
$start_date = $_POST['start_date'];
$project_tbl = new Table("project", $conn);

$this_week = ($start_date) ? mysqli_query($conn, "SELECT * FROM `piece` WHERE project_id=$project_id AND complete=0 AND due_date BETWEEN $start_date AND ADDDATE($start_date, INTERVAL 7 DAY) ORDER BY due_date ASC")
: mysqli_query($conn, "SELECT * FROM `piece` WHERE project_id=$project_id AND complete=0 AND due_date BETWEEN CURDATE() AND ADDDATE(CURDATE(), INTERVAL 7 DAY) ORDER BY due_date ASC");
// if($this_week)
//     print_r(Table::getRowsAssoc($this_week));
$start_date = ($start_date) ? $start_date : "now";

$start    = new DateTime("$start_date");
$end      = new DateTime("$start_date +6 days");//excludes end date I believe.
$interval = new DateInterval('P1D');
$period   = new DatePeriod($start, $interval, $end);
$days    = array();
$headers = array();
foreach($period as $date){
    $days[$date->format("Y-m-d")]    = "";
    $headers[$date->format("Y-m-d")] = $date->format("D") . "<br />" . $date->format("(m-d)");
}

foreach($this_week as $index => $data){
    $want = [
        "<div class='title-div'>",
            "<span class='x-button' onclick='deletePiece(" . $data['id'] . ");'>❎</span>",
            "<span class='task-title'>".
                $data["title"] .
            "</span>",
            "<span class='check-button' onclick='updateTask(" . $data['id'] . ");'>✅</span>",
        "</div>",
        "<span class='task-description'>".
            $data["description"] .
        "</span>",
        "<div class='task-update-div'>",
            "<span class='task-update-link'>".
                "<a href='updatePiece.php?id={$data['id']}&project_id={$project_id}' >Update</a>" .
            "</span>",
        "</div>",
        "<div class='task-due-date-div'>",
            "<span class='left-arrow-button' onclick='addTo(" . $data['id'] . ", -1);'>⬅️</span>",
            "<span class='task-due-date'>".
                $data["due_date"] . " <a href='print.php?id={$data['id']}' >🖨</a>" .
            "</span>",
            "<span class='right-arrow-button' onclick='addTo(" . $data['id'] . ", 1);'>➡️</span>",
        "</div>"
    ];
    $days[$data['due_date']] .= "<span class='task'>" . implode("\n", $want) . "</span>"; //fill up the tasks with the tasks for that day
}

$elements = array($headers, $days);

//printHtmlTableAssoc3($elements, $keys, $class="", $indent="")
printHtmlTableAssoc3($elements, array_keys($headers), $empty_text="No tasks", $class="", $indent="");
?>