<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>货品管理</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>
	<script>
		var deleteHtml = "" ;
	</script>
   <?php
   include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('default/style');
		echo $this->Html->css('../js/tab/jquery.ui.tabs');
		echo $this->Html->css('../js/layout/jquery.layout');
		echo $this->Html->css('../js/tree/jquery.tree');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery-ui');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('grid/query');
		echo $this->Html->script('modules/warehouse/bad/lists');
		echo $this->Html->script('calendar/WdatePicker');
		echo $this->Html->script('tab/jquery.ui.tabs');
		echo $this->Html->script('layout/jquery.layout');
		echo $this->Html->script('tree/jquery.tree');
		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$categorys = $SqlUtils->exeSql("sql_saleproduct_categorytree",array() ) ;

		$user = $this->Session->read("product.sale.user") ;
		$loginId = $user["GROUP_CODE"] ;
		if($loginId == 'general_manager'){
		?>
		<script>
			var deleteHtml = "<a href='#' class='action giveup btn'   type=3>删除</a>" ;
		</script>
		<?php
		}
	?>
</head>
<body>
  <div data-widget="layout" style="width:100%;height:100%;">
		<div region="center" split="true" border="true" title="货品列表" style="padding:2px;">
			<div class="toolbar toolbar-auto">
				<table style="width:100%;" class="query-table">	
					<tr>
						<th>名称：</th>
						<td>
							<input type="text" id="name"/>
						</td>
						<th>SKU：</th>
						<td>
							<input type="text" id="sku"/>
						</td>
						<th></th>
						<td>
							<button class="btn btn-primary query query-btn" >查询</button>
							<button class="btn btn-primary bad-in" >残品入库</button>
						</td>
					</tr>						
				</table>	
				<hr style="margin:2px;"/>	
			</div>
			
			<div id="details_tab"></div>
			<div class="grid-content" id="tab-content"></div>
		</div>
   </div>	
</body>
</html>
