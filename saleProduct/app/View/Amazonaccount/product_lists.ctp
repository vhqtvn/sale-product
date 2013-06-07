<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>营销产品列表</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>
    <?php
    include_once ('config/config.php');
    
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
	
	var treeData = {id:"root",text:"产品分类",isExpand:true,childNodes:[]} ;
    var treeMap  = {} ;

    <?php
    $Utils  = ClassRegistry::init("Utils") ;
    
    $Utils->echoTreeScript( $categorys ,null, function( $sfs, $index ,$ss ){
    	$id   = $sfs['ID'] ;
			$name = $sfs['NAME']."(".$sfs['TOTAL'].")" ;
			$pid  = $sfs['PARENT_ID'] ;
    	echo " var item$index = {id:'$id',text:'$name',memo:'".$sfs['MEMO']."',isExpand:true} ;\n" ;
    } ) ;
    
    echo " treeMap['id_uncategory'] = {id:'uncategory',text:'未分类产品',memo:'',isExpand:true} ;\n" ;
    echo " treeData.childNodes.push( treeMap['id_uncategory']  ) ;\n" ;
    
	?>
   
   var accountId = '<?php echo $accountId ;?>' ;
   
	var currentAccountId = accountId ;
	var currentCategoryId = "" ;
	var currentCategoryText = "" ;
	$(function(){
			$('#default-tree').tree({//tree为容器ID
				source:'array',
				data:treeData ,
				onNodeClick:function(id,text,record){
					if( id == 'root' ){
						currentCategoryId = "" ;
						currentCategoryText = "" ;
						$(".grid-content").llygrid("reload",getQueryCondition(),
							{ds:{type:"url",content:contextPath+"/grid/query/"+accountId}}) ;	
					}else{
						currentCategoryId = id ;
						currentCategoryText = text ;
						$(".grid-content").llygrid("reload",getQueryCondition(),
							{ds:{type:"url",content:contextPath+"/grid/query/"+accountId}}) ;	
					}
				}
	       }) ;
	       
	       var gridConfig = {
					columns:[
						{align:"center",key:"ID",label:"状态",width:"6%",format:function(val,record){
							var status = record.STATUS ;
							var html = [] ;
							if(record.COUNTRY >= 1){
								html.push("<span title='非美国卖家' class='country-area-flag'></span>") ;	
							}
							
							if(record.WARNING && record.WARNING.indexOf("rights_warning") >= 0 ){
								html.push("<span title='维权预警' class='rights-warning-flag'></span>") ;	
							}
							
							if(record.WARNING && record.WARNING.indexOf("ranking_warning")>=0 ){
								html.push("<span title='排名预警' class='ranking-warning-flag'></span>") ;	
							}
							return html.join("") ;
						}},
						/*
						{align:"center",key:"ID",label:"操作",width:"6%",format:function(val,record){
							var status = record.STATUS ;
							var html = [] ;
							html.push('<a href="#" class="edit-account-product" val="'+val+'"><?php echo $this->Html->image('example.gif',array("title"=>"修改")) ?></a>&nbsp;') ;
							return html.join("") ;
						}},*/
						{align:"left",key:"SKU",label:"产品SKU",width:"8%"},
						{align:"left",key:"REAL_SKU",label:"货品SKU",width:"8%",format:function(val,record){
								return "<a data-widget='dialog' data-options='{width:1000,height:650}' href='"+contextPath+"/saleProduct/details/10007/sku'>"+(val||"")+"</a>"
						}},
			           	{align:"left",key:"ASIN",label:"ASIN", width:"90",format:function(val,record){
			           		var memo = record.MEMO||"" ;
			           		return "<a href='#' class='product-detail' title='"+memo+"' asin='"+val+"' sku='"+record.SKU+"'>"+(val||"")+"</a>" ;
			           	}},
			           	{align:"center",key:"LOCAL_URL",label:"图片",width:"6%",forzen:false,align:"left",format:{type:'img'}},
			           	{align:"center",key:"P_TITLE",label:"产品标题",width:"10%",forzen:false,align:"left",format:function(val,record){
			           		return "<a href='http://www.amazon.com/gp/offer-listing/"+record.ASIN+"' target='_blank'>"+val+"</a>" ;
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
			           	{align:"center",key:"IS_FM",label:"FM产品",width:"8%" },
			           	{align:"center",key:"QUANTITY",label:"库存",width:"6%"},
			           	 {align:"center",key:"PRICE",label:"Price",group:"价格",width:"6%"},
			           {align:"center",key:"SHIPPING_PRICE",label:"Ship",group:"价格",width:"6%"},
			           	{align:"center",key:"FBM_PRICE__",label:"排名",group:"价格",width:"8%",format:function(val,record){
			           		var pm = '' ;
			           		if(record.FULFILLMENT_CHANNEL != 'Merchant') pm = record.FBA_PM  ;
			           		else if( record.ITEM_CONDITION == '1' ) pm =   record.U_PM||'-'  ;
			           		else if( record.IS_FM == 'FM' ) pm =   record.F_PM||'-'  ;
			           		else if( record.IS_FM == 'NEW' ) pm =   record.N_PM||'-'  ;
			           		if(!pm || pm == '0') return '-' ;
			           		return pm ;
			           	}},
			           	{align:"center",key:"FBM_PRICE__",label:"最低价",group:"价格",width:"8%",format:function(val,record){
			           		if(record.FULFILLMENT_CHANNEL != 'Merchant') return record.FBA_PRICE ;
			           		if( record.ITEM_CONDITION == '1' ) return  record.FBM_U_PRICE ;
			           		if( record.IS_FM == 'FM' ) return  record.FBM_F_PRICE ;
			           		if( record.IS_FM == 'NEW' ) return  record.FBM_N_PRICE ;
			           		return "" ;
			           	}},
			           	{align:"center",key:"EXEC_PRICE",label:"最低限价",group:"价格",width:"8%"},
			           	{align:"center",key:"STRATEGY_LABEL",label:"策略",group:"价格",width:"11%",format:function(val){
			           		return val||"-" ;
			           	}}
			         ],
			         //ds:{type:"url",content:contextPath+"/amazongrid/product/"+accountId},
			         ds:{type:"url",content:contextPath+"/grid/query"},
					 limit:15,
					 pageSizes:[15,20,30,40],
					 height:420,
					 title:"",
					 indexColumn:false,
					 querys:{accountId:accountId,sqlId:"sql_account_product_list"},
					 loadMsg:"数据加载中，请稍候......",
					 loadAfter:function(){
						 $(".grid-content").uiwidget();
					 }
				} ;
	       
			setTimeout(function(){
				$(".grid-content").llygrid(gridConfig) ;
			},200) ;
			
			$(".edit-account-product").live("click",function(){
				var val = $(this).attr("val") ;
				openCenterWindow(contextPath+"/amazonaccount/editAccountProduct/"+val,600,480) ;
			}) ;
			
			$(".product-detail").live("click",function(){
				var asin = $(this).attr("asin") ;
				var sku = $(this).attr("sku") ;
				openCenterWindow(contextPath+"/product/details/"+asin+"/"+accountId+"/"+sku,950,650) ;
			}) ;
			
			$(".query-btn").click(function(){
				$(".grid-content").llygrid("reload",getQueryCondition(),
					{ds:{type:"url",content:contextPath+"/grid/query/"+accountId}}) ;	
			}) ;
			
			$(".query-reply-btn").click(function(){
				$(".grid-content").llygrid("reload",{accountId:currentAccountId,reply:'1',sqlId:"sql_account_product_double_list"},
					{ds:{type:"url",content:contextPath+"/grid/query/"+accountId}} ) ;	
			}) ;
			
			$(".product-category-btn").click(function(){
				if( currentCategoryId ){
					openCenterWindow(contextPath+"/amazonaccount/assignCategoryProduct/"+accountId+"/"+currentCategoryId,950,650) ;
				}
			}) ;
			
			
			
			function getQueryCondition(){
				var asin = $("[name='asin']").val() ;
				var title = $("[name='title']").val() ;
				var querys = {} ;
				querys.reply = 0 ;
				querys.accountId = currentAccountId||'-----';
				querys.asin = asin ;
				querys.title = title ;
				querys.quantity1 = $("[name='quantity1']").val() ;
				querys.quantity2 = $("[name='quantity2']").val() ;
				querys.price1 = $("[name='price1']").val() ;
				querys.price2 = $("[name='price2']").val() ;
				//querys.itemCondition = $("[name='itemCondition']").val() ;
				//querys.fulfillmentChannel = $("[name='fulfillmentChannel']").val() ;
				querys.isFM = $("[name='isFM']").val() ;
				var pm = $("[name='pm']").val() ;
				if(pm=='other') pm = 0 ;
				querys.pm = pm ;
				querys.type = '' ;
				querys.test_status = $("[name='test_status']").val()||"" ;
				querys.warning = $("[name='warning']").val()||"" ;
				//querys.limitArea = $("[name='limitArea']").val()||"" ;
				
				var limitArea = $("[name='limitArea']").val()||"" ;
				if(limitArea == 1){
					querys.outAemricanArea = 1 ;
				}else if(limitArea == 2){
					querys.inAemricanArea = '0' ;
				}
				
				var fulfillmentChannel = $("[name='fulfillmentChannel']").val() ;
				if(fulfillmentChannel == '-'){
					querys.fulfillmentChannelNull = 1 ;
				}else if(fulfillmentChannel){
					querys.fulfillmentChannel = fulfillmentChannel ;
				}
				
				var itemCondition = $("[name='itemCondition']").val() ;
				if(itemCondition == '-'){
					querys.itemContidtionNull = 1 ;
				}else if(itemCondition){
					querys.itemCondition = itemCondition ;
				}
				
				//isPriceQuery isQuantityQuery
				if( currentCategoryId=='-'||currentCategoryId=='uncategory'){
					querys.uncategory = 1;
				}else if(currentCategoryId){
					querys.categoryId = currentCategoryId;
				}
				
				querys.sqlId = "sql_account_product_list" ;
				
				return querys ;
			}
			
			$(".lly-grid-cell-input").live("blur",function(){
				var sku = $(this).attr("SKU")||$(this).attr("sku") ;
				var price = "" ;
				var quantity = "" ;
				var key = $(this).attr("key") ;
				var val = $(this).val() ;
					
				$.ajax({
					type:"post",
					url:contextPath+"/amazonaccount/saveAccountProductFeed",
					data:{type:key,sku:sku,value:val,accountId:currentAccountId},
					cache:false,
					dataType:"text",
					success:function(result,status,xhr){
					}
				}); 
			}) ;
   	 });
   </script>
   
   <style style="text/css">
   		*{
   			font:12px "微软雅黑";
   		}
   		
   		.rights-warning-flag{
   			width:10px;
   			height:10px;
   			margin-top:5px;
   			background:red;
   			display:block;
   			float:left;
   		}
   		
   		.ranking-warning-flag{
   			width:10px;
   			height:10px;
   			margin-top:5px;
   			background:#800000;
   			display:block;
   			float:left;
   		}
   		
   		.country-area-flag{
   			width:10px;
   			height:10px;
   			margin-top:5px;
   			background:#0000FF;
   			display:block;
   			float:left;
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
	<div data-widget="layout" style="width:100%;height:100%;">
		<div region="center" split="true" border="true" title="产品列表" style="padding:2px;">
			<div class="toolbar toolbar-auto query-bar">
				<table style="width:100%;" class="query-table">	
					<tr>
						<th>ASIN:</th>
						<td>
							<input type="text" name="asin" style="width:100px"/>
						</td>
						<th>名称:</th>
						<td>
							<input type="text" name="title" style="width:100px"/>
						</td>
						<th>价格:</th>
						<td>
							从<input type="text" name="price1" style="width:50px"/>到<input type="text" name="price2" style="width:50px"/>
						</td>
						<th>销售渠道:</th>
						<td>
							<select name='fulfillmentChannel'  class="span2">
								<option value=''>全部</option>
								<option value='AMAZON_NA'>Amazon</option>
								<option value='Merchant'>Merchant</option>
								<option value='-'>未知</option>
							</select>
						</td>
					</tr>
					<tr>	
						<th>使用程度:</th>
						<td>
							<select name='itemCondition'  style="width:100px">
								<option value=''>全部</option>
								<option value=11>New</option>
								<option value=1>Used</option>
								<option value='-'>未知</option>
							</select>
						</td>
						<th>FM商品:</th>
						<td>
							<select name='isFM'   style="width:100px">
								<option value=''>全部</option>
								<option value="FM">FM</option>
								<option value="NEW">NEW</option>
							</select>
						</td>
						<th>排名:</th>
						<td>
							<select name='pm'   style="width:100px">
								<option value=''>全部</option>
								<option value="1">1</option>
								<option value="2">2</option>
								<option value="3">3</option>
								<option value="4">4</option>
								<option value="5">5</option>
								<option value="other">其他</option>
							</select>
						</td>
						<td colspan="2">
							<button class="btn btn-primary query query-btn" >查询</button>
							
							<button class="btn btn-primary btn-mini query-reply-btn">重复产品过滤</button>
				 			<button class="btn btn-primary btn-mini product-category-btn">编辑分类产品</button>
						</td>
					</tr>						
				</table>
			</div>
			
			<div style="clear:both;height:5px;"></div>
			<div class="grid-content" style="width:99%;">
			</div>
			
		</div>
		<div region="west" icon="icon-edit" split="true" border="true" title="营销产品分类" style="width:200px;">
			<div id="default-tree" class="tree" style="padding: 5px; "></div>
		</div>
   </div>
	
</body>
</html>
