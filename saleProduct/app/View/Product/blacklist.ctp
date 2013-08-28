<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>废弃产品列表</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   include_once ('config/config.php');

   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('../js/tree/jquery.tree');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('grid/query');
		echo $this->Html->script('tree/jquery.tree');
	?>
	
   <script type="text/javascript">
 
	$(function(){
			setTimeout(function(){
				$(".grid-content").llygrid({
					columns:[
			           	{align:"center",key:"ASIN",label:"ASIN", width:"90",format:function(val,record){
			           		return "<a href='#' class='product-detail' asin='"+val+"'>"+val+"</a>" ;
			           	}},
			           	{align:"center",key:"LOCAL_URL",label:"Image",width:"6%",forzen:false,align:"left",format:function(val,record){
			           		if(val){
			           			val = val.replace(/%/g,'%25') ;
			           		}else{
			           			return "" ;
			           		}
			           		return "<img src='/"+fileContextPath+"/"+val+"' onclick='showImg(this)' style='width:50px;height:50px;'>" ;
			           	}},
			           	{align:"center",key:"TITLE",label:"TITLE",width:"30%",forzen:false,align:"left",format:function(val,record){
			           		return "<a href='"+contextPath+"/page/forward/Platform.asin/"+record.ASIN+"' target='_blank'>"+val+"</a>" ;
			           	}},
			           	{align:"center",key:"DESCRIPTION",label:"废弃理由",width:"30%"} ,
			            {align:"center",key:"ID",label:"操作",width:"10%",format:function(val,record){
			            	return "<a href='#' class='enable-product' val='"+val+"' asin='"+record.ASIN+"'>启用</a>&nbsp;" ;
			            }}
			         ],
			         ds:{type:"url",content:contextPath+"/grid/productBlack/"},
					 limit:30,
					 pageSizes:[10,20,30,40],
					 height:400,
					 title:"",
					 indexColumn:false,
					 querys:{name:"hello",name2:"world"},
					 loadMsg:"数据加载中，请稍候......"
				}) ;
			},200) ;
				
			
			$(".product-detail").live("click",function(){
				var asin = $(this).attr("asin") ;
				openCenterWindow(contextPath+"/product/details/"+asin,950,650) ;
			}) ;
			
			$(".enable-product").live("click",function(){
				if( window.confirm("确认启用该产品？") ){
					var asin = $(this).attr("asin") ;
					$.ajax({
						type:"post",
						url:contextPath+"/product/enableBlackProduct/"+asin ,
						data:{},
						cache:false,
						dataType:"text",
						success:function(result,status,xhr){
							$(".grid-content").llygrid("reload") ;
						}
					});
				}
			}) ;
			
			$(".query-btn").click(function(){
				var asin = $("[name='asin']").val() ;
				var title = $("[name='title']").val() ;
				var querys = {} ;
				
				querys.asin = asin ;
				querys.title = title ;
					
				$(".grid-content").llygrid("reload",querys) ;	
			}) ;
   	 });
   </script>
   
   <style>
   		*{
   			font:12px "微软雅黑";
   		}
   </style>

</head>
<body style="magin:0px;padding:0px;">
			<div class="toolbar toolbar-auto">
				<table style="width:100%;" class="query-table">	
					<tr>
						<th>ASIN:</th>
						<td>
							<input type="text" name="asin"/>
						</td>
						<th>TITLE:</th>
						<td>
							<input type="text" name="title"/>
						</td>
						<th></th>
						<td>
							<button class="btn btn-primary query query-btn" >查询</button>
						</td>
					</tr>						
				</table>
			</div>
			
			<div class="grid-content" style="width:98%;">
			
			</div>
	
</body>
</html>
