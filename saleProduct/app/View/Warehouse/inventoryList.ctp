<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>库存列表</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>
	<script>
		var deleteHtml = "" ;
	</script>
   <?php
		include_once ('config/config.php');
  		include_once ('config/header.php');
  		
		echo $this->Html->script('modules/warehouse/inventoryList');
		echo $this->Html->css('../js/modules/tag/tagutil');
		echo $this->Html->script('modules/tag/tagutil');
		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$categorys = $SqlUtils->exeSql("sql_saleproduct_categorytree",array() ) ;
		
		$security  = ClassRegistry::init("Security") ;
		
		$user = $this->Session->read("product.sale.user") ;
		$loginId = $user["LOGIN_ID"] ;
		
		$product_add = $security->hasPermission($loginId , 'product_add') ;
		$product_edit = $security->hasPermission($loginId , 'product_edit') ;
		$product_giveup = $security->hasPermission($loginId , 'product_giveup') ;
		$view_giveup_product = $security->hasPermission($loginId , 'view_giveup_product') ;
		$product_stock_quanity_assign = $security->hasPermission($loginId , 'product_stock_quanity_assign') ;
		//销售状态变更权限
		$product_onsale =  $security->hasPermission($loginId , 'product_onsale') ;

		$user = $this->Session->read("product.sale.user") ;
		$loginId = $user["GROUP_CODE"] ;
		if($loginId == 'general_manager'){
		?>
		<script>
			var deleteHtml = "<a href='#' class='action giveup btn'   type=3>删除</a>" ;
		</script>
		<?php
		}
	?>
	
	<script type="text/javascript">
		$product_edit = <?php echo $product_edit?'true':'false';?> ;
		$product_giveup = <?php echo $product_giveup?'true':'false';?> ;
		$view_giveup_product = <?php echo $view_giveup_product?'true':'false';?> ;
		$product_stock_quanity_assign = <?php echo $product_stock_quanity_assign?'true':'false';?> ;
		$product_onsale = <?php echo $product_onsale?'true':'false';?> ;


		function loadTree(){
			$('#default-tree').tree({//tree为容器ID
				//source:'array',
				//data:treeData ,
				rootId  : 'root',
				rootText : '产品分类',
				expandLevel:2,
				asyn:false,
				CommandName : 'sqlId:sql_saleproduct_categorytree',
				recordFormat:true,
				dataFormat:function(data){
					data.push({id:'uncategory',text:'未分类产品',memo:'',isExpand:true});
					return data;
				},
				nodeFormat:function(record){
					if(record.id=='root' ||record.id == 'uncategory') return record ;
					record.text = record.text+"("+record.TOTAL+")"
					return record ;
				},
				params : {
					//accountId: accountId
				},
				onNodeClick:function(id,text,record){
					var uncategory = "" ;
					if(id == 'uncategory'){
						id="" ;
						uncategory = 1 ;
					}else{
						uncategory = "" ;
					}
					
					if( id == 'root' ){
						$(".grid-content").llygrid("reload",{categoryId:"",uncategory:uncategory}) ;
					}else{
						$(".grid-content").llygrid("reload",{categoryId:id,uncategory:uncategory}) ;
					}
				}
	       }) ;
		}
		$(function(){
			loadTree() ;

		     DynTag.listByType("productTag",function(entityType,tagId){
		    	 $(".grid-content").llygrid("reload",{tagId:tagId},true) ;
			}) ;
		});
   </script>
   
   <style>
   	.span1_5{
		width:100px;
   	}
   </style>

</head>
<body>
  <div data-widget="layout" style="width:100%;height:100%;">
		<div region="center" split="true" border="true" title="货品列表" style="padding:2px;">
			<div class="toolbar toolbar-auto">
				<table style="width:100%;" class="query-table">	
					<tr>
						<th>关键字：</th>
						<td>
							<input type="text" id="searchKey" class="span5" placeHolder="输入名称、SKU、ASIN、备注进行查询"/>
						</td>
						<th>仓库：</th>
						<td>
						    <select class="span1_5"  id="warehouseId">
						    	<option value="">全部</option>
						   <?php 
						     // sql_warehouse_lists
						     $warehouses = $SqlUtils->exeSql("sql_warehouse_lists",array()) ;
                             foreach($warehouses as $w){
                             	  $w = $SqlUtils->formatObject( $w ) ;
                             	  echo "<option value='".$w['ID']."'>".$w['NAME']."</option>" ;
                             }
						   ?>
							</select>
						</td>
						<td>
							<button class="btn btn-primary query query-btn" >查询</button>
						</td>
					</tr>							
				</table>	
				<hr style="margin:2px;"/>	
			</div>
			
			<div class="grid-content"  ></div>
			<div class="grid-content-details"  style="margin-top:3px;"></div>
		</div>
		<div region="west"  split="true" border="true" title="货品分类" style="width:180px;">
			<div id="tree-wrap">
			<div id="default-tree" class="tree" style="padding: 5px; "></div>
			</div>
		</div>
   </div>	
</body>
</html>
