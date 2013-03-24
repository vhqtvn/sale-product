<?php
include_once ('corn_config.php');

$random = date("U") ;
file_get_contents("http://".domain."/".context."/index.php/taskAsynAmazon/listOrders/".accountId."?".$random);