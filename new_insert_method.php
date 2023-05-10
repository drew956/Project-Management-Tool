<?php

    /* 
        using prepared statements
        
        
        format is like so:(col1, col2, col3, col4, col5) 
            PREPARE statement FROM "INSERT INTO table_name VALUES(?,?,?,?,?)";
            SET @col1 = "val1", 
                @col2 = "val2",
                @col3 = "val3",
                @col4 = "val4";
            EXECUTE statement USING @col1, @col2, @col3, @col4;
            
            DEALLOCATE statement;
                
            prepare statement FROM "INSERT INTO
    */
    $sql_prep = 
    'PREPARE statement FROM "INSERT INTO {$this->tbl_name} (' .implode(", "  , $keys)    .  
    ') VALUES(' . substr(str_repeat("?,", count($matches)), 0, -1) . ')"';
    
    //$matches $keys
    //could check that count($keys) == count($matches), but that's not necessarily tru, depending on how many rows we are inserting.
    

    mysql_query($sql_prep);
    //can insert multiple rows at the same time, so will use the prepared statement over and over again.
    for($j = 0; $j < count($matches) && (count($matches) % count($keys) == 0); $j++){
        $sql_execute = "EXECUTE statement USING "; 
        $sql_bind = "SET ";
        
        for($i = 0; ($i < count($keys)) && ($j <= count($matches) - 1); $i++ ){        
            $sql_bind .= "@" . $keys[$i] . "='" . $matches[$j] . "',"
            $sql_execute .= "@" . $keys[$i] . ",";
            $j++;
        }
        $sql_bind = substr($sql_bind, 0, -1);//gets rid of the last comma
        $sql_execute = substr($sql_execute, 0, -1);
        if(!mysql_query($sql_bind))
            return mysql_error();

        if(!mysql_query($sql_execute))
            return mysql_error();
        
    }
    $sql_deallocate = 'DEALLOCATE PREPARE satement';
    if(!mysql_query($sql_deallocate) )    
        return mysql_error();
        
    public function insert_prepared($columns, $values){
        $matches = array();
        $keys = array();

        foreach( $values as $key => $value){
            if(in_array($key,$columns)){
                array_push($matches, $value); //this would be better if we actually collected the matching keys too instead of just using $columns
                                              //thus it will ignore anything which would cause errors for lack of pairing
                array_push($keys, $key); //this means the order you put the columns in doesn't need to match the order of $_POST ($values really)
            }
        }
        $sql_prep = 
        'PREPARE statement FROM "INSERT INTO {$this->tbl_name} (' .implode(", "  , $keys)    .  
        ') VALUES(' . substr(str_repeat("?,", count($keys)), 0, -1) . ')"';//use count keys cuz matches could have multiple rows
            
        if(!mysql_query($sql_prep))
            return mysql_error();

        for($j = 0; $j < count($matches) && (count($matches) % count($keys) == 0); $j++){
            $sql_execute = "EXECUTE statement USING "; 
            $sql_bind    = "SET ";
        
            for($i = 0; ($i < count($keys)) && ($j <= count($matches) - 1); $i++ ){        
                $sql_bind    .= "@" . $keys[$i] . "='" . $matches[$j] . "',";
                $sql_execute .= "@" . $keys[$i] . ",";
                $j++;
            }
            $sql_bind    = substr($sql_bind   , 0, -1);//gets rid of the last comma
            $sql_execute = substr($sql_execute, 0, -1);
            if(!mysql_query($sql_bind))
                return mysql_error();

            if(!mysql_query($sql_execute))
                return mysql_error();
        
        }
        $sql_deallocate = 'DEALLOCATE PREPARE statement';
        if(!mysql_query($sql_deallocate) )    
            return mysql_error();
              
    }
?>