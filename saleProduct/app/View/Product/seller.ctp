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
   //result.records , result.totalRecord
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
		           	{align:"center",key:"ID",label:"编号", width:"8%"},
		           	{align:"center",key:"NAME",label:"商家名称",width:"20%",forzen:false,align:"left",format:function(val,record){
		           		var val1 = record.ID ;
		           		return "<a href='#' class='show-products' val='"+val1+"'>"+val+"</a>" ;
		           	}},
		           	{align:"center",key:"URL",label:"商家地址",width:"30%"},
		           	{align:"center",key:"TOTAL",label:"产品总数",width:"8%"},
		           	{align:"center",key:"USERNAME",label:"创建人",width:"8%"},
				   {align:"center",key:"ID",label:"采集操作",width:"20%",format:function(val,record){
					var html = [] ;
					html.push("<a href='#' class='gather-action' val='"+val+"'>产品采集</a>&nbsp;&nbsp;") ;
					return html.join("") ;
				}}
		         ],
		         ds:{type:"url",content:"/saleProduct/index.php/grid/seller"},
		          ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:400,
				 title:"商家列表",
				 indexColumn:true,
				 querys:{sqlId:"sql_seller"},
				 loadMsg:"数据加载中，请稍候......"
			}) ;

			$(".register").click(function(){
				openCenterWindow("/saleProduct/index.php/seller/add",600,400) ;
			}) ;
			
			$(".show-products").live("click",function(){
				var val = $(this).attr("val") ;
				openCenterWindow("/saleProduct/index.php/product/index/"+val,900,600) ;
			}) ;
			
			var currentGather = null ;
			$(".gather-action").live("click",function(){
				var id = $(this).attr("val") ;
				$.ajax({
					type:"post",
					url:"/saleProduct/index.php/gatherUpload/sellerAsins/"+id,
					data:{},
					cache:false,
					dataType:"text",
					success:function(result,status,xhr){
					}
				}); 
			});
			
   	 });

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
	<div class="alert alert-info">
		从商家采集：
		第一步，注册商家（地址格式如：http://www.amazon.com/s/?me=A1L3WBCG312F8S）；第二步，采集
	</div>

	<button class="register btn btn-primary">注册商家</button>
	<div class="grid-content" style="width:99.5%">
	
	</div>
	
	<div class="message">
	</div>
	<div class="loading">
		处理中......
	</div>
</body>
</html>
