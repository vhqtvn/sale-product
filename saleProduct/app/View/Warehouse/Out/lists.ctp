<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>出库计划列表</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('default/style');
		echo $this->Html->css('../js/tab/jquery.ui.tabs');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery-ui');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('tab/jquery.ui.tabs');
		echo $this->Html->script('modules/warehouse/out/lists');
		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$security  = ClassRegistry::init("Security") ;
		$loginId   = $user['LOGIN_ID'] ;
		
		$hasEditPermission = $security->hasPermission($loginId , 'OUT_STATUS0') ;
	?>
  
</head>
<body>
<div class="toolbar toolbar-auto">
		<table class="query-table">
			<tr>
				<th>
					出库号:
				</th>
				<td>
					<input type="text" name="inNumber"/>
				</td>							
				<td class="toolbar-btns">
					<button class="query-btn btn btn-primary">查询</button>
					<?php if($hasEditPermission){ ?>
					<button class="add-btn btn">添加出库单</button>
					<?php } ?>
				</td>
			</tr>						
		</table>					

	</div>

	<div id="details_tab"></div>
	<div class="grid-content" id="tab-content"></div>
</body>
</html>
