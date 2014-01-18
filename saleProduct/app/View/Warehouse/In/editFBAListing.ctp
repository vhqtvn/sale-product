<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>添加产品到包装箱</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   		include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('../js/listselectdialog/jquery.listselectdialog');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('grid/query');
		echo $this->Html->script('validator/jquery.validation');	
		echo $this->Html->script('listselectdialog/jquery.listselectdialog');
		echo $this->Html->script('calendar/WdatePicker');
		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$boxId = $params['arg1'] ;
		
		$boxInstance = $SqlUtils->getObject("sql_warehouse_box_getById",array('boxId'=>$boxId)) ;
		
		$inId = $boxInstance['IN_ID'] ;
		
		$warehoseIn = $SqlUtils->getObject("sql_warehouse_in_getById",array("id"=>$inId)) ;
		//debug($warehoseIn) ;
		$inSourceType = $warehoseIn['IN_SOURCE_TYPE'] ;
		$sourceWarehouseId = $inSourceType =='warehouse'? $warehoseIn['SOURCE_WAREHOUSE_ID']:"" ;
		
		
		//debug($warehoseIn) ;
		
	?>
	<script type="text/javascript">
   	var boxId = '<?php echo $params['arg1'] ;?>' ;	 
   	var sourceWarehouseId = '<?php echo $sourceWarehouseId ;?>' ;
   	var inId = '<?php echo $inId ;?>' ;	 
   </script>
   
   <style type="text/css">
		.detail-table td,.detail-table th,.inventory-detail-table td,.inventory-detail-table th{
			padding:2px;
			height:22px;
		}
	</style>
	<script>
   $(function(){
		$(".view-detail").click(function(){
			var pid = $(this).attr("pid") ;
			if( $("."+pid+"-row").is(":visible") ){
				$("."+pid+"-row").hide() ;
			}else{
				$("."+pid+"-row").show() ;
			}
		}) ;

		$(".check-product-row-1  input:checkbox").click(function(){
				if($(this).attr("checked")){
					$(this).closest("tr").next().show() ;
				}else{
					$(this).closest("tr").next().hide() ;
				}
		}) ;

		$("[name='quantity']").blur(function(){
			var val = $(this).val() ;
			var json = $(this).closest(".check-product-row").toJson() ;
			var listingSku = json.listingSku ;
			statusRender(listingSku) ;
		}).trigger("blur") ;

		function statusRender(listingSku){
			//信息显示
			var json = $("."+listingSku+"-data").toJson() ;
			$("."+listingSku+"-log").hide() ;
			var quantity = parseInt( json.quantity||0) ;
			var realNum = parseInt( json.realNum||0) ;
			var reqNum = parseInt( json.reqNum||0) ;
			if(  quantity > realNum ){
				$("."+listingSku+"-log").show().find("td").html("入库数量超过最大库存数！") ;
				return ;
			}

			if( quantity > reqNum ){
				$("."+listingSku+"-log").show().find("td").html("入库数量大于需求数量！") ;
				//return ;
			}

			if(  reqNum >  realNum ){
				$("."+listingSku+"-log").show().find("td").html("需求数量大于实际库存数量！") ;
				//return ;
			}
			//需求状态  
			var _quantity = quantity ;
			$("."+listingSku+"-row").find(".req-row").each(function(){
					var _j = $(this).toJson() ;
					var _fq = _j.fixQuantity  ;
					_quantity = _quantity - _fq ;
					$(this).removeClass("alert-success") ;
					if( _quantity >=0 ){
						$(this).addClass("alert-success") ;
					}else{
						$(this).addClass("alert-danger") ;
					}
			}) ;
			var _quantity = quantity ;
			//锁定库存状态
			$("."+listingSku+"-row").find(".inventory-row").each(function(){
				var _j = $(this).toJson() ;
				var _fq = _j.realInventory  ;//实际库存
				var temp = _quantity ;
				_quantity = _quantity - _fq ;
				$(this).removeClass("alert-success") ;
				if( _quantity >=0 ){
					$(this).find("[name='lockQuantity']").val(_fq) ;
					$(this).addClass("alert-success") ;
				}else{
					if(temp >0){
						$(this).find("[name='lockQuantity']").val(temp) ;
					}
					$(this).addClass("alert-danger") ;
				}
		   }) ;
	    }

		$(".btn-save").click(function(){
			var row = [] ;
			$(".check-product-row-1").each(function(){
				if( $(this).find(":checkbox").attr("checked") ){
						var _ = $(this).next().toJson() ;
						var listingSku = _.listingSku ;
						//获取锁定库存数据
						var locks = [] ;
						
						$("."+listingSku+"-row").find(".inventory-row").each(function(){
							var _j = $(this).toJson() ;
							if( _j.lockQuantity >0  ){
								locks.push( _j ) ;
							}
						}) ;

						_.locks = locks ;
						row.push( _ ) ;
				}
			}) ;

			if( row.length >0  ){
				var json = {} ;
				json.boxId = boxId ;
				json.inId = inId ;
				json.items = row ;

				alert( $.json.encode(json) ) ; ;
				return ;
				$.dataservice("model:Warehouse.In.doSaveBoxProductForFBAReq",json,function(result){
					//window.close() ;
				});
			}
			return false ;
		}) ;
  }) ;
   </script>
