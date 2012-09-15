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
 	var id = '<?php echo $id;?>';
 	var type =  '<?php echo $type;?>';
  
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
		if( type == 1 ){
			$("[name='status']").val("1,2")
		}else if( type == 2 ){
			$("[name='status']").val(4)
		}else if( type == 3 ){
			$("[name='status']").val(6)
		}
		
			$(".grid-content").llygrid({
				columns:[
		           	{align:"center",key:"ASIN",label:"ASIN", width:"8%",format:function(val,record){
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
		           	{align:"center",key:"DAY_PAGEVIEWS",label:"每日PV",width:"6%"},
	           	{align:"center",key:"FM_NUM",label:"FM数量",width:"6%"},
	           	{align:"center",key:"NM_NUM",label:"NM数量",width:"6%"},
	           	{align:"center",key:"UM_NUM",label:"UM数量",width:"6%"},
	           	{align:"center",key:"FBA_NUM",label:"FBA数量",width:"6%"},
	           	{align:"center",key:"REVIEWS_NUM",label:"Reviews数量",width:"9%"},
	           	{align:"center",key:"QUALITY_POINTS",label:"质量分",width:"5%"},
					{align:"center",key:"STATUS",label:"STATUS",width:"6%",format:function(val,record){
						var status = record.STATUS ;
						var html = [] ;

						if(status == 1 || !status  ){ //未处理 、处理中
							return "未处理" ;
						}else  if( status == 2 ){//已废弃
							return "处理中" ;
						}else  if( status == 3 ){//已废弃
							return "已废弃" ;
						}else  if(status == 4 ){ // 大于或等于4 产品专员已经处理完成
							return "产品经理代审批中" ;
						}else  if(status == 5){
							return "产品经理审批完成" ;
						}else  if(status == 6){
							return "产品经理代审批" ;
						}else  if(status == 6){
							return "宗经理审批完成" ;
						}
						
						return html.join("") ;
					}},
					{align:"center",key:"FILTER_ID",label:"操作",width:"8%",format:function(val,record){
						var status = record.STATUS ;
						
						if(type == 1){
							if(status == 1 || !status || status == 2 ){ //未处理 、处理中
								return "<a href='#' class='process-action' val='"+val+"' asin='"+record.ASIN+"'>处理</a>&nbsp;" ;
							}
							return "" ;
						}else if(type == 2){
							if(status == 4 ){ //未处理 、处理中
								return "<a href='#' class='process-action' val='"+val+"' asin='"+record.ASIN+"'>处理</a>&nbsp;" ;
							}
							return "" ;
						}else if(type == 3){
							if(status == 6 ){ //未处理 、处理中
								return "<a href='#' class='process-action' val='"+val+"' asin='"+record.ASIN+"'>处理</a>&nbsp;" ;
							}
							return "" ;
						}
					}}
		         ],
		         ds:{type:"url",content:"/saleProduct/index.php/salegrid/filter/"+id},
				 limit:30,
				 pageSizes:[10,20,30,40],
				 height:400,
				 title:"产品列表",
				 indexColumn:true,
				 querys:{status:$("[name='status']").val()},
				 loadMsg:"数据加载中，请稍候......"
			}) ;
			
			$(".process-action").live("click",function(){
				var FILTER_ID = $(this).attr("val") ;
				var asin = $(this).attr("asin") ;
				openCenterWindow("/saleProduct/index.php/sale/details1/"+FILTER_ID+"/"+asin+"/"+type,950,650) ;
			}) ;
			
			$(".product-detail").live("click",function(){
				var asin = $(this).attr("asin") ;
				openCenterWindow("/saleProduct/index.php/product/details/"+asin,950,650) ;
			})
			
			$(".query-btn").click(function(){
				var asin = $("[name='asin']").val() ;
				var title = $("[name='title']").val() ;
				var status = $("[name='status']").val() ;
				var querys = {} ;
				if(asin){
					querys.asin = asin ;
				}
				if(title){
					querys.title = title ;
				}
				
				if(status){
					querys.status = status ;
				}
				
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
<body>
	<div class="query-bar">
		<label>ASIN:</label><input type="text" name="asin"/>
		<label>标题:</label><input type="text" name="title"/>
		<label>状态:</label>
		<select name="status">
			<option value="">-</option>
			<option value="1,2,3,4,5,6,7" selected>全部</option>
			<option value="5,7" selected>审批完成</option>
			<option value="1,2" selected>产品专员待处理</option>
			<option value="4">产品经理待审批</option>
			<option value="5">产品经理审批完成</option>
			<option value="6">总经理待审批</option>
			<option value="7">总经理审批完成</option>
			<option value="3">已废弃</option>
		</select>
		
		<button class="query-btn">查询</button>
	</div>
	<div class="grid-content">
	
	</div>
</body>
</html>
