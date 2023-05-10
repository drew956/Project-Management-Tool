<?php
    require "LH_Library.php";
    require "setup.php"; //not sure if this is a good idea for session-related stuff, but oh well.
    require "db.php";
s
    $month_offset = $_POST['start_date']; //(0,1,2,-1,-2,etc)

    $format_str = "Y-m-d";
    $begin      = new DateTime("first day of this month");
    $end        = new DateTime("first day of next month");
    if($month_offset != "0"){
        $begin->modify("$month_offset month");
        $end->modify("$month_offset month");
        $end->modify("first day of this month");
    }
    $interval   = DateInterval::createFromDateString('1 day');
    $date_range = new DatePeriod($begin, $interval, $end);
    $days       = array();
    $today      = (new DateTime("now"))->format("Y-m-d");
    $first_day_of_month = $begin->format("Y-m-d");

    $this_week = mysqli_query($conn, "SELECT piece.id, piece.title, piece.description, piece.due_date, piece.project_id, piece.complete, project.name FROM `piece` JOIN `project` ON project.id=piece.project_id WHERE complete=0 AND due_date BETWEEN '$first_day_of_month' AND ADDDATE('$first_day_of_month', INTERVAL 1 MONTH) AND MONTH(due_date) = MONTH('$first_day_of_month') ORDER BY due_date ASC");
    $headers = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
    $datas      = mysqli_fetch_all($this_week, MYSQLI_ASSOC);

    $elements = array();
    foreach ($date_range as $date) {
        if($date->format("Y-m-d") != $today)
            $elements[] =
                "<span class='date-header'>" .
                    $date->format("Y-m-d <br />") .
                "</span>";
        else
            $elements[] =
                "<span class='date-header today-date-header'>" .
                    $date->format("Y-m-d <br />") .
                "</span>";

    }
    for($i = 0; $i < count($datas); $i++){
        $data     = $datas[$i];
        $datetime = new DateTime($data['due_date']);
        $day_of_the_month = $datetime->format("d"); //kore yori suuji no hou ga daiji da na
                                                   //chokusetsu ni tsukaeru kara sa.
        $index = $day_of_the_month - 1;

        $want = [
            "<div class='project-name-div' >",
                "<span class='project-name'>",
                    "<a href='project.php?id={$data['project_id']}'>",
                        $data["name"] ,
                    "</a>" ,
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
        $elements[$index] .= "<span class='task'>" . implode("\n", $want) . "</span>"; //fill up the tasks with the tasks for that day
    }
    // $elements = array_merge($headers, $elements);

    //unshift the stuff you need back
    $day_of_the_week = $begin->format('l');
    $times = array_search($day_of_the_week, $headers); //should return 0 for Sunday, etc
    for($j = 0; $j < $times; $j++){
        array_unshift($elements, "No tasks.");
    };


    htmlTable("", $elements, $headers, "");


?>