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


	var selectedUser = null ;

	$(function(){
			$(".grid-content").llygrid({
				columns:[
		           	{align:"center",key:"ID",label:"ID", width:"5%",forzen:true},
		           	{align:"center",key:"NAME",label:"用户姓名",width:"20%",forzen:false,align:"left"},
		           	{align:"center",key:"LOGIN_ID",label:"登录ID",width:"20%"},
		           	{align:"center",key:"GROUP_NAME",label:"用户组",width:"20%"}
		         ],
		         ds:{type:"url",content:"/saleProduct/index.php/grid/users"},
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:200,
				 title:"用户列表",
				 rowDblClick:function(type,record){//
				 	selectedUser = record ;
				 	$(".ui-state-focus").html(record.NAME) ;
				 },
				 indexColumn:false,
				 // querys:{name:"hello",name2:"world"},
				 loadMsg:"数据加载中，请稍候......"
			}) ;
			
			$(".confirmBtn").click(function(){
				window.opener.addUser(selectedUser) ;
				window.close() ;
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
	<div class="ui-state-highlight" style="margin-bottom:3px;padding:2px 10px;"> 双击行选择用户 </div>
	<div class="grid-content">
	
	</div>
	<div class="ui-state-focus" style="margin-top:3px;padding:2px 10px;width:200px;float:left;">&nbsp; </div>
	<div style="width:100px;float:right;"><button class="confirmBtn">确定</button></div>
</body>
</html>
