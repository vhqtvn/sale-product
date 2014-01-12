<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>Listing编辑</title>
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
		
		$listingId = $params['arg1'] ;
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$sql = "select * from sc_amazon_account_product where id ='{@#id#}'" ;
		$u = $SqlUtils->getObject($sql , array("id"=>$listingId)) ;
		
	?>
	<script>
		$(function(){
			$(".save-user").click(function(){
				if( !$.validation.validate('#personForm').errorInfo ) {
					var json = $("#personForm").toJson() ;
					
					$.dataservice("model:Listing.saveListing",json,function(result){
							window.close();
					});
				}
			});
		}) ;

	</script>
  
</head>

<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page">
		<!-- 页面标题 -->
		<div class="container-fluid">

	        <form id="personForm" action="#" data-widget="validator,ajaxform" class="form-horizontal" >
	        	<input type="hidden" id="ID" value="<?php echo $u['ID'];?>"/>
				<!-- panel 头部内容  此场景下是隐藏的-->
				<div class="panel apply-panel">
					<!-- panel 中间内容-->
					<div class="panel-content">
						<!-- 数据列表样式 -->
						<table class="form-table col2" >
							<caption>基本信息</caption>
							<tbody>	
								<tr>
									<th>Listing SKU：</th>
									<td>
									<?php echo  $u['SKU'];?>
									</td>
								</tr>	
								<tr>
									<th>ASIN：</th>
									<td>
									<?php echo  $u['ASIN'];?>
									</td>
								</tr>								   
								<tr>
									<th>供应周期(天)：</th>
									<td>
									<input type="text"  data-validator="required" id="SUPPLY_CYCLE" value="<?php echo  $u['SUPPLY_CYCLE'];?>"
										/>
									</td>
								</tr>
								<tr>
									<th>需求调整系数：</th>
									<td>
									<input type="text"  data-validator="required" id="REQ_ADJUST" value="<?php echo  $u['REQ_ADJUST'];?>"
										/>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					
					<!-- panel脚部内容-->
                    <div class="panel-foot">
						<div class="form-actions col2">
							<button type="button" class="btn btn-primary save-user">提&nbsp;交</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</body>
</html>