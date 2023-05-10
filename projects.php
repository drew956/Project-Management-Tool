<!DOCTYPE html>
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
        <meta http-equiv="Content-Language" content="ja" />
        <link rel="shortcut icon" href="favicon.ico">
        <style>

            table#data-table , #data-table th, #data-table td {
               border: 1px solid black;
            }
            #data-table td, #data-table th {
                padding: 10px !important;
            }
            table#data-table {
                margin: auto;
            }
            div {
                background-color: teal;
                background-color: rosybrown;
                /* background-color: tan; */
                padding-top: 0.2em;
                padding-bottom: 0.5em;
            }
            body {
                background-color: tan;
            }
            input[type="text"] {
                background: grey;
                /*color: gold;*/
                color: lightblue;
            }
            h1 {
                margin-bottom: 0.15em;
                margin-top: 0;
            }
        </style>
        <link rel="stylesheet" type="text/css" href="css/jquery.datepick.css">
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script type="text/javascript" src="js/jquery.plugin.js"></script>
        <script type="text/javascript" src="js/jquery.datepick.js"></script>

        <script type="text/javascript">
            window.onload = function(){
                today = new Date();
                today = today.toLocaleDateString({
                        formatMatcher : "year, month, day",
                        year: "numeric",
                        month: "numeric",
                        day: "2-digit"
                    }).split("/").join("-");
                today = today.substr(today.length - 4, today.length) + "-" + today.substr(0, today.length - 5);

                document.getElementById('date_started').value = today;
                //$("#date_started").wrap("<p></p>");
            };
        </script>
    </head>
    <body>
        <div style="text-align: center; margin: auto">
<?php

    require "LH_Library.php";# or die("What it died!?"); //gives us the functions/objects we need
    require "db.php";
    //requrie "projects_functions.php"; // eventually will go in their own file

    $view = $_GET['name'] ? $_GET['name'] : "project";
    $projects = new Table($view, $conn);


    //function mysqlResourceTable($resource, $class="", $indent="") { #prototype v0.1
    //prints out the table
    //should be a generic function included inside of the Table class now that I think about it
    //along with generic functions for both handling and editing things
    //perhaps all methods of the object
    //so these 'default info pages' I want to create could consist simply of a Tabe object
    $cond = $_GET["show_all"] == "true" ? array() : array("status", "1");
    $data = $projects->getAllAssoc($cond, "ORDER BY date_started ASC");
    //print_r(array_keys($data[0]));
    echo "<h1 class='text-center'>" . "Projects" . "</h1>\n";
    echo "<span style='font-size: 18px; display: block;' class='text-center'>" . "<a href='weekView.php'>This Week's Tasks</a>" . "</span>\n";
    $checked = !(count($cond));
    htmlCheckbox("show_all", "Show All Projects?", $checked);
    //should come up with a way to cache this.
    //or make a table which stores this information idk
    foreach($data as $index => $row){
        $data[$index]['name']   = "<a href='project.php?id=" . $data[$index]['id'] . "'>" . $data[$index]['name'] . "</a>\n";
        $data[$index]['idea']   = "<a href='idea.php?id="    . $data[$index]['id'] . "'>Ideas</a>\n";
        $data[$index]['status'] = $data[$index]['status'] == 1 ? "Active" : "Retired";
    }
    array_unshift($data, array(         "name" => "Project Name",
                                "date_started" => "Date Started",
                                        "idea" => "Ideas",
                                        "status" => "Status"
                         )
    );
    printHtmlTableAssoc4($data, array("name", "date_started", "idea", "status"),  "", "data-table");

 /*   mysqlResourceTable(
            $projects->getAll()
    );
 */

    /*
        Generic tools to show what is in a database, and to interact with it.

    */
    $projects->printAjaxInput( array(       "name"     =>  "Name:",
                                       "date_started"  =>  "Date Started:"
                               ),
                               "ajax_project.php", "addToTable");



?>
            <br />
            <a href="sortTasks.php?x=1&timezone='MST'" alt='All the tasks for today' >Today's Tasks</a>
            <a href="sortTasks.php?x=2&timezone='MST'" alt='All of the incompleted past tasks' >Old, still active, tasks.</a>
            <br />
        </div>


    <?php
        //$projects->printAjaxScript();
    ?>
    <script src='site_functions.js' type='text/javascript'></script>
    <script>
        $("#date_started").datepick({dateFormat: 'yyyy-mm-dd'});
        $("#show_all").click(function(){
            let val = $("#show_all")[0].checked;
            window.location.href = "projects.php?show_all=" + val;
        });
    </script>
    </body>
</html>