<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>平台配置</title>
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
		$platformId = $params['arg1'] ;
		
		$platform =  $SqlUtils->getObject("select * from sc_platform where id= '{@#id#}'",array("id"=>$platformId)) ;
		//debug( $platform ) ;
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

					$.dataservice("model:System.savePlatform",json,function(result){
						$.unblock() ;
						$.dialogReturnValue(true) ;
						window.close() ;
					});
				};
			});

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
	        	<input type="hidden" id="ID" value="<?php echo $platform['ID'];?>"/>
				<!-- panel 头部内容  此场景下是隐藏的-->
				<div class="panel apply-panel">
					<!-- panel 中间内容-->
					<div class="panel-content" style="margin-bottom:50px;">
						<!-- 数据列表样式 -->
						<table class="form-table " >
							<tbody>										   
								<tr>
									<th style="width:170px;">平台名称：</th>
									<td><input data-validator="required" type="text" id="NAME"  value="<?php echo $platform['NAME'];?>"/></td>
									<th style="width:170px;">平台代码：</th>
									<td><input data-validator="required" type="text" id="CODE" disabled value="<?php echo $platform['CODE'];?>"/></td>
								</tr>
								<tr>
									<th style="width:170px;">采集处理器：</th>
									<td>
										<select  id="PROCCESS"   style="width:97%;" >
												<option value="">--选择--</option>
										    	<option value="AmazonGatherImpl"  <?php  echo  $platform['PROCCESS']=='AmazonGatherImpl'?"selected":"";?>>Amazon采集处理器</option>
											   <option value="EbayGatherImpl"   <?php  echo  $platform['PROCCESS']=='EbayGatherImpl'?"selected":"";?>>Ebay采集处理器</option>
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
					                             	  $selected = $platform['EXCHANGE_ID'] == $w['ID'] ?"selected":"" ;
					                             	  echo "<option $selected value='".$w['ID']."'>".$w['SOURCE_NAME']."</option>" ;
					                             }
											   ?>
											</select>
									</td>
									
								</tr>
								<tr>
									<th>FBA库存集中费：</th>
									<td>
										<input type="text" id="INVENTORY_CENTER_FEE" value="<?php echo $platform['INVENTORY_CENTER_FEE'];?>"/>
									</td>
									<th>账户地区税率：</th>
									<td>
									<input type="text" id="FEE_RATIO" value="<?php echo $platform['FEE_RATIO'];?>"/>
									</td>
								</tr>
								<tr>
									<th>供应周期（天）：</th>
									<td>
										<input type="text" id="SUPPLY_CYCLE" value="<?php echo $platform['SUPPLY_CYCLE'];?>"/>
									</td>
									<th>需求调整系数：</th>
									<td>
									<input type="text" id="REQ_ADJUST" value="<?php echo $platform['REQ_ADJUST'];?>"/>
									</td>
								</tr>
								<tr>
									<th>转仓物流单价（$/KG）：</th>
									<td>
										<input type="text" id="TRANSFER_WH_PRICE" value="<?php echo $platform['TRANSFER_WH_PRICE'];?>"/>
									</td>
									<th>账户平均转化率：</th>
									<td>
										<input type="text" id="CONVERSION_RATE" value="<?php echo $platform['CONVERSION_RATE'];?>"/>
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