//functions.js
/*
    1/29/2021
    projects.php uses site_functions not this.
    So the handleAjax functions signature is different.
*/
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
function getDate(){
    today = new Date();
    today = today.toLocaleDateString({
            formatMatcher : "year, month, day",
            year: "numeric",
            month: "numeric",
            day: "2-digit"
        }).split("/").join("-");
    today = today.substr(today.length - 4, today.length) + "-" + today.substr(0, today.length - 5);
    return today;
}


function addToTable(data, tableNum = 0){
    console.log(data);
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
                if(typeof callback != undefined){ //make the callback optional
                    callback(ajaxRequest.responseText);
                }
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