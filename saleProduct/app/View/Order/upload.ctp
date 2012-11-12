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
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('calendar/WdatePicker');
		echo $this->Html->script('validator/jquery.validation');
	?>
  
   <script type="text/javascript">
		var accountId = '<?php echo $accountId;?>' ;
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
			$(".grid-content").llygrid({
				columns:[
		           	{align:"center",key:"ID",label:"编号", width:"10%"},
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
				 querys:{sqlId:"sql_order_upload_list",accountId:accountId},
				 loadMsg:"数据加载中，请稍候......"
			}) ;
		})
   </script>


</head>
<body>

   <div style="border:1px solid #CCC;margin:3px;">
	    <form action="/saleProduct/index.php/order/doUpload/<?php echo $accountId;?>"
	    	data-widget="validator" method="post" target="form-target" enctype="multipart/form-data" onsubmit="return validateForm()">
		   <table border=0 cellPadding=3 cellSpacing=4 >
		    <tr>
		     <td>订单文件：</td>
		     <td><input name="orderFile" data-validator="required" type="file"/></td>
		      <td>开始时间：</td>
		     <td><input name="startTime" data-validator="required" data-widget="calendar" data-options="{isShowWeek:true,dateFmt:'yyyy-MM-dd HH:mm:ss'}" type="text"/></td>
		     <td>结束时间：</td>
		     <td><input name="endTime" data-validator="required" data-widget="calendar" data-options="{isShowWeek:true,dateFmt:'yyyy-MM-dd HH:mm:ss'}" type="text"/></td> 
		     <td colSpan=2 align=center><input type="submit" class="btn btn-primary" value="上传订单文件"></td> 
		    </tr>
		   </table>
	   </form>
	   <iframe style="width:0; height:0; border:0;display:none;" name="form-target"></iframe>
	</div>  
	<div class="grid-content">
	</div>
	
</body>
</html>
