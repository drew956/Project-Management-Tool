<?php
    /*
        12-19-2016

        You could make this a generic ajax.php thing,
        for creating things.

        ajax-create.php

        and you  could pass it the arguments it needs

    */

    require "db.php";//LH_Library and connectDB etc
    $project_tbl = new Table("project", $conn);
    $result = $project_tbl->insert( array("name", "date_started", "date_finished", "estimated_finish_date"), $_GET);
    if($result){
        $lid = mysqli_insert_id($conn);
        $arr_data = Array( "<a href='project.php?id=" . $lid . "'>" . $_GET["name"] . "</a>\n",
                                                               $_GET["date_started"],
                                                               "<a href='idea.php?id="    . $lid . "'>Ideas</a>\n",
                                                              "Active"
        );
        echo json_encode($arr_data);
        // echo "[\"<a href='project.php?id=$lid'>{$_GET["name"]}</a>\n\", \"{$_GET["date_started"]}\", '{$_GET["date_finished"]}', \"<a href='idea.php?id=$lid'>Ideas</a>\n\",\"Active\"]";
        // echo "[" . "<a href='project.php?id=" . $lid . "'>" . $_GET["name"] . "</a>\n" . ", " .
        //                                                       $_GET["date_started"] . ", " .
        //                                                       $_GET["date_finished"] . ", " .
        //                                                       "<a href='idea.php?id="    . $lid . "'>Ideas</a>\n" . ", " .
        //                                                       "Active" . "]";
    }else{
        if ($conn->error) {
            try {
                throw new Exception("MySQL error $conn->error <br> Query:<br> $query", $conn->errno);
            } catch(Exception $e ) {
                echo "Error No: ".$e->getCode(). " - ". $e->getMessage() . "<br >";
                echo nl2br($e->getTraceAsString());
            }
        }else{
            echo "Created Project Properly.";
        }
    }
/*
    //Example of how to make it abstracted
    $tbl = new Table($_GET['table_name']);
    unset($_GET['table_name']);
    unset($_GET['Submit']);//this is bad, it assumes the button is named Submit..
    //we could do something like this...
    //unset($_GET[ $_GET['button_name'] ]);
    //unset($_GET['button_name']);
    echo $piece_tbl->insert(array_keys($_GET), $_GET);//workaround

*/

?>