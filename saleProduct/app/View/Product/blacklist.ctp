<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>llygrid demo</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../grid/redmond/ui');
		echo $this->Html->css('../grid/grid');
		echo $this->Html->css('../grid/redmond/ui');
		echo $this->Html->css('../kissu/widgets/core/layout/layout');
		echo $this->Html->css('../kissu/widgets/core/tree/ui.tree');

		echo $this->Html->script('jquery');
		echo $this->Html->script('../kissu/scripts/jquery.utils');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('../grid/grid');
		echo $this->Html->script('../kissu/widgets/core/layout/jquery.layout');
		echo $this->Html->script('../kissu/widgets/core/tree/jquery.tree');
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
	<div class="widget-class" widget="layout" style="width:100%;height:90%;">
		<div region="center" split="true" border="true" title="废弃产品列表" style="height:60px;background:#efefef;">
			<div class="query-bar">
				<label>ASIN:</label><input type="text" name="asin"/>
				<label>TITLE:</label><input type="text" name="title"/>
				
				<button class="query-btn">查询</button>
			</div>
			<div class="grid-content" style="width:98%;">
			
			</div>
		</div>
   </div>
	
</body>
</html>
