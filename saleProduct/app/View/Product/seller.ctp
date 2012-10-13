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
		echo $this->Html->script('../grid/grid');
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
					html.push("<a href='#' class='gather-action' val='"+val+"'>基本信息</a>&nbsp;&nbsp;") ;
					html.push("<a href='#' class='gather-com-action' val='"+val+"'>竞争信息</a>&nbsp;&nbsp;") ;
					html.push("<a href='#' class='gather-fba-action' val='"+val+"'>FBA信息</a>") ;
					return html.join("") ;
				}}
		         ],
		         ds:{type:"url",content:"/saleProduct/index.php/grid/seller"},
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:400,
				 title:"商家列表",
				 indexColumn:true,
				 querys:{name:"hello",name2:"world"},
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
				monitor(id) ;
				$.ajax({
					type:"post",
					url:"/saleProduct/index.php/task/sellerAsin/"+id,
					data:{},
					cache:false,
					dataType:"text",
					success:function(result,status,xhr){
						
						//alert(result);
					}
				}); 
			});
			
			$(".gather-com-action").live("click",function(){
				var id = $(this).attr("val") ;
				monitor(id) ;
				$.ajax({
					type:"post",
					url:"/saleProduct/index.php/task/gatherCompetitions/"+id,
					data:{},
					cache:false,
					dataType:"text",
					success:function(result,status,xhr){
						
						//alert(result);
					}
				}); 
			});
			
			$(".gather-fba-action").live("click",function(){
				var id = $(this).attr("val") ;
				monitor(id) ;
				$.ajax({
					type:"post",
					url:"/saleProduct/index.php/task/gatherFba/"+id,
					data:{},
					cache:false,
					dataType:"text",
					success:function(result,status,xhr){
						
						//alert(result);
					}
				}); 
			});
			
			
   	 });
   	 
   	 function monitor(id){
   	 	$(".message,.loading").show() ;
   	 	var interval = window.setInterval(function(){
				$.ajax({
					type:"post",
					url:"/saleProduct/index.php/task/getLog/"+id,
					data:{},
					cache:false,
					dataType:"text",
					success:function(result,status,xhr){
						var result = jQuery.parseJSON(result) ;
						if(result && result.length >0 ){
							$(result).each(function(){
								var message = this['sc_exe_log'].MESSAGE ;
								if(message == "end!"){
									setTimeout(function(){
										$(".message,.loading").hide() ;
									},2000) ;
									window.clearInterval(interval) ;
								}
								$(".message").append(message+"<br>") ;
								$(".message")[0].scrollTop = $(".message")[0].scrollHeight; 
							}) ;
						}
					}
				}); 
			},2000) ;
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
	<div>
		从商家采集：
		第一步，注册商家（地址格式如：http://www.amazon.com/s/?me=A1L3WBCG312F8S）；第二步，采集
	</div>

	<button class="register">注册商家</button>
	<div class="grid-content">
	
	</div>
	
	<div class="message">
	</div>
	<div class="loading">
		处理中......
	</div>
</body>
</html>
