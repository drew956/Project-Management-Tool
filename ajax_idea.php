<?php
    /*
        12-18-2016
        Ajax Piece creator.
        Probably could be abstracted to be ajax for any of the tables,
        but that is okay. Right now wwe are only focusing on the pieces.

        The text to return is success or failure.
        Basically, return the mysql_error() message if you get one
        Or return the date that you inserted into the database.
            (^this can be used when showing the created row immediately)


        The data this thing expects to get is an object of keys => values
        It will insert into the table the new thing.
        Automatically creating the date as well.

        I need to brush up on security measures, but for now this will be okay.
        Probably better to abstract the data-collection part so we can modify it whenever
        (i.e. inside a function like Olin or a utility class)
        But this is okay for now.

        I have an OLIN, but it expects $_POST only.
        Which is a bit more annoying when working with ajax, but only slightly
        Because you have to set/send the header/content type

    */

    require "db.php";//LH_Library and connectDB etc
    $idea_tbl = new Table("idea", $conn);


    //print_r($_POST);
    //die;
    //the array is a mask which excludes "Submit" etc
    $last_id = $idea_tbl->insert_prepared( array("project_id", "title", "info", "date"), $_POST);

    // print_r($result);
    if($last_id){
        $order = array("title", "info", "date");
        // $last_id = mysqli_insert_id($conn);//$conn->insert_id;
        // echo $result . "\n";
        $last = $idea_tbl->getAllAssoc(array("id", $last_id));
        $last = $last[0];
        //print_r($last);
        $data = array_map(function($el) use ($last){
                            return $last[$el];
                            //return (($last && $last[0]) ? $last[0][$el] : "out of scope");
                          },
                          $order
                );
        $id = $last["id"];
        $data[] = "<input id='delete$id' type='submit' value='Delete' onclick='deleteIdea(" .
        $id . ", \"delete$id\" )' />";
        $data[] = "<a href='updateIdea.php?id=" . $id . "' >Update</a>" ;
        print(json_encode($data));
    }else{
        echo "Unable to create idea.";
    }
    // echo $result ? $result : mysql_error();




?>