<!DOCTYPE html>
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
        <meta http-equiv="Content-Language" content="ja" />
        <link rel="shortcut icon" href="favicon.ico">
        <style>

            table, th, td {
               border: 1px solid black;
            }
            td {
                padding: 10px;
            }
            table{
                margin: auto;
            }
            .text-center {
                text-align: center;
            }
            div {
                background-color: teal;
                background-color: rosybrown; /* Might only work on firefox*/
                background-color: tan;
                padding-left: 3em;
                padding-right: 3em;
            }
            td {
                color: purple;
            }
            h1 {
                font-family: monospace;
                text-decoration: underline;
                margin-bottom: 0px;
            }
            body {
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
        </style>
        <script type='text/javascript'>
            window.onload = function(){

                tables = document.getElementsByTagName("table");
                table = tables[0];
                table.insertRow(0);
                titles = ["PROJECT NAME", "TITLE", "DESCRIPTION", "DUE_DATE", "ID_PARENT", "DELETE", "UPDATE"]
                titles.forEach(function(element){
                    cell = table.rows[0].insertCell();
                    text = document.createTextNode(element);
                    cell.appendChild(text);
                });
            };
            function deletePiece(id, deleteID){
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
                            tables = document.getElementsByTagName('table');
                            table = tables[0];
                            table.deleteRow(tr.rowIndex);
                        }
                    }

                };

                ajaxRequest.open("POST", "deletePiece.php");
                ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                ajaxRequest.send("id=" + id);
                return false;

            }
        </script>
    </head>
    <body>
        <div style="text-align: center; margin: auto">
<?php
    /*
        Features:
        Designed to do any sort of sorting and listing

        Has a table with pagination with the results
        has get variable with how many results to display per page


        To-Do:
        Join the project and piece table, to show the project names
        group by project.id etc
    */

    require_once('db.php');
    $ACTIVE_TODAY = "1";
    $ACTIVE_OLD   = "2";
    //require_once "includes.php";//really should phase this out in favor of "includes.php"

    $project_tbl = new Table("piece", $conn);

    $request_type = $_GET['x'];
    $tz           = $_GET['timezone'];
?>
    <h1><?php
        switch($request_type){
            case $ACTIVE_TODAY:
                echo "Today's Active Tasks";
                break;
            case $ACTIVE_OLD:
                echo "Old Tasks which are Active";
                break;
            default:
                echo "Default Sort Tasks page";
                break;
        }
    ?></h1>
            <span id='return-link'><a href='projects.php'>Return to Projects</a></span>

<?php
    //date_default_timezone_set($tz);
    date_default_timezone_set('America/Phoenix');//fixes it so the date(blah blah) logic works.
    switch($request_type){
        case $ACTIVE_TODAY:
            $sql = "SELECT a.name, b.id, b.title, b.description, b.due_date, b.id_parent FROM piece b JOIN project a ON a.id = b.project_id WHERE due_date = '" . date("Y-m-d") . "' AND complete=0";
            $results = mysqli_query($conn, $sql);
            if(!$results){
                echo mysqli_error($conn);
                die;
            }
            $results = Table::getRowsAssoc($results);

            //$results = $project_tbl->getAllAssoc(array(), "WHERE due_date = '" . date("Y-m-d") . "' AND complete=0");
            for($i = 0; $i < count($results); $i++){
                $results[$i]['delete_link'] = "<input id='delete$i' type='submit' value='Delete' onclick='deletePiece(" .
                $results[$i]['id'] . ", \"delete$i\" )' />";
                $results[$i]['update_link'] = "<a href='updatePiece.php?id=" . $results[$i]['id'] . "&project_id=$project_id' >Update</a>" ;
            }
            if(count($results) == 0)
                echo "<h3>No tasks for today!</h3>\n";
            else
                printHtmlTableAssoc2($results, array("name", "title", "description", "due_date", "id_parent", "delete_link", "update_link"));

            break;

        case $ACTIVE_OLD:
            $sql = "SELECT a.name, b.id, b.title, b.description, b.due_date, b.id_parent FROM piece b JOIN project a ON a.id = b.project_id WHERE due_date < '" . date("Y-m-d") . "' AND complete=0";
            $results = mysqli_query($conn, $sql);
            if(!$results){
                echo mysqli_error($conn);
                die;
            }
            $results = Table::getRowsAssoc($results);

            for($i = 0; $i < count($results); $i++){
                $results[$i]['delete_link'] = "<input id='delete$i' type='submit' value='Delete' onclick='deletePiece(" .
                $results[$i]['id'] . ", \"delete$i\" )' />";
                $results[$i]['update_link'] = "<a href='updatePiece.php?id=" . $results[$i]['id'] . "' >Update</a>" ;
            }
            printHtmlTableAssoc2($results, array("name", "title", "description", "due_date", "id_parent", "delete_link", "update_link") );
            //echo date("Y-m-d") . "\n";
            break;


        default:
            echo "Nothing at all dearie.. hee hurf horf\n";
            break;
    }

?>
        </div>
    <?php
        //$piece_tbl->printAjaxScript("project_id=" . $project_id);
    ?>
    <script src='site_functions.js' type='text/javascript'></script>
    </body>
</html>