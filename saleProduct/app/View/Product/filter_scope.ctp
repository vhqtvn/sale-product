<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>选择筛选范围</title>
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
	?>
  
   <script type="text/javascript">

	$(function(){
			$(".grid-content").llygrid({
				columns:[
					{align:"center",key:"ID",label:"编号", width:"10%",format:{type:"checkbox"}},
		           	{align:"center",key:"ID",label:"编号", width:"20%"},
		           	{align:"center",key:"NAME",label:"任务名称",width:"30%",forzen:false,align:"left"},
		           	{align:"center",key:"UPLOAD_TIME",label:"添加时间",width:"30%"}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:150,
				 title:"上传列表",
				 indexColumn:true,
				 querys:{sqlId:"sql_pdev_upload_list"},
				 loadMsg:"数据加载中，请稍候......"
			}) ;
			
			$(".seller-grid-content").llygrid({
				columns:[
					{align:"center",key:"ID",label:"编号", width:"10%",format:{type:"checkbox"}},
		           	{align:"center",key:"ID",label:"编号", width:"20%"},
		           	{align:"center",key:"NAME",label:"商家名称",width:"30%",forzen:false,align:"left"},
		           	{align:"center",key:"CREATE_TIME",label:"添加时间",width:"30%"}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/sellerUpload"},
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:150,
				 title:"商家列表",
				 indexColumn:true,
				 querys:{},
				 loadMsg:"数据加载中，请稍候......"
			}) ;

   	 });
   	 
   	 $(".grid-content [type='checkbox']").live("click",function(){
   	 	var vals1 = $(".seller-grid-content").llygrid("getSelectedValue",{key:"ID"})   ;
   	 	var vals2 = $(".grid-content").llygrid("getSelectedValue",{key:"ID"})   ;
   	 	var split = vals1.join(",")?(vals2.join(",")?",":""):"" ;
   	 	$("textarea").val( vals1.join(",")+split+vals2.join(",") ) ;
   	 }) ;
   	 $(".seller-grid-content [type='checkbox']").live("click",function(){
   	 	var vals1 = $(".seller-grid-content").llygrid("getSelectedValue",{key:"ID"})   ;
   	 	var vals2 = $(".grid-content").llygrid("getSelectedValue",{key:"ID"})   ;
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
