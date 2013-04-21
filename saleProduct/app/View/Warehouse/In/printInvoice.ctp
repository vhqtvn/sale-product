<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>物流计划发票</title>
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
		echo $this->Html->script('modules/warehouse/in/process');
		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$in = $SqlUtils->getObject("sql_warehouse_in_getById",array('id'=>$params['arg1'] )) ;
		
		$inProducts = $SqlUtils->exeSql("sql_warehouse_in_products",array('inId'=>$params['arg1'] )) ;
		
		
	?>
  
   <script type="text/javascript">
	   	var inId = '<?php echo $params['arg1'] ;?>' ;	
	   	var warehouseId =  '<?php echo $in['WAREHOUSE_ID'] ;?>' ;	
   </script>
   
    <style type="text/css">
			.en-label{
				font-size:13px;
				font-weight:normal; 
			}
			.zh-label{
				font-size:12px;
				font-weight:normal; 
			}
			
			.invoice-value{
				font-size:15px;
				font-weight:bold;
			}
			
			table td{
				vertical-align: middle!important;
			}
			
			.table-bordered {
				border: 1px solid #EEE;
				}
				
				.table th, .table td {
					padding:2px 8px;
				}
			
				.table-bordered th, .table-bordered td {
				border-left: 1px solid #EEE;
				}
				.table th, .table td {
				border-bottom: 1px solid #EEE;
				}
	</style>
