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
		
		$COST_VIEW_TOTAL  						= $security->hasPermission($loginId , 'COST_VIEW_TOTAL') ;
		$COST_VIEW_PROFIT  						= $security->hasPermission($loginId , 'COST_VIEW_PROFIT') ;
		$COST_VIEW_PURCHASE  				= $security->hasPermission($loginId , 'COST_VIEW_PURCHASE') ;
		$COST_VIEW_LOGISTIC  					= $security->hasPermission($loginId , 'COST_VIEW_LOGISTIC') ;
		$COST_VIEW_PRODUCT_CHANNEL = $security->hasPermission($loginId , 'COST_VIEW_PRODUCT_CHANNEL') ;
		$COST_VIEW_FEE  							= $security->hasPermission($loginId , 'COST_VIEW_FEE') ;
		$COST_VIEW_OTHER  						= $security->hasPermission($loginId , 'COST_VIEW_OTHER') ;
		
		$COST_EDIT_PURCHASE  				= $security->hasPermission($loginId , 'COST_EDIT_PURCHASE') ;
		$COST_EDIT_LOGISTIC  					= $security->hasPermission($loginId , 'COST_EDIT_LOGISTIC') ;
		$COST_EDIT_PRODUCT_CHANNEL 	= $security->hasPermission($loginId , 'COST_EDIT_PRODUCT_CHANNEL') ;
		$COST_EDIT_FEE    							= $security->hasPermission($loginId , 'COST_EDIT_FEE') ;
		$COST_EDIT_OTHER   						= $security->hasPermission($loginId , 'COST_EDIT_OTHER') ;
		$COST_EDIT_SALEPRICE   				= $security->hasPermission($loginId , 'COST_EDIT_SALEPRICE') ;
		
		$COST_EDIT = $COST_EDIT_PURCHASE || $COST_EDIT_LOGISTIC || $COST_EDIT_PRODUCT_CHANNEL || $COST_EDIT_FEE||$COST_EDIT_OTHER||$COST_EDIT_SALEPRICE ;
	
	?>
  
   <script type="text/javascript">
   var columns = [] ;

	<?php  if( $COST_EDIT ){?>
	columns.push({align:"center",key:"ID",label:"操作",width:"6%",forzen:true,format:function(val,record){
		var status = record.STATUS ;
		var html = [] ;
		html.push("<a href='#' class='edit-action' val='"+val+"'>编辑</a>&nbsp;") ;
		return html.join("") ;
	}}) ;
	<?php }?>
	columns.push( {align:"center",key:"TYPE",label:"成本类型", width:"6%" }) ;
	columns.push( {align:"left",key:"ASIN",label:"SKU/ASIN", width:"15%" ,format:function(val,record){
		if(record.SKU) return "(SKU)"+record.SKU ;
		return "<a href='' product-detail='"+val+"'>(ASIN)"+record.ASIN+"</a>" ;
	}}) ;
	columns.push( {align:"left",key:"TITLE",label:"名称", width:"20%" ,format:function(val,record){
		if( record.SKU ){
			return record.REAL_PRODUCT_TITLE||record.PRODUCT_TITLE||"" ;
		}
		return "<a href='#' offer-listing='"+record.ASIN+"'>"+(record.REAL_PRODUCT_TITLE||record.PRODUCT_TITLE||"")+"</a>" ;
	}}) ;

	<?php  if($COST_VIEW_PROFIT){ ?>
	//利润率
	columns.push({align:"left",key:"PROFIT_NUM",label:"产品利润",forzen:true,width:"7%",format:function(val ,record){
		var pn = record.PROFIT_NUM ;
		var totalCost = record.TOTAL_COST ;
		var pl = pn/totalCost ;
		if(pl<=0){
			return "亏本" ;
		}

		if(pl<0.15) return "低利润" ;
		return "利润达标" ;
	}}) ;
	<?php }?>
	<?php  if($COST_VIEW_TOTAL){ ?>
	//总成本
	columns.push( {align:"center",key:"TOTAL_COST",label:"总成本",forzen:true,width:"6%"} ) ;
	<?php }?>
	<?php  if($COST_VIEW_PURCHASE){ ?>
	//采购成本
	columns.push( {align:"center",key:"PURCHASE_COST",label:"采购成本",width:"8%"}) ;
	<?php }?>
	<?php  if($COST_VIEW_LOGISTIC){ ?>
	//物流成本
	columns.push( {align:"center",key:"BEFORE_LOGISTICS_COST",label:"入库前物流费用",width:"8%",forzen:false,align:"left"}) ;
	columns.push(	{align:"center",key:"TARIFF",label:"关税",width:"6%",forzen:false,align:"left"}) ;
	columns.push(	{align:"center",key:"WAREHOURSE_COST",label:"仓储费用",width:"6%"}) ;
	columns.push( {align:"center",key:"USPS_COST",label:"USPS邮费",width:"6%"}) ;
	<?php }?>
	<?php  if($COST_VIEW_PRODUCT_CHANNEL){ ?>
	//产品渠道成本
	columns.push( {align:"center",key:"AMAZON_FEE",label:"amazon佣金",width:"8%"}) ;
	columns.push( {align:"center",key:"VARIABLE_CLOSURE_COST",label:"可变关闭费用",width:"8%"}) ;
	columns.push( {align:"center",key:"OORDER_PROCESSING_FEE",label:"订单处理费",width:"6%"}) ;
	columns.push( {align:"center",key:"TAG_COST",label:"标签费用",width:"8%"} ) ;
	columns.push( {align:"center",key:"PACKAGE_COST",label:"打包费",width:"6%"}) ;
	columns.push( {align:"center",key:"STABLE_COST",label:"称重费",width:"8%"}) ;
	<?php }?>
	<?php  if($COST_VIEW_FEE){ ?>
	//税费人工成本
	columns.push( {align:"center",key:"LOST_FEE",label:"当地税费",width:"6%"}) ;
	columns.push( {align:"center",key:"LABOR_COST",label:"人工成本",width:"6%"}) ;
 	columns.push( {align:"center",key:"SERVICE_COST",label:"服务成本",width:"6%"}) ;
 	<?php }?>
	<?php  if($COST_VIEW_OTHER){ ?>
 	//其他成本
 	columns.push(	{align:"center",key:"OTHER_COST",label:"其他成本",width:"8%"} ) ;
 	<?php }?>
 	columns.push(	{align:"center",key:"LAST_UPDATE_TIME",label:"更新时间",width:"15%"} ) ;
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
					<input type="text" name="asin" class="span2"/>
				</td>
				<th>
					SKU:
				</th>
				<td>
					<input type="text" name="realSku" class="span2"/>
				</td>
				<th>
					名称:
				</th>
				<td>
					<input type="text" name="title"  class="span3"/>
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
	<!-- 
		<?php if( $COST_EDIT ){
			echo '<button class="add-cost btn btn-primary">添加成本</button>' ;
		} ?>
	 -->	
	</div>
	<div class="grid-content-details" style="margin-top:5px;">
	</div>
</body>
</html>
