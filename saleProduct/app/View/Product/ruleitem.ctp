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
			$(".grid-content").llygrid({
				columns:[
					{align:"center",key:"ID",label:"操作",width:"10%",format:function(val,record){
						var key = record.TYPE ;
						
						if( key == 'strategy' || key=="devStrategy"){
							var html = [] ;
							html.push("<a href='#' class='edit-config' val='"+val+"'>修改</a>&nbsp;") ;
							html.push("<a href='#' class='delete-config' val='"+val+"'>删除</a>") ;
							return html.join("");
						}
						return "";
					}},
		          // 	{align:"center",key:"ID",label:"编号", width:"5%"},
		           	{align:"center",key:"LABEL",label:"名称",width:"10%",forzen:false,align:"left"},
		           	{align:"center",key:"KEY",label:"值",width:"25%"},
		           	{align:"center",key:"MEMO",label:"备注",width:"30%"},
		           	{align:"center",key:"TYPE",label:"类型",width:"10%",format:{type:'json',content:{'strategy':'策略','devStrategy':'产品开发策略','field':'字段','relation':'关系'}}}
					
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:function(){
						return $(window).height() - 150 ;
					 },
				 title:"上传列表",
				 indexColumn:true,
				 querys:{sqlId:"sql_rule_item_config"},
				 loadMsg:"数据加载中，请稍候......"
			}) ;

			$(".add-config").click(function(){
				openCenterWindow(contextPath+"/config/add/" ,450,350,function(){
					$(".grid-content").llygrid("reload",{},true);
					}) ;
			}) ;
			
			$(".edit-config").live('click',function(){
				var id = $(this).attr("val");
				openCenterWindow(contextPath+"/config/add/"+id ,450,350,function(){
					$(".grid-content").llygrid("reload",{},true);
				}) ;
				return false;
			}) ;
			
			$(".delete-config").live('click',function(){
				var id = $(this).attr("val");
				if(window.confirm("确认删除吗？")){
					$.ajax({
						type:"post",
						url:contextPath+"/config/deleteConfigItem/"+id,
						data:{},
						cache:false,
						dataType:"text",
						success:function(result,status,xhr){
							$(".grid-content").llygrid("reload",{},true) ;	
						}
					}); 
				}
				return false;
			}) ;
			
				
			
			$(".query-btn").click(function(){
				var type = $("#type").val() ;
				var querys = {} ;
					querys.type = type||"" ;
				$(".grid-content").llygrid("reload",querys) ;	
			}) ;
   	 });
  
   </script>
   
   <style>
   		*{
   			font:12px "微软雅黑";
   		}
   		
   		.message{
   			width:600px;
   			border:1px solid #CCC;
   		}
   </style>

</head>
<body>
	<div class="toolbar toolbar-auto">
		<table>
			<tr>
				<th>
					类型:
				</th>
				<td>
					<select id="type">
						<option value="">--选择--</option>
						<option value="strategy">策略</option>
						<option value="relation">关系</option>
						<option value="field" >字段</option>
						<option value="devStrategy" >开发策略</option>
					</select>
				</td>								
				<td class="toolbar-btns">
					<button class="query-btn btn btn-primary">查询</button>
					<button class="add-config btn btn-primary">添加配置项</button>
				</td>
			</tr>						
		</table>					

	</div>	
	
	<div class="grid-content">
	
	</div>
	
</body>
</html>
