<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>物流商编辑</title>
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
		echo $this->Html->script('modules/saleproduct/postage/editPostageVendor');
		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$security  = ClassRegistry::init("Security") ;
		
		
		$loginId   = $user['LOGIN_ID'] ;
		
		$record = null ;
		$vendorId = $params['arg1'] ;
		if(!empty($vendorId)){
			$record = $SqlUtils->getObject("sql_postage_vender_getById",array('id'=>$vendorId) ) ;
		}
	?>
</head>

<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page">
		<!-- 页面标题 -->
		<div class="page-title">
			<h2>物流商编辑</h2>
		</div>
		<div class="container-fluid">

	        <form id="personForm" action="#" data-widget="validator,ajaxform" class="form-horizontal" >
			<input type="hidden" id="id" value="<?php echo  $record['ID'];?>"/>
				<!-- panel 头部内容  此场景下是隐藏的-->
				<div class="panel apply-panel">
					<!-- panel 中间内容-->
					<div class="panel-content">
						<!-- 数据列表样式 -->
						<table class="form-table col2" >
							<caption>基本信息</caption>
							<tbody>										   
								<tr>
									<th>名称：</th><td><input type="text" id="name"   data-validator="required"
										value="<?php echo  $record['NAME'];?>"/></td>
								</tr>
								<tr>
									<th>编码：</th><td><input type="text" id="code"  data-validator="required"
										value="<?php echo  $record['CODE'];?>"/></td>
								</tr>
								<tr>
									<th>物流软件商：</th>
									<td>
										<select name="softwareId" id="softwareId">
											<option value="">--选择--</option>
											<?php
												$softVendors = $SqlUtils->exeSql("sql_postage_software_vender_list" , array()) ;
												$softwareId = $record['SOFTWARE_ID'] ;
												foreach($softVendors as $svendor){
													$svendor = $SqlUtils->formatObject($svendor) ;
													$selected = $softwareId == $svendor['ID']?"selected":"" ;
													echo "<option $selected value='".$svendor['ID']."'>".$svendor['NAME']."</option>" ;
												}
											?>
										</select>
									</td>
								</tr>
								<tr>
									<th>备注：</th>
									<td>
										<textarea id="memo" name="memo" style="width:80%;height:100px;"><?php echo  $record['MEMO'];?></textarea>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					
					<!-- panel脚部内容-->
                    <div class="panel-foot">
						<div class="form-actions col2">
							<button type="button" class="btn btn-primary">提&nbsp;交</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</body>
</html>