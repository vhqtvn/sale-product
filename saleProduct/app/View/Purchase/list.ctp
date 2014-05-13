<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
  		include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('modules/purchase/list');
		
		$user = $this->Session->read("product.sale.user") ;
		$groupCode = $user["GROUP_CODE"] ;
		$loginId = $user['LOGIN_ID'] ;
		
		/**
		 *  create_pp 添加计划产品操作
			add_pp_product 添加审批产品操作
			add_pp_audit_product 导出操作
			export_pp 打印操作
			print_pp 编辑采购产品操作
			edit_pp_product 删除采购产品操作
			delete_pp_product 申请采购操作
			apply_purchase 审批通过操作
			audit_pass_purchase 审批不通过操作
			audit_nopass_purchase
		*/
		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$security  = ClassRegistry::init("Security") ;

		$pp_edit 				= $security->hasPermission($loginId , 'pp_edit') ;
		$ppp_add_product				= $security->hasPermission($loginId , 'ppp_add_product') ;
		$ppp_export		= $security->hasPermission($loginId , 'ppp_export') ;
		$ppp_audit	= $security->hasPermission($loginId , 'ppp_audit') ;
		$ppp_setlimitprice				= $security->hasPermission($loginId , 'ppp_setlimitprice') ;
		$ppp_assign_executor				= $security->hasPermission($loginId , 'ppp_assign_executor') ;
		$ppp_qc		= $security->hasPermission($loginId , 'ppp_qc') ;
		$ppp_inwarehouse		= $security->hasPermission($loginId , 'ppp_inwarehouse') ;
		$ppp_confirm 		= $security->hasPermission($loginId , 'ppp_confirm') ;
		/*$apply_purchase 		= $security->hasPermission($loginId , 'apply_purchase') ;
		$audit_purchase 	= $security->hasPermission($loginId , 'audit_purchase') ;
		$purchase_cost_view 	= $security->hasPermission($loginId , 'purchase_cost_view') ;
		$confirm_purchase 	= $security->hasPermission($loginId , 'confirm_purchase') ;*/
		
	?>
	
	<script type="text/javascript">

		var $pp_edit = <?php echo $pp_edit?"true":"false" ;?> ;
		var $ppp_add_product = <?php echo $ppp_add_product?"true":"false" ;?> ;
		var $ppp_export= <?php echo $ppp_export?"true":"false" ;?> ;
		var $ppp_audit = <?php echo $ppp_audit?"true":"false" ;?> ;
		var $ppp_setlimitprice = <?php echo $ppp_setlimitprice?"true":"false" ;?> ;
		var $ppp_assign_executor = <?php echo $ppp_assign_executor?"true":"false" ;?> ;
		var $ppp_qc = <?php echo $ppp_qc?"true":"false" ;?> ;
		var $ppp_inwarehouse = <?php echo $ppp_inwarehouse?"true":"false" ;?> ;
		var $ppp_confirm = <?php echo $ppp_confirm?"true":"false" ;?> ;

		var img0 = '<?php echo $this->Html->image('example.gif',array("title"=>"所有")) ?>' ;
		var img1 = '<?php echo $this->Html->image('example.gif',array("title"=>"未处理")) ?>' ;
		var img2 = '<?php echo $this->Html->image('apply.png',array("title"=>"申请采购")) ?>' ;
		var img25 = '<?php echo $this->Html->image('error.gif',array("title"=>"审批不通过，终止采购")) ?>' ;
		var img3 = '<?php echo $this->Html->image('success.gif',array("title"=>"审批通过，限价待确认")) ?>' ;
		var img4 = '<?php echo $this->Html->image('forum.gif',array("title"=>"限价确认完成，待分配责任人")) ?>' ;
		var img45 = '<?php echo $this->Html->image('cmp.gif',array("title"=>"采购执行")) ?>' ;
		var img5 = '<?php echo $this->Html->image('pkg.gif',array("title"=>"待QC验货产品")) ?>' ;
		var img6 = '<?php echo $this->Html->image('cake.icon.png',array("title"=>"货品待入库")) ?>' ;
		var img7 = '<?php echo $this->Html->image('icon-grid.gif',array("title"=>"采购审计")) ?>' ;
		var img8 = '<?php echo $this->Html->image('test-pass-icon.png',array("title"=>"结束采购")) ?>' ;
	</script>

	 <style type="text/css">
		img{
			cursor:pointer;
		}
	</style>
 <style type="text/css">
		.flow-node{
			min-width:50px; 
			height:20px; 
			border:5px solid #0FF; 
			border-radius:5px;
			font-weight:bold;
			cursor:pointer;
		}
		
		.flow-node.active{
			border-color:#3809F7 ;
			background-color:#3809F7 ;
			color:#EEE;
		}
		
		.flow-node.passed{
			border-color:#92E492 ;
			background-color:#92E492 ;
			
		}
		
		.flow-node.termination{
			color:red;
	        background-color:pink ;
			border-color:pink;
		    white-space: nowrap;
		}
		
		.flow-node.disabled{
			border-color:#CCC ;
			background-color:#CCC ;
			color:#EEE;
		}
		
		.flow-table{
			text-align:center;
			margin:5px 0px;
		}
		

		.flow-action{
			position:absolute;;
			right:10px;
			top:48px;
			z-index:100;
		}
		
		.flow-split{
			font-size:30px;
		}
		
		.memo{
			position:absolute;
			top:85px;
			z-index:1;
			right:10px;
			width:300px;
			height:50px;
			background:#ffd700;
			display:none;
		}
		
		.memo-control{
			display:none;
		}
		
		.tag-container li{
			float:left;
			list-style: none;
			margin:2px 5px;
		 	padding:2px;
		}
	</style>
