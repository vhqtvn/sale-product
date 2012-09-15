<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>llygrid demo</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('../grid/redmond/ui');
		echo $this->Html->css('../grid/grid');

		echo $this->Html->script('jquery');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('../grid/grid');
		
		$loginId = $user["GROUP_CODE"] ;//transfer_specialist cashier purchasing_officer general_manager product_specialist
	?>
  
   <script type="text/javascript">
   
   var taskId = '' ;
   
   //result.records , result.totalRecord
 	function formatGridData(data){
 		var records = data.record ;
 		var count   = data.count ;
 		
 		count = count[0][0]["count(*)"] ;
 		
		var array = [] ;
		$(records).each(function(){
			var row = {} ;
			for(var o in this){
				var _ = this[o] ;
				for(var o1 in _){
					row[o1] = _[o1] ;
				}
			}
			array.push(row) ;
		}) ;
	
		var ret = {records: array,totalRecord:count } ;
			
		return ret ;
   }

	var currentAsin = "" ;

	$(function(){
			$(".grid-content").llygrid({
				columns:[
		           	{align:"center",key:"ASIN",label:"ASIN", width:"12%",format:function(val,record){
		           		return "<a href='#' class='product-detail' asin='"+val+"'>"+val+"</a>" ;
		           	}},
		           	{align:"center",key:"TITLE",label:"TITLE",width:"20%",forzen:false,align:"left",format:function(val,record){
		           		return "<a href='http://www.amazon.com/gp/offer-listing/"+record.ASIN+"' target='_blank'>"+val+"</a>" ;
		           	}},
		           	{align:"center",key:"TARGET_PRICE",label:"目标价格",width:"6%"},
		           	{align:"center",key:"DAY_PAGEVIEWS",label:"每日PV",width:"6%",format:function(val,record){
		           		if(!val)return val ;
		           		return Math.round(val) ;
		           	}},
		           	{align:"center",key:"FM_NUM",label:"FM数量",width:"6%"},
		           	{align:"center",key:"NM_NUM",label:"NM数量",width:"6%"},
		           	{align:"center",key:"UM_NUM",label:"UM数量",width:"6%"},
		           	{align:"center",key:"FBA_NUM",label:"FBA数量",width:"6%"},
		           	{align:"center",key:"REVIEWS_NUM",label:"Reviews数量",width:"9%"},
		           	{align:"center",key:"QUALITY_POINTS",label:"质量分",width:"5%"}
		           	
		         ],
		         ds:{type:"url",content:"/saleProduct/index.php/cost/product/"},
				 limit:10,
				 pageSizes:[10,20,30,40],
				 height:200,
				 title:"产品列表",
				 indexColumn:true,
				 querys:{},
				 loadMsg:"数据加载中，请稍候......",
				 rowClick:function(rowIndex , rowData){
				 	var asin = rowData.ASIN  ;
				 	currentAsin = asin ;
				 	$(".grid-content-details").llygrid("reload",{asin:asin}) ;
				 }
			}) ;
			
			$(".grid-content-details").llygrid({
				columns:[
					{align:"center",key:"ID",label:"操作",width:"6%",forzen:true,format:function(val,record){
						var status = record.STATUS ;
						var html = [] ;
						html.push("<a href='#' class='edit-action' val='"+val+"'>编辑</a>&nbsp;") ;
						//html.push("<a href='#' class='del-action' asin='"+record.ASIN+"' planId='"+record.PLAN_ID+"' val='"+val+"'>删除</a>&nbsp;") ;
						return html.join("") ;
					}},
					
					<?php if($loginId == "purchasing_officer"){//采购专员
						echo '{align:"center",key:"PURCHASE_COST",label:"采购成本",width:"8%"} ' ;
					}else if($loginId == "transfer_specialist"){//物流专员
						echo '
				           	{align:"center",key:"BEFORE_LOGISTICS_COST",label:"入库前物流费用",width:"8%",forzen:false,align:"left"},
				           	{align:"center",key:"TARIFF",label:"关税",width:"6%",forzen:false,align:"left"},
				           	{align:"center",key:"WAREHOURSE_COST",label:"仓储费用",width:"6%"},
				           	{align:"center",key:"USPS_COST",label:"USPS邮费",width:"6%"}' ;
					}else if($loginId == "product_specialist"){//产品专员
						echo '
						{align:"center",key:"TYPE",label:"成本类型", width:"6%" },
						{align:"center",key:"AMAZON_FEE",label:"amazon佣金",width:"8%"},
				           	{align:"center",key:"VARIABLE_CLOSURE_COST",label:"可变关闭费用",width:"8%"},
				           	{align:"center",key:"OORDER_PROCESSING_FEE",label:"订单处理费",width:"6%"},
				           	{align:"center",key:"TAG_COST",label:"标签费用",width:"8%"} ,
				           	{align:"center",key:"PACKAGE_COST",label:"打包费",width:"6%"},
				            {align:"center",key:"STABLE_COST",label:"稳重费",width:"8%"}' ;
					}else if($loginId == "cashier"){//会计
						echo '{align:"center",key:"LOST_FEE",label:"当地税费",width:"6%"},
				           	{align:"center",key:"LABOR_COST",label:"人工成本",width:"6%"},
				           	{align:"center",key:"SERVICE_COST",label:"服务成本",width:"6%"}' ;
					}else if($loginId == "general_manager" || $loginId == 'manage'){//总经理
						echo '{align:"center",key:"TOTAL_COST",label:"总成本",forzen:true,width:"6%"} ,
							{align:"center",key:"PURCHASE_COST",label:"采购成本",width:"8%"} ,
							{align:"center",key:"TYPE",label:"成本类型",forzen:true, width:"6%" },
				           	{align:"center",key:"BEFORE_LOGISTICS_COST",label:"入库前物流费用",width:"8%",forzen:false,align:"left"},
				           	{align:"center",key:"TARIFF",label:"关税",width:"6%",forzen:false,align:"left"},
				           	{align:"center",key:"WAREHOURSE_COST",label:"仓储费用",width:"6%"},
				           	{align:"center",key:"USPS_COST",label:"USPS邮费",width:"6%"},
				           	{align:"center",key:"AMAZON_FEE",label:"amazon佣金",width:"8%"},
				           	{align:"center",key:"VARIABLE_CLOSURE_COST",label:"可变关闭费用",width:"8%"},
				           	{align:"center",key:"OORDER_PROCESSING_FEE",label:"订单处理费",width:"6%"},
				           	{align:"center",key:"TAG_COST",label:"标签费用",width:"8%"} ,
				           	{align:"center",key:"PACKAGE_COST",label:"打包费",width:"6%"},
				            {align:"center",key:"STABLE_COST",label:"稳重费",width:"8%"},
			            	{align:"center",key:"LOST_FEE",label:"当地税费",width:"6%"},
				           	{align:"center",key:"LABOR_COST",label:"人工成本",width:"6%"},
				           	{align:"center",key:"SERVICE_COST",label:"服务成本",width:"6%"},
				           	{align:"center",key:"OTHER_COST",label:"其他成本",width:"8%"} 
		            ' ;
					}?>
		           	
		         ],
		         ds:{type:"url",content:"/saleProduct/index.php/cost/productCost"},
				 limit:30,
				 pageSizes:[10,20,30,40],
				 height:100,
				 title:"",
				 indexColumn:true,
				 querys:{asin:'-----'},
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
			
			$(".edit-action").live("click",function(){
				var id = $(this).attr("val") ;
				openCenterWindow("/saleProduct/index.php/cost/add/"+currentAsin+"/"+id,680,650) ;
			})
			
			$(".product-detail").live("click",function(){
				var asin = $(this).attr("asin") ;
				openCenterWindow("/saleProduct/index.php/product/details/"+asin,950,650) ;
			})
			
			$(".query-btn").click(function(){
				var asin = $("[name='asin']").val() ;
				var title = $("[name='title']").val() ;
				var querys = {} ;
				if(asin){
					querys.asin = asin ;
				}
				if(title){
					querys.title = title ;
				}
				
				$(".grid-content").llygrid("reload",querys) ;
			}) ;
			
			$(".add-cost").click(function(){
				if(currentAsin){
				 	openCenterWindow("/saleProduct/index.php/cost/add/"+currentAsin,680,650) ;
				}else{
					
					alert("请选择某个产品！");
				}
				
			}) ;
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
		<label>ASIN:</label><input type="text" name="asin"/>
		<label>TITLE:</label><input type="text" name="title"/>
		
		<button class="query-btn">查询</button>
	</div>
	<div class="grid-content">
	</div>
	<div class="query-bar">
		<button class="add-cost">添加成本</button>
	</div>
	<div class="grid-content-details" style="margin-top:5px;">
	</div>
</body>
</html>
