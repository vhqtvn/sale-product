<?php

 $tiem = new DateTime('now', new DateTimeZone('UTC')) ;
 
// $tiem->modify( '+6 hour +28 minute +01 second' );
// $dateInterval = new DateInterval('P06H28M01S');

//$tiem->setTime(6,28,1) ;
 
// $tiem->sub($dateInterval) ;
 
 print_r( $tiem->format(DATE_ISO8601) ) ;

 phpinfo();
 
 //2012-08-26T06:04:12+0000
 
 //2012-08-25T23:23:49+0000
 
