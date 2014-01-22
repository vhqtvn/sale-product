<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>Amazon账户</title>
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
		$domain =  $account[0]['sc_amazon_account']['DOMAIN'];
	?>
	
   <style>
   		*{
   			font:12px "微软雅黑";
   		}

		.rule-content-item{
			clear:both;
		}

		.item-label,.item-relation,.item-value,.item-value{
			float:left;
		}
		
		input{
			width:90%;
		}
		.table th, .table td{
			padding:5px 8px;
		}
		
		.amazon-tbody ,.ebay-tbody{
			display:none;
		}
   </style>

   <script>
		$(function(){

			$("button").click(function(){
				
				if( !$.validation.validate('#personForm').errorInfo ) {
					$.block() ;
					var json = $("#personForm").toJson() ;
					json.CODE = json.CODE||json.MERCHANT_ID ;
					$.ajax({
						type:"post",
						url:contextPath+"/amazonaccount/saveAccount",
						data:json,
						cache:false,
						dataType:"text",
						success:function(result,status,xhr){
							$.unblock() ;
							$.dialogReturnValue(true) ;
							window.close() ;
						}
					}); 
				};
			});

			$("#PLATFORM_ID").change(function(){
				var text = $(this).find("option:selected").text() ;
				var _t = text.toLowerCase() ;
				if( _t.indexOf("ebay") >=0  ){
					$(".amazon-tbody").hide() ;
					$(".ebay-tbody").show() ;
				}else{
					$(".ebay-tbody").hide() ;
					$(".amazon-tbody").show() ;
				}
			}).trigger("change") ;
		}) ;
   </script>

</head>

