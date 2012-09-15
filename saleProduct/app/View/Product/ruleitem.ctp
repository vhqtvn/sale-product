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
					{align:"center",key:"ID",label:"操作",width:"10%",format:function(val,record){
						var key = record.TYPE ;
						
						if( key == 'strategy' ){
							var html = [] ;
							html.push("<a href='#' class='edit-config' val='"+val+"'>修改</a>&nbsp;") ;
							html.push("<a href='#' class='delete-config' val='"+val+"'>删除</a>") ;
							return html.join("");
						}
						return "";
					}},
		           	{align:"center",key:"ID",label:"编号", width:"5%"},
		           	{align:"center",key:"LABEL",label:"名称",width:"10%",forzen:false,align:"left"},
		           	{align:"center",key:"KEY",label:"值",width:"25%"},
		           	{align:"center",key:"MEMO",label:"备注",width:"30%"},
		           	{align:"center",key:"TYPE",label:"类型",width:"10%"}
					
		         ],
		         ds:{type:"url",content:"/saleProduct/index.php/grid/config"},
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:400,
				 title:"上传列表",
				 indexColumn:true,
				 querys:{name:"hello",name2:"world"},
				 loadMsg:"数据加载中，请稍候......"
			}) ;

			$(".add-config").click(function(){
				openCenterWindow("/saleProduct/index.php/config/add/" ,450,350) ;
			}) ;
			
			$(".edit-config").live('click',function(){
				var id = $(this).attr("val");
				openCenterWindow("/saleProduct/index.php/config/add/"+id ,450,350) ;
				return false;
			}) ;
			
			$(".delete-config").live('click',function(){
				var id = $(this).attr("val");
				if(window.confirm("确认删除吗？")){
					$.ajax({
						type:"post",
						url:"/saleProduct/index.php/config/deleteConfigItem/"+id,
						data:{},
						cache:false,
						dataType:"text",
						success:function(result,status,xhr){
							$(".grid-content").llygrid("reload") ;	
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
	<div class="query-bar">
				<label>类型:</label>
				<select id="type">
					<option value="">--选择--</option>
					<option value="strategy">策略</option>
					<option value="relation">关系</option>
					<option value="field" >字段</option>
				</select>
				
				<button class="query-btn">查询</button>
				<button class="add-config">添加配置项</button>
	</div>
	
	<div class="grid-content">
	
	</div>
	
</body>
</html>
