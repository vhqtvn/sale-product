<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>客户列表</title>
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

	$(function(){
			$(".action").live("click",function(){
				var id = $(this).attr("val") ;
				if( $(this).hasClass("update") ){
					openCenterWindow(contextPath+"/users/editUser/"+id,600,400) ;
				}else if( $(this).hasClass("del") ){
					if(window.confirm("确认删除吗")){
						$.ajax({
							type:"post",
							url:contextPath+"/product/deleteScript/"+id,
							data:{id:id},
							cache:false,
							dataType:"text",
							success:function(result,status,xhr){
								$(".grid-content").llygrid("reload") ;
							}
						}); 
					}
				}else if( $(this).hasClass("add") ){
					openCenterWindow(contextPath+"/saleProduct/editProduct",600,400) ;
				} 
				return false ;
			})

			$(".grid-content").llygrid({
				columns:[
					{align:"center",key:"EMAIL",label:"操作",width:"6%",format:{type:"checkbox",render:function(record){
							if(record.STATUS == 'danger'){
								$(this).attr("checked",true) ;
							}
					}}},
					{align:"center",key:"STATUS",label:"状态",width:"6%",format:function(val,record){
							if(val == "danger"){
								return "<div class='danger-user'></div>" ;
							}
							return "" ;
					}},
		           	{align:"left",key:"EMAIL",label:"邮箱",width:"20%",forzen:false,align:"left"},
		           	{align:"left",key:"NAME",label:"姓名",width:"10%"},
		           	{align:"right",key:"PHONE",label:"电话",width:"10%"},
		           	{align:"right",key:"PHONE",label:"地址",width:"20%",format:function(val,record){
		           		return record.ADDRESS_1+"<br/>"+record.ADDRESS_2+"<br>"+record.ADDRESS_3 ;
		           	}},
		           	{align:"center",key:"STATUS",label:"地区",width:"15%",format:function(val,record){
		           		return record.COUNTRY+","+record.STATE+","+record.CITY ;
		           	}},
		           	{align:"center",key:"POSTAL_CODE",label:"邮编",width:"10%"}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:function(){
				 	return $(window).height() - 140 ;
				 },
				 title:"客户列表",
				 autoWidth:true,
				 indexColumn:false,
				  querys:{sqlId:"sql_saleuser_list"},
				 loadMsg:"数据加载中，请稍候......"
			}) ;
			
			$(".query-btn").click(function(){
				var json = $(".query-table").toJson() ;
				$(".grid-content").llygrid("reload",json,true) ;
			}) ;
			
			$(".set-danger").click(function(){
				var checked = $(".grid-content").llygrid("getSelectedValue",{key:"EMAIL",checked:true},true) ;
				var nochecked = $(".grid-content").llygrid("getSelectedValue",{key:"EMAIL",checked:false},true) ;
				$.ajax({
					type:"post",
					url:contextPath+"/saleUser/setDanger" ,
					data:{checked_emails:checked.join(","),unchecked_emails:nochecked.join(",")},
					cache:false,
					dataType:"text",
					success:function(result,status,xhr){
						alert("保存成功!");
						$(".grid-content").llygrid("reload");
					}
				});
			}) ;
   	 });
   </script>
   
   <style>
   		*{
   			font:12px "微软雅黑";
   		}
   		
   		.danger-user{
   			width:20px;
   			height:20px;
   			background-color:red;
   		}
   </style>

</head>
<body>

	<div class="toolbar toolbar-auto">
		<table style="width:100%;" class="query-table">	
			<tr>
				 <th>姓名：</th>
			     <td><input name="name"  type="text" class="span2"/></td>
			     <th>邮箱：</th>
			     <td><input name="email" type="text" class="span2"/></td> 
			     <th>状态：</th>
			     <td><select name="status" class="span2">
					<option value="">-</option>
					<option value="danger">风险客户</option>
				</select>
				</td>
				<th></th>
				<td>
					<input type="button" class="btn btn-primary query-btn" value="查询">
		     		<input type="button" class="btn btn-danger set-danger" value="设置为风险客户">
		     		
		     		<input type="button" class="btn" value="导出">
		     		<input type="button" class="btn" value="导入">
				</td>
			</tr>						
		</table>
	</div>
  
	<div class="grid-content">
	
	</div>
</body>
</html>
