<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>供应商列表</title>
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
		$group=  $user["GROUP_CODE"] ;
		
		$supplierId = $params['arg1'] ;
		$productId = "" ;
		$type = $params['arg2'] ;
		if( $type == 'product' ){
			$productId = $supplierId ;
			$supplierId = "" ;
		}
		
	?>
  
   <script type="text/javascript">
   
   var taskId = '' ;
   

	$(function(){
			$(".grid-content").llygrid({
				columns:[
					//{align:"center",key:"ID",label:"编号",width:"4%"},
					{align:"left",key:"ID",label:"操作",forzen:false,width:"5%",format:function(val,record){
						var isSku = record.SKU?true:false ;
						
						var status = record.STATUS ;
						var html = [] ;
	
					//	if(status == 4 || status == 6){
						isSku && html.push('<a href="#" title="查看" class="edit-action" val="'+val+'"><img src="/'+fileContextPath+'/app/webroot/img/pre_print.gif"/></a>&nbsp;') ;
					
						return html.join("") ;	
					}},
					{align:"left",key:"STATUS",label:"状态",forzen:false,width:"5%",format:{type:'purchaseProductStatus'}},
					{align:"center",key:"REAL_PURCHASE_DATE",label:"采购时间",width:"15%"},
					{align:"center",key:"CODE",label:"采购编号",width:"10%",format:function(val,record){
						return "<a href='#' purchase-product='"+record.ID+"'>"+val+"<a>";
					}},
		        	{align:"center",key:"QUOTE_PRICE",label:"计划采购价",width:"10%"},
		        	{align:"center",key:"QUALIFIED_PRODUCTS_NUM",label:"合格数量" ,width:"5%"},
		           	{align:"center",key:"REAL_QUOTE_PRICE",label:"实际采购价",width:"10%"},
		        	{align:"center",key:"REAL_SHIP_FEE",label:"运费" ,width:"5%"},
		        	{align:"center",key:"PROVIDOR_NAME",label:"采购供应商",width:"10%",format:function(val,record){
									return "<a supplier-id='"+record.PROVIDOR+"'>"+val+"</a>" ;
			        	}},
					{align:"left",key:"REAL_SKU",label:"货品SKU", width:"8%",format:{type:'realSku'}},
		           	{align:"center",key:"IMAGE_URL",label:"图片",width:"5%",forzen:false,align:"left",format:{type:'img'}},
		           	{align:"center",key:"TITLE",label:"标题",width:"10%",forzen:false,align:"left"},
		        	{align:"center",key:"EXECUTOR_NAME",label:"执行用户",width:"8%",forzen:false,align:"left"},
		        	{align:"center",key:"CREATOR_NAME",label:"发起人",width:"6%",forzen:false,align:"left"},
		        	{align:"center",key:"BAD_PRODUCTS_NUM",label:"不合格数量" ,width:"5%"},
		           	{align:"center",key:"AREA",label:"采购地区",width:"6%",
		           			format:{type:"json",content:{"china":"大陆","taiwan":"台湾","american":"美国"}}}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:30,
				 pageSizes:[10,20,30,40],
				 height:function(){
				 	return $(window).height() - 80 ;
				 },
				 title:"",
				 indexColumn:false,
				 querys:{supplierId:'<?php echo $supplierId;?>',productId:'<?php echo $productId;?>',sqlId:"sql_purchase_product_listByRealId"},
				 loadMsg:"数据加载中，请稍候......"
			}) ;

			
			$(".query-btn").click(function(){
				var name = $("[name='name']").val() ;
				var querys = {} ;
				if(name){
					querys.name = name ;
				}
				$(".grid-content").llygrid("reload",querys) ;	
			}) ;

   	 });
   </script>
   
   <style>
   		*{
   			font:12px "微软雅黑";
   		}
   </style>

</head>
<body style="overflow:hidden;">
	<div class="grid-content"></div>
</body>
</html>
