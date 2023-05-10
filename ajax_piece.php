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
    //$_GET has all hte things we need. Including 'Submit', which we don't need.

    $piece_tbl = new Table("piece", $conn);
    $id = $piece_tbl->insert_prepared( array("project_id", "title", "description", "due_date"), $_POST);
    if($id){
        $order = array("title", "description", "due_date");
        $last = $piece_tbl->getAllAssoc( array("id", $id) );
        $last = $last[0];

        $data = array_map(function($el) use ($last){
                            return $last[$el];
                            //return (($last && $last[0]) ? $last[0][$el] : "out of scope");
                          },
                          $order
                );
        $id = $last["id"];
        $data[] = "<input type='checkbox' name='$id'>";
        $data[] = "<input id='delete$id' type='submit' value='Delete' onclick='deletePiece(" .
        $id . ", \"delete$id\" )' />";
        $data[] = "<a href='updatePiece.php?id=" . $id . "&project_id=" . $_POST["project_id"] . "' >Update</a>" ;
        print(json_encode($data));
    }else{
        mysqli_error($conn);
    }





?>