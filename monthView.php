<!DOCTYPE html>
<html>
    <head>
        <link rel="shortcut icon" href="favicon.ico">
        <meta http-equiv="Content-Type" content="text/html;charset=utf8mb4" />
        <meta http-equiv="Content-Language" content="ja" />
        <link rel="stylesheet" type="text/css" href="css/project.css" />
        <style>
            #week-view {
                display: block;
            }

            .title-div {

            }

            .project-name {
                color: rgba(130,0,0);
                background: lightgrey;
                border-radius: 2px;
                padding: 2px;
                margin-bottom: 5px;
                background: rgba(68,68,68,0.1);
                border: 1px solid rgba(120,0,0,0.1);
                cursor: pointer;
                white-space: nowrap;
                display: block;
            }
            .project-name-div {
                margin-bottom: 6px;
                /* min-inline-size: max-content; */
            }
            #project_id {
                margin-bottom: 5px;
            }
            #day_buttons{
                padding: 20px;
            }
            #youtube-stuff{
                text-align: center;
                background: tan;
            }
            #youtube-buttons{
                margin-bottom: 3px;
            }
            body {
                background-color: tan !important;
            }
            #video-title-div{
                text-align: center;
                padding: 10px;
                display: none;
            }
            #video-title{
                padding: 10px;
                background: rgba(255,255,255,0.3);
                border: aliceblue;
                border-style: dashed;
            }
        </style>
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
                let date = document.getElementById("start_date").value;
                console.log(date);
                $.ajax({
                    type: "POST",
                    url: "ajax_all_month_view.php",
                    data: {
                        "start_date" : date
                    }
                }).done(function(data){
                    document.getElementById("week-view").innerHTML = data;
                    setEvents();
                });

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
                        // updateTable();
                    });

            }
            function setDate(){
                let today = new Date();
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

                $('#summernote').summernote();
                //updateWeekView();
            };
            /*
                These should just shift the date +1 month, - 1 month etc.
            */
            function shift_date_left(){
                let hidden_date = document.getElementById("start_date");
                hidden_date.value = parseInt(hidden_date.value) - 1;
                console.log(hidden_date.value)
                updateWeekView();
            }
            function shift_date_right(){
                let hidden_date = document.getElementById("start_date");
                hidden_date.value = parseInt(hidden_date.value) + 1;
                console.log(hidden_date.value);
                updateWeekView();
            }
        </script>
        <link rel="stylesheet" type="text/css" href="css/css-fixes.css">

    </head>
    <body>
        <div id="youtube-stuff">
            <iframe id="ytplayer" width="560" height="0" src="https://www.youtube.com/embed/videoseries?list=PLrwMnO7tCSGDvcHR2JY9gqsAJzeLlVmfI&enablejsapi=1" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
        <div class="colorized" style="text-align: center; margin: auto">
<?php

    require "LH_Library.php";
    require "setup.php"; //not sure if this is a good idea for session-related stuff, but oh well.
    require "project-functions.php";
    require "db.php";

    $piece_tbl   = new Table(  "piece", $conn);
    $project_tbl = new Table("project", $conn);


    $format_str = "Y-m-d";
    $begin      = new DateTime("first day of this month");
    $end        = new DateTime("first day of next month");
    $interval   = DateInterval::createFromDateString('1 day');
    $date_range = new DatePeriod($begin, $interval, $end);
    $days       = array();
    $today      = (new DateTime("now"))->format("Y-m-d");

    $first_day_of_month = $begin->format("Y-m-d");

    $this_week = mysqli_query($conn, "SELECT piece.id, piece.title, piece.description, piece.due_date, piece.project_id, piece.complete, project.name FROM `piece` JOIN `project` ON project.id=piece.project_id WHERE complete=0 AND due_date BETWEEN '$first_day_of_month' AND ADDDATE('$first_day_of_month', INTERVAL 1 MONTH) AND MONTH(due_date) = MONTH('$first_day_of_month') ORDER BY due_date ASC");

    $headers = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");


    $datas      = mysqli_fetch_all($this_week, MYSQLI_ASSOC);
    /*
        HERE WE SHOULD pre-load the HTML for the Dates if you want that in each section
        or we can use the hack below
        Let's find out!
    */

    $elements = array();
    foreach ($date_range as $date) {
        if($date->format("Y-m-d") != $today)
            $elements[] =
                "<span class='date-header'>" .
                    $date->format("Y-m-d <br />") .
                "</span>";
        else
            $elements[] =
                "<span class='date-header today-date-header'>" .
                    $date->format("Y-m-d <br />") .
                "</span>";

    }
    for($i = 0; $i < count($datas); $i++){
        $data     = $datas[$i];
        $datetime = new DateTime($data['due_date']);
        $day_of_the_month = $datetime->format("d"); //kore yori suuji no hou ga daiji da na
                                                   //chokusetsu ni tsukaeru kara sa.
        $index = $day_of_the_month - 1;

        $want = [
            "<div class='project-name-div' >",
                "<span class='project-name'>",
                    "<a href='project.php?id={$data['project_id']}'>",
                        $data["name"] ,
                    "</a>" ,
                "</span>",
            "</div>",
            "<div class='title-div'>",
                "<span class='x-button' onclick='deletePiece(" . $data['id'] . ");'>❎</span>",
                "<span class='task-title'>".
                    $data["title"] .
                "</span>",
                "<span class='check-button' onclick='updateTask(" . $data['id'] . ");'>✅</span>",
            "</div>",
            "<span class='task-description'>".
                $data["description"] .
            "</span>",
            "<div class='task-update-div'>",
                "<span class='task-update-link'>".
                    "<a href='updatePiece.php?id={$data['id']}&project_id={$data['project_id']}' >Update</a>" .
                "</span>",
            "</div>",
            "<div class='task-due-date-div'>",
                "<span class='left-arrow-button' onclick='addTo(" . $data['id'] . ", -1);'>⬅️</span>",
                "<span class='task-due-date'>".
                    $data["due_date"] .
                "</span>",
                "<span class='right-arrow-button' onclick='addTo(" . $data['id'] . ", 1);'>➡️</span>",
            "</div>"
        ];
        $elements[$index] .= "<span class='task'>" . implode("\n", $want) . "</span>"; //fill up the tasks with the tasks for that day
    }
    // $elements = array_merge($headers, $elements);

    //unshift the stuff you need back
    $day_of_the_week = $begin->format('l');
    $times = array_search($day_of_the_week, $headers); //should return 0 for Sunday, etc
    for($j = 0; $j < $times; $j++){
        array_unshift($elements, "No tasks.");
    };

    echo "<h3 class='text-center'>" . "<a href='projects.php'> Projects </a>" . "</h3>\n";