</head>

<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page"  style="padding-bottom:50px;">
		<!-- 页面标题 -->
		<table class="form-table  table table-bordered" >
			<tr class="rk-product-row">
				<th style="width:4%;"></th>
				<th style="width:12%;">图片</th>
				<th style="width:30%;">产品名称</th>
				<th style="width:10%;">产品SKU</th>
				<th style="width:10%;">Listing SKU</th>
				<th style="width:10%;">需求数量</th>
				<th style="width:25%;">实际库存数量</th>
			</tr>
	<?php  
	
	//列出可出库到目标仓库的产品（出库应该不按照需求计划来）
	$reqProductList = $SqlUtils->exeSqlWithFormat("sql_supplychain_inventory_canToWarehouseForFBA",array('sourceWarehouseId'=>$warehoseIn['SOURCE_WAREHOUSE_ID'])) ;
	//debug($reqProductList) ;
	//$reqProductList = $SqlUtils->exeSqlWithFormat("sql_supplychain_requirement_listProductFBMByWarehouseId",array('warehouseId'=>$warehoseIn['WAREHOUSE_ID'])) ;
	foreach($reqProductList as $product){ 
		$imgUrl = '/'.$fileContextPath.'/'.$product['IMAGE_URL']  ;
		//获取可以发货到目标仓库的库存明细
		$inventoryItemList = $SqlUtils->exeSqlWithFormat("sql_supplychain_inventoryItem_canToWarehouseForFBA",
						array('sourceWarehouseId'=>$warehoseIn['SOURCE_WAREHOUSE_ID'],'accountId'=>$warehoseIn['ACCOUNT_ID'],"listingSku"=>$product['LISTING_SKU'])) ;

		//获取需求明细
		$reqProductItemList = $SqlUtils->exeSqlWithFormat("sql_supplychain_requirement_listFBAItemByWarehouseId",
					array('accountId'=>$warehoseIn['ACCOUNT_ID'],"listingSku"=>$product['LISTING_SKU'])) ;

		//获取自由库存
		$reqProductFree = $SqlUtils->getObject("sql_supplychain_requirement_listFreeByWarehouseId",
				array('sourceWarehouseId'=>$warehoseIn['SOURCE_WAREHOUSE_ID'],"realId"=>$product['ID'])) ;

		//$inventoryItemList[] = $reqProductFree ;

		$realNum = 0 ;
		//debug($inventoryItemList);
		foreach($inventoryItemList as $item){ 
			$canInventory = $item['QUANTITY'] - $item['LOCK_QUANTITY'] ;
			//$_ = $item['QUANTITY'] ;
			//if(empty($_)) $_ = 0 ;
			$realNum = $canInventory +$realNum ;
			
		}
		
		$freeNum = 0 ;
		if(!empty( $reqProductFree )){
			$freeNum = $reqProductFree['QUANTITY'] - $reqProductFree['LOCK_QUANTITY'] ;
			//$freeNum = $reqProductFree['QUANTITY']  ;
			$realNum = $realNum + $freeNum;
		}
		
		//实际库存不存在，不显示
		if( $realNum <=0  ) continue ;
		
		//计算需求库存
		$reqQuantity = 0 ;
		$reqItemId = "" ;
		foreach( $reqProductItemList as $rpi ){
			$fixQuantity = $rpi['FIX_QUANTITY'] ;
			$reqQuantity = $reqQuantity + $fixQuantity ;
			
			if(empty($reqItemId)){
				$reqItemId = $rpi['ID'] ;
			}else{
				$reqItemId =$reqItemId.','.$rpi['ID'] ;
			}
		}
		
		
		$outNum = 0 ;
		if( $reqQuantity > $realNum  ){
			$outNum = $realNum ;
		}else{
			$outNum = $reqQuantity ;
		}
		?>
			
					<tr class="check-product-row-1">
						<td>
							<input type="checkbox"/>
						</td>
						<td>
							<?php if( !empty($product['IMAGE_URL'] ) ){ ?>
							<img style="width:25px;height:25px;" src="<?php echo $imgUrl;?>"/>
							<?php } ?>
						</td>
						<td ><?php echo $product['NAME'] ?></td>
						<td ><?php echo $product['REAL_SKU'] ?></td>
						<td ><?php echo $product['LISTING_SKU'] ?></td>
						<td><?php echo $reqQuantity ?></td>
						<td>
								<?php echo $realNum;?>(其中自由库存<?php echo $freeNum;?>)&nbsp;&nbsp;
								<a href="#"  pid='<?php echo $product['LISTING_SKU'];?>'  class="view-detail">明细</a>
						</td>
					</tr>
					<tr class="check-product-row hide  <?php echo $product['LISTING_SKU'];?>-data"  >
						<td  colspan="7" style="padding-left:40px;">
								<input type="hidden" name="reqNum"  value="<?php echo $reqQuantity;?>"/>
								<input type="hidden" name="realNum"  value="<?php echo $realNum;?>"/>
								<input type="hidden" name="reqItemIds"  value="<?php echo $reqItemId;?>"/>
								<input type="hidden" name="realId"  value="<?php echo $product['ID'];?>"/>
								<input type="hidden" name="accountId"  value="<?php echo $warehoseIn['ACCOUNT_ID'];?>"/>
								<input type="hidden" name="listingSku"  value="<?php echo $product['LISTING_SKU'];?>"/>
								<span>出库库存：<span/><input type="text"  name="quantity"  value="<?php echo $outNum;?>" style="width:50px;"/>
								供货时间：<input data-validator="required"  data-widget="calendar" 
											type="text" id="DELIVERY_TIME"   style="width:100px;"/>
								货品跟踪码： <input data-validator="required" type="text" id="PRODUCT_TRACKCODE"
											value=""  style="width:130px;"/>
						</td>
					</tr>
					<tr class="hide  errorLog  <?php echo $product['LISTING_SKU'];?>-log">
					   	<td  colspan="7"  class="alert alert-danger" style="padding:2px;height:18px;">
					   			实际库存总量小于需求库存(需求总库存【<?php echo $reqQuantity;?>】,实际库存【<?php echo $realNum;?>】)
					    </td>
					 </tr>
					<tr class="rk-product-row  hide  <?php echo $product['LISTING_SKU'];?>-row">
					   	<td  colspan="7">
					   		<table  class="inventory-detail-table  table">
					   				<caption>实际库存明细</caption>
					   				<tr>
					   					<th>Listing SKU</th>
					   					<th>账号</th>
					   					<th>渠道</th>
					   					<th>可用库存数量</th>
					   					<th>锁定数量</th>
					   				</tr>
					   				
								<?php   foreach($inventoryItemList as $item){ ?>
								   	<tr  class="inventory-row">
										<td>
											<input type="hidden" name="inventoryId"  value="<?php echo $item['INVENTORY_ID'];?>"/>
											<input type="hidden" name="realInventory"  value="<?php echo $item['QUANTITY'];?>"/>
											<?php echo $item['LISTING_SKU'] ?>
										</td>
										<td><?php echo $item['ACCOUNT_NAME'] ?></td>
										<td><?php echo $item['FULFILLMENT_CHANNEL'] ?></td>
										<td><?php echo $item['QUANTITY'] - $item['LOCK_QUANTITY'] ;?></td>
										<td><input type="text" style="width:50px;"  name="lockQuantity"  value="0"/></td>
								    </tr>
								<?php    }?>
								<tr  class="inventory-row">
										<td>
											自由库存
										</td>
										<td></td>
										<td></td>
										<td><?php echo $freeNum?></td>
										<td><input type="text" style="width:50px;"  name="lockQuantity"  value="0"/></td>
								    </tr>
					        </table>
					   		<table  class="detail-table">
					   				<caption>需求明细</caption>
					   				<tr>
					   					<th>所属需求</th>
					   					<th>Listing SKU</th>
					   					<th>账号</th>
					   					<th>渠道</th>
					   					<th>优先级</th>
					   					<th>需求数量</th>
					   					<th>计划采购数量</th>
					   					<th>实际入库数量</th>
					   				</tr>
								<?php   foreach($reqProductItemList as $item){ ?>
								   	<tr  class="req-row">
								   		<td>
								   			<input type="hidden" name="fixQuantity"  value="<?php echo $item['FIX_QUANTITY'];?>"/>
											<?php echo $item['REQ_NAME'] ?>
										</td>
										<td>
											<?php echo $item['LISTING_SKU'] ?>
										</td>
										<td><?php echo $item['ACCOUNT_NAME'] ?></td>
										<td><?php echo $item['FULFILLMENT_CHANNEL'] ?></td>
										<td><?php echo $item['URGENCY'] ?></td>
										<td><?php echo $item['FIX_QUANTITY'];?></td>
										<td><?php echo $item['PURCHASE_QUANTITY'];?></td>
										<td><?php echo $item['REAL_PURCHASE_QUANTITY'];?></td>
								</tr>
								<?php    }?>
					   </table>
				</td>
			</tr>
	<?php  	}  ?>
	</table>
	<div>
		
	</div>
		<div class="panel-foot"  style="background:#FFF;">
			<div class="form-actions">
					<button type="button" class="btn btn-primary btn-save">提&nbsp;交</button>
			</div>
		</div>
	</div>
</body>
</html>