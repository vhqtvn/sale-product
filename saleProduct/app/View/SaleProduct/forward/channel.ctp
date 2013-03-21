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

	var currentAccountId = '' ;
	var currentCategoryId = "" ;
	var currentCategoryText = "" ;
	$(function(){
	
	       var gridConfig = {
					columns:[
						/*{align:"center",key:"SKU",label:"操作",width:"6%",format:{type:"checkbox",render:function(record){
								if(record.checked >=1){
									$(this).attr("checked",true) ;
								}
						}}},*/
						{align:"center",key:"CHANNEL_NAME",label:"ACCONT",width:"8%"},
						{align:"center",key:"SKU",label:"SKU",width:"8%",format:function(val,record){
							return val||record.REL_SKU ;
						}},
			           	{align:"left",key:"ASIN",label:"ASIN", width:"90",format:function(val,record){
			           		var memo = record.MEMO||"" ;
			           		return "<a href='#' class='product-detail' title='"+memo+"' asin='"+val+"' sku='"+record.SKU+"'>"+(val||'')+"</a>" ;
			           	}},
			           	{align:"center",key:"LOCAL_URL",label:"Image",width:"6%",forzen:false,align:"left",format:function(val,record){
			           		if(val){
			           			val = val.replace(/%/g,'%25') ;
			           		}else{
			           			return "" ;
			           		}
			           		return "<img src='/saleProduct/"+val+"' onclick='showImg(this)' style='width:25px;height:25px;'>" ;
			           	}},
			           	{align:"center",key:"TITLE",label:"TITLE",width:"10%",forzen:false,align:"left",format:function(val,record){
			           		return "<a href='http://www.amazon.com/gp/offer-listing/"+record.ASIN+"' target='_blank'>"+(val||'')+"</a>" ;
			           	}},
			           	
			           	{align:"center",key:"DAY_PAGEVIEWS",label:"每日PV",width:"8%",format:function(val){
			           		if(!val) return '-' ;
			           		return Math.round(val) ;
			           	}},
			           	{align:"center",key:"FULFILLMENT_CHANNEL",label:"销售渠道",width:"8%"},
			           	{align:"center",key:"ITEM_CONDITION",label:"使用程度",width:"8%",format:function(val){
			           		if(val == 1) return "Used" ;
			           		if(val == 11) return 'New' ;
			           		return '' ;
			           	}},
			           	{align:"center",key:"IS_FM",label:"FM产品",width:"8%" }
			           	
			         ],
			         ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
					 limit:10,
					 pageSizes:[10,15,20,30,40],
					 height:350,
					 autoWidth:true,
					 title:"",
					 indexColumn:false,
					 querys:{id:'<?php echo $id;?>',sqlId:"sql_saleproduct_channel_list"},
					 loadMsg:"数据加载中，请稍候......",
					 loadAfter:function(){
						//$(".country-area-flag").parents("tr").css("background","#EEE") ;
						//$(".country-area-flag").parents("tr").css("background","#EEE") ;
						//$(".country-area-flag").parents("tr").css("background","#EEE") ;
					 }
				} ;
	       
			setTimeout(function(){
				$(".grid-content").llygrid(gridConfig) ;
			},200) ;

			$(".query-btn").click(function(){
				$(".grid-content").llygrid("reload",getQueryCondition(),
					{ds:{type:"url",content:"/saleProduct/index.php/grid/query/"}}) ;	
			}) ;
			
			function getQueryCondition(){
				var asin = $("[name='asin']").val() ;
				var title = $("[name='title']").val() ;
				var querys = {} ;
				querys.reply = 0 ;
				querys.asin = asin ;
				querys.title = title ;
				querys.sku = $("[name='sku']").val() ;
				return querys ;
			}
			
			$(".add-channel-product").click(function(){
				openCenterWindow("/saleProduct/index.php/saleProduct/bindProduct/<?php echo $id;?>/1",1000,640) ;
			}) ;
			
			$(".add-sku").click(function(){
				openCenterWindow("/saleProduct/index.php/saleProduct/bindProduct/<?php echo $id;?>/2",1000,640) ;
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
				 	<button class="btn btn-primary btn-mini add-btn add-channel-product">选择渠道产品</button>
				 	<button class="btn btn-primary btn-mini add-btn add-sku">相关产品SKU</button>
				 </li>
			   </ul>
			
			</div>
			<div style="clear:both;height:5px;"></div>
			<div class="grid-content" style="width:99%;">
			</div>
	
</body>
</html>
