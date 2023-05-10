list of all the files which use printAjaxBlahBlah
how they use them
and what will need to be done to fix them after we do what we are doing.


project.php
    $piece_tbl->printAjaxInput( array(       "title"  =>  "Title:",
                                        "description" =>  "Description:",
                                        "due_date"    =>  "Due date:(yyyy-mm-dd)"
                                 ),
                                  "ajax_piece.php", "POST");  
    
    $piece_tbl->printAjaxScript("project_id=" . $project_id);
    
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
            }());
            
            /* 
                need to account for the fact that
                we could have the variables mixed in either direction.
                
                also it would be cooler and easier to use ajax
                and replace the entire table with a new one
                generated via an ajax query
            */
            function reloadActive(){
                active = document.getElementById('active');

                url = new String(window.location);
                current_project = /id=([^&]*)/.exec(url);
                state           = /active=([^&]{0,})/.exec(url);
                if(current_project && current_project.length == 2){
                    current_project = current_project[1];
                    if(state && state.length == 2){
                        if(state[1] == "true"){ //this will always be true == true
                                                     //because you NEED to click it to trigger the onlick event
                            window.location = "project.php?id=" + current_project +
                             "&active=" + "false";                            
                            return false;
                        }
                    }
                    
                    window.location = "project.php?id=" + current_project +
                     "&active=" + "true";
                    
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
            window.onload = function(){
                setDate();
                submit = document.querySelector("input[value='Submit']");
                on(submit, "click", setDate);
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
    
    
projects.php
    $projects->printAjaxInput( array(       "name"     =>  "Name:",
                                       "date_started"  =>  "Date Started:"
                               ),
                               "ajax_project.php"); 
    
    
    $projects->printAjaxScript();
    
    
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
    







functions to pull out and generalize:
    function setDate(elementID){
        today = new Date();
        today = today.toLocaleDateString({
                formatMatcher : "year, month, day",
                year: "numeric",
                month: "numeric",
                day: "2-digit"
            }).split("/").join("-");
        today = today.substr(today.length - 4, today.length) + "-" + today.substr(0, today.length - 5);
        
        document.getElementById(elementID).value = today;
    }
    
    //this is turning into handleAjax. LOL
    //deleteXYZ is just a version of handleAjax.. go figure
    //could generalize to deleteEntity
    function deletePiece(data, url, finished){
        ajaxRequest = new XMLHttpRequest();
        if(!ajaxRequest)
            return "ERROR creation of ajax component failed :(";
        
        ajaxRequest.onreadystatechange = function(){
            
            if(ajaxRequest.readyState == XMLHttpRequest.DONE){
                if(ajaxRequest.status == 200){
                    //delete row from table and alert
                    alert(ajaxRequest.responseText);
                    finished();//does whatever needs to be done
                }
            }
            
        };
        
        ajaxRequest.open("POST", "deletePiece.php");
        ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        ajaxRequest.send("id=" + id);
        return false;
        
    }
    function addToTable(data, tableNum = 0){
        tables = document.getElementsByTagName("table");

        if(!tables.length){
            table = document.createElement("table");
            document.body.appendChild(table);
            tables = document.getElementsByTagName("table");
        }
        tr = document.createElement("tr");
        
        /*
            The idea behind this is that when you insert something into the table
            you don't know what it's auto-assigned ID is, so you put a "refresh to see"
            thing in it's spot instead.
            The only way to make sure the padding goes where it is supposed to is to use
            objects/hashes/associative arrays
            That way we can see which ones it is missing, and put padding where it needs to go.
            
            For example:
            
            addToTable({
                "title" : "Newly Made Title",
                "description" : "This is the best piece ever",
                "due_date"    : "2016-12-22"
            }, ["title", "description", "due_date", "delete_link", "update_link"], 0);
            
            
            However, we CAN get the id assigned using php's mysql functions, so we should
            theoretically be able to generate all of the normal things that are generated
            
            
            this should be abstracted, because some tables have dynamic elements on the right, not left
            padding used to only get defined if the if statement works, (causing issues)
            so we initialize it here:
        */
        padding = "";
        if(tables[tableNum].rows.length && (tables[tableNum].rows[0].cells.length != data.length)){
            for(i = 0; i < tables[tableNum].rows[0].cells.length - data.length; i++){
                padding += "<td>Refresh to see</td>";    
            }
        }
        
        //can't use join because data is an object
        tr.innerHTML = padding + "<td>" + data.join("</td><td>") + "</td>";
        tables[tableNum].appendChild(tr); //not sure if this will work
    }
    
    function handleAjax(url, method, data, callback){
        ajaxRequest = new XMLHttpRequest();
        if(!ajaxRequest){
            alert("Unable to make the ajax request");
            return false;
        }

        ajaxRequest.onreadystatechange = function(){
            if(ajaxRequest.readyState === XMLHttpRequest.DONE){//ajax is DONE
                if(ajaxRequest.status == 200){ //all OK
                    callback();
                }else{
                    alert("There was a problem with the request");
                }
            }
        }
        if(method.toUpperCase() == "GET"){
            ajaxRequest.open(method, url + "?" + data);
            ajaxRequest.send();
        }else{
            ajaxRequest.open(method, url);
            ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            ajaxRequest.send(data);            
        }
        
    }