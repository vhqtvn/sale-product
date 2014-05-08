<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>营销产品列表</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>
    <?php
   		include_once ('config/config.php');
  		include_once ('config/header.php');
		echo $this->Html->script('modules/account/product_list_nocalcreq');

		echo $this->Html->css('../js/modules/tag/tagutil');
		echo $this->Html->script('modules/tag/tagutil');
		
		$group=  $user["GROUP_CODE"] ;
	?>
	
    <script type="text/javascript">

   var accountId = '' ;
   $(function(){
	   
	}) ;
   </script>
   
   <style style="text/css">
   		*{
   			font:12px "微软雅黑";
   		}
   		
   		.rights-warning-flag{
   			width:10px;
   			height:10px;
   			margin-top:5px;
   			background:red;
   			display:block;
   			float:left;
   		}
   		
   		.ranking-warning-flag{
   			width:10px;
   			height:10px;
   			margin-top:5px;
   			background:#800000;
   			display:block;
   			float:left;
   		}
   		
   		.country-area-flag{
   			width:10px;
   			height:10px;
   			margin-top:5px;
   			background:#0000FF;
   			display:block;
   			float:left;
   		}
   		
   		.lly-grid-cell-input{
   		}
   		
   		.query-bar ul{
   			display:block;
   			margin_bottom:5px;
   			height:auto;
   			width:100%;
   		}
   		
   		.query-bar ul li{
   			list-style-type:none;
   			float:left;
   			padding:3px 0px;
   		}
   		
   		.query-bar ul li label{
   			float:left;
   			margin:0px 0px;
   			margin-left:15px;
   		}
   		
   		.query-bar{
   			clear:both;
   		}
   		
   		li select,li input{
   			width:auto;
   			padding:0px;
   		}
   		
   		.popover-inner  .popover-title{
			font-size:12px;
   		}
   </style>

</head>
<body style="magin:0px;padding:0px;">
	<div data-widget="layout" style="width:100%;height:100%;">
		<div region="center" split="true" border="true" title="产品列表" style="padding:2px;">
			<div class="toolbar toolbar-auto query-bar">
				<table style="width:100%;" class="query-table">	
					<tr>
						<td>
							<input type="text" name="searchKey"  placeHolder="SKU,NAME"/>
							<?php 
							$SqlUtils  = ClassRegistry::init("SqlUtils") ;
											$items = $SqlUtils->exeSqlWithFormat("select * from sc_config where type= 'riskType'",array()) ;
										?>
											<select  name="riskType" style="width:120px;">
													<option value="">-选择类型-</option>
													<?php  foreach( $items as $item ){ ?>
															<option value="<?php echo $item['KEY']?>"><?php echo $item['LABEL']?></option>
													<?php  } ?>
													
										    </select>
										    
										    <select name="accountId" style="width:120px">
				     		<option value="">--选择账号--</option>
					     	<?php
					     		 $amazonAccount  = ClassRegistry::init("Amazonaccount") ;
				   				 $accounts = $amazonAccount->getAllAccounts(); 
					     		foreach($accounts as $account ){
					     			$account = $account['sc_amazon_account'] ;
					     			echo "<option value='".$account['ID']."'>".$account['NAME']."</option>" ;
					     		} ;
					     	?>
							</select>
						
							<button class="btn btn-primary query query-btn" >查询</button>
						</td>
					</tr>						
				</table>
			</div>
			
			<div style="clear:both;height:5px;"></div>
			<div class="grid-content" style="width:99%;">
			</div>
			
		</div>
   </div>
	
</body>
</html>
