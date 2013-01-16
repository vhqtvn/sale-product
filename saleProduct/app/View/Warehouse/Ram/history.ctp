<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>残品出入库记录</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('../js/listselectdialog/jquery.listselectdialog');
		echo $this->Html->css('default/style');
		echo $this->Html->css('../js/grid/jquery.llygrid');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('dialog/jquery.dialog');
		echo $this->Html->script('grid/query');
		echo $this->Html->script('validator/jquery.validation');	
		echo $this->Html->script('listselectdialog/jquery.listselectdialog');
		echo $this->Html->script('modules/warehouse/ram/history');
		echo $this->Html->script('calendar/WdatePicker');
	
		$result = null ;
		
		$realProductId = $params['arg1'] ;
		
	?>
	<script type="text/javascript">
		var realProductId = '<?php echo $realProductId ;?>' ;
	</script>
</head>

<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page">
		
	<div class="grid-content-rma" id="tab-rma" style="margin-top:5px;zoom:1;"></div>
</body>
</html>