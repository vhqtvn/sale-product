<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>用户编辑</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
  		include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('validator/jquery.validation');
	?>
	
	<style type="text/css">
		input[type='text'] {
			width:90%!important;
		}
		
		select{
			width:90%!important;	
		}
		
		.image-items li{
			list-style: none;
			float:left ;
			width:20%;
			height:100px;
			border:1px solid #CCC;
			display:block;
			margin:0px 5px;
		}
	</style>
</head>



<body class="container-popup">
	<div>
		<ul>
			<li>
				<?php 
					$SqlUtils  = ClassRegistry::init("SqlUtils") ;
					$security  = ClassRegistry::init("Security") ;
				?>
			</li>
		</ul>
	</div>
</body>
</html>