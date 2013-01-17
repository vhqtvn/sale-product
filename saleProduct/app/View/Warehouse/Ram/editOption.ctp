<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>RAM选项信息编辑</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('../js/listselectdialog/jquery.listselectdialog');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('dialog/jquery.dialog');
		echo $this->Html->script('grid/query');
		echo $this->Html->script('validator/jquery.validation');	
		echo $this->Html->script('listselectdialog/jquery.listselectdialog');
		echo $this->Html->script('modules/warehouse/ram/editOption');
		echo $this->Html->script('calendar/WdatePicker');
		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$security  = ClassRegistry::init("Security") ;
		
		
		$loginId   = $user['LOGIN_ID'] ;
		
		$result = null ;
		$diskId = $params['arg1'] ;
		if(!empty($diskId)){
			$result = $SqlUtils->getObject("sql_ram_options_getByCode",array('code'=>$diskId) ) ;
		}
	?>
	
	<script>
		var diskId = '<?php echo $diskId ; ?>';
   </script>
</head>

<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page">
		<div class="container-fluid">

	        <form id="personForm" action="#" data-widget="validator" class="form-horizontal" >
				<!-- panel 头部内容  此场景下是隐藏的-->
				<div class="panel apply-panel">
					
					<!-- panel 中间内容-->
					<div class="panel-content">
						<!-- 数据列表样式 -->
						<table class="form-table " >
							<caption>RAM选项信息编辑</caption>
							<tbody>	
								<tr>
									<th>名称：</th><td colspan=3><input data-validator="required" type="text" id="name"
										value="<?php echo $result['NAME'];?>"/></td>
								</tr>
								<tr>
									<th>代码：</th><td  colspan=3><input data-validator="required" type="text" id="code"
										value="<?php echo $result['CODE'];?>"/></td>
								</tr>
								<tr>
									<th>类型：</th>
									<td  colspan=3>
										RAM原因：<input data-validator="required" 
											<?php echo $result['TYPE']=='cause'?"checked":"";?>
											type="radio" name="type" value="cause"/>
										RAM解决策略：<input data-validator="required" 
											<?php echo $result['TYPE']=='policy'?"checked":"";?>
										type="radio" name="type" value="policy"/>
									</td>
								</tr>
								
								<tr class="resendRow" style="<?php echo $result['TYPE']=='policy'?"":"display:none;";?>" >
									<th>是否需要重发货：</th>
									<td  colspan=3>
										是：<input 
											<?php echo $result['IS_RESEND']=='1'?"checked":"";?>
											type="radio" name="isResend" value="1"/>
										否：<input
											<?php echo $result['IS_RESEND']!='1'?"checked":"";?>
										type="radio" name="isResend" value="0"/>
									</td>
								</tr>
								
								<tr class="resendRow" style="<?php echo $result['TYPE']=='policy'?"":"display:none;";?>" >
									<th>是否需要重退款：</th>
									<td  colspan=3>
										是：<input 
											<?php echo $result['IS_REFUND']=='1'?"checked":"";?>
											type="radio" name="isRefund" value="1"/>
										否：<input
											<?php echo $result['IS_REFUND']!='1'?"checked":"";?>
										type="radio" name="isRefund" value="0"/>
									</td>
								</tr>
								
								<tr class="resendRow" style="<?php echo $result['TYPE']=='policy'?"":"display:none;";?>" >
									<th>是否需要退货：</th>
									<td  colspan=3>
										是：<input 
											<?php echo $result['IS_BACK']=='1'?"checked":"";?>
											type="radio" name="isBack" value="1"/>
										否：<input
											<?php echo $result['IS_BACK']!='1'?"checked":"";?>
										type="radio" name="isBack" value="0"/>
									</td>
								</tr>
								
								<tr>
									<th>备注：</th><td  colspan=3>
										<textarea name="memo"  style="width:90%;height:40px;"><?php echo $result['MEMO'];?></textarea>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
			
				</div>
			</form>
		</div>
				<div class="panel-foot">
						<div class="form-actions" style="padding:5px;">
							<button type="button" class="btn btn-primary btn-save">保&nbsp;存</button>
						</div>
					</div>
	</div>
</body>
</html>