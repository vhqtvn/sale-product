<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>流量信息列表</title>
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
		echo $this->Html->script('grid/query');
	?>
  
   <script type="text/javascript">
     var taskId = "<?php echo $taskId;?>"

	$(function(){
		$(".message,.loading").hide() ;
			$(".grid-content").llygrid({
				columns:[
		           	{align:"center",key:"ASIN",label:"ASIN", width:"90",format:function(val,record){
		           		return "<a href='#' class='product-detail' asin='"+val+"'>"+val+"</a>" ;
		           	}},
		           	{align:"center",key:"TITLE",label:"TITLE",width:"20%",forzen:false,align:"left",format:function(val,record){
		           		return "<a href='"+contextPath+"/page/forward/Platform.asin/"+record.ASIN+"' target='_blank'>"+val+"</a>" ;
		           	}},
		           	{align:"right",key:"PAGEVIEWS",label:"总流量", width:"10%"},
		        	{align:"right",key:"DAY_PAGEVIEWS",label:"每日流量", width:"10%"},
		        	{align:"center",key:"START_TIME",label:"开始时间", width:"10%"},
		        	{align:"center",key:"END_TIME",label:"结束时间", width:"10%"},
		           	{align:"center",key:"PAGEVIEWS_PERCENT",label:"PAGEVIEWS_PERCENT", width:"10%"},
		           	{align:"center",key:"BUY_BOX_PERCENT",label:"BUY_BOX_PERCENT", width:"10%"},
		           	{align:"center",key:"UNITS_ORDERED",label:"UNITS_ORDERED", width:"10%"},
		           	{align:"center",key:"ORDERED_PRODUCT_SALES",label:"ORDERED_PRODUCT_SALES", width:"10%"},
		           	{align:"center",key:"ORDERS_PLACED",label:"ORDERS_PLACED", width:"10%"},
		           	{align:"center",key:"CREATE_TIME",label:"CREATE_TIME", width:"10%"},
		           	{align:"center",key:"CREATOR",label:"CREATOR", width:"10%"}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query/"+taskId},
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:function(){
					return $(window).height() - 150 ;
				 },
				 title:"流量信息列表",
				 indexColumn:true,
				 querys:{sqlId:"sql_flow_details_list",taskId:taskId},
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
<div class="toolbar toolbar-auto toolbar1 query-container">
		<table>
			<tr>
				<th>
				ASIN:
				</th>
				<td>
					<input type="text" id="asin" placeHolder="输入ASIN" style="width:400px;"/>
				</td>								
				<td class="toolbar-btns">
					<button class="query-btn btn btn-primary" data-widget="grid-query"  data-options="{gc:'.grid-content',qc:'.toolbar1'}">查询</button>
				</td>
			</tr>						
		</table>
	</div>	
	<div class="grid-content">
	
	</div>
</body>
</html>
