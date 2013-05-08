<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>成本编辑</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   include_once ('config/config.php');
   
   		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('validator/jquery.validation');
		
		$security  = ClassRegistry::init("Security") ;
		$loginId   = $user['LOGIN_ID'] ;
		
		$COST_EDIT = $security->hasPermission($loginId , 'COST_EDIT') ;
		$COST_VIEW_PURCHASE_COST = $security->hasPermission($loginId , 'COST_VIEW_PURCHASE_COST') ;
		$COST_VIEW_POSTAGE = $security->hasPermission($loginId , 'COST_VIEW_POSTAGE') ;
		$COST_VIEW_PRODUCT_REL = $security->hasPermission($loginId , 'COST_VIEW_PRODUCT_REL') ;
		$COST_VIEW_FEE = $security->hasPermission($loginId , 'COST_VIEW_FEE') ;
		$COST_VIEW_OTHER = $security->hasPermission($loginId , 'COST_VIEW_OTHER') ;
	?>
   <style>

		fieldset legend {
			margin-bottom:5px;
		}
		fieldset {
			margin-bottom:8px;
		}
   </style>

   <script>
   		var groupCode = '<?php echo $loginId;?>' ;
   		var asin = '<?php echo $asin;?>' ;
   
		$(function(){
			
			$("button").click(function(){
				if( !$.validation.validate('#personForm').errorInfo ) {
					var json = $("#personForm").toJson() ;
					
					$.dataservice("model:Cost.saveCost" , json , function(){
						window.close() ;
					})

				};
				return false ;
			}) ;
			
			$(".cost").keyup(function(){
				calcTotalCost() ;
			}) ;
			$(".cost").blur(function(){
				calcTotalCost() ;
			}) ;
			
			calcTotalCost() ;
			
		})
		
		function calcTotalCost(){
			var totalCost = 0 ;
				$(".cost").each(function(){
					totalCost = totalCost + parseFloat($(this).val()||0) ;
				}) ;
				$("#TOTAL_COST").val(totalCost.toFixed(2)) ;
		}
   </script>
</head>


