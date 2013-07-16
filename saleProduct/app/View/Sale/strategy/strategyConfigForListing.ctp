<!DOCTYPE html PUBLIC "-//W3C//Dth XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/Dth/xhtml1-transitional.dth">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>策略配置</title>
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
		echo $this->Html->script('modules/sale/strategyConfigForListing');
		
		//sql_account_product_list
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$accountProduct = $SqlUtils->getObject("sql_account_product_list",array("accountId"=>$params['arg1'],"sku"=>$params['arg2'])) ;
		
		
		$result = $SqlUtils->exeSqlWithFormat("sql_saleStrategy_findListingConfig",array("accountId"=>$params['arg1'],"sku"=>$params['arg2'])) ;
		$result = json_encode($result) ;
	?>
	
	<script>
	var accountId = '<?php echo $params['arg1'] ;?>' ;
	var sku = '<?php echo $params['arg2'] ;?>' ;
	var configs = <?php echo $result ;?> ;
	</script>
	
	<style type="text/css">

		.strategy-div{
			width:100%;
			overflow:auto;
		}
		
		.hour-row th{
			text-align: center!important;
		}
		
		.edit_config{
			/*filter:alpha(opacity=20);
			-moz-opacity:0.2;
			-khtml-opacity: 0.2;
			opacity: 0.2;*/
			display:none;
			cursor:pointer;
			width:13px;
			height:13px;
			right:2px;
			top:2px;
			position:absolute;
		}
		
		.strategy-details tbody td{
			position:relative;
		}
		
		.alert{
			-webkit-border-radius: 0px;
			-moz-border-radius: 0px;
			border-radius: 0px;
		}
		
		.product-info{
			height:70px;
		}
		
		.product-info div div div{
			padding:2px;
			font-weight: bold;
		}
		
		td.label-success{
			color:#FFF;
			font-weight:bold;
			font-size:13px;
		}
		
		ul li{
			list-style: none;
			padding:3px;
			position:relative;
		}
		
		ul li .delete{
			position:absolute;
			cursor:pointer;
		}
	</style>
	
	<script type="text/javascript">
		
	</script>
  
</head>

<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page">
		<!-- 页面标题 -->
		<div class="page-title">
			<h2>策略配置</h2>
		</div>
		<div class="container-fluid">
				<!-- panel 头部内容  此场景下是隐藏的-->
				<div class="panel apply-panel">
					<!-- panel 中间内容-->
					<div class="panel-content" style="padding:2px;">
						<div class="product-info">
							<div class="row-fluid">
								<div class="span1">
									<?php
										$url = str_replace("%" , "%25",$accountProduct['P_LOCAL_URL']) ;
										$imgString = "<img src='/".$fileContextPath."/".$url."' style='width:70px;height:70px;'>" ;
										echo $imgString ;
								   ?>
								</div>
								<div class="span8">
									<div><a href="#" offer-listing="<?php echo $accountProduct['ASIN']?>"><?php echo $accountProduct["P_TITLE"]?>(<?php echo $accountProduct['ASIN']?>) </a></div>
									<div>
											 销售渠道：<?php echo $accountProduct['FULFILLMENT_CHANNEL'] ;?>
									</div>
									<div>
									    是否FM产品： <?php echo $accountProduct['IS_FM']; ?>
									</div>
								</div>
								<div class="span3">
									<table>
										<tr>
											<td>
												<div>在售价格：<?php echo $accountProduct['PRICE'] ; ?></div>
												<div>运输费用：<?php echo $accountProduct['SHIPPING_PRICE'] ; ?></div>
											</td>
											<td>
												<div>排名：<?php
													$pm = '' ;
													if($accountProduct['FULFILLMENT_CHANNEL']  != 'Merchant') $pm = $accountProduct['FBA_PM']  ;
													else if( $accountProduct['ITEM_CONDITION']  == '1' ) $pm =  $accountProduct['U_PM']||'-'  ;
													else if( $accountProduct['IS_FM'] == 'FM' ) $pm =  $accountProduct['F_PM']||'-'  ;
													else if($accountProduct['IS_FM']  == 'NEW' ) $pm =   $accountProduct['N_PM']||'-'  ;
													if(!$pm || $pm == '0') echo '-' ;
													echo $pm ;
												?></div>
												<div>最低价：<?php
												if( $accountProduct['FULFILLMENT_CHANNEL'] != 'Merchant') echo $accountProduct['FBA_PRICE'] ;
												else if( $accountProduct['ITEM_CONDITION'] == '1' ) echo $accountProduct['FBM_U_PRICE'] ;
												else if( $accountProduct['IS_FM'] == 'FM' ) echo  $accountProduct['FBM_F_PRICE'] ;
												else if( $accountProduct['IS_FM'] == 'NEW' ) echo  $accountProduct['FBM_N_PRICE'] ;
												echo "" ;?></div>
											</td>
										</tr>
									</table>
								</div>
							</div>
							
						</div>
					
						<div class="row-fluid" style="margin:0px;">
							<div class="span1">
								<table class="form-table" >
									<tbody>										   
										<tr>
											<th style="width:10%;">星期</th>
										</tr>
										<tr>
											<th>星期一</th>
										</tr>
										<tr>
											<th>星期二</th>
										</tr>
										<tr>
											<th>星期三</th>
										</tr>
										<tr>
											<th>星期四</th>
										</tr>
										<tr>
											<th>星期五</th>
										</tr>
										<tr>
											<th>星期六</th>
										</tr>
										<tr>
											<th>星期日</th>
										</tr>
									</tbody>
								</table>
							</div>
							<div class="span11"  style="margin-left:0px;">
								<div class="strategy-div">
									<table class="form-table strategy-details "  style="margin-left:0px;">
										<thead>										   
											<tr class="hour-row">
												
											</tr>
										</thead>
										<tbody>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<!-- 数据列表样式 -->
						
						<!-- 备注 -->
						<div style="height:174px;overflow-y:auto;">
							<ul>
							<?php 
							$memos  = $SqlUtils->exeSqlWithFormat("sql_saleStrategyMemo_find",array("accountId"=>$params['arg1'],"sku"=>$params['arg2'])) ;
							//debug($memos) ;
							foreach( $memos as $memo ){
							?>
								<li class="memo-item"  memoId ="<?php echo $memo['ID'];?>">
									<?php echo $memo['MEMO'];?>
									&nbsp;&nbsp;&nbsp;&nbsp;(
									<?php echo $memo['USERNAME'] ;?>
									&nbsp;
									<?php echo $memo['CREATE_DATE'] ;?>
									)
								</li>
							<?php 
							//	debug($memo) ;
							}
							?>
							<li>
							<textarea placeHolder="输入备注" style="width:80%;" class="stragegymemo"></textarea>
							<button class="btn  save-stragegymemo"> 保存</button>
							</li>
							
							</ul>
							
						</div>
						
					</div>
					
					<!-- panel脚部内容-->
                    <div class="panel-foot">
						<div class="form-actions">
							<button type="button" class="btn btn-primary save-stragegy">提&nbsp;交</button>
						</div>
					</div>
				</div>
		</div>
	</div>
	
</body>
</html>