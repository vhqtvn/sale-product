<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>选择采购货品</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   		include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('default/style');
		echo $this->Html->css('../js/tab/jquery.ui.tabs');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery-ui');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('grid/query');
		echo $this->Html->script('tab/jquery.ui.tabs');
		echo $this->Html->script('modules/sale/selectPurchaseProduct');
	?>
	
	<script type="text/javascript">
		var planId = '<?php echo $params['arg1'];?>' ;
	</script>
	
	<style type="text/css">
		.select-container  ul{
			list-style: none;
		}
		
		.select-container  ul li{
			float:left;
			margin:3px 5px;
		}
	</style>

</head>
<body>
	<div id="details_tab"></div>
	
	<div id="sku-input-content">
		<textarea style="width:96%;height:220px;margin:10px;" class="skus"></textarea>
		<div style="text-align:right;margin-right:20px;position:relative;">
			<div class="alert alert-error" style="float:left;">逗号分割，如1001,1002</div>
		<button class="btn btn-vali-sku btn-vali">确&nbsp;认</button></div>
	</div>
	
	<div id="product-select-content">
		<div class="toolbar toolbar-auto"  style="width:96%;margin:2px 10px;">
				<table  class="query-table">	
					<tr>
						<th>名称：</th>
						<td>
							<input type="text" id="name" class="span2"/>
						</td>
						<th>SKU：</th>
						<td>
							<input type="text" id="sku" class="span2"/>
						</td>
						<th></th>
						<td>
							
							<button class="btn btn-primary query" >查询</button>
						</td>
					</tr>						
				</table>
			</div>
		<div class="product-select-grid" style="width:96%;margin:10px;"></div>
	</div>
	
	<div id="asin-input-content">
		<textarea style="width:96%;height:220px;margin:10px;" class="asins"></textarea>
		<div style="text-align:right;margin-right:20px;position:relative;">
			<div class="alert alert-error" style="float:left;">逗号分割，如B004OB0EBQ,B004OB0E11...</div>
		<button class="btn btn-vali-asin btn-vali">确认</button></div>
	</div>
	
	<div id="dev-select-content">
		<div class="row-fluid">
			<div class="span5">
					<div class="dev-product-filter-grid" style="width:96%;margin:10px;"></div>
			</div>
			<div class="span7">
					<div class="dev-product-grid" style="width:96%;margin:10px 10px 10px 0px;"></div>
			</div>
		</div>
	</div>
	
	<div class="select-container alert" style="height:160px;margin:10px;">
		<ul style="margin:0px;padding:0px;">
		</ul>
	</div>
	
	<div class="panel-foot" style="position:fixed;bottom:0px;right:0px;width:100%;">
		<div class="form-actions">
			<button type="button" class="btn btn-primary  submit-select">提&nbsp;交</button>
			<button type="button" class="btn">关&nbsp;闭</button>
		</div>
	</div>
</body>
</html>