<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page">
		<!-- 页面标题 -->
		<div class="page-title">
			<h2>成本信息</h2>
		</div>
		<div class="container-fluid">
	        <form id="personForm" action="#" data-widget="validator,ajaxform" class="form-horizontal" >
				<div class="panel apply-panel">
					<!-- panel 中间内容-->
					<div class="panel-content">
						<!-- 数据列表样式 -->
						<input type="hidden" id="ID" value="<?php echo $id;?>"/>
						<input type="hidden" id="ASIN" value="<?php echo $asin;?>"/>
						<table  class="form-table" style="<?php echo $COST_VIEW_PURCHASE_COST?'':'display:none;'?>">
							<caption>采购成本</caption>
							<tr>
								<th>采购费用：</th>
								<td><input class="cost span2"  type="text" id="PURCHASE_COST" value="<?php echo $productCost[0]["sc_product_cost"]["PURCHASE_COST"];?>"/></td>
							</tr>
						</table>
						
						<table  class="form-table" style="<?php echo $COST_VIEW_POSTAGE?'':'display:none;'?>">
							<caption>物流成本</caption>
							<tr>
								<th>入库前物流费用：</th><td><input class="cost span2"   type="text" id="BEFORE_LOGISTICS_COST" value="<?php echo $productCost[0]["sc_product_cost"]["BEFORE_LOGISTICS_COST"];?>"/></td>
								<th>关税：</th><td><input class="cost span2"   type="text" id="TARIFF" value="<?php echo $productCost[0]["sc_product_cost"]["TARIFF"];?>"/></td>
							</tr>
							<tr>
								<th>仓储费用 ：</th><td><input class="cost span2"   type="text" id="WAREHOURSE_COST" value="<?php echo $productCost[0]["sc_product_cost"]["WAREHOURSE_COST"];?>"/></td>
								<th>USPS邮费 ：</th><td><input  class="cost span2"   type="text" id="USPS_COST" value="<?php echo $productCost[0]["sc_product_cost"]["USPS_COST"];?>"/></td>
							</tr>
							
						</table>
						
						<table  class="form-table" style="<?php echo $COST_VIEW_PRODUCT_REL?'':'display:none;'?>">
							<caption>产品成本</caption>
							<tr>
								<th>成本类型：</td><td>
									<select id="TYPE" class=" span2">
										<option value=""></option>
										<option value="FBM" <?php if($productCost[0]["sc_product_cost"]["TYPE"] == 'FBM') echo 'selected';?> >FBM</option>
										<option value="FBA" <?php if($productCost[0]["sc_product_cost"]["TYPE"] == 'FBA') echo 'selected';?>>FBA</option>
									</select>
								</th>
							</tr>
							<tr>
								<th>amazon佣金：</th><td><input class="cost span2"   type="text" id="AMAZON_FEE" value="<?php echo $productCost[0]["sc_product_cost"]["AMAZON_FEE"];?>"/></td>
								<th>可变关闭费用：</th><td><input class="cost span2"   type="text" id="VARIABLE_CLOSURE_COST" value="<?php echo $productCost[0]["sc_product_cost"]["VARIABLE_CLOSURE_COST"];?>"/></td>
							</tr>
							<tr>
								<th>标签费用 ：</th><td><input class="cost span2"   type="text" id="TAG_COST" value="<?php echo $productCost[0]["sc_product_cost"]["TAG_COST"];?>"/></td>
								<th>打包费：</th><td><input class="cost span2"   type="text" id="PACKAGE_COST" value="<?php echo $productCost[0]["sc_product_cost"]["PACKAGE_COST"];?>"/></td>
							</tr>
							<tr>
								<th>订单处理费：</th><td><input class="cost span2"   type="text" id="OORDER_PROCESSING_FEE" value="<?php echo $productCost[0]["sc_product_cost"]["OORDER_PROCESSING_FEE"];?>"/></td>
								<th>稳重费 ：</th><td><input class="cost span2"   type="text" id="STABLE_COST" value="<?php echo $productCost[0]["sc_product_cost"]["STABLE_COST"];?>"/></td>
							</tr>
						</table>
						
						<table  class="form-table" style="<?php echo $COST_VIEW_FEE?'':'display:none;'?>">
							<caption>会计</caption>
							<tr>
								<th>当地税费  ：</th><td><input class="cost span2"   type="text" id="LOST_FEE" value="<?php echo $productCost[0]["sc_product_cost"]["LOST_FEE"];?>"/></td>
								<th>人工成本：</th><td><input  class="cost span2"  type="text" id="LABOR_COST" value="<?php echo $productCost[0]["sc_product_cost"]["LABOR_COST"];?>"/></td>
							</tr>
							<tr>
								<th> 服务成本  ：</th><td><input class="cost span2"   type="text" id="SERVICE_COST" value="<?php echo $productCost[0]["sc_product_cost"]["SERVICE_COST"];?>"/></td>
							</tr>
						</table>
						
						<table  class="form-table" style="<?php echo $COST_VIEW_OTHER?'':'display:none;'?>">
							<tr>
								<td>其他成本 ：</td><td><input class="cost span2"   type="text" id="OTHER_COST" value="<?php echo $productCost[0]["sc_product_cost"]["OTHER_COST"];?>"/></td>
							</tr>
						</table>
						
						<div class="alert alert-info area" style="width:50%;">
						总成本:&nbsp;<input type="text" id="TOTAL_COST" readonly="readOnly"  value="<?php echo $productCost[0]["sc_product_cost"]["TOTAL_COST"];?>"/>
						</div>
					</div>
					
					<?php if($COST_EDIT){ ?>
					<!-- panel脚部内容-->
                    <div class="panel-foot">
						<div class="form-actions col2">
							<button type="submit" class="btn btn-primary">保存</button>
						</div>
					</div>
					<?php } ?>
				</div>
			</form>
		</div>
	</div>
</body>

</html>