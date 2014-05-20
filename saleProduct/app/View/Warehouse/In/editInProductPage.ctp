<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>入库计划产品选择</title>
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
		
		$inId = $params['arg1'] ;
		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$warehoseIn = $SqlUtils->getObject("sql_warehouse_in_getById",array("id"=>$inId)) ;
		$sourceWarehoueId = $warehoseIn['SOURCE_WAREHOUSE_ID'] ;
		$accountId = $warehoseIn['ACCOUNT_ID'] ;//ACCOUNT_ID
	?>
  
   <script type="text/javascript">
		var accountId = '<?php echo $accountId;?>' ;

		$(function(){
			
			$(".select-product").live("click",function(){
				var pickId = $(this).attr("pickId") ;
				openCenterWindow(contextPath+"/order/selectPickedProduct/"+pickId,1000,600) ;
			});
			
			$(".grid-content").llygrid({
				columns:[
		           	//{align:"center",key:"ID",label:"编号", width:"10%"},
		           	{align:"center",key:"LISTING_SKU",label:"操作",width:"3%",format:{type:"checkbox",callback:function(record){
							var checked = $(this).attr("checked") ;
							var itemId = record.REAL_ID+"__"+record.SKU ;
							if( checked ){
								var li = $("<li  class='item'>"+record.REAL_SKU+"("+record.SKU+")</li>").appendTo(".selected-container ul")  ;
								li.attr("item",record.REAL_ID+"__"+record.SKU)
									.attr("realId",record.REAL_ID)
									.attr("listingSku",record.SKU) 
									.attr("accountId",record._ACCOUNT_ID)
									.attr("quantity",record.QUANTITY||0) ;
							}else{
								$(".selected-container ul") .find("li[item='"+itemId+"']").remove();
							}
			         }}},
					{align:"center",key:"IMAGE_URL",label:"",width:"3%",forzen:false,align:"left",format:{type:'img'}},
		           	{align:"center",key:"REAL_NAME",label:"货品名称",width:"20%",forzen:false,align:"left"},
		           	{align:"center",key:"REAL_SKU",label:"货品SKU",width:"12%",forzen:false,align:"left"},
		           	{align:"center",key:"SKU",label:"Listing SKU",width:"15%"},
		           	{align:"center",key:"QUANTITY",label:"本地库存数量",width:"10%"},
		           	{align:"center",key:"TOTAL_SUPPLY_QUANTITY",label:"账户总库存",width:"8%"},
		           	{align:"center",key:"IN_STOCK_SUPPLY_QUANTITY",label:"账户可售库存",width:"10%"},
		           	{align:"center",key:"SALES_FOR_THELAST14DAYS",label:"14天销量",width:"8%"},
		           	{align:"center",key:"SALES_FOR_THELAST30DAYS",label:"30天销量",width:"8%"}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:300,
				 autoWidth:false,
				 title:"产品列表",
				 querys:{sqlId:"sc_warehouse_in_new_listFBAProduct_ALL",accountId:'<?php echo $accountId;?>',warehouseId:'<?php echo $sourceWarehoueId;?>',includeOnly:1},
				 loadMsg:"数据加载中，请稍候......",
				 loadAfter:function(){
					
				}
			}) ;

			$(".btn-commit").click(function(){
					var items = [] ;
					$(".selected-container ul").find("li.item").each(function(){
						items.push( {
								"accountId":$(this).attr("accountId"),
								"listingSku":$(this).attr("listingSku"),
								"realId":$(this).attr("realId"),
								"quantity":$(this).attr("quantity")||0
							} ) ;
					}) ;
					//alert( $.json.encode(items) ) ;
					$.dataservice("model:Warehouse.In.saveInProduct",{inId:'<?php echo $inId;?>',items : items },function(result){
						if(result){
							alert(result) ;
						}else{
							window.close();
						}
					});
			}) ;

			$(".query-btn").click(function(){
				var json = $(".query-container").toJson() ;
				var includeOnly = json.includeOnly ;
				if( includeOnly == 1 ){
					//json.sqlId= "sc_warehouse_in_new_listFBAProduct" ;
					json.includeOnly = 1 ;
				}else{
					//json.sqlId= "sc_warehouse_in_new_listFBAProduct_ALL" ;
					json.includeOnly = "" ;
				}
				$(".grid-content").llygrid("reload",json) ;
			}) ;
		});
   </script>

	<style>
   	.selected-container{
		margin-top:5px;
		border:1px solid #CCC;
		height:150px;
   	}
   </style>
</head>
<body>
	<div class="toolbar toolbar-auto toolbar1 query-container">
		<table>
			<tr>
				<td>
					<input type="text" id="searchKey" placeHolder="输入ASIN、产品名称、开发标题" style="width:300px;"/>
				</td>
				<td>
					<input type="checkbox"  name="includeOnly"  value="1"  checked/>&nbsp;只包括存在对应库存货品
				</td>								
				<td class="toolbar-btns">
					<button class="query-btn btn btn-primary"  >查询</button>
				</td>
			</tr>						
		</table>
	</div>	
	<div class="grid-content">
	</div>
	<div class="selected-container">
			<ul></ul>
	</div>
	<div class="panel-foot">
		<div class="form-actions">
			<button type="button" class="btn btn-primary btn-commit">提&nbsp;交</button>
		</div>
	</div>
</body>
</html>
