<?php
require('staff.inc.php');       //application/octet-stream
function get($parent) {
    $result =  db_query("select * from ost_category where parent_id=".$parent);
    while($row=db_fetch_row($result)) {
        echo $row[0].",";
        get($row[0]);
    }
}
get($_GET['id']);