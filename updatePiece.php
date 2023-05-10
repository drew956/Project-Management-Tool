<?php
    require_once "db.php";//at the very top so the entire script can use it
    $piece_tbl   = new Table("piece", $conn);
    $project_tbl = new Table("project", $conn);
    /*
        Handle the Ajax
        and redirect? because if it reloads the same page, that would be weird.
        Cuz it will echo out the data before it loads itself.. right?


    */
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $id = $_POST['id'];
        $proj_id = $_POST['project_id'];

        unset($_POST['id']);
        unset($_POST['project_id']);
        unset($_POST['submit']);
        //print_r($_POST);
        $result = $piece_tbl->update($_POST, array("id" => $id) );
        if(!$result){
            echo mysqli_error($conn);
            die;
        }
        echo "Update Successful $result";
//        header("Location: project.php?id=" . $proj_id);
        die;
    }

    $project_name = $project_tbl->getAttribute("name", array("id", $_GET['project_id']));

?>
<!DOCTYPE html>
<html>
<head>
    <title>Update Piece</title>
    <style>
        .text-center {
            text-align: center;
        }
        #return-link {
            font-size: 20;
            display: block;
            padding-bottom: 0.3em;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/jquery.datepick.css">
    <script type="text/javascript" src="js/jquery.plugin.js"></script>
    <script type="text/javascript" src="js/jquery.datepick.js"></script>

    <!-- include summernote css/js -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

    <script type="text/javascript">
        window.onload = function(){
            $('#summernote').summernote();
        };
    </script>
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

    <link rel="stylesheet" type="text/css" href="css/css-fixes.css">

</head>
    <body>
        <div class='text-center'>
            <span id='return-link'><a href='project.php?id=<?php echo $_GET['project_id'] ?>'>Return to <?php echo $project_name; ?></a></span>
            <span id='return-link'><a href='weekView.php'>Return to This Week's Tasks</a></span>
            <!--
            <span id='return-link'><a href='sortTasks.php?x=1&timezone=MST'>Return to Today's Tasks</a></span>
            <span id='return-link'><a href='sortTasks.php?x=2&timezone=MST'>Return to Old Tasks</a></span>
            -->

<?php
    /*
        updatePiece.php

        Probably should abstract this so we don't have to rewrite it for each table
        Make an ajax version eventually, and code the javascript to turn the table rows into inputs
    */


    //$title = $piece_tbl->getAttribute("title", array("id", $_POST[$id]));//should now be cached
    //$desc  = $piece_tbl->getAttribute("title");
    $data = $piece_tbl->getAllAssoc(array("id", $_GET['id']));//I think this will work
    $data = $data[0];


        echo '<div id="new-entry-div" >';
                //echo "<input type='hidden' value='" . $project_id . "' name='project_id' />\n";
                htmlField("text"       , "title"      , $data['title'      ], "Title:"                                  );

                //want name description but id summernote
                 htmlTextareaAssoc(
                     array(
                         "id"        =>  "summernote" ,
                         "name"      =>  "description",
                         "rows"      =>  ""           ,
                         "cols"      =>  ""           ,
                         "default"   =>  $data['description'],
                         "label"     =>  "Description:",
                         "maxlength" => ""
                     )
                 );
                htmlField("text"       , "due_date"   , $data['due_date'   ], "Due date:"                               );
                htmlCheckbox("complete", "Completed:" , $data['complete'   ]                                            );
                htmlField("hidden"     , "id"         , $data['id']                                                     );
                htmlField("hidden"     , "project_id" , $data['project_id']                                                     );
                htmlField("submit"     , "submit"     , "Update"            , "handleAjax(['title', 'description', 'due_date', 'id', 'project_id', 'complete'],'updatePiece.php', 'POST', updateCallback);" );
        echo "</div>";
?>
        </div>
        <script src='site_functions.js' type='text/javascript'></script>

        <script type="text/javascript">
            $("#due_date").datepick({dateFormat: 'yyyy-mm-dd'});
            $('#summernote').summernote('code', `<?php echo $data['description'] ?>`);
            function updateCallback(data){
                alert(data);
                if(confirm("Return to " + "<?php echo $project_name ?>"))
                    window.location.href = "project.php?id=" + <?php echo $_GET['project_id'];?> ;
                if(confirm("Return to this week's tasks page?"))
                    window.location.href = "weekView.php";
            }
            // function gval(id){
            //
            //     if(document.getElementById(id).type && (document.getElementById(id).type == "text" || document.getElementById(id).type == "hidden") )
            //         return document.getElementById(id).value;
            //
            //     if(document.getElementById(id).nodeName == "TEXTAREA" )
            //         return document.getElementById(id).value;
            //
            //     if(document.getElementById(id).type && document.getElementById(id).type == "checkbox")
            //         return document.getElementById(id).checked;
            //
            // }

            // function updatePiece(url, method){
            //     data =       "title=" + gval("title")       + "&" +
            //            "description=" + gval("description") + "&" +
            //               "due_date=" + gval("due_date")    + "&" +
            //                     "id=" + gval("id")          + "&" +
            //             "project_id=" + <?php echo $_GET['project_id'] ? "\"" + $_GET['project_id'] + "\"" : "''" ?> + "&" +
            //               "complete=";
            //     data += gval("complete") ? "1" : "0";
            //
            //     handleAjax(url, method, data);
            //
            // }
            // function handleAjax(url, method, data){
            //     ajaxRequest = new XMLHttpRequest();
            //     if(!ajaxRequest){
            //         alert("Unable to make the ajax request");
            //         return false;
            //     }
            //
            //     ajaxRequest.onreadystatechange = function(){
            //         if(ajaxRequest.readyState === XMLHttpRequest.DONE){//ajax is DONE
            //             if(ajaxRequest.status == 200){ //all OK
            //                  alert("This is the response: " + ajaxRequest.responseText);
            //             }else{
            //                 alert("There was a problem with the request " + ajaxRequest.responseText)
            //             }
            //         }
            //     }
            //     if(method.toUpperCase() == "GET"){
            //         ajaxRequest.open(method, url + "?" + data);
            //         ajaxRequest.send();
            //     }else{
            //         ajaxRequest.open(method, url);
            //         ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            //         ajaxRequest.send(data);
            //     }
            // }
        </script>
    </body>
</html>