</head>
<body style="margin:0px;padding:2px 10px;">
	<center>
	<div class="invoice-title">
			<h2 class="en-title">COMERCIAL INVOICE</h2>
			<h3 class="zh-title">形式发票</h3>
			<div class="invoice-date"  style="position:absolute;right:10px;top:45px;">
				<h5>DATE:  <?php echo date('Y-m-d')?></h5>
			</div>
	</div>
	<hr style="border-color:#EEE;margin:0px;margin-bottom:5px;"/>
	</center>
	<div class="invoice-base">
			<table class="table table-bordered">
				<tr>
					<th class="span4">
					<div class="invoice-label">
						<div class="en-label">INETERNATIONAL AIR WAYBILL NO</div>
						<div class="zh-label">运单号码</div>
					</div>
					 </th>
					<td colspan="3">
					<div class="invoice-value">
						<?php
							 $d =  $in['SHIP_NO'] ;
							 echo $d ;
						?>
						</div>
					</td>
				</tr>
				<tr>
					<th>
					<div class="invoice-label">
						<div class="en-label">DATE OF EXPORTATION</div>
						<div class="zh-label">出口日期</div>
					</div>
					</th>
					<td>
						<div class="invoice-value">
						<?php
							 $d =  $in['SHIP_DATE'] ;
							 $d = explode(" ", $d);
							 echo $d[0] ;
						?>
						</div>
					</td>
					<th>
					<div class="invoice-label">
						<div class="en-label">ORIGIN</div>
						<div class="zh-label">原产地</div>
					</div> 
					</th>
					<td>
						<div class="invoice-value">
						CHINA
						</div>
					</td>
				</tr>
			</table>
			<table class="table table-bordered">
			<!-- 寄件人信息 -->
			<tr>
					<th class="span4">
					<div class="invoice-label">
						<div class="en-label">SHIPPER NAME</div>
						<div class="zh-label">寄件人姓名</div>
					</div>
					 </th>
					<td>
					<div class="invoice-value">
						<?php
							 $d =  $in['SEND_COMPANY_CONTACTOR'] ;
							 echo $d ;
						?>
						</div>
					</td>
					<th>
					<div class="invoice-label">
						<div class="en-label">SHIPPER'S PHONE</div>
						<div class="zh-label">寄件人电话</div>
					</div>
					 </th>
					<td>
					<div class="invoice-value">
						<?php
							 $d =  $in['SEND_COMPANY_PHONE'] ;
							 echo $d ;
						?>
						</div>
					</td>
				</tr>
				<tr>
					<th>
					<div class="invoice-label">
						<div class="en-label">SHIPPER'S EMAIL</div>
						<div class="zh-label">寄件人Email</div>
					</div>
					 </th>
					<td colspan="3">
					<div class="invoice-value">
						<?php
							 $d =  $in['SEND_COMPANY_EMAIL'] ;
							 echo $d ;
						?>
						</div>
					</td>
				</tr>
				<tr>
					<th>
					<div class="invoice-label">
						<div class="en-label">SHIPPER'S POST</div>
						<div class="zh-label">寄件人邮编</div>
					</div>
					 </th>
					<td>
					<div class="invoice-value">
						<?php
							 $d =  $in['SEND_COMPANY_POST'] ;
							 echo $d ;
						?>
						</div>
					</td>
					<th>
					<div class="invoice-label">
						<div class="en-label">SHIPPER'S COUNTRY</div>
						<div class="zh-label">寄件人国家</div>
					</div>
					 </th>
					<td>
					<div class="invoice-value">
						<?php
							 $d =  $in['SEND_COMPANY_COUNTRY'] ;
							 echo $d ;
						?>
						</div>
					</td>
				</tr>
				<tr>
					<th>
					<div class="invoice-label">
						<div class="en-label">SHIPPER'S COMPANY NAME</div>
						<div class="zh-label">寄件人公司名称</div>
					</div>
					 </th>
					<td colspan=3>
					<div class="invoice-value">
						<?php
							 $d =  $in['SEND_COMPANY'] ;
							 echo $d ;
						?>
						</div>
					</td>
				</tr>
				<tr>
					<th>
					<div class="invoice-label">
						<div class="en-label">SHIPPER'S COMPANY ADDRESS</div>
						<div class="zh-label">寄件人公司地址</div>
					</div>
					 </th>
					<td colspan=3>
					<div class="invoice-value">
						<?php
							 $d =  $in['SEND_COMPANY_ADDRESS'] ;
							 echo $d ;
						?>
						</div>
					</td>
				</tr>
			<!-- 收件人信息 -->
			<tr>
					<th>
					<div class="invoice-label">
						<div class="en-label">CONSIGNEE NAME</div>
						<div class="zh-label">收件人姓名</div>
					</div>
					 </th>
					<td>
					<div class="invoice-value">
						<?php
							 $d =  $in['RECEIVE_COMPANY_CONTACTOR'] ;
							 echo $d ;
						?>
						</div>
					</td>
					<th>
					<div class="invoice-label">
						<div class="en-label">CONSIGNEE'S PHONE</div>
						<div class="zh-label">收件人电话</div>
					</div>
					 </th>
					<td>
					<div class="invoice-value">
						<?php
							 $d =  $in['RECEIVE_COMPANY_PHONE'] ;
							 echo $d ;
						?>
						</div>
					</td>
				</tr>
				<tr>
					<th>
					<div class="invoice-label">
						<div class="en-label">CONSIGNEE'S EMAIL</div>
						<div class="zh-label">收件人Email</div>
					</div>
					 </th>
					<td colspan="3">
					<div class="invoice-value">
						<?php
							 $d =  $in['RECEIVE_COMPANY_EMAIL'] ;
							 echo $d ;
						?>
						</div>
					</td>
				</tr>
				<tr>
					<th>
					<div class="invoice-label">
						<div class="en-label">CONSIGNEE'S POST</div>
						<div class="zh-label">收件人邮编</div>
					</div>
					 </th>
					<td>
					<div class="invoice-value">
						<?php
							 $d =  $in['RECEIVE_COMPANY_POST'] ;
							 echo $d ;
						?>
						</div>
					</td>
					<th>
					<div class="invoice-label">
						<div class="en-label">CONSIGNEE'S COUNTRY</div>
						<div class="zh-label">收件人国家</div>
					</div>
					 </th>
					<td>
					<div class="invoice-value">
						<?php
							 $d =  $in['RECEIVE_COMPANY_COUNTRY'] ;
							 echo $d ;
						?>
						</div>
					</td>
				</tr>
				<tr>
					<th>
					<div class="invoice-label">
						<div class="en-label">CONSIGNEE'S COMPANY NAME</div>
						<div class="zh-label">收件人公司名称</div>
					</div>
					 </th>
					<td colspan=3>
					<div class="invoice-value">
						<?php
							 $d =  $in['RECEIVE_COMPANY'] ;
							 echo $d ;
						?>
						</div>
					</td>
				</tr>
				<tr>
					<th>
					<div class="invoice-label">
						<div class="en-label">CONSIGNEE'S COMPANY ADDRESS</div>
						<div class="zh-label">收件人公司地址</div>
					</div>
					 </th>
					<td colspan=3>
					<div class="invoice-value">
						<?php
							 $d =  $in['RECEIVE_COMPANY_ADDRESS'] ;
							 echo $d ;
						?>
						</div>
					</td>
				</tr>
			</table>
			
			
	</div>

	<div class="invoice-details">
		<table class="table table-bordered">
			<tr>
				<th>
					<div class="invoice-label">
						<div class="en-label">NO.OF PKG(S)</div>
						<div class="zh-label">件数</div>
					</div>
				</th>
				<th>
				<div class="invoice-label">
						<div class="en-label">DESCRIPTION OF GOODS</div>
						<div class="zh-label">货物描述</div>
					</div>
				</th>
				<th>
					<div class="invoice-label">
						<div class="en-label">PC(S)</div>
						<div class="zh-label">数量</div>
					</div>
				</th>
				<th>
					<div class="invoice-label">
						<div class="en-label">UNIT VALUE(USD)</div>
						<div class="zh-label">单价</div>
					</div>
				</th>
				<th>
					<div class="invoice-label">
						<div class="en-label">TOTAL VALUE(USD)</div>
						<div class="zh-label">总价</div>
					</div>
				</th>
			</tr>
			<?php 
			   $total = 0 ;
			  foreach( $inProducts as $p ){
				$p = $SqlUtils->formatObject($p) ;
				$total = $total + $p['DECLARATION_PRICE'] * $p['QUANTITY'] ;
			?>
				<tr>
					<td><div class="invoice-value"></div></td>
					<td><div class="invoice-value"><?php echo $p['DECLARATION_NAME'] ;?></div></td>
					<td><div class="invoice-value"><?php echo $p['QUANTITY'] ;?></div></td>
					<td><div class="invoice-value"><?php echo $p['DECLARATION_PRICE'] ;?></div></td>
					<td><div class="invoice-value"><?php echo $p['DECLARATION_PRICE'] * $p['QUANTITY']   ;?></div></td>
				</tr>
			<?php }?>
		</table>
		
		<div class="invoice-total" style="float:right">
			<h3>TOTAL:<?php echo  'USD'.$total;?></h3>
		</div>
		<div style="clear:both;"></div>
		
	</div>
</body>
</html>
