<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>包装明细项编辑</title>
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
		echo $this->Html->script('modules/saleproduct/package/editPackageGroupItem');
		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$security  = ClassRegistry::init("Security") ;
		
		$loginId   = $user['LOGIN_ID'] ;
		
		$record = null ;
		$groupId = $params['arg1'] ;
		$itemId = $params['arg2'] ;
		if(!empty($itemId)){
			$record = $SqlUtils->getObject("sql_package_group_item_getById",array('id'=>$itemId) ) ;
		}
	?>
</head>

<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page">
		<!-- 页面标题 -->
		<div class="page-title">
			<h2>包装明细项编辑</h2>
		</div>
		<div class="container-fluid">

	        <form id="personForm" action="#" data-widget="validator,ajaxform" class="form-horizontal" >
			<input type="hidden" id="id" value="<?php echo  $record['ID'];?>"/>
			<input type="hidden" id="groupId" value="<?php echo  $groupId;?>"/>
				<!-- panel 头部内容  此场景下是隐藏的-->
				<div class="panel apply-panel">
					<!-- panel 中间内容-->
					<div class="panel-content">
						<!-- 数据列表样式 -->
						<table class="form-table" >
							<caption>基本信息</caption>
							<tbody>										   
								<tr>
									<th>名称：</th><td><input type="text" id="name"   data-validator="required"
										value="<?php echo  $record['NAME'];?>"/></td>
								</tr>
								<tr>
									<th> Weight：</th>
									<td>
										<input type="text" id="fromWeight"  data-validator="required"  class="span2"
										value="<?php echo  $record['FROM_WEIGHT'];?>"/>
										TO
										<input type="text" id="toWeight"  data-validator="required"  class="span2"
										value="<?php echo  $record['TO_WEIGHT'];?>"/>
									</td>
								</tr>
								<tr>
									<th>Packaging Weight：</th>
									<td><input type="text" id="packagingWeight"  
										data-validator="required"  class="span2"
										value="<?php echo  $record['PACKAGING_WEIGHT'];?>"/></td>
								</tr>
								
								<tr>
									<th>Lenght*Width*Height：</th><td>
										<input type="text" id="length" class="span2" data-validator="required"
										value="<?php echo  $record['LENGTH'];?>"/>*
										<input type="text" id="width" class="span2" data-validator="required"
										value="<?php echo  $record['WIDTH'];?>"/>*
										<input type="text" id="height" class="span2" data-validator="required"
										value="<?php echo  $record['HEIGHT'];?>"/>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					
					<!-- panel脚部内容-->
                    <div class="panel-foot">
						<div class="form-actions">
							<button type="button" class="btn btn-primary">提&nbsp;交</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</body>
</html>