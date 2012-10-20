<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>供应商列表</title>
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
		
		$userId  = $_COOKIE["userId"] ; 
		App::import('Model', 'User') ;
		$u = new User() ;
		$user1 = $u->queryUserByUserName($userId) ;
		$user = $user1[0]['sc_user'] ;
		$group=  $user["GROUP_CODE"] ;
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

	$(function(){
			$(".grid-content").llygrid({
				columns:[
					{align:"center",key:"ID",label:"操作",forzen:true, width:"10%",format:function(val,record){
							var html = [] ;
							html.push("<a href='#' class='action-update' val='"+val+"'>修改</a>&nbsp;") ;
							<?php if($group == 'purchasing_manager' || $group == 'general_manager' ){
							?>
								html.push("<a href='#' class='action-del' val='"+val+"'>删除</a>&nbsp;") ;
							<?php	
							} ?>
							
							html.push("<a href='#' class='action-view' val='"+val+"'>查看</a>&nbsp;") ;
							return html.join("") ;
					}},
		           	{align:"center",key:"NAME",label:"名称", width:"15%"},
		           	{align:"center",key:"ADDRESS",label:"地址",width:"15%"},
		           	{align:"center",key:"CONTACTOR",label:"联系人",width:"6%"},
		           	{align:"center",key:"PHONE",label:"联系电话",width:"8%"},
		           	{align:"center",key:"MOBILE",label:"手机",width:"8%"},
		           	{align:"center",key:"FAX",label:"传真",width:"8%"},
		           	{align:"center",key:"EMAIL",label:"EMAIL",width:"8%"},
		           	{align:"center",key:"ZIP_CODE",label:"邮编",width:"6%"},
		           	{align:"center",key:"QQ",label:"QQ/MSN/Skype",width:"6%"},
		           	{align:"center",key:"URL",label:"网址",width:"10%",format:function(val){
		           		if(!val) return "" ;
		           		return "<a href='"+val+"' target='_blank'>"+val+"</a>" ;
		           	}},
		           	{align:"center",key:"USERNAME",label:"创建人",width:"6%"},
		           	{align:"center",key:"CREATE_TIME",label:"创建时间",width:"10%"}
		         ],
		         ds:{type:"url",content:"/saleProduct/index.php/supplier/grid/"},
				 limit:30,
				 pageSizes:[10,20,30,40],
				 height:400,
				 title:"产品列表",
				 indexColumn:true,
				 querys:{},
				 loadMsg:"数据加载中，请稍候......"
			}) ;
			
			$(".action-update").live("click",function(){
				var id = $(this).attr("val") ;
				openCenterWindow("/saleProduct/index.php/supplier/add/"+id,600,500) ;
			});
			
			$(".action-del").live("click",function(){
				var id = $(this).attr("val") ;
				if(window.confirm("确认删除吗？")){
					$.ajax({
						type:"post",
						url:"/saleProduct/index.php/supplier/del/"+id,
						data:{},
						cache:false,
						dataType:"text",
						success:function(result,status,xhr){
							window.location.reload();
						}
					}); 
				}
			});
			
			$(".action-view").live("click",function(){
				var id = $(this).attr("val") ;
				viewSupplier(id) ;
			})
			
			
			$(".query-btn").click(function(){
				var name = $("[name='name']").val() ;
				var querys = {} ;
				if(name){
					querys.name = name ;
				}
				$(".grid-content").llygrid("reload",querys) ;	
			}) ;
			
			$(".add-btn").click(function(){
				openCenterWindow("/saleProduct/index.php/supplier/add/",600,500) ;
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
		<table>
			<tr>
				<th>
					供应商名称:
				</th>
				<td>
					<input type="text" name="name"/>
				</td>								
				<td class="toolbar-btns">
					<button class="query-btn btn btn-primary">查询</button>
					<button class="add-btn btn">添加供应商</button>
				</td>
			</tr>						
		</table>					

	</div>
	<div class="grid-content"></div>
</body>
</html>
