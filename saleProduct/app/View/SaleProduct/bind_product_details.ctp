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
	
	var treeData = {id:"root",text:"产品分类",isExpand:true,childNodes:[]} ;
    var treeMap  = {} ;

    <?php
    	$index = 0 ;
		foreach( $categorys as $Record ){
			$sfs = $Record['sc_amazon_product_category']  ;
			
			$id   = $sfs['ID'] ;
			$name = $sfs['NAME']."(".$Record[0]['TOTAL'].")" ;
			$pid  = $sfs['PARENT_ID'] ;
			echo " var item$index = {id:'$id',text:'$name',memo:'".$sfs['MEMO']."',isExpand:true} ;\n" ;
			
			
			echo " treeMap['id_$id'] = item$index  ;\n" ;
			$index++ ;
		} ;
		$index = 0 ;
		foreach( $categorys as $Record ){
			$sfs = $Record['sc_amazon_product_category']  ;
			$id   = $sfs['ID'] ;
			$name = $sfs['NAME']."(".$Record[0]['TOTAL'].")" ;
			$pid  = $sfs['PARENT_ID'] ;
			echo " item$index ['childNodes'] = item$index ['childNodes']||[] ;\n" ;
			if(empty($pid)){
				echo "treeData.childNodes.push( item$index ) ;\n" ;
			}else{
				echo " treeMap['id_$pid'].childNodes = treeMap['id_$pid'].childNodes||[] ;\n" ;
				echo " treeMap['id_$pid'].childNodes.push( item$index ) ;\n" ;
			}
			$index++ ;
		} ;
		
		echo " treeMap['id_uncategory'] = {id:'uncategory',text:'未分类产品',memo:'',isExpand:true} ;\n" ;
		echo " treeData.childNodes.push( treeMap['id_uncategory']  ) ;\n" ;
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
							{ds:{type:"url",content:"/saleProduct/index.php/grid/query/"+accountId}}) ;	
					}else{
						currentCategoryId = id ;
						currentCategoryText = text ;
						$(".grid-content").llygrid("reload",getQueryCondition(),
							{ds:{type:"url",content:"/saleProduct/index.php/grid/query/"+accountId}}) ;	
					}
				}
	       }) ;
	       
	       var gridConfig = {
					columns:[
						{align:"center",key:"SKU",label:"操作",width:"6%",format:{type:"checkbox",render:function(record){
								if(record.checked >=1){
									$(this).attr("checked",true) ;
								}
						}}},
						{align:"center",key:"SKU",label:"SKU",width:"8%"},
			           	{align:"left",key:"ASIN",label:"ASIN", width:"90",format:function(val,record){
			           		var memo = record.MEMO||"" ;
			           		return "<a href='#' class='product-detail' title='"+memo+"' asin='"+val+"' sku='"+record.SKU+"'>"+val+"</a>" ;
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
			           	{align:"center",key:"IS_FM",label:"FM产品",width:"8%" }
			           	
			         ],
			         ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
					 limit:15,
					 pageSizes:[15,20,30,40],
					 height:420,
					 autoWidth:true,
					 title:"",
					 indexColumn:false,
					 querys:{accountId:accountId,realSku:'<?php echo $sku;?>',sqlId:"sql_saleproduct_selelctproduct_list"},
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
			
			$(".save-btn").click(function(){
				
				var checkedRecords = $(".grid-content").llygrid("getSelectedRecords",{key:"SKU",checked:true},true) ;
				var items = [] ;
				$(checkedRecords).each(function(index,item){
					items.push(item.ACCOUNT_ID+"|"+item.SKU) ;
				}) ;
				
				var uncheckedRecords = $(".grid-content").llygrid("getSelectedRecords",{key:"SKU",checked:false},true) ;
				var unitems = [] ;
				$(uncheckedRecords).each(function(index,item){
					unitems.push(item.ACCOUNT_ID+"|"+item.SKU) ;
				}) ;
				
				if( window.confirm("确认保存？") ){
					$.ajax({
						type:"post",
						url:"/saleProduct/index.php/saleProduct/saveSelectedProducts/" ,
						data:{items:items.join(","),unitems:unitems.join(","),realSku:'<?php echo $sku;?>'},
						cache:false,
						dataType:"text",
						success:function(result,status,xhr){
							alert("保存成功!");
							window.location.reload();
						}
					});
				}
				
				
			}) ;
			
			$(".product-detail").live("click",function(){
				var asin = $(this).attr("asin") ;
				var sku = $(this).attr("sku") ;
				openCenterWindow("/saleProduct/index.php/product/details/"+asin+"/"+accountId+"/"+sku,950,650) ;
			}) ;
			
			$(".query-btn").click(function(){
				$(".grid-content").llygrid("reload",getQueryCondition(),
					{ds:{type:"url",content:"/saleProduct/index.php/grid/query/"+accountId}}) ;	
			}) ;
			
			function getQueryCondition(){
				var asin = $("[name='asin']").val() ;
				var title = $("[name='title']").val() ;
				var querys = {} ;
				querys.reply = 0 ;
				querys.accountId = currentAccountId||'-----';
				querys.asin = asin ;
				querys.title = title ;
				querys.sku = $("[name='sku']").val() ;
				
				//isPriceQuery isQuantityQuery
				if( currentCategoryId=='-'||currentCategoryId=='uncategory'){
					querys.uncategory = 1;
				}else if(currentCategoryId){
					querys.categoryId = currentCategoryId;
				}
				
				querys.sqlId = "sql_account_product_list" ;
				
				return querys ;
			}
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
			<div class="query-bar">
			   <ul>
			   	 <li><label>ASIN:</label><input type="text" name="asin" style="width:100px"/></li>
			   	 <li><label>名称:</label><input type="text" name="title" style="width:100px"/></li>
				<li><label>SKU:</label><input type="text" name="sku" style="width:100px"/></li>
			   	 <li>
				 	<button class="btn btn-primary btn-mini query-btn">查询</button>
				 	<button class="btn btn-primary btn-mini save-btn">保存</button>
				 </li>
			   </ul>
			
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
