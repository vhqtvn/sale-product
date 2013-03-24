<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>收货确认</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('modules/warehouse/in/acceptBox');
	?>
  
   <script type="text/javascript">
   	var inId = '<?php echo $params['arg1'] ;?>' ;	
   	
   	
   </script>
   
    <style type="text/css">
		.exception-form{
			background:#ffc0cb ;
			border:1px solid #CCC;
			padding:10px;
			text-align:left;
			display:none;
			position:absolute;
			top:32px;
			right:0px;
			z-index:1;
			text-align:right;
		}
		
		.exception-form div{
			margin:3px;
		}
	</style>

</head>
<body>


	<div class="toolbar toolbar-info row-fluid" style="text-align:right;">
		<span class="span8">&nbsp;</span>
		<span class="span3">
			<button class="btn btn-primary btn-confirm">确认收货</button>
			<button class="btn btn-danger">异常</button>
			
			<div class="exception-form">
			   <!--<div>
					<label>分类：</label>
					<select id="type">
						<option value="">-</option>
						<option value="exception_value">质量异常</option>
						<option value="exception_package">包装异常</option>
						<option value="exception_ship">物流错误</option>
						<option value="exception_weight">重量超标</option>
					</select>
				</div>
				-->
				<div>
					<label>备注:</label>
					<textarea id="memo" style="width:300px;height:50px;"></textarea>
				</div>
				<button class="btn btn-primary exception-btn">确认</button>
			</div>
		</div>
		</span>
	</div>
	
	
	<div class="grid-content" style="width:99.5%">
	</div>
	<br/>
	
	<div class="grid-content-details" style="width:99.5%">
	</div>
</body>
</html>
