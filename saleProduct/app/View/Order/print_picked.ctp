<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>订单列表</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

  
    <script type="text/javascript">
    	var pickedId = "<?php echo $pickId;?>"
     	var accountId = "" ;
     	var status = "5" ;
	</script>
   <?php
			echo $this->Html->meta('icon');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('default/style');
		echo $this->Html->css('../js/tab/jquery.ui.tabs');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery-ui');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('modules/order/print_picked');
		echo $this->Html->script('grid/query');
		echo $this->Html->script('calendar/WdatePicker');
		echo $this->Html->script('tab/jquery.ui.tabs');
		
	?>
   
</head>
<!--
风险客户<input type="radio" name="status" value="2">
										待退单<input type="radio" name="status" value="3">
										外购订单<input type="radio" name="status" value="4">
										合格订单<input type="radio" name="status" value="5">
										加急单<input type="radio" name="status" value="6">
										特殊单
-->
<body>
	<div class="grid-content" id="tab-content">
	
	</div>
</body>
</html>
