<?php
    if(!$_GET['id']){
        header("Location: projects.php");
    }
    require "LH_Library.php";
    require "setup.php";
    require "project-functions.php";
    require "db.php";
    //box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    $task              = "    background: rgba(255, 255, 255, 0.2); display: block; border: 1px solid rgba(255,255,255,0.2); margin: 7px; padding: 5px; max-width: 500px;";
    $task_due_date_div = "    padding: 3px; clear: both; min-inline-size: max-content;";
    $title_div         = "    clear: both; min-inline-size: max-content; margin-bottom: 7px;";
    $task_update_link  = "    color: rgba(130,0,0); background: lightgrey; border-radius: 2px; padding: 2px; margin-bottom: 3px; background: rgba(68,68,68,0.1); border: 1px solid rgba(120,0,0,0.1); cursor: pointer; white-space: nowrap; display: block;";
    $task_title        = "    color: rgba(68,0,0); background: lightgrey; border-radius: 2px; padding: 5px; margin-bottom: 5px; background: rgba(68,68,68,0.1); border: 1px solid rgba(120,0,0,0.1); cursor: pointer; white-space: nowrap;";
    $task_description  = "    color: rgb(68,68,68); display: block; padding: 5px; margin-top: 7px; margin-bottom: 3px; border: 1px solid rgba(120,0,0,0.1); clear: both; text-align: left;";
    $task_due_date     = "    color: rgb(68,68,68); ";
    $project_name      = "    color: rgba(130,0,0); background: lightgrey; border-radius: 2px; padding: 2px; margin-bottom: 5px; background: rgba(68,68,68,0.1); border: 1px solid rgba(120,0,0,0.1); cursor: pointer; white-space: nowrap; display: block;";
    $project_name_div  = "    margin-bottom: 6px; ";

    $project_tbl = new Table("project", $conn);
    $piece_tbl   = new Table("piece", $conn);

    $piece_id     = $_GET['id'];
    $project_id   = $piece_tbl->getAttribute('project_id', array("id", $piece_id) );
    $project_name = $project_tbl->getAttribute('name', array("id", $project_id) );
    $piece = $piece_tbl->getAllAssoc( array("id", $piece_id) );
    $html = "";
    echo "<div id='week-view'>\n";
        foreach($piece as $index => $data){
            $want = [
                "<div class='project-name-div' style='$project_name_div' >",
                    "<span class='project-name' style='$project_name'>",
                        $project_name ,
                    "</span>",
                "</div>",
                "<div class='title-div' style='$title_div'>",
                    "<span class='task-title'>",
                        $data["title"],
                    "</span>",
                "</div>",
                "<span class='task-description' style='$task_description'>",
                    $data["description"],
                "</span>",
                "<div class='task-due-date-div' style='$task_due_date_div'>",
                    "<span class='task-due-date' style='$task_due_date'>",
                        $data["due_date"],
                    "</span>",
                "</div>"
            ];
            $html= "<span class='task' style='$task'>" . implode("\n", $want) . "</span>"; //fill up the tasks with the tasks for that day
        }

        echo $html;
    echo "</div>";

?>