</head>
<body>

	<div style="clear:both;height:1px;" ></div>
	<div class="toolbar toolbar-auto toolbar1">
		<table>
			<tr>
				<th>
					关键字:
				</th>
				<td>
					<input type="text" id="searchKey" placeHolder="输入货品SKU、标题、执行人、编号" style="width:400px;"/>
				</td>	
				<th>账号：</th>
						<td>
						<select name="accountId" data-validator="required"  style="width:100px;">
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
				<td class="toolbar-btns">
					<button class="btn btn-primary query-btn"  data-widget="grid-query"  data-options="{gc:'.grid-content-details',qc:'.toolbar1'}">查询</button>
					<button class="btn btn-primary create-purchase-product"  >创建采购单</button>
				</td>
			</tr>						
		</table>
	</div>	
	<div class="flow-bar1">
		<center><table class="flow-table">						<tbody>
		<tr>	
		<td><div class="flow-node active total" status="">全部<span class="count"></span></div>
	
		</td>
		<td>	&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td><div class="flow-node disabled " status="45,46">待采购<span class="count"></span></div></td>
		<td class="flow-split">-</td><td><div class="flow-node disabled" status="47">采购审批<span class="count"></span></div></td>
		<td class="flow-split">-</td><td><div class="flow-node disabled" status="51">利润评估<span class="count"></span></div></td>
		<td class="flow-split">-</td><td><div class="flow-node disabled" status="48">待交易<span class="count"></span></div></td>
		<td class="flow-split">-</td><td><div class="flow-node disabled" status="49">待收货<span class="count"></span></div></td>
		<td class="flow-split">-</td><td><div class="flow-node disabled" status="50">QC验货<span class="count"></span></div></td>
		<td class="flow-split">-</td><td><div class="flow-node disabled" status="60">货品入库<span class="count"></span></div></td>
		<!--
		<td class="flow-split">-</td><td><div class="flow-node disabled" status="75">发货FBA<span class="count"></span></div></td>
		  -->
		<td class="flow-split">-</td><td><div class="flow-node disabled" status="80">结束<span class="count"></span></div></td>
		</tr>					</tbody>
		</table>										
		</center>
	</div>
	<div class="grid-content-details" style="margin-top:5px;">
	</div>
	<iframe src="" id="exportIframe" style="display:none"></iframe>
</body>
</html>
