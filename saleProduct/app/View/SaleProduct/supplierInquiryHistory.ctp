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
	?>
  
   <script type="text/javascript">
   
   var taskId = '' ;
  
	$(function(){
			/*UPDATE sale_product1.sc_purchase_supplier_inquiry 
		SET
		ID = 'ID' , 
		SUPPLIER_ID = 'SUPPLIER_ID' , 
		SKU = 'SKU' , 
		ASIN = 'ASIN' , 
		PLAN_ID = 'PLAN_ID' , 
		IS_USED = 'IS_USED' , 
		WEIGHT = 'WEIGHT' , 
		CYCLE = 'CYCLE' , 
		PACKAGE = 'PACKAGE' , 
		PAYMENT = 'PAYMENT' , 
		NUMBER_OFFER = 'NUMBER_OFFER' , 
		CREATOR = 'CREATOR' , 
		CREATE_TIME = 'CREATE_TIME' , 
		NUM1 = 'NUM1' , 
		OFFER1 = 'OFFER1' , 
		NUM2 = 'NUM2' , 
		OFFER2 = 'OFFER2' , 
		NUM3 = 'NUM3' , 
		OFFER3 = 'OFFER3' , 
		NUM4 = 'NUM4' , 
		OFFER4 = 'OFFER4' , 
		NUM5 = 'NUM5' , 
		OFFER5 = 'OFFER5' , 
		STATUS = 'STATUS' , 
		MEMO = 'MEMO' , 
		URL = 'URL' , 
		IMAGE = 'IMAGE' , 
		PRODUCT_SIZE = 'PRODUCT_SIZE' , 
		PACKAGE_SIZE = 'PACKAGE_SIZE'
		
		WHERE
		ID = 'ID' ;*/
			$(".grid-content-details").llygrid({
				columns:[
							//名称	产品重量	生产周期	包装方式	付款方式	产品尺寸	包装尺寸	报价1	报价2	报价3
							{align:"center",key:"CREATE_TIME",label:"询价时间",width:"15%",forzen:false,align:"left"},
							{align:"center",key:"USERNAME",label:"提交人",width:"6%",forzen:false,align:"left"},
							{align:"center",key:"IMAGE",label:"图片",width:"4%",forzen:false,align:"left",format:{type:'img'}},
					     	{align:"center",key:"NAME",label:"供应商名称",width:"15%",forzen:false,align:"left",format:function(val,record){
									return "<a href='#' supplier-id='"+record.SUPPLIER_ID+"'>"+val+"<a>" ;
						     }},
						     {align:"center",key:"URL",label:"产品网址",width:"10%",forzen:false,align:"left",format:function(val,record){
									return "<a href='"+val+"' target='_blank'>"+val+"<a>" ;
						     }},
				           	{align:"center",key:"WEIGHT",label:"产品重量",width:"6%",forzen:false,align:"left"},
				           	{align:"center",key:"CYCLE",label:"生产周期",width:"6%"},
				           	{align:"center",key:"PACKAGE",label:"包装方式",width:"6%"},
				           	{align:"center",key:"PAYMENT",label:"付款方式",width:"6%"},
				           	{align:"center",key:"PRODUCT_SIZE",label:"产品尺寸",width:"6%"},
				           	{align:"center",key:"PACKAGE_SIZE",label:"包装尺寸",width:"6%"},
				           	{align:"center",key:"NUM1",label:"报价1",width:"6%",format:function(val,record){
									return val+"/"+record.OFFER1 ;
					          }},
				           	{align:"center",key:"NUM2",label:"报价2",width:"6%",format:function(val,record){
								return val+"/"+record.OFFER2 ;
					          }},
				           	{align:"center",key:"NUM3",label:"报价2",width:"6%",format:function(val,record){
								return val+"/"+record.OFFER3 ;
					          }}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:30,
				 pageSizes:[10,20,30,40],
				 height:function(){
					return $(window).height() - 130 ;
				 },
				 title:"",
				 indexColumn:true,
				 querys:{realSku:'<?php echo $sku;?>',sqlId:"sql_list_supplierInquiryHistory"},
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

			$(".supplier-select").click(function(){
				openCenterWindow(contextPath+"/supplier/updateProductSupplierPage/<?php echo $sku;?>",800,600,function(){
					$(".grid-content-details").llygrid("reload",{},true);
					}) ;
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
	<div class="toolbar toolbar-auto">
				<table style="width:100%;" class="query-table">	
					<tr>
						<td>
							<button class="supplier-select btn">添加询价</button>
						</td>
					</tr>						
				</table>	
	 </div>
	
	<div class="grid-content-details" style="margin-top:5px;">
	</div>
</body>
</html>
