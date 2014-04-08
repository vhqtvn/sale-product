<?php
ignore_user_abort(1);
set_time_limit(0);
include_once ('corn_config.php');

$url = "http://www.smarteseller.com/saleProduct/index.php/cronTask/asynCost?".$random ;
sock_get($url) ;