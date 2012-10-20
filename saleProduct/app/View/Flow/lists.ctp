<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>流量信息列表</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
			echo $this->Html->meta('icon');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('grid/query');
	?>
  
   <script type="text/javascript">
     var taskId = "<?php echo $taskId;?>"
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
		$(".message,.loading").hide() ;
			$(".grid-content").llygrid({
				columns:[
		           	{align:"center",key:"ID",label:"编号", width:"5%"},
		           	{align:"center",key:"ASIN",label:"ASIN", width:"10%"},
		           	{align:"center",key:"TITLE",label:"TITLE", width:"10%"},
		           	{align:"center",key:"PAGEVIEWS",label:"PAGEVIEWS", width:"10%"},
		           	{align:"center",key:"PAGEVIEWS_PERCENT",label:"PAGEVIEWS_PERCENT", width:"10%"},
		           	{align:"center",key:"BUY_BOX_PERCENT",label:"BUY_BOX_PERCENT", width:"10%"},
		           	{align:"center",key:"UNITS_ORDERED",label:"UNITS_ORDERED", width:"10%"},
		           	{align:"center",key:"ORDERED_PRODUCT_SALES",label:"ORDERED_PRODUCT_SALES", width:"10%"},
		           	{align:"center",key:"ORDERS_PLACED",label:"ORDERS_PLACED", width:"10%"},
		           	{align:"center",key:"CREATE_TIME",label:"CREATE_TIME", width:"10%"},
		           	{align:"center",key:"CREATOR",label:"CREATOR", width:"10%"}
		         ],
		         ds:{type:"url",content:"/saleProduct/index.php/grid/flowDetail/"+taskId},
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:400,
				 title:"流量信息列表",
				 indexColumn:true,
				 querys:{name:"hello",name2:"world"},
				 loadMsg:"数据加载中，请稍候......"
			}) ;

			
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
	<div class="grid-content">
	
	</div>
	
	<div class="message">
	</div>
	<div class="loading">
		处理中......
	</div>
</body>
</html>
