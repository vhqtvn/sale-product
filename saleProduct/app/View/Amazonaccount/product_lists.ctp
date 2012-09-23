<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>营销产品列表</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

    <?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('../grid/redmond/ui');
		echo $this->Html->css('../grid/grid');
		echo $this->Html->css('../grid/redmond/ui');
		echo $this->Html->css('../kissu/widgets/core/layout/layout');
		echo $this->Html->css('../kissu/widgets/core/tree/ui.tree');

		echo $this->Html->script('jquery');
		echo $this->Html->script('../kissu/scripts/jquery.utils');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('../grid/grid');
		echo $this->Html->script('../kissu/widgets/core/layout/jquery.layout');
		echo $this->Html->script('../kissu/widgets/core/tree/jquery.tree');
		
		$userId  = $_COOKIE["userId"] ; 
		App::import('Model', 'User') ;
		$u = new User() ;
		$user1 = $u->queryUserByUserName($userId) ;
		$user = $user1[0]['sc_user'] ;
		$group=  $user["GROUP_CODE"] ;
	?>
	
    <script type="text/javascript">
	
	var treeData = {id:"root",text:"产品分类",isExpand:true,childNodes:[]} ;
    var treeMap  = {} ;

    <?php
    	$index = 0 ;
		foreach( $categorys as $Record ){
			$sfs = $Record['sc_amazon_product_category']  ;
			
			$id   = $sfs['ID'] ;
			$name = $sfs['NAME']."(".$Record[0]['TOTAL'].")" ;
			$pid  = $sfs['PARENT_ID'] ;
			echo " var item$index = {id:'$id',text:'$name',memo:'".$sfs['MEMO']."',isExpand:true} ;" ;
			
			
			echo " treeMap['id_$id'] = item$index  ;" ;
			echo " item$index ['childNodes'] = item$index ['childNodes']||[] ;" ;
			if(empty($pid)){
				echo "treeData.childNodes.push( item$index ) ;" ;
			}else{
				echo " treeMap['id_$pid'].childNodes.push( item$index ) ;" ;
			}
			$index++ ;
		} ;
		
		echo " treeMap['id_-'] = {id:'-',text:'未分类产品',memo:'',isExpand:true} ;" ;
		echo " treeData.childNodes.push( treeMap['id_-']  ) ;" ;
	?>
   
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
	$(function(){
			$('#default-tree').tree({//tree为容器ID
				source:'array',
				data:treeData ,
				onNodeClick:function(id,text,record){
					if( id == 'root' ){
						$(".grid-content").llygrid("reload",{categoryId:"",accountId:accountId},
							{ds:{type:"url",content:"/saleProduct/index.php/amazongrid/product/"+accountId}}) ;	
					}else{
						$(".grid-content").llygrid("reload",{categoryId:id,accountId:accountId},
							{ds:{type:"url",content:"/saleProduct/index.php/amazongrid/product/"+accountId}}) ;	
					}
				}
	       }) ;
	       
	       var gridConfig = {
					columns:[
						{align:"center",key:"ID",label:"操作",width:"6%",format:function(val,record){
							var status = record.STATUS ;
							var html = [] ;
							html.push('<a href="#" class="edit-account-product" val="'+val+'"><?php echo $this->Html->image('example.gif',array("title"=>"修改")) ?></a>&nbsp;') ;
							
							return html.join("") ;
						}},
			           	{align:"left",key:"ASIN",label:"ASIN", width:"90",format:function(val,record){
			           		return "<a href='#' class='product-detail' asin='"+val+"'>"+val+"</a>" ;
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
			           	{align:"center",key:"FEED_QUANTITY",label:'库存<?php echo $this->Html->image('example.gif',array("title"=>"修改")) ?>',width:"6%",format:{type:'editor',fields:['SKU']}},
			            {align:"center",key:"PRICE",label:"Price",group:"价格",width:"6%"},
			            {align:"center",key:"FEED_PRICE",label:'Price<?php echo $this->Html->image('example.gif',array("title"=>"修改")) ?>',group:'价格',width:"6%",format:{type:'editor',fields:['SKU']}},
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
			           	}}//,
			           	//{align:"center",key:"EXEC_PRICE",label:"执行价格",group:"价格",width:"8%"}
			           	/*,
			           	{align:"center",key:"FBM_PRICE",label:"最低价",group:"FBM",width:"6%"},
			           	{align:"center",key:"FBM_COST",label:"总成本",group:"FBM",width:"6%"},
			           	//{align:"center",key:"FBM_LOWER",label:"最低销售价",group:"FBM",width:"10%"},
			           	{align:"center",key:"FBA_PRICE",label:"最低价",group:"FBA",width:"6%"},
			           	{align:"center",key:"FBA_COST",label:"总成本",group:"FBA",width:"6%"}//,
			           	//{align:"center",key:"FBA_LOWER",label:"最低销售价",group:"FBA",width:"10%"}*/
			         ],
			         ds:{type:"url",content:"/saleProduct/index.php/amazongrid/product/"+accountId},
					 limit:15,
					 pageSizes:[15,20,30,40],
					 height:420,
					 title:"",
					 indexColumn:false,
					 querys:{accountId:accountId},
					 loadMsg:"数据加载中，请稍候......"
				} ;
	       
			setTimeout(function(){
				$(".grid-content").llygrid(gridConfig) ;
			},200) ;
			
			$(".edit-account-product").live("click",function(){
				var val = $(this).attr("val") ;
				openCenterWindow("/saleProduct/index.php/amazonaccount/editAccountProduct/"+val,600,480) ;
			}) ;
				
			
			$(".product-detail").live("click",function(){
				var asin = $(this).attr("asin") ;
				openCenterWindow("/saleProduct/index.php/product/details/"+asin+"/"+accountId,950,650) ;
			}) ;
			
			$(".query-btn").click(function(){
				$(".grid-content").llygrid("reload",getQueryCondition(),
					{ds:{type:"url",content:"/saleProduct/index.php/amazongrid/product/"+accountId}}) ;	
			}) ;
			
			$(".query-reply-btn").click(function(){
				$(".grid-content").llygrid("reload",{accountId:currentAccountId,reply:'1'},
					{ds:{type:"url",content:"/saleProduct/index.php/amazongrid/productReply/"+accountId}} ) ;	
			}) ;
			
			
			$(".price-update-btn").click(function(){
				var querys = getQueryCondition() ;
				querys.type = "price" ;
				$(".grid-content").llygrid("reload",querys ) ;	
			}) ;
			
			$(".quantity-update-btn").click(function(){
				var querys = getQueryCondition() ;
				querys.type = "quantity" ;
				$(".grid-content").llygrid("reload",querys ) ;	
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
				querys.itemCondition = $("[name='itemCondition']").val() ;
				querys.fulfillmentChannel = $("[name='fulfillmentChannel']").val() ;
				querys.isFM = $("[name='isFM']").val() ;
				querys.pm = $("[name='pm']").val() ;
				querys.type = '' ;
				querys.test_status = $("[name='test_status']").val()||"" ;
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
					url:"/saleProduct/index.php/amazonaccount/saveAccountProductFeed",
					data:{type:key,sku:sku,value:val,accountId:currentAccountId},
					cache:false,
					dataType:"text",
					success:function(result,status,xhr){
						
					}
				}); 
			}) ;
   	 });
   </script>
   
   <style>
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
   			padding:5px 5px;
   			display:block;
   			height:20px;
   			line-height:20px;
   		}
   		
   		.query-bar ul li label{
   			font-weight:bold;
   		}
   		
   		.query-bar{
   			clear:both;
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
				 	<button class="query-reply-btn">重复产品过滤</button>
				 </li>
			   </ul>
			
			</div>
			<div style="clear:both;height:5px;"></div>
			<div class="grid-content" style="width:99%;">
			</div>
			
			<div>
				
				<button class="price-update-btn">显示价格待更新列表</button>
				<button class="quantity-update-btn">显示库存待更新列表</button>
			</div>
		</div>
		<div region="west" icon="icon-edit" split="true" border="true" title="营销产品分类" style="width:150px;">
			<div id="default-tree" class="tree" style="padding: 5px; "></div>
		</div>
   </div>
	
</body>
</html>
