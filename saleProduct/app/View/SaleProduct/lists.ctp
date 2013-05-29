<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>货品管理</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>
	<script>
		var deleteHtml = "" ;
	</script>
   <?php
  		 include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('default/style');
		echo $this->Html->css('../js/tab/jquery.ui.tabs');
		echo $this->Html->css('../js/layout/jquery.layout');
		echo $this->Html->css('../js/tree/jquery.tree');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery-ui');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('grid/query');
		echo $this->Html->script('modules/saleproduct/list');
		echo $this->Html->script('calendar/WdatePicker');
		echo $this->Html->script('tab/jquery.ui.tabs');
		echo $this->Html->script('layout/jquery.layout');
		echo $this->Html->script('tree/jquery.tree');
		
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
	
	    var treeData = {id:"root",text:"产品分类",isExpand:true,childNodes:[]} ;
	    var treeMap  = {} ;
	
	    <?php
	    $Utils  = ClassRegistry::init("Utils") ;
	    
	    $Utils->echoTreeScript( $categorys ,null, function( $sfs, $index ,$ss ){
	    	$id   = $sfs['ID'] ;
	    	$name = $sfs['NAME']."(".$sfs['TOTAL'].")" ;
	    	$pid  = $sfs['PARENT_ID'] ;
	    	echo " var item$index = {id:'$id',text:'$name',isExpand:true} ;" ;
	    } ) ;
	    
	    $SqlUtils  = ClassRegistry::init("SqlUtils") ;
	    
		?>
		
		$(function(){
			$('#default-tree').tree({//tree为容器ID
				source:'array',
				data:treeData ,
				onNodeClick:function(id,text,record){
					if( id == 'root' ){
						$(".grid-content").llygrid("reload",{categoryId:""}) ;
					}else{
						$(".grid-content").llygrid("reload",{categoryId:id}) ;
					}
				}
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
							<input type="text" id="searchKey" class="span3" placeHolder="输入名称、SKU、ASIN、备注进行查询"/>
						</td>
						<th>货品类型：</th>
						<td>
							<select class="span2" name="type">
								<option value="">所有</option>
								<option value="base">基本货品</option>
								<option value="package">打包货品</option>
							</select>
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
					</tr>
					<tr>
						<th>销售状态：</th>
						<td>
						    <select class="span2" name="isOnsale">
								<option value="">所有</option>
								<option value="1">在售</option>
								<option value="0">未销售</option>
							</select>
						</td>
						<th></th>
						<td>
							<button class="btn btn-primary query query-btn" >查询</button>
							<?php if( $product_add ){ ?>
							<button class="action add btn btn-primary">添加货品</button>
							<?php  } ?>
						</td>
					</tr>							
				</table>	
				<hr style="margin:2px;"/>	
			</div>
			
			<div id="details_tab"></div>
			<div class="grid-content" id="tab-content"></div>
		</div>
		<div region="west"  split="true" border="true" title="货品分类" style="width:180px;">
			<div id="default-tree" class="tree" style="padding: 5px; "></div>
		</div>
   </div>	
</body>
</html>
