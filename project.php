<?php
    if(!$_GET['id']){
        header("Location: projects.php");
    }
?><!DOCTYPE html>
<html>
    <head>
        <link rel="shortcut icon" href="favicon.ico">
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
        <meta http-equiv="Content-Language" content="ja" />
        <link rel="stylesheet" type="text/css" href="css/project.css" />
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
                $("#due_date").wrap("<p></p>");
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

                document.getElementById('due_date').value = today;
            }

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
                formData.append('project_id', <?php echo $_GET['id'] ?>);
                fetch("ajax_week_view.php", {
                    method: "POST",
                    redirect: 'follow',
                    mode: 'no-cors', // no-cors, *cors, same-origin
                    headers: {
                      'Content-Type': 'application/json'
                      // 'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    redirect: 'follow', // manual, *follow, error
                    referrerPolicy: 'no-referrer',
                    body: formData
                }).then(
                    (result) => result.text()
                ).then(
                    (text) => {
                        document.getElementById("week-view").innerHTML = text;
                        setEvents();
                    }
                );
            }
            window.onload = function(){
                setDate();
                submit = document.querySelector("input[value='Submit']");
                on(submit, "click", setDate);

                $('#summernote').summernote();
                updateWeekView();
                $("#project-dropdown").change(function(){
                    let key = this.value;
                    let text = $("#project-dropdown option[value=" + (key) + "]").text();
                    $("#change-tasks-project-button")[0].value = "Move All To: " + text;
                });
            };
            function updateTable(){
                let formData = new FormData();
                formData.append('project_id', <?php echo $_GET['id'] ?>);
                formData.append("inactive", <?php echo $_GET['inactive'] ? "'" . $_GET['inactive'] . "'" : "'false'"; ?>);
                fetch("ajax_data_table.php", {
                    method: "POST",
                    redirect: 'follow',
                    mode: 'no-cors', // no-cors, *cors, same-origin
                    headers: {
                      'Content-Type': 'application/json'
                      // 'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    redirect: 'follow', // manual, *follow, error
                    referrerPolicy: 'no-referrer',
                    body: formData
                }).then(
                    (result) => result.text()
                ).then(
                    (text) => {
                        document.getElementById("data-table-div").innerHTML = text; //works, but kills the styling dog
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
                        updateWeekView();
                        updateTable();
                    });

            }

            function deletePieceAndUpdateTable(id, deleteID){
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
                            updateWeekView();
                        }
                    }

                };

                ajaxRequest.open("POST", "deletePiece.php");
                ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                ajaxRequest.send("id=" + id);
                return false;

            }

            function toggleWeekView(){
                let el   = document.getElementById("week-view");
                let butt = document.getElementById("week-button");
                let style = getComputedStyle(el);
                if(style['display'] == "none"){
                    el.style["display"] = "block";
                    butt.innerHTML = "Hide week view";
                    document.getElementById("data-table-div").style["display"] = "none";
                    document.getElementById("convenience-buttons").style["display"] = "none";
                }else {
                    el.style["display"] = "none";
                    butt.innerHTML = "Show week view";
                    document.getElementById("data-table-div").style["display"] = "block";
                    document.getElementById("convenience-buttons").style["display"] = "block";
                }

                return false;
            }
        </script>
        <link rel="stylesheet" type="text/css" href="css/css-fixes.css">

    </head>
    <body>
        <div class="colorized" style="text-align: center; margin: auto">
