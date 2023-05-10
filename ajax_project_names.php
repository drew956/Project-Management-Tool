<html>
<body>
    <div>
<?php
require "LH_Library.php";
require "setup.php"; //not sure if this is a good idea for session-related stuff, but oh well.
require "db.php";

$project_tbl = new Table("project", $conn);

$projects = $project_tbl->getAllAssoc(array("status", "1"));

$data = [];
foreach ($projects as $index => $project) {
   // print($index);
   // print_r($project);
   // echo "<br />\n";
   $data[$project['id']] = $project['name'];
}
//print_r($projects);

htmlSelect($data, 'project-select', 'project-select');
?>
    </div>
</body>
</html>