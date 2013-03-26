<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>订单信息(<?php echo $orderId;?>)</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
  	    include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('../js/listselectdialog/jquery.listselectdialog');
		echo $this->Html->css('default/style');
		echo $this->Html->css('../js/tab/jquery.ui.tabs');
		echo $this->Html->css('../js/grid/jquery.llygrid');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('jquery.ui');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('dialog/jquery.dialog');
		echo $this->Html->script('grid/query');
		echo $this->Html->script('validator/jquery.validation');	
		echo $this->Html->script('tab/jquery.ui.tabs');
		echo $this->Html->script('listselectdialog/jquery.listselectdialog');
		echo $this->Html->script('modules/norder/asynManual');
		echo $this->Html->script('calendar/WdatePicker');
		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$security  = ClassRegistry::init("Security") ;
		
		$loginId   = $user['LOGIN_ID'] ;
		

		
	?>
	<style>
		.table th, .table td{
			padding:5px !important;
		}
	</style>
</head>

<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page">
		<div class="container-fluid asyn-form"  data-widget="validator">
			<center>
				<table class="table table-bordered " style="width:50%;margin-top:100px;"  > 
					<tr>
						<th>账号：</th>
						<td>
						<select name="accountId" data-validator="required">
				     		<option value="">--选择--</option>
					     	<?php
					     		 $amazonAccount  = ClassRegistry::init("Amazonaccount") ;
				   				 $accounts = $amazonAccount->getAllAccounts(); 
					     		foreach($accounts as $account ){
					     			$account = $account['sc_amazon_account'] ;
					     			echo "<option value='".$account['ID']."'>".$account['NAME']."</option>" ;
					     		} ;
					     	?>
							</select>
						</td>
					</tr>
					<tr>
						<th>开始时间：</th>
						<td>
							<input  type="text"  id="LastUpdatedAfter" data-widget="calendar" data-validator="required"/>
						</td>
					</tr>
					<tr>
						<th>结束时间：</th>
						<td>
							<input  type="text" data-widget="calendar"   id="LastUpdatedBefore"/>
						</td>
					</tr>
					<tr>
						<td colspan=2>
						<center>
							<button class="btn btn-primary asyn-btn">执行同步</button>
						</center>
						</td>
					</tr>
				</table>
				</center>
		</div>
	
	</div>
</body>
</html>