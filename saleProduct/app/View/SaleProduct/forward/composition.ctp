<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>营销产品列表</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>
    <?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('../js/layout/jquery.layout');
		echo $this->Html->css('../js/tree/jquery.tree');
		
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('layout/jquery.layout');
		echo $this->Html->script('tree/jquery.tree');
		
		$user = $this->Session->read("product.sale.user") ;
		$group=  $user["GROUP_CODE"] ;
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
					
		           	{align:"center",key:"NAME",label:"名称",width:"20%",forzen:false,align:"left",format:function(val,record){
		           		return "<a href='"+record.URL+"' target='_blank'>"+val+"</a>" ;
		           	}},
		           	{align:"center",key:"REAL_SKU",label:"SKU",width:"20%"},
		           	{align:"center",key:"COM_QUANTITY",label:"数量",width:"20%"},
		           	{align:"center",key:"IMAGE_URL",label:"图片",width:"20%"},
		           	{align:"center",key:"POSITION",label:"仓库位置",width:"20%"},
		           	{align:"center",key:"BARCODE",label:"条形码",width:"20%"}
		         ],
		         ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:function(){
				 	return $(window).height() - 140 ;
				 },
				 title:"",
				 autoWidth:true,
				 indexColumn:false,
				 querys:{realSku:'<?php echo $sku;?>',sqlId:"sql_saleproduct_composition_list"},
				 loadMsg:"数据加载中，请稍候......"
			}) ;
	    
	    	$(".add-composition").click(function(){
	    		openCenterWindow("/saleProduct/index.php/saleProduct/forward/edit_composition/<?php echo $sku;?>",550,300) ;
	    	}) ;   
   	 });
   	 
   </script>
   
   <style style="text/css">
   		*{
   			font:12px "微软雅黑";
   		}
   	
   		.lly-grid-cell-input{
   		}
   		
   		.query-bar ul{
   			display:block;
   			margin_bottom:5px;
   			height:auto;
   			width:100%;
   		}
   		
   		.query-bar ul li{
   			list-style-type:none;
   			float:left;
   			padding:3px 0px;
   		}
   		
   		.query-bar ul li label{
   			float:left;
   			margin:0px 0px;
   			margin-left:15px;
   		}
   		
   		.query-bar{
   			clear:both;
   		}
   		
   		li select,li input{
   			width:auto;
   			padding:0px;
   		}
   </style>

</head>
<body style="magin:0px;padding:0px;">
			<div class="query-bar">
			   <ul>
			   	 <li>
				 	<button class="btn btn-primary btn-mini add-composition">添加打包产品</button>
				 </li>
			   </ul>
			
			</div>
			<div style="clear:both;height:5px;"></div>
			<div class="grid-content" style="width:99%;">
			</div>
	
</body>
</html>
