<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
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
		echo $this->Html->script('modules/sale/discardList');
		$security  = ClassRegistry::init("Security") ;
		
		$user = $this->Session->read("product.sale.user") ;
		$loginId = $user["LOGIN_ID"] ;
		
		$PDT_CREATE = $security->hasPermission($loginId , 'PDT_CREATE') ;
		$PDT_UPDATE = $security->hasPermission($loginId , 'PDT_UPDATE') ;
		
		$COST_EDIT_PROFIT   						= $security->hasPermission($loginId , 'COST_EDIT_PROFIT') ;
		$COST_VIEW_PROFIT  						= $security->hasPermission($loginId , 'COST_VIEW_PROFIT') ||$COST_EDIT_PROFIT  ;
	?>
  
   <script type="text/javascript">
    var $PDT_CREATE = <?php echo $PDT_CREATE?'true':'false' ;?> ;
	var $PDT_UPDATE = <?php echo $PDT_UPDATE?'true':'false' ;?> ;
	var $COST_VIEW_PROFIT  = <?php echo $COST_VIEW_PROFIT?'true':'false' ;?> ;
	var $loginId = '<?php echo $loginId;?>' ;
   </script>


</head>
<body>

	<div class="toolbar toolbar-auto toolbar1 query-container">
		<table>
			<tr>
				<th>
					关键字:
				</th>
				<td>
					<input type="text" id="searchKey" placeHolder="输入ASIN、产品名称、开发标题" style="width:400px;"/>
				</td>								
				<td class="toolbar-btns">
					<button class="query-btn btn btn-primary" data-widget="grid-query"  data-options="{gc:'.grid-content-details',qc:'.toolbar1'}">查询</button>
				</td>
			</tr>						
		</table>
	</div>	
	<div class="grid-content-details" style="width:99.5%;">
	</div>
</body>
</html>
