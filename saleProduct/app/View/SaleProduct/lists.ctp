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
	    var treeData = {id:"root",text:"产品分类",isExpand:true,childNodes:[]} ;
	    var treeMap  = {} ;
	
	    <?php
	    	$index = 0 ;
			foreach( $categorys as $Record ){
				$sfs = $Record['sc_product_category']  ;
				$id   = $sfs['ID'] ;
				$name = $sfs['NAME']."(".$Record[0]['TOTAL'].")" ;
				$pid  = $sfs['PARENT_ID'] ;
				echo " var item$index = {id:'$id',text:'$name',isExpand:true} ;" ;
				
				echo " treeMap['id_$id'] = item$index  ;" ;
				if(empty($pid)){
					echo " item$index ['childNodes'] = item$index ['childNodes']||[] ;" ;
					echo "treeData.childNodes.push( item$index ) ;" ;
				}else{
					echo " treeMap['id_$pid'].childNodes.push( item$index ) ;" ;
				}
				$index++ ;
			} ;
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

</head>
<body>
  <div data-widget="layout" style="width:100%;height:100%;">
		<div region="center" split="true" border="true" title="货品列表" style="padding:2px;">
			<div class="toolbar toolbar-auto">
				<table style="width:100%;" class="query-table">	
					<tr>
						<th>名称：</th>
						<td>
							<input type="text" id="name" class="span2"/>
						</td>
						<th>SKU：</th>
						<td>
							<input type="text" id="sku" class="span2"/>
						</td>
						<th>仓库：</th>
						<td>
						    <select class="span2"  id="warehouseId">
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
						   <!--
							<select class="span2" id="status">
								<option value="">所有</option>
								<option value="1">正常库存</option>
								<option value="2">低于告警库存</option>
								<option value="3">低于安全库存</option>
								<option value="4">告警库存未设置</option>
								<option value="5">安全库存未设置</option>
							</select>
						  -->
						</td>
						<th></th>
						<td>
							<button class="btn btn-primary query" >查询</button>
							<button class="action add btn btn-primary">添加货品</button>
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
