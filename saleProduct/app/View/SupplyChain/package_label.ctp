<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>更新计划</title>
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
		echo $this->Html->script('modules/supplychain/package_label');
		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$accountId = $params['arg1'] ;
		$shipmentId = $params['arg2'] ;
	?>
</head>

<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page">
		<!-- 页面标题 -->
		<div class="container-fluid">

	        <form id="personForm" action="#" data-widget="validator,ajaxform" class="form-horizontal" >
				<input type="hidden" id="shipmentId" value="<?php echo $params['arg2'];?>"/>
				<input type="hidden" id="accountId" value="<?php echo  $params['arg1'];?>"/>
				<!-- panel 头部内容  此场景下是隐藏的-->
				<div class="panel apply-panel">
					<!-- panel 中间内容-->
					<div class="panel-content">
						<!-- 数据列表样式 -->
						<table  class="table table-bordered">
							    <caption>基本信息</caption>
								<tr>
									<th style="width:30%;">Page Type：</th>
									<td>
										<select name="pageType" data-validator="required">
											<option value="">选择Page Type</option>
											<option value="PackageLabel_Letter_2">PackageLabel_Letter_2 </option>
											<option value="PackageLabel_Letter_4">PackageLabel_Letter_4  </option>
											<option value="PackageLabel_Letter_6">PackageLabel_Letter_6 </option>
											<option value="PackageLabel_A4_4">PackageLabel_A4_4  </option>
											<option value="PackageLabel_Plain_Paper">PackageLabel_Plain_Paper  </option>
										</select>
									</td>
							    </tr>
							    <tr>
									<th style="width:30%;">Number Of Packages：</th>
									<td>
										<input type="text"  data-validator="required" name="numberOfPackages"  value=""/>
									</td>
							    </tr>
						</table>
						
					</div>
					
					<!-- panel脚部内容-->
                    <div class="panel-foot">
						<div class="form-actions">
							<button type="button" class=" to-amazon btn btn-danger">获取Package Label</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</body>
</html>