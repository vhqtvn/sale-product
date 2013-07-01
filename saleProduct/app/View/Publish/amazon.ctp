<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>用户编辑</title>
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
		echo $this->Html->script('modules/users/edit_user');
		
		$u = $user ;
		
		/*
		<Header>
		<DocumentVersion>1.01</DocumentVersion>
		<MerchantIdentifier>AMZ ID</MerchantIdentifier>
		</Header>
		<MessageType>Product</MessageType>
		<PurgeAndReplace>false</PurgeAndReplace>
		<Message>
		<MessageID>1</MessageID>
		<OperationType>Update</OperationType>
		<Product>
		<SKU>ABFOB.12YOV1</SKU>
		<StandardProductID>
			<Type>ASIN</Type>
			<Value>B001P4WGQ6</Value>
		</StandardProductID>
		<LaunchDate>2012-06-18T04:59:29+01:00</LaunchDate>
		<DescriptionData>
			<Title>Aberfeldy 12 Year Old / 70cl</Title>
			<Brand>Aberfeldy</Brand>
			<Description>An award winning Eastern Highland malt that was almost unknown until it was bought by Bacardi in 1998, Aberfeldy's main claim to fame is as the heart of the excellent Dewar's blend.  Clean and polished malt with a touch of honey and spice.    Web-Exclusive Price!</Description>
			<BulletPoint>12 Years Old</BulletPoint>
			<BulletPoint>Bottled by Distillery Bottling</BulletPoint>
			<PackageWeight unitOfMeasure="KG">1.50</PackageWeight>
			<Manufacturer>Aberfeldy</Manufacturer>
			<ItemType>AlcoholicBeverages</ItemType>
			<RecommendedBrowseNode>359893031</RecommendedBrowseNode>
		</DescriptionData>
		<ProductData>
			<FoodAndBeverages>
				<ProductType>
					<AlcoholicBeverages>
						<CountryProducedIn>Scotland</CountryProducedIn>
						<RegionOfOrigin>Highland</RegionOfOrigin>
						<AlcoholContent unitOfMeasure="percent_by_volume">40.00</AlcoholContent>
					</AlcoholicBeverages>
				</ProductType>
			</FoodAndBeverages>
		</ProductData>
		</Product>
		</Message>*/
	?>
  
</head>

<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page">
		<!-- 页面标题 -->
		<div class="page-title">
			<h2>用户信息</h2>
		</div>
		<div class="container-fluid">

	        <form id="personForm" action="#" data-widget="validator,ajaxform" class="form-horizontal" >
	        	<input type="hidden" id="id" value="<?php echo $u['ID'];?>"/>
				<!-- panel 头部内容  此场景下是隐藏的-->
				<div class="panel apply-panel">
					<!-- panel 中间内容-->
					<div class="panel-content">
						<!-- 数据列表样式 -->
						<table class="form-table col2" >
							<caption>基本信息</caption>
							<tbody>										   
								<tr>
									<th>SKU：</th>
									<td></td>
									<th>ASIN：</th>
									<td></td>
								</tr>
								<tr>
									<th>$productTaxCode：</th><td>
									</td>
								</tr>
								<tr>
									<th>$title：</th><td><input type="password"  
										data-validator="equalToField[repassword]" id="password"/></td>
								</tr>
								<tr>
									<th>$Brand：</th><td><input type="password"  id="repassword"/></td>
								</tr>
								<tr>
									<th>$Description：</th>
									<td><input type="text"
										id="phone" value="<?php echo  $u['PHONE'];?>"/></td>
								</tr>
								<tr>
									<th>$BulletPoint1：</th>
									<td><input type="text"
										id="email" data-validator="email" value="<?php echo  $u['EMAIL'];?>"/></td>
								</tr>
								<tr>
									<th>$BulletPoint2：</th>
									<td><input type="text"
										id="email" data-validator="email" value="<?php echo  $u['EMAIL'];?>"/></td>
								</tr>
								<tr>
									<th>$currency：</th>
									<td><input type="text"
										id="email" data-validator="email" value="<?php echo  $u['EMAIL'];?>"/></td>
									<th>$MSRP：</th>
									<td><input type="text"
										id="email" data-validator="email" value="<?php echo  $u['EMAIL'];?>"/></td>
								</tr>
								<tr>
									<th>$$Manufacturer：</th>
									<td><input type="text"
										id="email" data-validator="email" value="<?php echo  $u['EMAIL'];?>"/></td>
								</tr>
								<tr>
									<th>$ItemType：</th>
									<td><input type="text"
										id="email" data-validator="email" value="<?php echo  $u['EMAIL'];?>"/></td>
								</tr>
								<tr>
									<th>Ingredients：</th>
									<td><input type="text"
										id="email" data-validator="email" value="<?php echo  $u['EMAIL'];?>"/></td>
								</tr>
								<tr>
									<th>Directions：</th>
									<td><input type="text"
										id="email" data-validator="email" value="<?php echo  $u['EMAIL'];?>"/></td>
								</tr>
							</tbody>
						</table>
					</div>
					
					<!-- panel脚部内容-->
                    <div class="panel-foot">
						<div class="form-actions col2">
							<button type="button" class="btn btn-primary save-user">提&nbsp;交</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</body>
</html>