<?php
/*
    NEXT:
        Flesh out the projects.php page and link the individual projects to project.php
            Display all projects nicely
            Form for creating new projects
            maybe way of editing existing projects


        Create the ideas.php page
            requires get project_id
            display all the ideas for that project
            have drop-down to sort and only display the ideas of a certain 'piece' (ajax)
            ability to add new ideas
            link to idea.php
        Create the idea.php
        create the ajax forms for adding data to the tables (if not already done)

        Add authentication and some basic permissions/user stuff
            (doing this would make it easier to delete stuff when you want, and not accidentially
            when you don't want. Login as Admin to delte or modify, whereas be yourself to be safer)

        Make the below htmlTableAssoc2 function able to insert a row with the row number
        on the left of each row, as an option.. perhaps?
            Consider making a htmlRow object, capable of all of these diverse behaviours
            and the ability to toggle options like this

        DONE
        Create a function or a method which can generate a table with only specific keys
        htmlTableAssoc($elements, $keys, $class, $blahh );
            ^if we include with the generic one, then we have a situation where
             when we wish to include a class or indent
             we have to do some weird thing like
             htmlTableAssoc($elements, getKeys($elements), "myClass", "   indent    ");

    12-17-2016
    This is the actual project displaying php page
    the one that shows all the pieces for the project, etc

    This page can actually also show sub-projects (really just pieces with subpieces)

    DONE :)
    Should we create a sort of caching scheme for objects
    perhaps using sessions or someting similar?
    Or perhaps using Objects ability to serialize etc?

    Or every time you access an object you can store the attributes of a specific row? idk
        $project_tbl->getAttribute( array("id", $project_id), "name" );//this type of thing?
        or something like

            $project_tbl->getAll( array("id", $project_id) );//the last accessed specific row
                    //is saved, and the Table object automatically stores the attributes for you
            $project_tbl->getAttribute("name");
                If this is how we wish for it to function, then we should make the getAttribute
                function switch it's arg order, to make the array of info optional
                    i.e. getAttribute( "name", array("id", $project_id) )
                    getAttribute ($attr, $conditions = "")
                Also if you do call it with an array of info then it caches it so you can
                do normal calls after
                    i.e.
                    $project_tbl->getAttribute( "name", array("id", $project_id));
                        #the other attributes have been cached now
                    $project_tbl->getAttribute( "due_date" );
                    etc

*/

    require "LH_Library.php";
    require "setup.php";
    require "project-functions.php";
    require "db.php";

    $project_id = $_GET['id'];

    $project_tbl = new Table("project", $conn);


    echo "<h1 class='text-center'>" . $project_tbl->getAttribute('name', array("id", $project_id) ) . "</h1>\n";
?>
            <a href="projects.php"><span id="projects_link">Return to Projects</span></a>
            <br />
            <label>Show completed tasks<input id='inactive' name='inactive' type='checkbox' onclick='reloadActive();'
            <?php
                echo $_GET['inactive'] == "true" ? " checked='checked' " : "";
            ?>
            /> </label>
            <br />
            <button id="week-button" onclick="toggleWeekView();">Show week view</button>

