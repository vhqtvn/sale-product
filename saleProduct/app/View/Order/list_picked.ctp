<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>llygrid demo</title>
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
	?>
  
   <script type="text/javascript">
		var accountId = '' ;
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
		function validateForm(){
			if( !$("[name='orderFile']").val() ){
				alert("请选择上传文件！");
				return false ;
			}
			return true ;
		}
		
		$(function(){
			$(".action").live("click",function(){
				var id = $(this).attr("val") ;
				if( $(this).hasClass("update") ){
					openCenterWindow("/saleProduct/index.php/users/editFunction/"+id,600,400) ;
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
					openCenterWindow("/saleProduct/index.php/order/editPicked",600,400) ;
				} 
				return false ;
			}) ;
			
			$(".select-product").live("click",function(){
				var pickId = $(this).attr("pickId") ;
				openCenterWindow("/saleProduct/index.php/order/selectPickedProduct/"+pickId,1000,600) ;
			})
			
			$(".grid-content").llygrid({
				columns:[
		           	//{align:"center",key:"ID",label:"编号", width:"10%"},
		           	{align:"center",key:"ID",label:"操作",width:"25%",format:function(val,record){
						var status = record.STATUS ;
						var html = [] ;
						html.push('<button class="btn select-product" pickId="'+val+'">编辑订单</button>&nbsp;') ;
						html.push('<button class="btn print-product" pickId="'+val+'">打印订单</button>&nbsp;') ;
						return html.join("") ;
					}},
		           	{align:"center",key:"NAME",label:"名称",width:"20%",forzen:false,align:"left"},
		           	{align:"center",key:"TOTAL",label:"订单总数",width:"10%",forzen:false,align:"left"},
		           	{align:"center",key:"CREATE_TIME",label:"上传时间",width:"20%"},
		           	{align:"center",key:"USERNAME",label:"上传用户",width:"20%",format:function(val,record){
		           		return val ;
		           	}}
		         ],
		         ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:400,
				 title:"订单上传列表",
				 querys:{sqlId:"sql_order_picked_list",accountId:accountId},
				 loadMsg:"数据加载中，请稍候......"
			}) ;
		})
   </script>


</head>
<body>
	<div class="grid-query-button">
		<button class="action add btn btn-primary">创建拣货单</button>
	</div>  
	<div class="grid-content">
	</div>
</body>
</html>
