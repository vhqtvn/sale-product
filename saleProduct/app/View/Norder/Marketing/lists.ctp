<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
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
	?>
  
   <script type="text/javascript">
   	 var type = '4' ;//查询已经审批通过

	$(function(){
			$(".grid-content").llygrid({
				columns:[
		           //	{align:"center",key:"ID",label:"编号", width:"5%"},
		           	{align:"center",key:"NAME",label:"试销计划名称",width:"15%",forzen:false,align:"left"},
		           	{align:"center",key:"PLAN_TIME",label:"试销开始时间",width:"13%"},
		           	{align:"center",key:"MEMO",label:"备注",width:"13%"},
		           	{align:"center",key:"USERNAME",label:"创建人",width:"8%"},
		           	{align:"center",key:"CREATE_TIME",label:"创建时间",width:"13%"},
					{align:"center",key:"ID",label:"操作",width:"20%",format:function(val,record){
						var status = record.STATUS ;
						var html = [] ;
						html.push("<a href='#' class='add-outer-product' val='"+val+"'>添加产品</a>&nbsp;") ;
						html.push("<a href='#' class='add-product' val='"+val+"'>添加审批产品</a>&nbsp;") ;
						html.push("<a href='#' class='export-product' val='"+val+"'>导出</a>&nbsp;") ;
						return html.join("") ;
					}}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},//salegrid/purchasePlan
				 limit:5,
				 pageSizes:[10,20,30,40],
				 height:100,
				 title:"筛选列表",
				 indexColumn:true,
				 querys:{sqlId:"sql_marketing_test_list"},
				 loadMsg:"数据加载中，请稍候......",
				 rowClick:function(rowIndex , rowData){
				 	var planId = rowData.ID  ;
				 	$(".grid-content-details").llygrid("reload",{planId:planId}) ;
				 }
			}) ;
			
			$(".export-product").live("click",function(){
				var val = $(this).attr("val") ;//采购计划ID
				$("#exportIframe").attr("src",contextPath+"/marketing/exportForMarketingTestDetails/"+val) ;
			}) ;
			
			$(".add-product").live("click",function(){
				var val = $(this).attr("val") ;//采购计划ID
				openCenterWindow(contextPath+"/marketing/selectMarketingTestProducts/"+val,1050,600) ;
			}) ;
			
			$(".add-outer-product").live("click",function(){
				var val = $(this).attr("val") ;
				openCenterWindow(contextPath+"/marketing/addMarketingTestOuterProduct/"+val,600,400) ;
			})
			
			$(".create-plan").click(function(){
				openCenterWindow(contextPath+"/marketing/createMarketingTestPlan/",600,400) ;
			}) ;
			
			$(".grid-content-details").llygrid({
				columns:[
					//{align:"center",key:"ID",label:"编号",width:"4%"},
		           	{align:"center",key:"ASIN",label:"ASIN", width:"10%",format:function(val,record){
		           		return "<a href='#' class='product-detail' asin='"+val+"'>"+val+"</a>" ;
		           	}},
		           	{align:"center",key:"LOCAL_URL",label:"Image",width:"6%",forzen:false,align:"left",format:function(val,record){
		           		
		           		if(val){
		           			val = val.replace(/%/g,'%25') ;
		           			return "<img src='/"+fileContextPath+"/"+val+"' onclick='showImg(this)' style='width:20px;height:20px;'>" ;
		           		}
		           		return "" ;
		           		
		           	}},
		           	{align:"center",key:"TITLE",label:"TITLE",width:"20%",forzen:false,align:"left",format:function(val,record){
		           		return "<a href='"+contextPath+"/page/forward/Platform.asin/"+record.ASIN+"' target='_blank'>"+val+"</a>" ;
		           	}},
		           	{align:"center",key:"DAY_PAGEVIEWS",label:"每日PV",width:"10%"},
		           	{align:"center",key:"TARGET_PRICE",label:"AMAZON最低价",width:"10%"},
		            {align:"center",key:"GUIDE_PRICE",label:"试销价格",width:"10%"},
		           	{align:"center",key:"ID",label:"操作",width:"8%",format:function(val,record){
						var status = record.STATUS ;
						var html = [] ;
						html.push("<a href='#' class='edit-action' val='"+val+"'>编辑</a>&nbsp;") ;
						html.push("<a href='#' class='del-action' asin='"+record.ASIN+"' planId='"+record.PLAN_ID+"' val='"+val+"'>删除</a>&nbsp;") ;
						return html.join("") ;
						
					}}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:30,
				 pageSizes:[10,20,30,40],
				 height:300,
				 title:"",
				 indexColumn:true,
				 querys:{planId:'-----',sqlId:"sql_marketing_test_details_list"},
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
			
			$(".edit-action").live("click",function(){
				var val = $(this).attr("val") ;//采购计划ID
				openCenterWindow(contextPath+"/marketing/editMarketingTestProduct/"+val,600,450) ;
			}) ;

			$(".del-action").live("click",function(){
				var val = $(this).attr("val") ;//采购计划ID
				var planId = $(this).attr("planId") ;
				var asin = $(this).attr("asin") ;
				if(window.confirm("确认删除该试销产品["+asin+"]吗？")){
					$.ajax({
						type:"post",
						url:contextPath+"/marketing/deleteMarketingTestProduct",
						data:{
							id:val
						},
						cache:false,
						dataType:"text",
						success:function(result,status,xhr){
							$(".grid-content-details").llygrid("reload",{planId:planId}) ;
						}
					});
				}
			}) ;
			
			
			$(".product-detail").live("click",function(){
				var asin = $(this).attr("asin") ;
				openCenterWindow(contextPath+"/product/details/"+asin,950,650) ;
			});
			
   	 });
   	 
   </script>
   

</head>
<body>
	<button class="create-plan btn btn-primary">创建试销计划</button>
	<div class="grid-content">
	</div>
	<div class="grid-content-details" style="margin-top:5px;">
	</div>
	
	<iframe src="" id="exportIframe" style="display:none"></iframe>
</body>
</html>
