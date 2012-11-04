<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>View Track</title>
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
		           	{align:"center",key:"STATUS",label:"变更状态",width:"20%",forzen:false,align:"left"},
		           	{align:"center",key:"MESSAGE",label:"说明",width:"30%",forzen:false,align:"left"},
		           	{align:"center",key:"ACT_TIME",label:"上传时间",width:"30%"},
		           	{align:"center",key:"USERNAME",label:"上传用户",width:"20%"}
		         ],
		         ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:400,
				 title:"订单上传列表",
				 querys:{sqlId:"sql_order_track_list",orderId:'<?php echo $orderId?>',orderItemId:'<?php echo $orderItemId?>'},
				 loadMsg:"数据加载中，请稍候......"
			}) ;
		})
   </script>

</head>
<body>
	<div class="grid-content">
	</div>
</body>
</html>
