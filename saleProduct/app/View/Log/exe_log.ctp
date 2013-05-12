<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>任务详细日志</title>
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

			$(".grid-content").llygrid({
				columns:[
					{align:"left",key:"TASK_ID",label:"内容", width:"15%"},
					{align:"center",key:"MESSAGE",label:"内容", width:"60%"},
		           	{align:"center",key:"LOG_TIME",label:"时间", width:"15%"}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:50,
				 pageSizes:[15,20,30,40],
				 height:function(){
					return $(window).height() - 140 ;
				},
				 title:"用户列表",
				 indexColumn:false,
				 querys:{sqlId:"sql_listExeLog"},
				 // querys:{name:"hello",name2:"world"},
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
	<div class="toolbar toolbar-auto toolbar1">
		<table>
			<tr>
				<th>
					关键字:
				</th>
				<td>
					<input type="text" id="searchKey" placeHolder="" style="width:400px;"/>
				</td>								
				<td class="toolbar-btns">
					<button class="query-btn btn btn-primary" data-widget="grid-query"  data-options="{gc:'.grid-content',qc:'.toolbar1'}">查询</button>
				</td>
			</tr>						
		</table>
	</div>	
	<div class="grid-content">
	
	</div>
</body>
</html>
