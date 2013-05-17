<?php
require('staff.inc.php');       //application/octet-stream
$result =  db_query("select answer from ost_kb_premade where premade_id=".$_GET['id']/10000);
$row=db_fetch_row($result);
echo $row[0];