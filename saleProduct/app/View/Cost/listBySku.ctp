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
		
		$loginId = $user["GROUP_CODE"] ;//transfer_specialist cashier purchasing_officer general_manager product_specialist
		$sku = $params['arg1'] ;
		
		$security  = ClassRegistry::init("Security") ;
		$loginId   = $user['LOGIN_ID'] ;
		/*
		    总成本查看   			COST_VIEW_TOTAL
		   利润查看				COST_VIEW_PROFIT
		   采购成本查看			COST_VIEW_PURCHASE
		   物流成本查看			COST_VIEW_LOGISTIC
		   产品渠道成本查看	COST_VIEW_PRODUCT_CHANNEL
		   税费人工成本查看	COST_VIEW_FEE
		   其他成本查看			COST_VIEW_OTHER
		   
		   采购成本编辑			COST_EDIT_PURCHASE
		   物流成本编辑			COST_EDIT_LOGISTIC
		   产品渠道成本编辑	COST_EDIT_PRODUCT_CHANNEL
		   税费人工成本编辑	COST_EDIT_FEE
		   其他成本编辑			COST_EDIT_OTHER
		   销售价格编辑			COST_EDIT_SALEPRICE
    */
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
   
   var taskId = '' ;
  
	$(function(){
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

			<?php  if($COST_VIEW_PROFIT){ ?>
			//利润率
			columns.push({align:"center",key:"PROFIT_NUM",label:"产品利润",forzen:true,width:"7%",format:function(val ,record){
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
           	
			$(".grid-content-details").llygrid({
				columns : columns ,
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:30,
				 pageSizes:[10,20,30,40],
				 height:function(){
					return $(window).height() - 100 ;
				 },
				 title:"",
				 indexColumn:true,
				 querys:{realSku:'<?php echo $sku;?>',sqlId:"sql_cost_product_details_list"},
				 loadMsg:"数据加载中，请稍候......",
				 loadAfter:function(){
				 	$(".grid-checkbox").each(function(){
				 		
						var val = $(this).attr("value") ;
						if( $(".product-list ul li[asin='"+val+"']").length ){
							$(this).attr("checked",true) ;
						}
					}) ;
				 }
			}) ;
			<?php  if( $COST_EDIT ){?>
			$(".edit-action").live("click",function(){
				var id = $(this).attr("val") ;
				openCenterWindow(contextPath+"/cost/addBySku/<?php echo $sku;?>/"+id,880,650,function(){
					$(".grid-content-details").llygrid("reload",{},true) ;
				}) ;
			})
			
			$(".add-cost").click(function(){
				 	openCenterWindow(contextPath+"/cost/addBySku/<?php echo $sku;?>/",880,650,function(){
				 		$(".grid-content-details").llygrid("reload",{},true) ;
					 }) ;
			}) ;
			<?php }?>
   	 });
   </script>
   
   <style>
   		*{
   			font:12px "微软雅黑";
   		}
   </style>

</head>
<body>
	<div class="query-bar">
	<?php  if( $COST_EDIT ){?>
		<button class="add-cost btn btn-primary">添加成本</button>
	<?php }?>
	</div>
	<div class="grid-content-details" style="margin-top:5px;">
	</div>
</body>
</html>
