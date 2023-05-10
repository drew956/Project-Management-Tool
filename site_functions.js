function addToTable(data){
    table = document.getElementById("data-table");
    //this part doesn't seem to be working for some reason..
    if(!table){
        table = document.createElement("table");
        document.body.appendChild(table);
        tables = document.getElementsByTagName("table");
    }
    tr = document.createElement("tr");
    //the idea here is that since the ids are auto-assigned, we have to let
    //can't know what the id is for the thing we are adding, so
    //if we are also displaying (presumably) the ID, then
    //we put a padding layer.


    //padding used to only get defined if the if statement works, (causing issues)
    // so we initialize it here:
    padding = "";
    // if(tables[0].rows.length && (tables[0].rows[0].cells.length != data.length)){
    //     for(i = 0; i < tables[0].rows[0].cells.length - data.length; i++){
    //         padding += "<td>Refresh to see</td>";
    //     }
    // }
    //
    // //can't use join because data is an object
    // tr.innerHTML = padding + "<td>" + data.join("</td><td>") + "</td>";
    // tables[0].appendChild(tr); //not sure if this will work
    if(table.rows.length && (table.rows[0].cells.length != data.length)){
        for(i = 0; i < table.rows[0].cells.length - data.length; i++){
            padding += "<td>Refresh to see</td>";
        }
    }
    //can't use join because data is an object
    tr.innerHTML = padding + "<td>" + data.join("</td><td>") + "</td>";
    table.appendChild(tr); //not sure if this will work
}

function handleAjax(keys, url, method, callback){
    ajaxRequest = new XMLHttpRequest();
    if(!ajaxRequest){
        alert("Unable to make the ajax request");
        return false;
    }
    //alert(keys);Fine here
    data = [];//associative arrays are bad, we should be using objects I guess
    request_data = "";
    keys.forEach(function(element){
    //this is pretty cool. We don't even need an iterator if(document.getElementById(id).type && document.getElementById(id).type == "checkbox")
    //         return document.getElementById(id).checked;
        let el = document.getElementsByName(element) ? document.getElementsByName(element)[0] : null;
        if(el){
            if(el.type && el.type == "checkbox"){
                data[data.length] = el.checked ? 1 : 0;
            }else{
                data[data.length] = el.value;
            }
        }else{
            data[data.length] = "NOT FOUND";
        }

        //this is where the summernote DIV thing breaks down. I'm assuming we have to escape characters or something.
        request_data += element + "=" + encodeURIComponent(data[data.length - 1]) + "&";
        //console.log(request_data);
    });
    // request_data = encodeURIComponent(request_data);
    //alert(request_data);
    ajaxRequest.onreadystatechange = function(){
        if(ajaxRequest.readyState === XMLHttpRequest.DONE){//ajax is DONE
            if(ajaxRequest.status == 200){ //all OK

                //console.log("This is the response: " + ajaxRequest.responseText);
                try{
                    let test = JSON.parse(ajaxRequest.responseText);

                    if(test instanceof Array && test.length > 0){ //i.e. we got the result
                        callback(test);
                    }
                }catch(e){
                    // console.log(ajaxRequest.responseText);
                    callback(ajaxRequest.responseText);
                }

            }else{
                alert("There was a problem with the request " + ajaxRequest.responseText);
            }
        }
    }
    if(method.toUpperCase() == "GET"){
        ajaxRequest.open(method, url + "?" + request_data);// + extra_data);
        ajaxRequest.send();
    }else{
        ajaxRequest.open(method, url);
        ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        console.log(request_data);
        ajaxRequest.send(request_data); // + extra_data);
    }

}


function wipeInputs(names){
    names.forEach(function(name){
        let el = document.getElementsByName(name)[0];
        el.value = "";
    });
}

//we don't need the responseText
//because we already ahve the data for the table in the data var
//though we should clear all of the inputs now
// keys.forEach(function(element){
//     let el = document.getElementsByName(element)[0];
//     if(el.type != "hidden") //works, but summernote is hiddne text so..
//         el.value = "";//not sure if this will work, because of closures and what have you
// });