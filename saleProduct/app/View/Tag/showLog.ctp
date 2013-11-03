<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>标签日志列表</title>
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
		
		$entityType = $params['arg1'] ;
		$entityId = $params['arg2'] ;
		
		
	?>
  
</head>

<script>
$(function(){
	$(".grid-content").llygrid({
		columns:[
           	{align:"center",key:"TAG_NAME",label:"标签",width:"15%",align:'left'},
        	{align:"center",key:"ACTION",label:"操作",width:"5%"},
           	{align:"center",key:"MEMO",label:"标签备注",width:"25%",align:'left'},
           	{align:"center",key:"LOGOR_NAME",label:"操作用户",width:"7%"},
           	{align:"left",key:"LOG_DATE",label:"操作时间",width:"18%"},
         	{align:"center",key:"CREATOR_NAME",label:"创建用户",width:"7%"},
           	{align:"left",key:"CREATE_DATE",label:"创建时间",width:"18%"}
         ],
         ds:{type:"url",content:contextPath+"/grid/query"},
		 limit:20,
		 pageSizes:[10,20,30,40],
		 height:function(){
		 	return $(window).height() - 100 ;
		 },
		 title:"用户列表",
		 indexColumn:false,
		  querys:{sqlId:"sql_tag_log",entityType:'<?php echo $entityType;?>',entityId:'<?php echo $entityId;?>'},
		 loadMsg:"数据加载中，请稍候......"
	}) ;
}) ;
</script>

<body>

	<div class="grid-content">
	</div>
</body>
</html>
