<?php 
   $context=trim($_SERVER['REQUEST_URI']);
   $array = explode("/", $context) ;
   
   $context = $array[1] ;

	$contextPath = "/$context/index.php" ;
	$fileContextPath = "$context" ;