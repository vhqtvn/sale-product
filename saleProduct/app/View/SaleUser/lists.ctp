<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>客户列表</title>
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
			$(".action").live("click",function(){
				var id = $(this).attr("val") ;
				if( $(this).hasClass("update") ){
					openCenterWindow("/saleProduct/index.php/users/editUser/"+id,600,400) ;
				}else if( $(this).hasClass("del") ){
					if(window.confirm("确认删除吗")){
						$.ajax({
							type:"post",
							url:"/saleProduct/index.php/product/deleteScript/"+id,
							data:{id:id},
							cache:false,
							dataType:"text",
							success:function(result,status,xhr){
								$(".grid-content").llygrid("reload") ;
							}
						}); 
					}
				}else if( $(this).hasClass("add") ){
					openCenterWindow("/saleProduct/index.php/saleProduct/editProduct",600,400) ;
				} 
				return false ;
			})

			$(".grid-content").llygrid({
				columns:[
					{align:"center",key:"EMAIL",label:"操作",width:"6%",format:{type:"checkbox",render:function(record){
							if(record.checked >=1){
								$(this).attr("checked",true) ;
							}
					}}},
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
		         ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
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
			
			$(".set-danger").click(function(){
				var checked = $(".grid-content").llygrid("getSelectedValue",{key:"EMAIL",checked:true},true) ;
				var nochecked = $(".grid-content").llygrid("getSelectedValue",{key:"EMAIL",checked:false},true) ;
				
				$.ajax({
					type:"post",
					url:"/saleProduct/index.php/amazonaccount/saveCategoryProducts" ,
					data:{checked_skus:checked.join(","),unchecked_skus:nochecked.join(","),accountId:currentAccountId,categoryId:currentCategoryId},
					cache:false,
					dataType:"text",
					success:function(result,status,xhr){
						alert("保存成功!");
					}
				});
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
 <div style="border:1px solid #CCC;margin:3px;">
	   <table border=0 cellPadding=3 cellSpacing=4 >
		    <tr>
		      <td>姓名：</td>
		     <td><input name="name"  type="text"/></td>
		     <td>邮箱：</td>
		     <td><input name="email" type="text"/></td> 
		     <td>状态：</td>
		     <td><select name="status">
				<option value="">-</option>
				<option value="1">风险客户</option>
			</select></td> 
		     <td colSpan=2 align=center>
		     	<input type="button" class="btn btn-primary query-btn" value="查询">
		     	<input type="button" class="btn btn-danger set-danger" value="设置为风险客户">
		     </td> 
		    </tr>
		   </table>
	</div>  
	<div class="grid-content">
	
	</div>
</body>
</html>
