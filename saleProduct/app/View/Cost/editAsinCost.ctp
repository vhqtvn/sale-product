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
		echo $this->Html->script('modules/cost/cost');

		$asin = $params['arg1'] ;

		$SqlUtils  = ClassRegistry::init("SqlUtils") ;//COST_TAG  COST_LABOR  COST_TAX_RATE
	
		$sql = "select * from sc_product_cost_asin where asin='{@#asin#}'" ;
		$productCost = $SqlUtils->getObject($sql,array("asin"=>$asin)) ;
	?>
   <style>

		th{
			width:120px!important;
		}
		
		.form-table{
			margin-bottom:5px!important;
		}
		
		caption{
			height:25px;
			line-height:25px;
		}
		.listing-cost th{
			text-align: center!important;
		}
   </style>

   <script>
   		$(function(){
			$(".save-btn").click(function(){
				if( !$.validation.validate('#personForm').errorInfo ) {
					var json = $("#personForm").toJson() ;
					
					$.dataservice("model:Cost.saveCostByFee",json,function(result){
						window.close() ;
					});
				}
			}) ;
   	   	}) ;
   </script>
</head>


<body class="container-popup" >
	<!-- apply 主场景 -->
	<div class="apply-page" >
		<!-- 页面标题 -->
		<div class="container-fluid">
	        <form id="personForm" action="#" data-widget="validator,ajaxform" class="form-horizontal" >
				<div class="panel apply-panel">
					<!-- panel 中间内容-->
					<div class="panel-content" style="margin-bottom:50px;">
						<!-- 数据列表样式 -->
						
						<table  class="form-table  product-cost" >
							<caption>ASIN成本</caption>
							<tr>
									<th>ASIN ：</th>
									<td > 
									<?php echo $productCost['ASIN']  ; ?>
									<input type="hidden"  id="asin"  value="<?php echo $productCost['ASIN'] ; ?>"/>
									</td>
							</tr>
							<tr>
									<th>Amazon佣金比率 ：</th>
									<td >
									<input class="cost span2"  type="text" 
									data-validator="double"
									id="commissionRatio" value="<?php echo $productCost["COMMISSION_RATIO"];?>"/>
									<span class="alert padding2" >如：0.15...</span>
									</td>
							</tr>
							<tr>
									<th>Amazon最低佣金 ：</th>
									<td >
									<input class="cost span2"  type="text" 
									data-validator="double"
									id="commissionLowlimit" value="<?php echo $productCost["COMMISSION_LOWLIMIT"];?>"/>
									<span class="alert padding2" >有些产品存在最低佣金，如最低为1$</span>
									</td>
							</tr>
							<tr>
									<th>可变关闭费用 ：</th>
									<td >
									<input class="cost span2"  type="text" 
									data-validator="double"
									id="variableClosingFee" value="<?php echo $productCost["VARIABLE_CLOSING_FEE"];?>"/>
									</td>
							</tr>
							<tr>
									<th>FBA费用 ：</th>
									<td >
									<input class="cost span2"  type="text" 
									data-validator="double"
									id="fbaCost" value="<?php echo $productCost["FBA_COST"];?>"/>
									</td>
							</tr>
						</table>
						
					</div>
                    <div class="panel-foot"  style="background:#FFF;">
						<div class="form-actions">
							<button type="submit" class="btn btn-primary save-btn">保存</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</body>

</html>