<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>开发产品文档列表</title>
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
		echo $this->Html->script('grid/query');
		echo $this->Html->script('modules/product/developer/doc_list');
		
		$devId = $params['arg1'] ;
	?>
  	<script>
		var devId = '<?php echo $devId;?>'
  	</script>
</head>
<body>

	
	<div class="toolbar toolbar-auto">
		<table style="width:100%;" class="query-table">	
			<tr>
				<td>
					<button class="action add btn btn-primary">添加资料</button>
				</td>
			</tr>						
		</table>
	</div>
	

	<div class="grid-content">
	</div>
</body>
</html>