<?php


    echo "<div id='week-view'>\n";
        //empty by default, ajax load it
    echo "</div>";

        //the attributes should be cached now
        //echo $project_tbl->getAttribute("date_started") . "\n";
        //$projects_tbl = null;
        #$projects = null;//cleaning up (or trying to anyway)

    $piece_tbl = new Table("piece", $conn);


    /*
        Probably need to do some sanitation on this to prevent SQL injection
    */
    if($_GET['inactive'] == "true"){
        $inactive = "AND complete = 1";
    }else{
        $inactive = "AND complete = 0";
    }
    $pieces = $piece_tbl->getAllAssoc( array("project_id", $project_id), "$inactive ORDER BY due_date ASC" );
        /*
        $piece_tbl->getRowsAssoc(
            $piece_tbl->getAll( array("project_id", $project_id) )
        ); */
    //print_r($pieces);
    //mysqlResourceTable($pieces);

    /*
        Make the date thing a function, and call it when you clear the fields

        Add in links to delete the pieces you don't want (should be able to create some sort
        of object which automates all of these processes, even making the php files)


        <input id="delete0" value="Delete" onclick="deletePiece(50, " delete0'="" )'="" type="submit">
    */
    for($i = 0; $i < count($pieces); $i++){
        $pieces[$i]['delete_link'] = "<input id='delete$i' type='submit' value='Delete' onclick='deletePieceAndUpdateTable(" .
        $pieces[$i]['id'] . ", \"delete$i\" )' />";
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
    //echo implode(htmlTableAssoc($pieces), "");
    //printHtmlTableAssoc4($elements, $keys, $empty_text ="", $id="", $class="", $indent="")
    echo "<div id='data-table-div'>";
        printHtmlTableAssoc4($pieces, array("title", "description", "due_date", "complete", 'delete_link', 'update_link'), "", "data-table");
    echo "</div>";

    echo "<div id='convenience-buttons'>\n";
        echo '<input id="update-tasks-button" type="Submit" value="Set Tasks Done" onclick="updateTasks();"/><br />';
        printProjectDropDown($project_tbl, "project-dropdown", $project_id);
        echo "<br />\n";
        echo '<input id="change-tasks-project-button" type="Submit" value="Move All To: '. $project_tbl->getAttribute('name', array("id", $project_id) ) . '" onclick="changeTaskProjects();"/><br />';
        echo '<input id="change-tasks-date-left-button" type="Submit" value="Sub 1 day from all" onclick="addToAll(-1);"/><br />';
        echo '<input id="change-tasks-date-right-button" type="Submit" value="Add +1 day to all" onclick="addToAll(1);"/><br />';
    echo "</div>\n";
    echo '<div id="new-entry-div" >';
    //echo "<input type='hidden' value='" . $project_id . "' name='project_id' />\n";
    $piece_tbl->printAjaxInput( array(       "title"  =>  "Title:",
                                 "description" =>  array("textarea", "Description:"),
                                 "due_date"    =>  "Due date:(yyyy-mm-dd)",
                                 "project_id"  =>  array("hidden", $project_id)
                                 ),
                                  "ajax_piece.php", 'addToProjectTable', "POST");
    echo "</div>";
?>
        </div>
        <div id="toggle-project-div" class="colorized">
            <label id="toggle-project-label">
                Project Status: <?php echo $project_tbl->getAttribute('status', array("id", $project_id) ) == 1 ? "Active" : "Inactive" ?>:
                <input onclick="toggleProject(<?php echo $project_id ?>, true);" type="Submit" value="Activate" />
                <input onclick="toggleProject(<?php echo $project_id ?>, false);" type="Submit" value="Deactivate" />
            </label>
        </div>
    <?php
    /*
        $piece_tbl->printAjaxScript("project_id=" . $project_id);
    */
    ?>
    <footer>
        <div id="server-info" class="colorized" style="text-align: center;">
            <?php echo date_default_timezone_get(); ?>
            <br />
            <?php echo date('l jS \of F Y h:i:s A'); ?>
        </div>
    </footer>
    <script src='site_functions.js' type='text/javascript'></script>
    <script>
        $("#due_date").datepick({dateFormat: 'yyyy-mm-dd'});

        function toggleProject(proj_id, active) {
            alert(active);
            $.ajax({
              type: "POST",
              url: "toggle_project.php",
              data: { id : proj_id, status: (active ? "1" : "0") }
          }).done(function(data){
              if(data)
                  alert(data);
              window.location.href = "project.php?id=" + proj_id;
          });
        }
        function updateTask(id){

            $.ajax({
                type: "POST",
                url: "setPieces.php",
                data: {
                    "pieces" : [id]
                }
            }).done(function(data){
                updateWeekView();
                updateTable();
            });
        }
        function updateTasks(){
            let pieces = [];
            $("#data-table > tbody > tr > td > input[type='checkbox']").each(function(index){
                //console.log(this.name);
                if(this.checked)
                    pieces.push(this.name);
            });

            $.ajax({
                type: "POST",
                url: "setPieces.php",
                data: {
                    "pieces" : pieces
                }
            }).done(function(data){
                if(data)
                    alert(data);
                window.location.href= "project.php?id=" + <?php echo $project_id ?>;
            });
        }
        function changeTaskProjects(){
            let pieces = [];
            $("#data-table > tbody > tr > td > input[type='checkbox']").each(function(index){
                //console.log(this.name);
                if(this.checked)
                    pieces.push(this.name);
            });
            let proj_id = document.getElementById("project-dropdown").value;
            $.ajax({
                type: "POST",
                url: "ajax_change_piece_project.php",
                data: {
                    "pieces" : pieces,
                    "project_id" : proj_id
                }
            }).done(function(data){
                if(data)
                    alert(data);
                updateWeekView();
                updateTable();
                // window.location.href= "project.php?id=" + <?php echo $project_id ?>;
            });
        }
        function addToAll(inc){
            let pieces = [];
            $("#data-table > tbody > tr > td > input[type='checkbox']").each(function(index){
                //console.log(this.name);
                if(this.checked)
                    pieces.push(this.name);
            });

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
                updateTable();
                // window.location.href= "project.php?id=" + <?php echo $project_id ?>;
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
                updateTable();
                // window.location.href= "project.php?id=" + <?php echo $project_id ?>;
            });
        }
        function addToProjectTable(data){
            //addToTable(data);
            updateWeekView();
            updateTable();
            wipeInputs(["title", "description"]);
            $('#summernote').summernote('code', '');
        }
    </script>
    </body>
</html>