<?php
    require "LH_Library.php";
    require "setup.php"; //not sure if this is a good idea for session-related stuff, but oh well.
    require "db.php";

    $start_date = $_POST['start_date'];

    $this_week = ($start_date) ?
      mysqli_query($conn, "SELECT piece.id, piece.title, piece.description, piece.due_date, piece.project_id, piece.complete, project.name FROM `piece` JOIN `project` ON project.id=piece.project_id WHERE complete=0 AND due_date BETWEEN '$start_date' AND ADDDATE('$start_date', INTERVAL 7 DAY) ORDER BY due_date ASC")
    : mysqli_query($conn, "SELECT piece.id, piece.title, piece.description, piece.due_date, piece.project_id, piece.complete, project.name FROM `piece` JOIN `project` ON project.id=piece.project_id WHERE complete=0 AND due_date BETWEEN CURDATE() AND ADDDATE(CURDATE(), INTERVAL 7 DAY) ORDER BY due_date ASC");

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
            "<div class='project-name-div' >",
                "<span class='project-name'>".
                    "<a href='project.php?id={$data['project_id']}'>" .
                        $data["name"] .
                    "</a>" .
                "</span>",
            "</div>",
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
                    "<a href='updatePiece.php?id={$data['id']}&project_id={$data['project_id']}' >Update</a>" .
                "</span>",
            "</div>",
            "<div class='task-due-date-div'>",
                "<span class='left-arrow-button' onclick='addTo(" . $data['id'] . ", -1);'>⬅️</span>",
                "<span class='task-due-date'>".
                    $data["due_date"] .
                "</span>",
                "<span class='right-arrow-button' onclick='addTo(" . $data['id'] . ", 1);'>➡️</span>",
            "</div>"
        ];
        $days[$data['due_date']] .= "<span class='task'>" . implode("\n", $want) . "</span>"; //fill up the tasks with the tasks for that day
    }

    $elements = array($headers, $days);

    //printHtmlTableAssoc3($elements, $keys, $class="", $indent="")
    printHtmlTableAssoc3($elements, array_keys($headers), $empty_text="No tasks", $class="", $indent="");


    // "<div class='project-name-div' >",
    //     "<span class='project-name'>",
    //         "<span class='x-button' onclick='deletePiece(" . $data['id'] . ");'>❎</span>",
    //         "<a href='project.php?id={$data['project_id']}'>",
    //             $data["name"],
    //         "</a>",
    //         "<span class='check-button' onclick='updateTask(" . $data['id'] . ");'>✅</span>",
    //     "</span>",
    // "</div>",
    // "<div class='title-div'>",
    //     "<span class='task-title'>".
    //         $data["title"] .
    //     "</span>",
    // "</div>",
    // "<span class='task-description'>".
    //     $data["description"] .
    // "</span>",

?>