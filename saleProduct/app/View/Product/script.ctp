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
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('grid/query');
	?>
  
   <script type="text/javascript">

	$(function(){
			$(".action").live("click",function(){
				var id = $(this).attr("val") ;
				if( $(this).hasClass("update") ){
					openCenterWindow(contextPath+"/product/editScript/"+id,800,600) ;
				}else if( $(this).hasClass("del") ){
					if(window.confirm("确认删除吗")){
						$.ajax({
							type:"post",
							url:contextPath+"/product/deleteScript/"+id,
							data:{id:id},
							cache:false,
							dataType:"text",
							success:function(result,status,xhr){
								$(".grid-content").llygrid("reload") ;
							}
						}); 
					}
				}else if( $(this).hasClass("add") ){
					openCenterWindow(contextPath+"/product/editScript",800,600) ;
				} 
				return false ;
			})

			$(".grid-content").llygrid({
				columns:[
		           	{align:"center",key:"ID",label:"ID", width:"5%",forzen:true},
				{align:"center",key:"ID",label:"Actions", width:"10%",format:function(val,record){
						var html = [] ;
						html.push("<a href='#' class='action update' val='"+val+"'>修改</a>&nbsp;") ;
						html.push("<a href='#' class='action del' val='"+val+"'>删除</a>") ;

						return html.join("") ;
				}},
		           	{align:"center",key:"NAME",label:"NAME",width:"40%",forzen:false,align:"left"},
		           	{align:"center",key:"SCRIPTS",label:"SCRIPTS",width:"40%"}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:function(){
					return $(window).height() - 130 ;
				},
				 title:"筛选规则列表",
				 indexColumn:false,
				 querys:{sqlId:"sql_rule_script"},
				 loadMsg:"数据加载中，请稍候......"
			}) ;
   	 });
   </script>
   
   <style>
   		*{
   			font:12px "微软雅黑";
   		}
   </style>

</head>
<body>

	<div class="grid-query-button">
		<button class="action add btn btn-primary">添加规则</button>
	</div>
	

	<div class="grid-content" style="width:99.5%">
	
	</div>
</body>
</html>