<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page">
		<!-- 页面标题 -->
		<div class="page-title">
			<h2>账户信息</h2>
		</div>
		<div class="container-fluid">

	        <form id="personForm" action="#" data-widget="validator,ajaxform" class="form-horizontal" >
	        	<input type="hidden" id="ID" value="<?php echo $account[0]['sc_amazon_account']['ID'];?>"/>
	        	<input type="hidden" id="URL" value="<?php echo $account[0]['sc_amazon_account']['URL'];?>"/>
				<!-- panel 头部内容  此场景下是隐藏的-->
				<div class="panel apply-panel">
					<!-- panel 中间内容-->
					<div class="panel-content" style="margin-bottom:50px;">
						<!-- 数据列表样式 -->
						<table class="form-table " >
							<tbody>										   
								<tr>
									<th style="width:170px;">账户名称：</th>
									<td colspan="3"><input data-validator="required" type="text" id="NAME" value="<?php echo $account[0]['sc_amazon_account']['NAME'];?>"/></td>
								</tr>
								<tr>
									<th>操作主机：</th><td><input type="text"  id="DOMAIN" value="<?php echo $domain;?>"/></td>
									<th>定时任务上下文：</th><td><input type="text"  id="CONTEXT" value="<?php echo $account[0]['sc_amazon_account']['CONTEXT'];?>"/></td>
								</tr>
								<tr>
									<th>所属平台：</th>
									<td colspan="3">
									<select id="PLATFORM_ID"    style="width:97%;" class="input " 
										<?php 
										if( !empty($account[0]['sc_amazon_account']['ID']) ){
											echo "disabled";
										}
										?>
									>
										<option value="">--选择平台--</option>
										<?php 
											$SqlUtils  = ClassRegistry::init("SqlUtils") ;
											$strategys = $SqlUtils->exeSql("sql_platform_list",array()) ;
											foreach( $strategys as $s){
												$s = $SqlUtils->formatObject($s) ;
												$selected = '' ;
												if( $s['ID'] == $account[0]['sc_amazon_account']['PLATFORM_ID'] ){
													$selected = "selected" ;
												}
												echo "<option $selected value='".$s['ID']."'>".$s['NAME']."</option>" ;
											}
										?>
										</select>
									</td>
								</tr>
							</tbody>
							<tbody class="amazon-tbody">
								<tr>
									<th>FBM发货仓库：</th>
									<td>
										<select  id="FBM_WAREHOUSE"  style="width:97%;">
										    	<option value="">--选择--</option>
											   <?php 
											     // sql_warehouse_lists
											     $warehouses = $SqlUtils->exeSql("sql_warehouse_lists",array()) ;
					                             foreach($warehouses as $w){
					                             	  $w = $SqlUtils->formatObject( $w ) ;
					                             	  $selected = $account[0]['sc_amazon_account']['FBM_WAREHOUSE'] == $w['ID'] ?"selected":"" ;
					                             	  echo "<option $selected value='".$w['ID']."'>".$w['NAME']."</option>" ;
					                             }
											   ?>
											</select>
									</td>
									<th>币种：</th>
									<td>
										<select  id="EXCHANGE_ID"   style="width:97%;" >
										    	<option value="">--选择--</option>
											   <?php 
											     // sql_warehouse_lists
											     $warehouses = $SqlUtils->exeSqlWithFormat("select * from sc_exchange_rate",array()) ;
					                             foreach($warehouses as $w){
					                             	  $selected = $account[0]['sc_amazon_account']['EXCHANGE_ID'] == $w['ID'] ?"selected":"" ;
					                             	  echo "<option $selected value='".$w['ID']."'>".$w['SOURCE_NAME']."</option>" ;
					                             }
											   ?>
											</select>
									</td>
									
								</tr>
								<tr>
									<th>FBA库存集中费：</th>
									<td>
										<input type="text" id="INVENTORY_CENTER_FEE" value="<?php echo $account[0]['sc_amazon_account']['INVENTORY_CENTER_FEE'];?>"/>
									</td>
									<th>账户地区税率：</th>
									<td>
									<input type="text" id="FEE_RATIO" value="<?php echo $account[0]['sc_amazon_account']['FEE_RATIO'];?>"/>
									</td>
								</tr>
								<tr>
									<th>供应周期（天）：</th>
									<td>
										<input type="text" id="SUPPLY_CYCLE" value="<?php echo $account[0]['sc_amazon_account']['SUPPLY_CYCLE'];?>"/>
									</td>
									<th>需求调整系数：</th>
									<td>
									<input type="text" id="REQ_ADJUST" value="<?php echo $account[0]['sc_amazon_account']['REQ_ADJUST'];?>"/>
									</td>
								</tr>
								<tr>
									<th>转仓物流单价（$/KG）：</th>
									<td>
										<input type="text" id="TRANSFER_WH_PRICE" value="<?php echo $account[0]['sc_amazon_account']['TRANSFER_WH_PRICE'];?>"/>
									</td>
									<th>账户平均转化率：</th>
									<td>
										<input type="text" id="CONVERSION_RATE" value="<?php echo $account[0]['sc_amazon_account']['CONVERSION_RATE'];?>"/>
									</td>
								</tr>
								<tr>
									<th>APPLICATION_NAME：</th>
									<td colspan="3"><input type="text" id="APPLICATION_NAME" value="<?php echo $account[0]['sc_amazon_account']['APPLICATION_NAME'];?>"/></td>
								</tr>
								<tr>
									<th>AWS_ACCESS_KEY_ID：</th><td colspan="3"><input type="text" id="AWS_ACCESS_KEY_ID" value="<?php echo $account[0]['sc_amazon_account']['AWS_ACCESS_KEY_ID'];?>"/></td>
								</tr>
								<tr>
									<th>AWS_SECRET_ACCESS_KEY：</th><td colspan="3"><input type="text" id="AWS_SECRET_ACCESS_KEY" value="<?php echo $account[0]['sc_amazon_account']['AWS_SECRET_ACCESS_KEY'];?>"/></td>
								</tr>
								
								<tr>
									<th>APPLICATION_VERSION：</th><td><input type="text" id="APPLICATION_VERSION" value="<?php echo $account[0]['sc_amazon_account']['APPLICATION_VERSION'];?>"/></td>
									<th>MERCHANT_ID：</th><td><input type="text" id="MERCHANT_ID" value="<?php echo $account[0]['sc_amazon_account']['MERCHANT_ID'];?>"/></td>
								</tr>
								<tr>
									<th>MARKETPLACE_ID：</th><td><input type="text" id="MARKETPLACE_ID" value="<?php echo $account[0]['sc_amazon_account']['MARKETPLACE_ID'];?>"/></td>							
									<th>MERCHANT_IDENTIFIER：</th><td><input type="text" id="MERCHANT_IDENTIFIER" value="<?php echo $account[0]['sc_amazon_account']['MERCHANT_IDENTIFIER'];?>"/></td>
								</tr>
							</tbody>
							<tbody class="ebay-tbody">
								<tr>
									<th>EBAY_APP_MODE：</th>
									<td colspan=3>
									<select  id="EBAY_APP_MODE" >
										<option value="1"  <?php echo $account[0]['sc_amazon_account']['EBAY_APP_MODE']==1?"selected":"";?>>测试环境（Sandbox）</option>
										<option value="0" <?php echo $account[0]['sc_amazon_account']['EBAY_APP_MODE']==0?"selected":"";?>>正式环境（Product）</option>
									</select>
									</td>
								</tr>
								<tr>
									<th>EBAY_SITE_ID：</th>
									<td colspan=3>
									<select  id="EBAY_SITE_ID" >
										<option value="0" selected>美国</option>
									</select>
									</td>
								</tr>
								<tr>
									<th>EBAY_DEV_ID：</th>
									<td colspan=3>
									<input type="text" id="EBAY_DEV_ID" value="<?php echo $account[0]['sc_amazon_account']['EBAY_DEV_ID'];?>"/>
									</td>
								</tr>
								<tr>
									<th>EBAY_APP_ID：</th>
									<td colspan=3>
									<input type="text" id="EBAY_APP_ID" value="<?php echo $account[0]['sc_amazon_account']['EBAY_APP_ID'];?>"/>
									</td>
								</tr>
								<tr>
									<th>EBAY_CERT_ID：</th>
									<td colspan=3>
									<input type="text" id="EBAY_CERT_ID" value="<?php echo $account[0]['sc_amazon_account']['EBAY_CERT_ID'];?>"/>
									</td>
								</tr>
								<tr>
									<th>EBAY_TOKEN：</th>
									<td colspan=3>
									<input type="text" id="EBAY_TOKEN" value="<?php echo $account[0]['sc_amazon_account']['EBAY_TOKEN'];?>"/>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					
					<!-- panel脚部内容-->
                    <div class="panel-foot" style="background:#FFF;">
						<div class="form-actions ">
							<button type="button" class="btn btn-primary save-user">提&nbsp;交</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</body>

</html>