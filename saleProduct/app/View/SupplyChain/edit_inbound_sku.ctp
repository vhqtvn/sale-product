<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>Inbound计划 SKU编辑</title>
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
		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$planId = $params['arg1'] ;
		
		$plan = $SqlUtils->getObject("sql_supplychain_inbound_local_plan_getByPlanId",array('planId'=>$planId)) ;
	?>
</head>

<script>
	var accountId = '<?php echo $plan['ACCOUNT_ID'] ;?>'
	$(function(){
		
		$(".select").click(function(){
			openCenterWindow(contextPath+"/page/forward/SupplyChain.bind_product_details/"+accountId,1000,600,function(){
				var itemSku = $.dialogReturnValue() ;
				$("#sku").val(itemSku) ;
			}) ;
		}) ;

		$(".save").click(function(){
			if( !$.validation.validate('#personForm').errorInfo ) {
				var json = $("#personForm").toJson() ;
				$.dataservice("model:SupplyChain.Inbound.savePlanSku",json,function(result){
					window.close();
				});
			}
		}) ;
	}) ;
</script>

<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page">
		<!-- 页面标题 -->
		<div class="container-fluid">

	        <form id="personForm" action="#" data-widget="validator,ajaxform" class="form-horizontal" >
				<input type="hidden" id="itemId" value=""/>
				<input type="hidden" id="planId" value="<?php echo $planId;?>"/>
				<!-- panel 头部内容  此场景下是隐藏的-->
				<div class="panel apply-panel">
					<!-- panel 中间内容-->
					<div class="panel-content">
						<!-- 数据列表样式 -->
						<table class="form-table" >
							<caption>基本信息</caption>
							<tbody>
								<tr>
									<th>Sku：</th><td><input type="text" id="sku"  data-validator="required"
										value=""/>
										<button class="btn select"> 选择</button>	
									</td>
								</tr>
								<tr>
									<th>数量：</th><td><input type="text" id="quantity"  data-validator="required"
										value=""/></td>
								</tr>
								<tr>
									<th>备注：</th><td><textarea name="memo"></textarea></td>
								</tr>
								
							</tbody>
						</table>
					</div>
					
					<!-- panel脚部内容-->
                    <div class="panel-foot">
						<div class="form-actions">
							<button type="button" class="btn btn-primary  save">提&nbsp;交</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</body>
</html>