<?php
    if(!$_GET['id']){
        header("Location: projects.php");
    }
?><!DOCTYPE html>
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
        <meta http-equiv="Content-Language" content="ja" />
        <link rel="shortcut icon" href="favicon.ico">
        <style>
            table, th, td {
               border: 1px solid black;
            }
            td, th {
                padding: 10px !important;
            }
            table{
                margin: auto;
            }
            .text-center {
                text-align: center;
            }
            div.colorized {
                background-color: teal;
                background-color: rosybrown; /* Might only work on firefox*/
                background-color: tan;
                padding-left: 3em;
                padding-right: 3em;
                padding-bottom: 3em;
            }
            .datepick{
                padding-left: 0;
                padding-right: 0;
            }
            .datepick div{
                padding-left: 0;
                padding-right: 0;
            }
            td {
                color: purple;
            }
            h1 {
                font-family: monospace;
                text-decoration: underline;
                margin-bottom: 0px;
            }
            body, html {
                background-color: tan;
            }
            input[type="text"] {
                background: grey;
                /*color: gold;*/
                color: lightblue;
            }
            #projects_link {
                font-size: 20px;
            }
            #summernote {
                text-align: left;
            }
            #toggle-project-div {
                padding: 10px;
                text-align: center;
            }
            #toggle-project-label {
                padding: 6px;
                background-color: white;
            }
            #new-entry-div{
                border: solid 1px black;
                padding: 5px;
                background: rgba(120,40,200, 0.5);
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
            //this cool nifty little guy was written by:Sean Kinsey of StackOverflow
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
                $("#date").wrap("<p></p>"); //is this needed? what does this do again?

            }());

            /*
                need to account for the fact that
                we could have the variables mixed in either direction.

                also it would be cooler and easier to use ajax
                and replace the entire table with a new one
                generated via an ajax query
            */
            function reloadActive(){
                inactive = document.getElementById('inactive');

                url = new String(window.location);
                current_project = /id=([^&]*)/.exec(url);
                state           = /inactive=([^&]{0,})/.exec(url);
                if(current_project && current_project.length == 2){
                    current_project = current_project[1];
                    if(state && state.length == 2){
                        if(state[1] == "true"){ //this will always be true == true
                                                     //because you NEED to click it to trigger the onlick event
                            window.location = "project.php?id=" + current_project +
                             "&inactive=" + "false";
                            return false;
                        }
                    }

                    window.location = "project.php?id=" + current_project +
                     "&inactive=" + "true";

                }
                return false;
            }

            function setDate(){
                today = new Date();
                today = today.toLocaleDateString({
                        formatMatcher : "year, month, day",
                        year: "numeric",
                        month: "numeric",
                        day: "2-digit"
                    }).split("/").join("-");
                today = today.substr(today.length - 4, today.length) + "-" + today.substr(0, today.length - 5);

                document.getElementById('date').value = today;
            }
            window.onload = function(){
                setDate();
                submit = document.querySelector("input[value='Submit']");
                on(submit, "click", setDate);

                $('#summernote').summernote();
                //the problem is this:
                /*
                    click
                        ->ajax starts
                        ->setDate() resets date to current date
                        ->ajax finishes
                        ->onreadystatechange nukes all fields, erasing current data
                */
                //submit.onclick = setDate();
            };

            function deleteIdea(id, deleteID){
                ajaxRequest = new XMLHttpRequest();
                if(!ajaxRequest)
                    return "ERROR creation of ajax component failed :(";

                ajaxRequest.onreadystatechange = function(){

                    if(ajaxRequest.readyState == XMLHttpRequest.DONE){
                        if(ajaxRequest.status == 200){
                            //delete row from table and alert
                            alert(ajaxRequest.responseText);
                            input = document.getElementById(deleteID);
                            tr = input.parentElement.parentElement;
                            table = document.getElementById('data-table');
                            table.deleteRow(tr.rowIndex);
                        }
                    }

                };

                ajaxRequest.open("POST", "deleteIdea.php");
                ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                ajaxRequest.send("id=" + id);
                return false;

            }
        </script>
        <link rel="stylesheet" type="text/css" href="css/css-fixes.css">

    </head>
    <body>
        <div class="colorized" style="text-align: center; margin: auto">
<?php
/*
    To-Do:
    We want it to list all of the ideas
    for a given project



*/

    require "LH_Library.php";
    require "db.php";

    $project_id   = $_GET['id'];
    $project_tbl  = new Table("project", $conn);
    $project_name = $project_tbl->getAttribute('name', array("id", $project_id) );
    echo "<h1 class='text-center'>" . $project_name . "</h1>\n";
?>
            <a href="project.php?id=<?php echo $project_id; ?>"><span id="projects_link">Return to Project <?php echo $project_name; ?></span></a>
            <br />
<?php

    /* Idea portion of our program */
    $idea_tbl = new Table("idea", $conn);
    $piece_id = $_GET['piece'];
    /*
        Probably need to do some sanitation on this to prevent SQL injection
    */
    $ideas = ($piece_id != "" && isset($piece_id) ) ?
        $idea_tbl->getAllAssoc( array("project_id", $project_id, "piece_id", $piece_id), " ORDER BY date ASC" )
        :
        $idea_tbl->getAllAssoc( array("project_id", $project_id), " ORDER BY date ASC" );


    //echo "<h3>$project_name</h3>\n";

    for($i = 0; $i < count($ideas); $i++){
        $ideas[$i]['delete_link'] = "<input id='delete$i' type='submit' value='Delete' onclick='deleteIdea(" .
        $ideas[$i]['id'] . ", \"delete$i\" )' />";
        $ideas[$i]['project_name'] = $project_name;
        $ideas[$i]['update_link'] = "<a href='updateIdea.php?id=" . $ideas[$i]['id'] . "' >Update</a>" ;
    }
    //id project_id piece_id title date info

    array_unshift($ideas, array(
        "id"          => "Idea ID",
        "project_name"=> "Project Name",
        "title"       => "Title",
        "date"        => "Date Created",
        "info"        => "Idea",
        "delete_link" => "Delete",
        "update_link" => "Update"

    ));

    printHtmlTableAssoc4($ideas, array("title", "info", "date", "delete_link", "update_link"),  "", "data-table");
    echo "<input type='hidden' value='" . $project_id . "' name='project_id' />\n";


    /* What happens if the piece id is null or not set??? hmm idk
    I must have put this here for a reason...
    but the refactoring I did forces it into the printAjaxInput function...
    if($piece_id != "" && isset($piece_id)){
        echo "<input type='hidden' value='" . $piece_id . "' name='piece_id' />\n";
    }
    */
    echo "<div id='new-entry-div'>\n";
    $idea_tbl->printAjaxInput( array(   "title"  =>  "Title:",
                                        "info"   =>  array("textarea", "Description:"),
                                        "date"   =>  "Date:(yyyy-mm-dd)",
                                        "project_id"  =>  array("hidden", $project_id)
                                 ),
                                  "ajax_idea.php", 'addToIdeaTable', "POST");
    echo "</div>\n";
?>
        </div>
    <?php
        //$idea_tbl->printAjaxScript("project_id=" . $project_id);
    ?>
    <script src='site_functions.js' type='text/javascript'></script>
    <script type="text/javascript">
        function addToIdeaTable(data){
            addToTable(data);
            wipeInputs(["title", "info"]); //prevents project_id being overriden
            $('#summernote').summernote('code', '');
        }
    </script>
    <script>
        $("#date").datepick({dateFormat: 'yyyy-mm-dd'});
    </script>
    </body>
</html>