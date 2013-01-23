<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>功能列表</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('../js/tree/jquery.tree');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('grid/query');
		echo $this->Html->script('tree/jquery.tree');
		echo $this->Html->script('modules/users/list_functions');
		
		//test tree
		$Utils  = ClassRegistry::init("Utils") ;
		$result = $Utils->formatTree("sql_security_listAllFUnctions",array()) ;
		//debug($result) ;
	?>
  
   <script type="text/javascript">
		var treeData = <?php echo $result;?> ;
   </script>
   
</head>
<body>

	<div class="grid-query-button" style="padding:5px;">
		
	</div>
	
	<div class="row-fluid">
		<div class="span2">
			<div id="default-tree" class="tree" style="padding: 5px; "></div>
		</div>
		<div class="span10">
			<div class="toolbar toolbar-auto">
				<table style="width:100%;" class="query-table">	
					<tr>
						<th>名称：</th>
						<td>
							<input type="text" id="name"/>
						</td>
						<th></th>
						<td>
							<button class="btn btn-primary query" >查询</button>
							<button class="action add btn btn-primary">添加功能</button>
						</td>
					</tr>						
				</table>
			</div>
			
			<div class="grid-content"></div>
		</div>
	</div>
	
	
</body>
</html>
