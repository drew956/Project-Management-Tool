<!DOCTYPE html>
<html>
    <head>
        <link rel="shortcut icon" href="favicon.ico">
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
        <meta http-equiv="Content-Language" content="ja" />
        <link rel="stylesheet" type="text/css" href="css/project.css" />
        <style>
            #week-view {
                display: block;
            }

            .title-div {

            }

            .project-name {
                color: rgba(130,0,0);
                background: lightgrey;
                border-radius: 2px;
                padding: 2px;
                margin-bottom: 5px;
                background: rgba(68,68,68,0.1);
                border: 1px solid rgba(120,0,0,0.1);
                cursor: pointer;
                white-space: nowrap;
                display: block;
            }
            .project-name-div {
                margin-bottom: 6px;
                /* min-inline-size: max-content; */
            }
            #project_id {
                margin-bottom: 5px;
            }
        </style>
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

        <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

        <link rel="stylesheet" type="text/css" href="css/jquery.datepick.css">
        <!--
        I think this JQuery is older so I am going to try the 3.5.1 one, (though might break something)
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        -->
        <script type="text/javascript" src="js/jquery.plugin.js"></script>
        <script type="text/javascript" src="js/jquery.datepick.js"></script>
        <!-- include summernote css/js -->
        <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
        <script type="text/javascript">
            var on = (function(){
                if (window.addEventListener) {
                    return function(target, type, listener){
                        target.addEventListener(type, listener, false);
                    };
                }
                else {
                    return function(object, sEvent, fpNotify){
                        object.attachEvent("on" + sEvent, fpNotify);
                    };
                }
                // $("#due_date").wrap("<p></p>");
            }());
            function setEvents(){
                $(".task-title").click(function(){
                  display = $(this).parent().next(".task-description").css("display");
                  if(display == "none")
                    $(this).parent().next(".task-description").css("display", "block");
                  else
                    $(this).parent().next(".task-description").css("display", "none");
                });
            }

            function updateWeekView(){
                let formData = new FormData();
                fetch("ajax_all_week_view.php", {
                    method: "POST",
                    redirect: 'follow',
                    mode: 'no-cors', // no-cors, *cors, same-origin
                    headers: {
                      'Content-Type': 'application/json'
                      // 'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    redirect: 'follow', // manual, *follow, error
                    referrerPolicy: 'no-referrer'
                }).then(
                    (result) => result.text()
                ).then(
                    (text) => {
                        document.getElementById("week-view").innerHTML = text;
                        setEvents();
                    }
                );
            }

            function deletePiece(id){
                let yes = confirm("Delete task?");
                if(yes)
                    $.ajax({
                        type: "POST",
                        url: "deletePiece.php",
                        data: {
                            "id" : id
                        }
                    }).done(function(data){
                        if(data)
                            alert(data);
                        // updateWeekView();
                        // updateTable();
                    });

            }
            function setDate(){
                let today = new Date();
                today = today.toLocaleDateString({
                        formatMatcher : "year, month, day",
                        year: "numeric",
                        month: "numeric",
                        day: "2-digit"
                    }).split("/").join("-");
                today = today.substr(today.length - 4, today.length) + "-" + today.substr(0, today.length - 5);

                document.getElementById('start_date').value = today;
            }
            window.onload = function(){
                setDate();
                submit = document.querySelector("input[value='Submit']");
                on(submit, "click", setDate);

                $('#summernote').summernote();
                //updateWeekView();
            };
        </script>
        <link rel="stylesheet" type="text/css" href="css/css-fixes.css">

    </head>
    <body>
        <div class="colorized" style="text-align: center; margin: auto">
<?php
    require "LH_Library.php";
    require "setup.php"; //not sure if this is a good idea for session-related stuff, but oh well.
    require "project-functions.php";
    require "db.php";

    $piece_tbl   = new Table(  "piece", $conn);
    $project_tbl = new Table("project", $conn);

    $cols  = array("id", "name");
    $conds = array("status", "1");
    $projects = $project_tbl->getColumnsAssoc($cols, $conds);

    $data = [];
    foreach ($projects as $index => $project) {
       $data[$project['id']] = $project['name'];
    }

    echo '<div id="new-entry-div" >';
    //echo "<input type='hidden' value='" . $project_id . "' name='project_id' />\n";
        // printProjectDropDown($project_tbl, "project_id");
        echo "<br />\n";
        $piece_tbl->printAjaxInput( array(       "title"       =>  "Title:",
                                                 "description" =>  array("textarea", "Description:"),
                                                 "start_date"  =>  "Start date:(yyyy-mm-dd)",
                                                 "end_date"    =>  "Start date:(yyyy-mm-dd)",
                                                 "interval"    =>  "P1D, P2D, P7D, etc: ",
                                                 "project_id"  => array("select", $data)
                                     ),
                                      "ajax_recurring.php", 'console.log', "POST");
    echo "</div>";
?>
    <footer>
        <div id="server-info" class="colorized" style="text-align: center;">
            <?php echo date_default_timezone_get(); ?>
        </div>
    </footer>
    <script src='site_functions.js' type='text/javascript'></script>
    <script>

        $("#start_date").datepick({dateFormat: 'yyyy-mm-dd'});
        $("#end_date").datepick({dateFormat: 'yyyy-mm-dd'});
        $("#start_date").attr("autocomplete", "off");
        $("#end_date").attr("autocomplete", "off");

        setEvents();
        function updateTask(id){

            $.ajax({
                type: "POST",
                url: "setPieces.php",
                data: {
                    "pieces" : [id]
                }
            }).done(function(data){
                updateWeekView();
            });
        }

        function addTo(id, inc){
            let pieces = [id];

            $.ajax({
                type: "POST",
                url: "ajax_change_dates.php",
                data: {
                    "pieces" : pieces,
                    "increment" : inc
                }
            }).done(function(data){
                if(data)
                    alert(data);
                updateWeekView();
            });
        }

        function addToProjectTable(data){
            updateWeekView();
            wipeInputs(["title", "description"]);
            $('#summernote').summernote('code', '');
        }

    </script>
    </body>
</html>