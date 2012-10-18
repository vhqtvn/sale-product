<?php
$random = date("U") ;
file_get_contents("http://www.smarteseller.com/saleProduct/index.php/gatherLevel/execute/5/A?".$random);