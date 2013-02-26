<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('modules/warehouse/out/editTrack');
	?>
  
   <script type="text/javascript">
   	var inId = '<?php echo $params['arg1'] ;?>' ;	 
   </script>

</head>
<body>
	<div class="toolbar toolbar-auto" style="padding:0px;">
		<table>
			<tr>
				<th>
				</th>
				<td>
				</td>								
				<td class="toolbar-btns">
					<button class="add-track btn btn-primary">添加跟踪状态</button>
				</td>
			</tr>						
		</table>
	</div>
	<div class="grid-content" style="width:99.5%">
	</div>

</body>
</html>
