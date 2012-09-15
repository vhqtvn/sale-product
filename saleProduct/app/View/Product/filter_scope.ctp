<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>选择筛选范围</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('../grid/redmond/ui');
		echo $this->Html->css('../grid/grid');

		echo $this->Html->script('jquery');
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
			$(".grid-content").llygrid({
				columns:[
					{align:"center",key:"ID",label:"编号", width:"10%",format:{type:"checkbox"}},
		           	{align:"center",key:"ID",label:"编号", width:"20%"},
		           	{align:"center",key:"NAME",label:"任务名称",width:"30%",forzen:false,align:"left"},
		           	{align:"center",key:"UPLOAD_TIME",label:"添加时间",width:"30%"}
		         ],
		         ds:{type:"url",content:"/saleProduct/index.php/grid/upload"},
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:150,
				 title:"上传列表",
				 indexColumn:true,
				 querys:{name:"hello",name2:"world"},
				 loadMsg:"数据加载中，请稍候......"
			}) ;
			
			$(".seller-grid-content").llygrid({
				columns:[
					{align:"center",key:"ID",label:"编号", width:"10%",format:{type:"checkbox"}},
		           	{align:"center",key:"ID",label:"编号", width:"20%"},
		           	{align:"center",key:"NAME",label:"商家名称",width:"30%",forzen:false,align:"left"},
		           	{align:"center",key:"CREATE_TIME",label:"添加时间",width:"30%"}
		         ],
		         ds:{type:"url",content:"/saleProduct/index.php/grid/sellerUpload"},
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:150,
				 title:"商家列表",
				 indexColumn:true,
				 querys:{name:"hello",name2:"world"},
				 loadMsg:"数据加载中，请稍候......"
			}) ;

   	 });
   	 
   	 $(".grid-content [type='checkbox']").live("click",function(){
   	 	var vals1 = $(".seller-grid-content").llygrid("getSelectedValue","ID")   ;
   	 	var vals2 = $(".grid-content").llygrid("getSelectedValue","ID")   ;
   	 	var split = vals1.join(",")?(vals2.join(",")?",":""):"" ;
   	 	$("textarea").val( vals1.join(",")+split+vals2.join(",") ) ;
   	 }) ;
   	 $(".seller-grid-content [type='checkbox']").live("click",function(){
   	 	var vals1 = $(".seller-grid-content").llygrid("getSelectedValue","ID")   ;
   	 	var vals2 = $(".grid-content").llygrid("getSelectedValue","ID")   ;
   	 	var split = vals1.join(",")?(vals2.join(",")?",":""):"" ;
   	 	$("textarea").val( vals1.join(",")+split+vals2.join(",") ) ;
   	 }) ;
   	 
   	 $(".select-confirm").live("click",function(){
   	 	var val = $("textarea").val() ;
   	 	window.opener.window.$(".select-scope-input").val(val) ;
   	 	window.opener.window.refreshGrid() ;
   	 	window.close() ;
   	 }) ;
   	 
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
	
	<div class="seller-grid-content">
	</div>
	
	<textarea style="width:98%;height:30px;"></textarea>
	<button class="select-confirm">确定</button>
</body>
</html>
