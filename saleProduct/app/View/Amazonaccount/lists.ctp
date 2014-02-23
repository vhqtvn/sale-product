<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>llygrid demo</title>
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
		
		$user = $this->Session->read("product.sale.user") ;
		$group=  $user["GROUP_CODE"] ;
	?>
  
   <script type="text/javascript">
  
	$(function(){
			/*INVENTORY_CENTER_FEE, 
		FEE_RATIO, 
		SUPPLY_CYCLE, 
		REQ_ADJUST*/
		
			$(".grid-content").llygrid({
				columns:[
					{align:"center",key:"ID",label:"操作",width:"5%",format:function(val,record){
						var status = record.STATUS ;
						var html = [] ;
						html.push("<a href='#' class='edit-account' val='"+val+"'>修改</a>&nbsp;&nbsp;") ;
						return html.join("") ;
					}},
		           	{align:"center",key:"NAME",label:"账户名称",width:"20%",forzen:false,align:"left"},
		           	{align:"center",key:"CODE",label:"账户Code",width:"15%"},
		        	{align:"center",key:"PLATFORM_NAME",label:"所属平台",width:"8%"},
		        	{align:"center",key:"EXCHANGTE_NAME",label:"货币",width:"4%"},
		        	{align:"center",key:"SUPPLY_CYCLE",label:"供应周期",width:"5%"},
		        	{align:"center",key:"FEE_RATIO",label:"区域税率",width:"8%"},
		        	{align:"center",key:"REQ_ADJUST",label:"需求调整系数",width:"8%"},
		        	{align:"center",key:"TRANSFER_WH_PRICE",label:"转仓成本单价",width:"8%"},
		           	{align:"center",key:"USERNAME",label:"创建人",width:"5%"}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:10,
				 pageSizes:[10,20,30,40],
				 height:function(){
					return $(window).height() - 120;
				},
				 title:"商家列表",
				 indexColumn:false,
				 querys:{sqlId:"sql_account_list",countSqlId:"sql_account_list_count"},
				 loadMsg:"数据加载中，请稍候......"
			}) ;
			

			$(".register").click(function(){
				openCenterWindow(contextPath+"/amazonaccount/add",750,530,function(win,re){
					if(re){
						$(".grid-content").llygrid("reload") ;
					}
				}) ;
			}) ;
			
			$(".edit-account").live("click",function(){
				var val = $(this).attr("val") ;
				openCenterWindow(contextPath+"/amazonaccount/add/"+val,750,530,function(win,re){
					if(re){
						$(".grid-content").llygrid("reload",{},true) ;
					}
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
<?php
	if( $group == 'manage' || $group== 'general_manager'){
		echo '<button class="register">注册Amazon账户</button>' ;
	}
?>
	
	<div class="grid-content">
	
	</div>
</body>
</html>
