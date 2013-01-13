<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>入库单计划编辑</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('../js/listselectdialog/jquery.listselectdialog');
		echo $this->Html->css('default/style');
		echo $this->Html->css('../js/grid/jquery.llygrid');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('dialog/jquery.dialog');
		echo $this->Html->script('grid/query');
		echo $this->Html->script('validator/jquery.validation');	
		echo $this->Html->script('listselectdialog/jquery.listselectdialog');
		echo $this->Html->script('modules/warehouse/ram/addRma');
		echo $this->Html->script('calendar/WdatePicker');
	
		$result = null ;
		
		$orderId = $params['arg1'] ;
		$ramId   = $params['arg2'] ;
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$orders = null ;
		if(!empty($orderId)){
			$orders = $SqlUtils->exeSql("sql_order_list",array('orderId'=>$orderId) ) ;
		}
	?>
	
	<script>
	
   </script>
</head>

<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page">
		<!-- 页面标题 -->
		<div class="page-title">
			<h2>入库单信息</h2>
		</div>
		<div class="container-fluid">

	        <form id="personForm" action="#" data-widget="validator" class="form-horizontal" >
	        	<input type="hidden" id="id" value="<?php echo $result['ID'];?>"/>
	        	<input type="hidden" id="ramId" value="<?php echo $ramId;?>"/>
				<!-- panel 头部内容  此场景下是隐藏的-->
				<div class="panel apply-panel">
					<!-- panel 中间内容-->
					<div class="panel-content">
						<!-- 数据列表样式 -->
						<table class="form-table" >
							<tbody>										   
								<tr>
									<th>货品：</th>
									<td colspan=3>
									<?php
										if( !empty($orders) ){
										?>
										<table>
										<tr style="padding:0px;margin:0px;">
											<th style="padding:0px;text-align:center;">选择</th>
											<th style="padding:0px;text-align:center;">货品SKU</th>
											<th style="padding:0px;text-align:center;">货品名称</th>
											<th style="padding:0px;text-align:center;">货品图片</th>
											<th style="padding:0px;text-align:center;">购买数量</th>
										</tr>
										<?php
										if( !empty($orders) ){
											foreach( $orders as $order ){
												$order = $SqlUtils->formatObject($order) ;
												$imageUrl = $order['IMAGE_URL'] ;
												$imageUrl = str_replace("%" , "%25",$imageUrl) ;
												?>
												<tr style="padding:0px;margin:0px;">
													<td style="text-align:center;">
													<input type="radio" data-validator="required" name="realProductId" value="<?php echo $order['REAL_ID']?>"></td>
													<td key="realSku"><?php echo $order['REAL_SKU']?></td>
													<td ><?php echo $order['REAL_NAME']?></td>
													<td ><?php echo "<img style='width:25px;height:25px;' src='/saleProduct/".$imageUrl."'>"?></td>
													<td ><?php echo $order['QUANTITY_TO_SHIP']?></td>
												</tr>
												<?php
												//debug($order);
											}
										}
										?>
									</table>
										<?
										}else{
											?>
											<input type="hidden" data-validator="required" id="realProductId" 
											value=""/>
											<input type="text" data-validator="required" id="realProductName" 
											value=""/>
											<button class="btn btn-select-product">选择</button>
											<?php
										}
									?>
									
									</td>
								</tr>
								<tr>
									<th>质量：</th>
									<td colspan=3>
										良品&nbsp;<input type="radio" data-validator="required" name="quality" value="good"/>
										&nbsp;&nbsp;&nbsp;&nbsp;
										残品&nbsp;<input type="radio" data-validator="required" name="quality" value="bad"/>
									</td>
								</tr>
								<tr>
									<th>数量：</th>
									<td colspan=3>
										<input type="text" class="alert-danger" data-validator="required" name="quantity" value=""/>
									</td>
								</tr>
								<tr>
									<th>目标仓库：</th>
									<td colspan=3>
									<input data-validator="required" type="hidden" id="warehouseId" 
										value=""/>
									<input type="text" data-validator="required" id="warehouseName" readonly
										value=""/>
									<button class="btn btn-warehouse">选择</button>
									</td>
								</tr>
								<tr>
									<th>备注：</th><td  colspan=3>
										<textarea name="memo"  style="width:90%;height:50px;"><?php echo $result['MEMO'];?></textarea>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					
					<!-- panel脚部内容-->
                    <div class="panel-foot">
						<div class="form-actions">
							<button type="button" class="btn btn-primary btn-save">保存</button>
							<button type="button" class="btn btn-primary btn-save-continue">保存继续入库</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
	<div class="grid-content-rma" id="tab-rma" style="margin-top:5px;zoom:1;"></div>
</body>
</html>