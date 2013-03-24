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
		echo $this->Html->script('modules/cost/lists');
		
		//$loginId = $user["GROUP_CODE"] ;//transfer_specialist cashier purchasing_officer general_manager product_specialist
		
		$security  = ClassRegistry::init("Security") ;
		$loginId   = $user['LOGIN_ID'] ;
		
		$COST_EDIT = $security->hasPermission($loginId , 'COST_EDIT') ;
		$COST_VIEW_PURCHASE_COST = $security->hasPermission($loginId , 'COST_VIEW_PURCHASE_COST') ;
		$COST_VIEW_POSTAGE = $security->hasPermission($loginId , 'COST_VIEW_POSTAGE') ;
		$COST_VIEW_PRODUCT_REL = $security->hasPermission($loginId , 'COST_VIEW_PRODUCT_REL') ;
		$COST_VIEW_FEE = $security->hasPermission($loginId , 'COST_VIEW_FEE') ;
		$COST_VIEW_OTHER = $security->hasPermission($loginId , 'COST_VIEW_OTHER') ;
		
		/*COST_EDIT
		COST_VIEW_PURCHASE_COST
		COST_VIEW_POSTAGE
		COST_VIEW_PRODUCT_REL
		COST_VIEW_FEE
		COST_VIEW_OTHER*/
	?>
  
   <script type="text/javascript">
    var $COST_EDIT 					= <?php echo $COST_EDIT?"true":"false" ?> ;
    var $COST_VIEW_PURCHASE_COST 	= <?php echo $COST_VIEW_PURCHASE_COST?"true":"false" ?> ;
    var $COST_VIEW_POSTAGE 			= <?php echo $COST_VIEW_POSTAGE?"true":"false" ?> ;
    var $COST_VIEW_PRODUCT_REL 		= <?php echo $COST_VIEW_PRODUCT_REL?"true":"false" ?> ;  
    var $COST_VIEW_FEE 				= <?php echo $COST_VIEW_FEE?"true":"false" ?> ;  
    var $COST_VIEW_OTHER 			= <?php echo $COST_VIEW_OTHER?"true":"false" ?> ;  
   
   </script>

</head>
<body>
	<div class="toolbar toolbar-auto">
		<table>
			<tr>
				<th>
					ASIN:
				</th>
				<td>
					<input type="text" name="asin"/>
				</td>
				<th>
					名称:
				</th>
				<td>
					<input type="text" name="title"/>
				</td>								
				<td class="toolbar-btns">
					<button class="query-btn btn btn-primary">查询</button>
				</td>
			</tr>						
		</table>					

	</div>	
	
	<div class="grid-content">
	</div>
	<div class="query-bar">
		<?php if( $COST_EDIT ){
			echo '<button class="add-cost btn btn-primary">添加成本</button>' ;
		} ?>
		
	</div>
	<div class="grid-content-details" style="margin-top:5px;">
	</div>
</body>
</html>
