<?php
	header('Content-type: application/csv;charset=GB2312');
	header('Content-Disposition: attachment; filename="'.$name.'.txt"');
	
	echo $feed ;
?>