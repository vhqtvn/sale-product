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

	$(function(){
			$(".grid-content").llygrid({
				columns:[
					{align:"center",key:"ID",label:"名称", width:"5%",format:{type:'checkbox',callback:function(record){
						var checked = $(this).attr("checked");
						if(checked){
							$(".product-list ul").append("<li supplier='"+record.ID+"'>"+record.NAME+"</li>") ;
						}else{
							$(".product-list ul").find("[supplier='"+record.ID+"']").remove() ;
						}
					}}},
		           	{align:"center",key:"NAME",label:"名称", width:"15%"},
		           	{align:"center",key:"ADDRESS",label:"地址",width:"15%"},
		           	{align:"center",key:"CONTACTOR",label:"联系人",width:"8%"},
		           	{align:"center",key:"PHOME",label:"联系电话",width:"12%"},
		           	{align:"center",key:"EMAIL",label:"EMAIL",width:"10%"},
		           	{align:"center",key:"ZIP_CODE",label:"邮编",width:"10%"},
		           	{align:"center",key:"URL",label:"网站地址",width:"15%",format:function(val){
		           		if(!val) return "" ;
		           		return "<a href='"+val+"' target='_blank'>"+val+"</a>" ;
		           	}}
		         ],
		         ds:{type:"url",content:"/saleProduct/index.php/supplier/grid/"},
				 limit:30,
				 pageSizes:[10,20,30,40],
				 height:200,
				 title:"产品列表",
				 indexColumn:true,
				 querys:{},
				 loadMsg:"数据加载中，请稍候......",
				 loadAfter:function(){
				 	$(".grid-checkbox").each(function(){
						var val = $(this).attr("value") ;
						if( $(".product-list ul li[supplier='"+val+"']").length ){
							$(this).attr("checked",true) ;
						}
					}) ;
				 }
			}) ;
			
			$(".action-update").live("click",function(){
				var id = $(this).attr("val") ;
				openCenterWindow("/saleProduct/index.php/supplier/add/"+id,400,300) ;
			})
			
			$(".query-btn").click(function(){
				var name = $("[name='name']").val() ;
				var querys = {} ;
				if(name){
					querys.name = name ;
				}
				$(".grid-content").llygrid("reload",querys) ;	
			}) ;
			
			$(".add-btn").click(function(){
				openCenterWindow("/saleProduct/index.php/supplier/add/",400,300) ;
			}) ;
			
			$(".save-product-supplier").click(function(){
				var suppliers = [] ;
				$(".product-list li").each(function(){
					suppliers.push( $(this).attr("supplier") ) ;
				}) ;
				
				window.opener.doSelectedValue(suppliers) ;
				window.close() ;
			}) ;
   	 });
   </script>
   
    <style>
   		*{
   			font:12px "微软雅黑";
   		}
   		
   		.product-list ul{
   			list-style:none;
   			margin:3px;padding:0px;
   			display:block;
   			width:100%;
   		}
   		
   		.product-list ul li{
   			float:left;
   			width:auto;
   			background:#AACCDD;
   			padding:2px;
   			border:1px solid #00ff00;
   			cursor:pointer;
   			margin:2px;
   		}
   </style>

</head>
<body>
	<div class="query-bar">
		<label>供应商名称:</label><input type="text" name="name"/>
		<button class="query-btn">查询</button>
		<button class="add-btn">添加供应商</button>
	</div>
	<div class="grid-content">
	</div>
	<div class="product-list" style="border:1px solid #CCC;width:100%;height:100px;">
     		<ul>
     		</ul>
     </div>
     <button class="save-product-supplier">保存产品供应商</button>
</body>
</html>
