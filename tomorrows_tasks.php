<?php
    ini_set("include_path", '/home/flzogdom8ejc/php:' . ini_get("include_path") );
    require_once 'Mail.php';
    require_once "LH_Library.php";
    require "db.php";


    // echo "made it past require lh_library";
    $days = 4;//the number of days into the future we want our assignments to be within
    //default range is from today to 3 days into the future

    function emailHtml($from, $subject, $message, $to) {
      $host = "localhost";
      $username = "";
      $password = "";

      $headers = array ('MIME-Version' => "1.0", 'Content-type' => "text/html; charset=iso-8859-1;", "From" => $from, "To" => $to, "Subject" => $subject);

      $smtp = Mail::factory('smtp', array ('host' => $host, 'auth' => false));

      $mail = $smtp->send($to, $headers, $message);
      if (PEAR::isError($mail))
        return 0;
      else
        return 1;
    }

    date_default_timezone_set("MST");

    $task              = "    background: rgba(255, 255, 255, 0.2); display: block; border: 1px solid rgba(255,255,255,0.2); margin: 7px; padding: 5px; box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);";
    $task_due_date_div = "    clear: both; min-inline-size: max-content; margin-bottom: 5px;";
    $title_div         = "    clear: both; min-inline-size: max-content; margin-bottom: 8px;";
    $task_update_link  = "    color: rgba(130,0,0); background: lightgrey; border-radius: 2px; padding: 2px; margin-bottom: 3px; background: rgba(68,68,68,0.1); border: 1px solid rgba(120,0,0,0.1); cursor: pointer; white-space: nowrap; display: block;";
    $task_title        = "    color: rgba(68,0,0); background: lightgrey; border-radius: 2px; padding: 5px; margin-bottom: 5px; background: rgba(68,68,68,0.1); border: 1px solid rgba(120,0,0,0.1); cursor: pointer; white-space: nowrap;";
    $task_description  = "    color: rgb(68,68,68); display: block; padding: 5px; margin-top: 7px; margin-bottom: 3px; background: rgba(68,0,0,0.1); border: 1px solid rgba(120,0,0,0.1); clear: both; text-align: left;";
    $task_due_date     = "    color: rgb(68,68,68); background: rgba(68,68,68,0.1); border-radius: 2px; border: 1px solid rgba(120,0,0,0.1); padding: 2px;";
    $project_name      = "    color: rgba(130,0,0); background: lightgrey; border-radius: 2px; padding: 2px; margin-bottom: 5px; background: rgba(68,68,68,0.1); border: 1px solid rgba(120,0,0,0.1); cursor: pointer; white-space: nowrap; display: block;";
    $project_name_div  = "    margin-bottom: 8px; ";

    $this_week = mysqli_query($conn, "SELECT piece.id, piece.title, piece.description, piece.due_date, piece.project_id, piece.complete, project.name FROM `piece` JOIN `project` ON project.id=piece.project_id WHERE complete=0 AND due_date BETWEEN CURDATE() AND ADDDATE(CURDATE(), INTERVAL $days DAY) ORDER BY due_date ASC");

    $today = Date("Y-m-d");
    $start = new DateTime( $today );
    $end   = new DateTime( $today . " +$days days" );
    $end->setTime(0,0,1);
    $interval = new DateInterval('P1D');
    $period   = new DatePeriod($start, $interval, $end);
    $days    = array();
    $headers = array();

    foreach($period as $date){
        $days[$date->format("Y-m-d")]    = "";
        $headers[$date->format("Y-m-d")] = $date->format("D") . " " . $date->format("(m-d)");
    }
    foreach($this_week as $index => $data){

        $want = [
            "<div class='task-due-date-div' style='$task_due_date_div'>",
                "<span class='task-due-date' style='$task_due_date'>",
                    $headers[$data["due_date"]],
                "</span>",
            "</div>",
            "<div class='project-name-div' style='$project_name_div' >",
                "<span class='project-name' style='$project_name'>",
                    $data["name"] ,
                "</span>",
            "</div>",
            "<div class='title-div' style='$title_div'>",
                "<span class='task-title' style='$task_title'>",
                    $data["title"],
                "</span>",
            "</div>",
            "<span class='task-description' style='$task_description'>",
                $data["description"],
            "</span>"
        ];
        $days[$data['due_date']] .= "<span class='task' style='$task'>" . implode("\n", $want) . "</span>"; //fill up the tasks with the tasks for that day
    }
    $body = "Tasks from: " . $start->format("Y-m-d") . " - " . $end->format("Y-m-d") . " <br />\n";

    foreach ($days as $date => $tasks) {
        $body .= $tasks . "\n";
    }

    // echo $body;
    if(emailHtml("Admin <admin@invertedcode.com>", "Upcoming Tasks", $body, "logandrew95@yahoo.com"))
        echo "Email successfully sent\n";
    else
        echo "Email did not send\n";

?>