?>
    <div id="youtube-buttons">
        <div id="video-title-div" style="text-align: center;">
            <span id="video-title"></span>
        </div>
        <input type="submit" id="start_video" name="start_video" value="Start Music" onclick="startMusic();">
        <input type="submit" id="stop_video"  name="stop_video"  value="Pause Music"  onclick="pauseVideo();">
        <input type="submit" id="next_video"  name="next_video"  value="Next Song"  onclick="nextVideo();">
    </div>
<?php
    // print_r($elements);
    echo "<div id='week-view'>\n";
        htmlTable("", $elements, $headers, "");
    echo "</div>";

    $cols  = array("id", "name");
    $conds = array("status", "1");
    $projects = $project_tbl->getColumnsAssoc($cols, $conds);

    $data = [];
    foreach ($projects as $index => $project) {
       $data[$project['id']] = $project['name'];
    }

    echo '<div id="new-entry-div" >';
    //echo "<input type='hidden' value='" . $project_id . "' name='project_id' />\n";
        // printProjectDropDown($project_tbl, "project_id");
        echo "<br />\n";
        $piece_tbl->printAjaxInput( array(       "title"       =>  "Title:",
                                                 "description" =>  array("textarea", "Description:"),
                                                 "due_date"    =>  "Due date:(yyyy-mm-dd)",
                                                 "project_id"  => array("select", $data),
                                                 "start_date"  => array("hidden", 0) //really month offset
                                     ),
                                      "ajax_piece.php", 'addToProjectTable', "POST");
    echo "</div>";
?>
    <footer>
        <div id="server-info" class="colorized" style="text-align: center;">
            <?php echo date_default_timezone_get(); ?>
        </div>

    </footer>
    <script src='site_functions.js' type='text/javascript'></script>
    <script>
        $("#due_date").datepick({dateFormat: 'yyyy-mm-dd'});
        setEvents();
        function updateTask(id){

            $.ajax({
                type: "POST",
                url: "setPieces.php",
                data: {
                    "pieces" : [id]
                }
            }).done(function(data){
                updateWeekView();
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
            });
        }

        function addToProjectTable(data){
            updateWeekView();
            wipeInputs(["title", "description"]);
            $('#summernote').summernote('code', '');
        }
        function handleKey(e) {

            if (e.code == 'ArrowRight') {
                shift_date_right();
            } else if (e.code == 'ArrowLeft') {
                shift_date_left();
            }
        }
        document.addEventListener("keyup", handleKey);

    </script>
    <script type="text/javascript">
        var tag = document.createElement('script');
        tag.id = 'iframe-demo';
        tag.src = 'https://www.youtube.com/iframe_api';
        var firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

        var player;
        function onYouTubeIframeAPIReady() {
            player = new YT.Player('ytplayer', {
                events: {
                  'onReady': onPlayerReady,
                  'onStateChange': onPlayerStateChange
                }
            });
        }
        function onPlayerReady(event) {
            document.getElementById('ytplayer').style.borderColor = '#FF6D00';
            player.setShuffle(true);
        }
        function onPlayerStateChange(event) {
            if(event.data == 1) //i.e. loaded a new video{
            {
                let data    =  player.getVideoData();
                let title   =  data['title'];
                let vid_id  =  data['video_id'];
                let url     = "https://www.youtube.com/watch?v=";
                let a       = document.createElement("a");
                a.href      = url + vid_id;
                a.innerHTML = title;
                document.getElementById("video-title").replaceChildren(a);
            }
            // console.log(event);
            // changeBorderColor(event.data);
        }

        function startMusic(){
            player.playVideo();
            window.setTimeout(
                    ()=>{
                        player.nextVideo();
                        document.getElementById("video-title-div").style.setProperty("display", "block");
                    },
                    100
            );
        }
        function pauseVideo(){
            let status = player.playerInfo['playerState']
            if(status == 1) {
                //playing
                player.pauseVideo();
                document.getElementById("stop_video").value = "Resume Music";
            }else{
                //playing
                player.playVideo();
                document.getElementById("stop_video").value = "Pause Music";
            }
        }
        function nextVideo(){
            player.nextVideo();
        }
    </script>
    </body>
</html>