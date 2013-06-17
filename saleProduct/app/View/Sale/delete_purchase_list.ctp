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
		
		$user = $this->Session->read("product.sale.user") ;
		$groupCode = $user["LOGIN_ID"] ;
	?>
  
   <script type="text/javascript">
   	 var type = '4' ;//查询已经审批通过
   
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

	$(function(){
			$(".grid-content-details").llygrid({
				columns:[
		           	{align:"center",key:"ASIN",label:"ASIN", width:"12%",format:function(val,record){
		           		return "<a href='#' class='product-detail' asin='"+val+"'>"+val+"</a>" ;
		           	}},
		           	{align:"center",key:"LOCAL_URL",label:"Image",width:"6%",forzen:false,align:"left",format:function(val,record){
		           		
		           		if(val){
		           			val = val.replace(/%/g,'%25') ;
		           			return "<img src='/"+fileContextPath+"/"+val+"' onclick='showImg(this)' style='width:50px;height:50px;'>" ;
		           		}
		           		return "" ;
		           		
		           	}},
		           	{align:"center",key:"TITLE",label:"TITLE",width:"15%",forzen:false,align:"left",format:function(val,record){
		           		return "<a href='"+contextPath+"/page/forward/Platform.asin/"+record.ASIN+"' target='_blank'>"+val+"</a>" ;
		           	}},
		           	{align:"center",key:"PLAN_NAME",label:"采购计划名称",width:"12%"},
		           	{align:"center",key:"CREATOR_NAME",label:"创建人",width:"6%"},
		           	{align:"center",key:"CREATE_TIME",label:"创建时间",width:"13%"},
		           	{align:"center",key:"DELETOR_NAME",label:"删除人",width:"6%"},
		           	{align:"center",key:"DELETE_TIME",label:"删除时间",width:"13%"}
		           	<?php if($groupCode == 'general_manager' || $groupCode == 'cashier' ){
		           	?>
		           	,
		           	{align:"center",key:"P_COST",label:"产品成本",width:"6%"},
		           	{align:"center",key:"TARGET_PRICE",label:"FBM/FBA最低价",width:"10%"}
		           	<?php	
		           	}?>
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:30,
				 pageSizes:[10,20,30,40],
				 height:300,
				 title:"",
				 indexColumn:true,
				 querys:{planId:'-----',sqlId:"sql_purchase_delete_list"},
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
			
			$(".product-detail").live("click",function(){
				var asin = $(this).attr("asin") ;
				openCenterWindow(contextPath+"/product/details/"+asin,950,650) ;
			})
   	 });
   	 
   </script>
   
   <style>
   		*{
   			font:12px "微软雅黑";
   		}
   		
   		.message{
   			width:600px;
   			border:1px solid #CCC;
   			overflow:auto;
   			margin:5px;
   			height:200px;
   			background:#000;
   			color:#FFF;
   			margin-bottom:0px;
   		}
   		
   		.loading{
   			width:600px;
   			background:#000;
   			color:#FFF;
   			margin-top:-1px;
   			display:hidden;
   			margin-left:6px;
   		}
   </style>

</head>
<body>

	<div class="grid-content-details" style="margin-top:5px;">
	</div>
</body>
</html>
