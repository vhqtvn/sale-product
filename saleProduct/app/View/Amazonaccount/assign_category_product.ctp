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

   var accountId = '<?php echo $accountId ;?>' ;
   
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
	var currentAccountId = accountId ;
	var currentCategoryId = "<?php echo $categoryId ;?>" ;
	$(function(){
			var querys = getQueryCondition() ;
		
	       var gridConfig = {
					columns:[
						{align:"center",key:"SKU",label:"操作",width:"6%",format:{type:"checkbox",render:function(record){
							if(record.checked >=1){
								$(this).attr("checked",true) ;
							}
						}}},
			           	{align:"left",key:"ASIN",label:"ASIN", width:"90",format:function(val,record){
			           		return "<a href='#' class='product-detail' asin='"+val+"' sku='"+record.SKU+"'>"+val+"</a>" ;
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
			           	{align:"center",key:"SKU",label:"SKU",width:"8%"},
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
			         ds:{type:"url",content:"/saleProduct/index.php/grid/query/"},
					 limit:15,
					 pageSizes:[15,20,30,40],
					 height:420,
					 title:"",
					 indexColumn:false,
					 querys:querys,
					 loadMsg:"数据加载中，请稍候......"
				} ;
	       
			setTimeout(function(){
				gridConfig.querys = getQueryCondition() ;
				$(".grid-content").llygrid(gridConfig) ;
			},200) ;	
			
			$(".product-detail").live("click",function(){
				var asin = $(this).attr("asin") ;
				var sku = $(this).attr("sku") ;
				openCenterWindow("/saleProduct/index.php/product/details/"+asin+"/"+accountId+"/"+sku,950,650) ;
			}) ;
			
			$(".query-btn").click(function(){
				$(".grid-content").llygrid("reload",getQueryCondition(),
					{ds:{type:"url",content:"/saleProduct/index.php/grid/query/"+accountId}}) ;	
			}) ;
			
			$(".checked-btn").click(function(){
				var querys = getQueryCondition() ;
				querys.checked = 1 ;
				$(".grid-content").llygrid("reload",querys,
					{ds:{type:"url",content:"/saleProduct/index.php/grid/query/"+accountId}}) ;	
			}) ;
			
			$(".unchecked-btn").click(function(){
				var querys = getQueryCondition() ;
				querys.checked = 0 ;
				$(".grid-content").llygrid("reload",querys,
					{ds:{type:"url",content:"/saleProduct/index.php/grid/query/"+accountId}}) ;	
			}) ;
			
			
			$(".save-btn").click(function(){
				var checked = $(".grid-content").llygrid("getSelectedValue",{key:"SKU",checked:true},true) ;
				var nochecked = $(".grid-content").llygrid("getSelectedValue",{key:"SKU",checked:false},true) ;
				
				$.ajax({
					type:"post",
					url:"/saleProduct/index.php/amazonaccount/saveCategoryProducts" ,
					data:{checked_skus:checked.join(","),unchecked_skus:nochecked.join(","),accountId:currentAccountId,categoryId:currentCategoryId},
					cache:false,
					dataType:"text",
					success:function(result,status,xhr){
						alert("保存成功!");
					}
				});
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
				
				querys.categoryId = currentCategoryId;
				querys.sqlId = "sql_account_product_assign_category_list" ;
				
				return querys ;
			}
			
   	 });
   </script>
      <style type="text/css">
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
	<div class="widget-class" widget="layout" style="width:100%;height:100%;">
		<div region="center" split="true" border="true" title="产品列表" style="padding:2px;">
			<div class="query-bar">
			   <ul>
			   	 <li><label>ASIN:</label><input type="text" name="asin" style="width:100px"/></li>
			   	 <li><label>名称:</label><input type="text" name="title" style="width:100px"/></li>
			   	 <li><label>库存:</label>从<input type="text" name="quantity1" style="width:50px"/>到<input type="text" name="quantity2" style="width:50px"/></li>
			   	 <li><label>价格:</label>从<input type="text" name="price1" style="width:50px"/>到<input type="text" name="price2" style="width:50px"/></li>
			   	 <li><label>销售渠道:</label><select name='fulfillmentChannel'>
					<option value=''>全部</option>
					<option value='AMAZON_NA'>Amazon</option>
					<option value='Merchant'>Merchant</option>
					<option value='-'>未知</option>
				</select></li>
				<li><label>使用程度:</label><select name='itemCondition'>
					<option value=''>全部</option>
					<option value=11>New</option>
					<option value=1>Used</option>
					<option value='-'>未知</option>
				</select></li>
				 <li><label>FM商品:</label><select name='isFM'>
					<option value=''>全部</option>
					<option value="FM">FM</option>
					<option value="NEW">NEW</option>
				</select></li>
				 <li>
				 <label>排名:</label><select name='pm'>
					<option value=''>全部</option>
					<option value="1">1</option>
					<option value="2">2</option>
					<option value="3">3</option>
					<option value="4">4</option>
					<option value="5">5</option>
					<option value="other">其他</option>
				</select>
				 </li>
				 <li>
				 	<button class="query-btn">查询</button>
				 	<button class="checked-btn">已选择</button>
				 	<button class="unchecked-btn">未选择</button>
				 	<button class="save-btn">保存</button>
				 </li>
			   </ul>
			</div>
			<div style="clear:both;height:5px;"></div>
			<div class="grid-content" style="width:99%;">
			</div>
		</div>
   </div>
	
</body>
</html>
