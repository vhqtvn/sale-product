<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>llygrid demo</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('../grid/redmond/ui');
		echo $this->Html->css('../grid/grid');

		echo $this->Html->script('jquery');
		echo $this->Html->script('jquery-ui');
		echo $this->Html->script('../grid/grid');
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
		$(".message,.loading").hide() ;
			$(".grid-content").llygrid({
				columns:[
		           	{align:"center",key:"ID",label:"编号", width:"10%"},
		           	{align:"center",key:"NAME",label:"名称",width:"20%",forzen:false,align:"left",format:function(val,record){
		           		return "<a href='#' class='show-details' val='"+record.ID+"'>"+val+"</a>" ;
		           	}},
		           	{align:"center",key:"CREATE_TIME",label:"上传时间",width:"30%"},
		           	{align:"center",key:"USERNAME",label:"上传用户",width:"30%"}
		         ],
		         ds:{type:"url",content:"/saleProduct/index.php/grid/flow"},
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:200,
				 title:"上传列表",
				 indexColumn:true,
				 querys:{name:"hello",name2:"world"},
				 loadMsg:"数据加载中，请稍候......"
			}) ;

	
			$(".show-details").live("click",function(){
				var taskId = $(this).attr("val") ;
				openCenterWindow("/saleProduct/index.php/flow/lists/"+taskId,900,600) ;
				return false ;
			}) ;
			
			$( "[name='startTime']" ).datepicker({dateFormat:"yy-mm-dd"});
			$( "[name='endTime']" ).datepicker({dateFormat:"yy-mm-dd"});
			
   	 });
   	 
		
		function validateForm(){
			if( !$("[name='flowFile']").val() ){
				alert("请选择上传文件！");
				return false ;
			}
			
			if( !$("[name='startTime']").val() ){
				alert("请选择开始时间！");
				return false ;
			}
			
			if( !$("[name='endTime']").val() ){
				alert("请选择结束时间！");
				return false ;
			}
			
			return true ;
			
		}
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

   <div style="border:1px solid #CCC;margin:3px;">
	    <form action="/saleProduct/index.php/taskUpload/doFlowUpload" method="post" target="form-target" enctype="multipart/form-data" onsubmit="return validateForm()">
		   <table border=0 cellPadding=3 cellSpacing=4 >
		    <tr>
		     <td>流量文件：</td>
		     <td><input name="flowFile" type="file"/></td>
		     <td>开始时间：</td>
		     <td><input name="startTime" type="text"/></td>
		     <td>结束时间：</td>
		     <td><input name="endTime" type="text"/></td> 
		     <td colSpan=2 align=center><input type="submit" value="上传流量文件"></td> 
		    </tr>
		   </table>
	   </form>
	   <iframe style="width:0; height:0; border:0;display:none;" name="form-target"></iframe>
	</div>  
	<div class="grid-content">
	</div>
	
	<div class="message">
	</div>
	<div class="loading">
		处理中......
	</div>
</body>
</html>
