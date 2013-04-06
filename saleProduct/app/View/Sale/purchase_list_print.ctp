<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>采购计划单：<?php echo $plan[0]['sc_purchase_plan']['NAME']?></title>
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
		
		$user = $this->Session->read("product.sale.user") ;
		$groupCode = $user["GROUP_CODE"] ;
		
	?>
  
   <script type="text/javascript">
   	 var type = '4' ;//查询已经审批通过

	$(function(){
		
			var index = 0 ;
			$(".grid-content-details").llygrid({
				columns:[
					{align:"center",key:"ASIN",label:"序号", width:"40",format:function(){
						return ++index ;
					} },
		           	{align:"center",key:"ASIN",label:"产品详细信息", width:"510",format:function(val,record){
		           		var knowledge = record.KNOWLEDGE||"" ;
		           		var knows = knowledge.split("<p") ;
		           		var kHtml = [] ;
		           		$(knows).each(function(index , know){
		           			if(index == 0) return ;
		           			kHtml.push("<p"+know.split("</p>")[0]+"</p>") ;
		           		}) ;
		           		
		           		var localUrl = (record.LOCAL_URL+"").replace(/\%/g,"%25") ;
		           		
		           		if(localUrl && localUrl != 'null')
		           			localUrl = '<img src="/'+fileContextPath+'/'+localUrl+'"/>' ;
		           		else
		           			localUrl = '' ;
		           		
		           		var html = '\
		           		<div>\
							<div class="product-image" style="width:152px;float:left;">'+localUrl+'</div>\
							<div class="product-base" style="width:342px;float:left;">\
								<div class="product-content product-asin">'+record.ASIN+'</div>\
								<div class="product-content product-title">'+record.TITLE+'</div>\
								<div class="product-content product-gg">'+kHtml.join("")+'</div>\
							</div>\
						</div>\
		           		' ;
		           		return html ;
		           	} },
		           	{align:"center",key:"ASIN",label:"供应商信息", width:"150",format:function(val,record){
			           	var isUsed = record.IS_USED?'（采用）':"" ;
		           		var html = '\
		           		<div>\
							<div class="product-provider">\
								<label>供应商名称'+isUsed+'：</label>\
								<div class="label-content">'+(record.PROVIDOR_NAME||"")+'</div>\
								<label>联系人：</label>\
								<div class="label-content">'+(record.PROVIDOR_CONTACTOR||"")+'</div>\
								<label>联系电话：</label>\
								<div class="label-content">'+(record.PROVIDOR_PHONE||"")+'</div>\
							</div>\
						</div>\
		           		' ;
		           		return html ;
		           	} },
		           	{align:"center",key:"ASIN",label:"采购信息", width:"70",format:function(val,record){
		           		var totalPrice = record.QUOTE_PRICE * record.PLAN_NUM ;
		           		if(totalPrice) totalPrice = totalPrice.toFixed(2) ;
		           		
		           		var html = '\
		           		<div>\
								<div class="product-purchase">\
									<label>单价：</label>\
									<div class="label-content">'+record.QUOTE_PRICE+'</div>\
									<label>采购数量：</label>\
									<div class="label-content">'+record.PLAN_NUM+'</div>\
									<label>总价：</label>\
									<div class="label-content">'+(totalPrice||"-")+'</div>\
								</div>\
						</div>\
		           		' ;
		           		return html ;
		           	} },
		           	{align:"center",key:"QUOTE_PRICE",label:"备注(手工输入)",width:"180",format:function(){
		           		return "" ;
		           	}}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},///salegrid/purchasePlanPrints
				 limit:1000,
				 //height:300,
				 title:"",
				 // indexColumn:true,
				 querys:{planId:'<?php echo $planId;?>',sqlId:"sql_purchase_plan_details_listForPrint"},
				 loadMsg:"数据加载中，请稍候......",
				 loadAfter:function(){
				 	$(".grid-toolbar-div table td:gt(1)").remove();
				 	$(".lly-grid-caption").remove();
				 	var h = $(".grid-toolbar-div table td:eq(1)") ;
				 	h.html( h.find("span").html() ).css("padding-right","20px") ;
				 }
			}) ;
   	 });
   	 
   </script>
 	<style>
   		*{
   			font:12px "微软雅黑";
   		}
   		
   		.cell-div {
   			white-space:normal;
   			padding:2px;
   			vertical-align:top;
   		}
   		
   		.product-content {
   			padding:2px;
   		}
 
   		.product-image img{
   			width:140px;
   			height:140px;
   			margin:5px;
   		}
   		
   		.product-asin , .product-title{
   			text-align:left;
   			font-weight:bold;
   		}
   		
   		.product-gg{
   			text-align:left;
   		}
   		
   		label{
   			margin-top:5px;
   			display:block;
   			text-align:left;
   		}
   		
   		.lly-grid-body-column{
   			vertical-align:top;
   		}
   		
   		.label-content{
   			font-weight:bold;
   			text-align:left;
   			text-indent:5px;
   			margin-bottom:7px;
   		}
   		
   		input{
   			border:none!important;
   			border-bottom:1px solid #CCC!important;;
   			filter:none!important;
   			width:100px;
   			-webkit-border-radius: 0px!important;;
			-moz-border-radius: 0px!important;;
			border-radius:0px!important;;
   			background:none!important;;
   			-webkit-box-shadow: none!important;;
			-moz-box-shadow:none!important;;
			box-shadow: none!important;;
			-webkit-transition: none!important;;
			-moz-transition: none!important;;
			-ms-transition: none!important;;
			-o-transition:none!important;;
			transition:none!important;;
   		}
   		
   		div.lly-grid .product-gg span{
   			font-size:11px;
   		}
   		
   		tr,td,th,.lly-grid-head{
			background:none!important;
   		}
   		.lly-grid-pager{
			display:none!important;
   		}
   		
   		table,tr,td,th,div{
			border-color: #000!important;
   		}
   		
   		.lly-grid-2-body{
			overflow:hidden!important;
   		}
   		
   		.title-bar div{
			font-weight:bold!important;	
   		}
   </style>

</head>
<body>

	<div style="padding:5px 10px;font-size:13px;font-weight:bold;">
	   <div class="row-fluid title-bar">
	   <div class="span6">
		采购计划名称：<?php echo $plan[0]['sc_purchase_plan']['NAME']?>
		</div>
		<div class="span3">
		申请人：<?php echo $plan[0][0]['USERNAME']?>
		</div>
		<div class="span3">
		申请时间：<?php echo $plan[0]['sc_purchase_plan']['CREATE_TIME']?>
		</div>
		</div>
	</div>
	<center>
	<div style="width:98%;">
	<div class="grid-content-details" style="margin-top:5px;overflow:hidden;">
	</div>
	</div>
	</center>
	<div style="padding:5px 10px;font-size:13px;font-weight:bold;">
		<table style="width:100%;">
			<tr>
				<td style="font-weight:bold;">采购经理：<input type="text" /></td>
				<td style="font-weight:bold;">会计：<input type="text" /></td>
				<td style="font-weight:bold;">总经理：<input type="text" /></td>
			</tr>
		</table>
	</div>
</body>
</html>
