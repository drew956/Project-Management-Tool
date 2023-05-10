<?php

/*
    2021-08-15
    project_tbl should be a Table object from LH_Library
*/
function printProjectDropDown($project_tbl, $id, $select_id="", $class=""){
    $cols  = array("id", "name");
    $conds = array("status", "1");
    $projects = $project_tbl->getColumnsAssoc($cols, $conds);

    $data = [];
    foreach ($projects as $index => $project) {
       $data[$project['id']] = $project['name'];
    }

  //htmlSelect($data, $name, $selected="", $class=""){
    htmlSelect($data, $id, $select_id, $class);
}
?>