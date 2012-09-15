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
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('../grid/grid');
	?>
  
   <script type="text/javascript">
 	var id = '<?php echo $planId;?>';
  
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
   
   function showImg(el){
   		var src = el.src ;
   		openCenterWindow(src,500,300) ;
   }

	$(function(){
			$(".grid-content-filter").llygrid({
				columns:[
		           	{align:"center",key:"NAME",label:"筛选名称",width:"40%",forzen:false,align:"left"},
		           	{align:"center",key:"CREATE_TIME",label:"筛选时间",width:"30%"},
		           	{align:"center",key:"STATUS57",label:"审批完成",width:"15%" }
		         ],
		         ds:{type:"url",content:"/saleProduct/index.php/salegrid/filterTask4" },
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:300,
				 title:"筛选列表",
				 indexColumn:true,
				 querys:{},
				 loadMsg:"数据加载中，请稍候......",
				 rowClick:function(rowIndex , rowData){
				 	var taskId = rowData.ID  ;
				 	$(".grid-content").llygrid("reload",{taskId:taskId}) ;
				 }
			}) ;
		
			$(".grid-content").llygrid({
				columns:[
					{align:"center",key:"ASIN",label:"",width:"10%",format:{type:"checkbox",callback:function(record){
						var checked = $(this).attr("checked");
						if(checked){
							$(".product-list ul").append("<li asin='"+record.ASIN+"'>"+record.ASIN+"</li>") ;
						}else{
							$(".product-list ul").find("[asin='"+record.ASIN+"']").remove() ;
						}
					}}},
		           	{align:"center",key:"ASIN",label:"ASIN", width:"10%",format:function(val,record){
		           		return "<a href='#' class='product-detail' asin='"+val+"'>"+val+"</a>" ;
		           	}},
		           	{align:"center",key:"LOCAL_URL",label:"Image",width:"6%",forzen:false,align:"left",format:function(val,record){
		           		
		           		if(val){
		           			val = val.replace(/%/g,'%25') ;
		           		}
		           		
		           		return "<img src='/saleProduct/"+val+"' onclick='showImg(this)' style='width:50px;height:50px;'>" ;
		           	}},
		           	{align:"center",key:"TITLE",label:"TITLE",width:"20%",forzen:false,align:"left",format:function(val,record){
		           		return "<a href='http://www.amazon.com/gp/offer-listing/"+record.ASIN+"' target='_blank'>"+val+"</a>" ;
		           	}},
		           	{align:"center",key:"DIMENSIONS",label:"DIMENSIONS",width:"10%"},
		           	{align:"center",key:"WEIGHT",label:"WEIGHT",width:"12%"},
					{align:"center",key:"COST",label:"COST",width:"9%"}
		         ],
		         ds:{type:"url",content:"/saleProduct/index.php/salegrid/filterApply/"+id},
				 limit:30,
				 pageSizes:[10,20,30,40],
				 height:300,
				 title:"产品列表",
				 indexColumn:true,
				 querys:{status:$("[name='status']").val()},
				 loadMsg:"数据加载中，请稍候......",
				 loadAfter:function(){
				 	$(".grid-checkbox").each(function(){
						var val = $(this).attr("value") ;
						if( $(".product-list ul li[asin='"+val+"']").length ){
							$(this).attr("checked",true) ;
						}
					}) ;
				 }
			}) ;
			
			$(".process-action").live("click",function(){
				var FILTER_ID = $(this).attr("val") ;
				var asin = $(this).attr("asin") ;
				openCenterWindow("/saleProduct/index.php/sale/details1/"+FILTER_ID+"/"+asin+"/"+type,950,650) ;
			}) ;
			
			$(".product-detail").live("click",function(){
				var asin = $(this).attr("asin") ;
				openCenterWindow("/saleProduct/index.php/product/details/"+asin,950,650) ;
			}) ;
			
			$(".select-filter").click(function(){
				openCenterWindow("/saleProduct/index.php/sale/filter/4",800,600) ;
			}) ;
			
			$(".query-btn-filter").click(function(){
				var filterName = $("[name='filterName']").val() ;
				var querys = {} ;
				if(filterName){
					querys.filterName = filterName ;
				}
				
				$(".grid-content-filter").llygrid("reload",querys) ;	
			}) ;
			
			$(".query-btn").click(function(){
				var asin = $("[name='asin']").val() ;
				var title = $("[name='title']").val() ;
				var querys = {} ;
				if(asin){
					querys.asin = asin ;
				}
				if(title){
					querys.title = title ;
				}
				
				$(".grid-content").llygrid("reload",querys) ;	
			}) ;
			
			$(".save-product-list").click(function(){
				var planId = id ;
				var asins = [] ;
				$(".product-list li").each(function(){
					asins.push( $(this).attr("asin") ) ;
				}) ;
				
				$.ajax({
					type:"post",
					url:"/saleProduct/index.php/sale/savePurchasePlanProducts",
					data:{
						planId:planId,
						asins:asins.join(",")
					},
					cache:false,
					dataType:"text",
					success:function(result,status,xhr){
						window.opener.location.reload() ;
						window.close() ;
					}
				}); 
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
   			width:80px;
   			background:#AACCDD;
   			padding:2px;
   			border:1px solid #00ff00;
   			cursor:pointer;
   		}
   </style>

</head>
<body>
  <table>
     <tr>
     	<td  style="width:370px">
     		<div class="query-bar">
				<label>刷选名称:</label><input type="text" name="filterName"/>
				<button class="query-btn-filter">查询</button>
			</div>
     		<div class="grid-content-filter">
			</div>
     	</td>
     	<td  style="width:600px">
     		<div class="query-bar">
				<label>ASIN:</label><input type="text" name="asin"/>
				<label>标题:</label><input type="text" name="title"/>
				<button class="query-btn">查询</button>
			</div>
			<div class="grid-content">
			</div>
     	</td>
     </tr>
     <tr>
     <td colspan=2>
     	<div class="product-list" style="border:1px solid #CCC;width:100%;height:100px;">
     		<ul></ul>
     	</div>
     </td>
      
     </tr>
      <tr>
     <td colspan=2 style="text-align:right;padding-right:20px;">
     	<button class="save-product-list">保存产品到采购列表</button>
     </td>
      
     </tr>
     
  </table>
	
</body>
</html>
