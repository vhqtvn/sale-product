<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>Ebay 刊登物品</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>
	
	<script type="text/javascript">
	   	var base_dir='/';
	</script>
	<?php
  		include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../ebay_publish_files/screen');
		echo $this->Html->css('../ebay_publish_files/pagination');
		echo $this->Html->css('../ebay_publish_files/facebox');
		echo $this->Html->css('../ebay_publish_files/02');
		echo $this->Html->css('../ebay_publish_files/jquery.combobox');
		echo $this->Html->css('../ebay_publish_files/default');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('../js/listselectdialog/jquery.listselectdialog');
		
		//echo $this->Html->script('../ebay_publish_files/jquery-1.4.min');
		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('../ebay_publish_files/god');
		echo $this->Html->script('../ebay_publish_files/facebox');
		echo $this->Html->script('../ebay_publish_files/act');
		echo $this->Html->script('../ebay_publish_files/countrytime');
		echo $this->Html->script('../ebay_publish_files/jquery.combobox.min');
		echo $this->Html->script('../ebay_publish_files/kindeditor-min');
		echo $this->Html->script('validator/jquery.validation');
		echo $this->Html->script('listselectdialog/jquery.listselectdialog');
		
		$PublishUtils  = ClassRegistry::init("PublishUtils") ;
		$Utils  = ClassRegistry::init("Utils") ;
		
		$accountId = $params['arg1'] ;
		$templateId = $params['arg2'] ;
		$template = null ;
		if(!empty($templateId))
			$template = $PublishUtils->getObject("select * from sc_ebay_template where id = '{@#id#}'",array("id"=>$templateId)) ;
		
		$templateJson = json_encode($template) ;
	?>
	
	<style type="text/css">
		th{
			vertical-align: middle;
		}
	</style>
	<script>
		$(function(){
			
		}) ;
		</script>
</head>
<body>
	<div class="container">

		<div class="bottom694 span-24 last" style="width: 100%">
			<div class="content">
				<form action="<?php echo $contextPath;?>/publishEbay/saveTemplate" method="post" name="m" id="m" target="_self"   data-widget="validator">
					<input type="hidden" id="currency" name="currency" value="USD" />
					<input type="hidden" id="id" name="id" value="" class="val_ID" />
					<input type="hidden" id="accountId" name="accountId" value="<?php echo $accountId;?>"  />
					<table class="ebay_table" id="pos_site">
						<caption>刊登平台与细节</caption>
						<tbody>
							<tr>
								<th class="lh">平台</th>
								<td>
										<select id="site" name="site"  class="val_SITE">
												<option value="0" selected="selected">美国</option>
										</select>
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										<!-- 
										<b>其他平台可见($)</b>:
										<input id="crossbordertrade" type="checkbox" name="crossbordertrade" value="1"/> 
										<label for="crossbordertrade"> ebay.co.uk </label>
										 -->
								</td>
							</tr>
							<tr>
								<th>出售方式 <span class="needstar">*</span></th>
								<td>
									<select id="listingtype" name="listingtype"  class="val_LISTINGTYPE" data-validator="required">
											<option value="Chinese">拍卖</option>
											<option value="FixedPriceItem">一口价</option>
									</select>
								</td>
							</tr>
							<tr>
								<th>刊登分类<span class="needstar">*</span></th>
								<td>
									<span> 
									<input size="10" value=""
										name="primarycategory" id="primarycategory"  class="val_PRIMARYCATEGORY"/>
											&nbsp; <a class="select-category"
											href="#"
											style="font-weight: bold">浏览eBay产品分类</a>
											&nbsp;&nbsp; </span> 
									<input  value="" style="width:400px;" readOnly="readonly"
										name="primarycategorytext" id="primarycategorytext"  class="val_PRIMARYCATEGORYTEXT"/>		
											<input type="hidden" name="attribute"
									id="attribute" value="" class="val_ATTRIBUTE"/>
								</td>
							</tr>
							<tr>
								<th>物品状况(Condition)<span class="needstar">*</span></th>
								<td id="itemCondition">请先设置分类</td>
							</tr>

							<tr>
								<th>Item Specifics</th>
								<td>
									<table id="speci" class="ebay_table">
									</table>
								</td>
							</tr>
						</tbody>
					</table>
					<table class="ebay_table" id="pos_title">
						<caption>标题与价格</caption>
						<tbody>
							<tr>
								<th>刊登标题<span class="needstar">*</span></th>
								<td><input type="text" name="itemtitle" id="itemtitle"  value="" 
									class="itemTitle val_ITEMTITLE" size="80"    data-validator="required,length[1,80]"/> <span
										id="length_itemtitle" style="font-weight: bold; color: green;">80</span>
								</td>
							</tr>
							<tr>
								<th class="lh">数量</th>
								<td>
									<table>
										<tbody>
											<tr>
												<td><input type="text" name="quantity" value="1" class="val_QUANTITY"
													size="3" disabled="disabled" /></td>
												<td width="50"><strong>LotSize：</strong></td>
												<td width="400"><input type="text" name="lotsize" class="val_LOTSIZE"
													value="0" size="3"/>(每数量1中所包含的小单位 ) </td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
							<tr>
								<th>上架时间<span class="needstar">*</span></th>
								<td>
								<select id="listingduration" name="listingduration" class="val_LISTINGDURATION">
										<option value="Days_1">1天</option>
										<option value="Days_3">3天</option>
										<option value="Days_5" >5天</option>
										<option value="Days_7" >7天</option>
										<option value="Days_10" >10天</option>
								</select> 
								<select id="listingduration_fixedprice"
									name="listingduration_fixedprice" style="display: none">
										<option value="Days_3">3天</option>
										<option value="Days_5">5天</option>
										<option value="Days_7">7天</option>
										<option value="Days_10">10天</option>
										<option value="Days_30">30天(此选项仅用于店铺与部分平台)</option>
										<option value="GTC">卖完为止(此选项仅用于店铺)</option>
								</select> 
								<select id="listingduration_auction"
									name="listingduration_auction" style="display: none">
										<option value="Days_1">1天</option>
										<option value="Days_3">3天</option>
										<option value="Days_5">5天</option>
										<option value="Days_7">7天</option>
										<option value="Days_10">10天</option>
								</select></td>
							</tr>
							<tr class="auction_only" style="">
								<th id="startprice_label">拍卖底价 (≥0.01) <span
									class="needstar">*</span>
								</th>
								<td>
									<table>
										<tbody>
											<tr>
												<td>
													<div id="startprice_ucurrencyinput">
														<input type="text" class="pinput" name="startprice" class="val_STARTPRICE"
															id="startprice" value="0.0" size="6"/> USD
															&nbsp;&nbsp; <span class="upinput" style="display: none;">
																<input type="text" value="0" size="6"/> CNY 
														</span> <span class="upview" style=""> <span>0</span> CNY
														</span>
													</div> <script language="javascript">
													<?php echo $PublishUtils->getShippingServiceCost("startprice") ?>
</script>
												</td>
												<td width="120">&nbsp; &nbsp; （<strong>保底拍卖价</strong><span
													class="lableebayfee">($)</span>
												</td>
												<td width="230">
													<div id="reserveprice_ucurrencyinput">
														<input type="text" class="pinput" name="reserveprice" class="val_RESERVEPRICE"
															id="reserveprice" value="0.0" size="6"/> USD
															&nbsp;&nbsp; <span class="upinput" style="display: none;">
																<input type="text" value="0" size="6"/> CNY 
														</span> <span class="upview" style=""> <span>0</span> CNY
														</span>
													</div> <script language="javascript">
																	<?php echo $PublishUtils->getShippingServiceCost("reserveprice") ?>
																</script>
												</td>
												<td></td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
							<tr>
								<th id="buyitnowprice_label">一口价 (≥0.99) <span
									class="needstar fixedprice_only" style="display: none;">*</span>
								</th>
								<td>
									<div id="buyitnowprice_ucurrencyinput">
										<input type="text" class="pinput val_BUYITNOWPRICE" name="buyitnowprice"
											id="buyitnowprice" value="0.0" size="6"/> USD
											&nbsp;&nbsp; <span class="upinput" style="display: none;">
												<input type="text" value="0" size="6"/> CNY 
										</span> <span class="upview" style=""> <span>0</span> CNY
										</span>
									</div> <script language="javascript">
													<?php echo $PublishUtils->getShippingServiceCost("buyitnowprice") ?>
												</script>
								</td>
							</tr>
							<tr class="auction_only" style="">
								<th>SecondOffer</th>
								<td>&gt;=<input type="text" name="secondoffer" size="5" class="val_SECONDOFFER"
									value="0.0"/>USD
										(系统将自动在拍卖贴结束后,为拍卖者价格超过您设置价格的买家自动发送secondoffer)</td>
							</tr>
						</tbody>
					</table>
					<table class="ebay_table" id="pos_desc">
						<caption>图片与描述</caption>
						<tbody>
							<tr>
								<th class="lh">刊登图片</th>
								<td colspan="2">
									<table width="100%">
										<tbody>
											<tr>
												<td><span class="lableebayfee">($)</span><font
													color="red">使用多张图片会产生额外的费用</font></td>
												<td align="right">
													<input type="button" value="上传图片(Smart服务器)"
														onclick="window.open('/index.php/muban/uploadpicture','上传图片','width=450,height=200,menubar=no,scrollbars=yes','true')"/>
													<input type="button" value="再添加一个图片" onclick="javascript:Addimgurl_input();return false;"/></td>
											</tr>
										</tbody>
									</table>
									<div id="div_imgurl_input">
										<div>
											<img id="img1" src="" width="50" height="50"/> <input
												id="img" type="text" name="imgurl[0]" size="80"
												onblur="javascript:imgurl_input_blur(this)" value="http://"/>
														<div id="divbindconstantpicture" class="divbindconstan"></div>
										</div>
									</div> <font color="red">ebay要求使用多图片功能,必须使用ebay的图片服务器.所以,请务必确认图片属性或通过'上传图片至ebay'功能来获取ebay服务器图片的url</font><br>
								</td>
							</tr>
							<tr>
								<td colspan="3">
								
									<script> 
							            KE.show({
							                id : 'itemdescription',
							                allowPreviewEmoticons : false,
							                shadowMode : true,
							                
							                allowUpload : false,
							                items : [
							                'source', '|','title','fontname', 'fontsize', '|', 'textcolor', 'bgcolor', 'bold', 'italic', 'underline','strikethrough', '|',
							                'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist','insertunorderedlist','indent', 'outdent', 'hr','removeformat', '|',
							                'link','unlink','image','advtable','fullscreen' ]
							            });
							        </script>
									
									<textarea id="itemdescription" name="itemdescription"
										style="height: 450px; width: 950px; display: none;"><?php echo $template['ITEMDESCRIPTION']?></textarea>
									<input type="button" classs="anniu" value="预览刊登效果"
									onclick="previewItem()"></td>
							</tr>
						</tbody>
					</table>

					<div id="div_shippingdetails">
						<table id="detail_wuliu" class="ebay_table">
							<caption>
								物流设置
								<div style="float: right;">
									<a href="#" class="profile_save">保存为范本</a> 
									<select id="detail_wuliu_profile" name="detail_wuliu_profile" class="profile">
										<option value=""></option>
									</select> 
									<a href="#" class="profile_load">读取</a> <a href="#"  class="profile_del">删除</a>
								</div>
							</caption>
							<tbody>
								<tr>
									<th class="lh">运至美国境内</th>
									<td></td>
								</tr>
								<tr class="tobecost">
								</tr>
								<tr id="tobecost0">
									<th>运输方式1</th>
									<td><select
										id="shippingdetails[ShippingServiceOptions][0][ShippingService]"
										name="shippingdetails[ShippingServiceOptions][0][ShippingService]"
										onchange="changeshipservice(0)"
										class="val_SD_SSO1_SHIPPINGSERVICE"
										>
											<option value="" selected="selected">选择境内物流</option>
											<?php echo $PublishUtils->getShippingService1();?>
									</select> &nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="免运费"
										onclick="freeship(1,0)"></td>
								</tr>
								<tr class="tobecost0" id="tobecost0">
									<th id="nei">首件运费</th>
									<td>
										<div id="ntr10_ucurrencyinput">
											<input type="text" class="pinput val_SD_SSO1_SHIPPINGSERVICECOST"
												name="shippingdetails[ShippingServiceOptions][0][ShippingServiceCost]"
												id="ntr10" value="0.0" size="6"> USD &nbsp;&nbsp; <span
												class="upinput" style="display: none;"> <input
													type="text" value="0" size="6"> CNY </span> <span
												class="upview" style=""> <span>0</span> CNY
											</span>
										</div> 
										<script language="javascript">
												<?php echo $PublishUtils->getShippingServiceCost("ntr10")?>
										</script>
									</td>
								</tr>
								<tr class="tobecost0" id="tobecost0">
									<th>续件运费</th>
									<td>
										<div id="ntr110_ucurrencyinput">
											<input type="text" class="pinput val_SD_SSO1_SHIPPINGSERVICEADDITIONALCOST"
												name="shippingdetails[ShippingServiceOptions][0][ShippingServiceAdditionalCost]"
												id="ntr110" value="0.0" size="6"> USD &nbsp;&nbsp; <span
												class="upinput" style="display: none;"> <input
													type="text" value="0" size="6"> CNY </span> <span
												class="upview" style=""> <span>0</span> CNY
											</span>
										</div> <script language="javascript">
										<?php echo $PublishUtils->getShippingServiceCost("ntr110")?>
									</script>
									</td>
								</tr>
								<tr class="tobecost">
								</tr>
								<tr id="tobecost11">
									<th>运输方式2</th>
									<td><select
										id="shippingdetails[ShippingServiceOptions][1][ShippingService]"
										name="shippingdetails[ShippingServiceOptions][1][ShippingService]"
										class="val_SD_SSO2_SHIPPINGSERVICE"
										onchange="changeshipservice(1)">
											<option value="" selected="selected">选择境内物流</option>
											<?php echo $PublishUtils->getShippingService1();?>
									</select> &nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="免运费"
										onclick="freeship(1,1)"></td>
								</tr>
								<tr class="tobecost1" id="tobecost1" style="display: none;">
									<th id="nei">首件运费</th>
									<td>
										<div id="ntr11_ucurrencyinput">
											<input type="text" class="pinput val_SD_SSO2_SHIPPINGSERVICECOST"
												name="shippingdetails[ShippingServiceOptions][1][ShippingServiceCost]"
												id="ntr11" value="0.0" size="6"> USD &nbsp;&nbsp; <span
												class="upinput" style="display: none;"> <input
													type="text" value="0" size="6"> CNY </span> <span
												class="upview" style=""> <span>0</span> CNY
											</span>
										</div> <script language="javascript">
										<?php echo $PublishUtils->getShippingServiceCost("ntr11")?>
</script>
									</td>
								</tr>
								<tr class="tobecost1" id="tobecost7" style="display: none;">
									<th>续件运费</th>
									<td>
										<div id="ntr111_ucurrencyinput">
											<input type="text" class="pinput val_SD_SSO2_SHIPPINGSERVICEADDITIONALCOST"
												name="shippingdetails[ShippingServiceOptions][1][ShippingServiceAdditionalCost]"
												id="ntr111" value="0.0" size="6"> USD &nbsp;&nbsp; <span
												class="upinput" style="display: none;"> <input
													type="text" value="0" size="6"> CNY </span> <span
												class="upview" style=""> <span>0</span> CNY
											</span>
										</div> <script language="javascript">
										<?php echo $PublishUtils->getShippingServiceCost("ntr111")?>
</script>
									</td>
								</tr>
								<tr class="tobecost">
								</tr>
								<tr id="tobecost22">
									<th>运输方式3</th>
									<td><select
										id="shippingdetails[ShippingServiceOptions][2][ShippingService]"
										name="shippingdetails[ShippingServiceOptions][2][ShippingService]"
										class="val_SD_SSO3_SHIPPINGSERVICE"
										onchange="changeshipservice(2)">
											<option value="" selected="selected">选择境内物流</option>
											<?php echo $PublishUtils->getShippingService1();?>
									</select> &nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="免运费"
										onclick="freeship(1,2)"></td>
								</tr>
								<tr class="tobecost2" id="tobecost2" style="display: none;">
									<th id="nei">首件运费</th>
									<td>
										<div id="ntr12_ucurrencyinput">
											<input type="text" class="pinput val_SD_SSO3_SHIPPINGSERVICECOST"
												name="shippingdetails[ShippingServiceOptions][2][ShippingServiceCost]"
												id="ntr12" value="0.0" size="6"> USD &nbsp;&nbsp; <span
												class="upinput" style="display: none;"> <input
													type="text" value="0" size="6"> CNY </span> <span
												class="upview" style=""> <span>0</span> CNY
											</span>
										</div> <script language="javascript">
										<?php echo $PublishUtils->getShippingServiceCost("ntr12")?>
</script>
									</td>
								</tr>
								<tr class="tobecost2" id="tobecost14" style="display: none;">
									<th>续件运费</th>
									<td>
										<div id="ntr112_ucurrencyinput">
											<input type="text" class="pinput val_SD_SSO3_SHIPPINGSERVICEADDITIONALCOST"
												name="shippingdetails[ShippingServiceOptions][2][ShippingServiceAdditionalCost]"
												id="ntr112" value="0.0" size="6"> USD &nbsp;&nbsp; <span
												class="upinput" style="display: none;"> <input
													type="text" value="0" size="6"> CNY </span> <span
												class="upview" style=""> <span>0</span> CNY
											</span>
										</div> <script language="javascript">
										<?php echo $PublishUtils->getShippingServiceCost("ntr112")?>
										</script>
									</td>
								</tr>
								<tr>
									<th class="lh">运至美国境外</th>
									<td></td>
								</tr>
								<tr class="tobecost"></tr>
								<tr id="tobecost33">
									<th>运输方式1</th>
									<td><select
										id="shippingdetails[InternationalShippingServiceOption][3][ShippingService]"
										name="shippingdetails[InternationalShippingServiceOption][3][ShippingService]"
										class="val_SD_ISSO1_SHIPPINGSERVICE"
										onchange="changeshipservice(3)">
											<option value="" selected="selected">选择境外物流</option>
											<?php echo $PublishUtils->getShippingService2();?>
									</select> &nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="免运费"
										onclick="freeship(2,3)"></td>
								</tr>
								<tr class="tobecost3" id="tobecost3" style="display: none;">
									<th id="wai">首件运费</th>
									<td>
										<div id="ntr23_ucurrencyinput">
											<input type="text" class="pinput val_SD_ISSO1_SHIPPINGSERVICECOST"
												name="shippingdetails[InternationalShippingServiceOption][3][ShippingServiceCost]"
												id="ntr23" value="0.0" size="6"> USD &nbsp;&nbsp; <span
												class="upinput" style="display: none;"> <input
													type="text" value="0" size="6"> CNY </span> <span
												class="upview" style=""> <span>0</span> CNY
											</span>
										</div> <script language="javascript">
	<?php echo $PublishUtils->getShippingServiceCost("ntr23")?>
</script>
									</td>
								</tr>
								<tr class="tobecost3" id="tobecost21" style="display: none;">
									<th>续件运费</th>
									<td>
										<div id="ntr223_ucurrencyinput">
											<input type="text" class="pinput val_SD_ISSO1_SHIPPINGSERVICEADDITIONALCOST"
												name="shippingdetails[InternationalShippingServiceOption][3][ShippingServiceAdditionalCost]"
												id="ntr223" value="0.0" size="6"> USD &nbsp;&nbsp; <span
												class="upinput" style="display: none;"> <input
													type="text" value="0" size="6"> CNY </span> <span
												class="upview" style=""> <span>0</span> CNY
											</span>
										</div> <script language="javascript">
										<?php echo $PublishUtils->getShippingServiceCost("ntr223")?>
</script>
									</td>
								</tr>
								<tr id="tobecost39" style="display: none;">
									<th>可运至的国家</th>
									<td style="border-bottom: 1px #CCC solid"><span
										style="white-space: nowrap;"><input type="checkbox"
											name="shippingdetails[InternationalShippingServiceOption][3][ShipToLocation][]"
											id="shippingdetails[InternationalShippingServiceOption][3][ShipToLocation]_6"
											value="Worldwide" checked="checked"> <label
												id="shippingdetails[internationalshippingserviceoption][3][shiptolocation]_6_label"
												name="shippingdetails[internationalshippingserviceoption][3][shiptolocation]_6_label"
												for="shippingdetails[InternationalShippingServiceOption][3][ShipToLocation]_6"
												class="">全球</label></span><br> <span
											style="white-space: nowrap;"><input type="checkbox"
												name="shippingdetails[InternationalShippingServiceOption][3][ShipToLocation][]"
												id="shippingdetails[InternationalShippingServiceOption][3][ShipToLocation]_1"
												value="Americas" disabled=""> <label
													id="shippingdetails[internationalshippingserviceoption][3][shiptolocation]_1_label"
													name="shippingdetails[internationalshippingserviceoption][3][shiptolocation]_1_label"
													for="shippingdetails[InternationalShippingServiceOption][3][ShipToLocation]_1"
													class="">美洲</label></span> &nbsp; &nbsp; <span
											style="white-space: nowrap;"><input type="checkbox"
												name="shippingdetails[InternationalShippingServiceOption][3][ShipToLocation][]"
												id="shippingdetails[InternationalShippingServiceOption][3][ShipToLocation]_2"
												value="Asia" disabled=""> <label
													id="shippingdetails[internationalshippingserviceoption][3][shiptolocation]_2_label"
													name="shippingdetails[internationalshippingserviceoption][3][shiptolocation]_2_label"
													for="shippingdetails[InternationalShippingServiceOption][3][ShipToLocation]_2"
													class="">亚洲</label></span> &nbsp; &nbsp; <span
											style="white-space: nowrap;"><input type="checkbox"
												name="shippingdetails[InternationalShippingServiceOption][3][ShipToLocation][]"
												id="shippingdetails[InternationalShippingServiceOption][3][ShipToLocation]_3"
												value="AU" disabled=""> <label
													id="shippingdetails[internationalshippingserviceoption][3][shiptolocation]_3_label"
													name="shippingdetails[internationalshippingserviceoption][3][shiptolocation]_3_label"
													for="shippingdetails[InternationalShippingServiceOption][3][ShipToLocation]_3"
													class="">澳大利亚</label></span> &nbsp; &nbsp; <span
											style="white-space: nowrap;"><input type="checkbox"
												name="shippingdetails[InternationalShippingServiceOption][3][ShipToLocation][]"
												id="shippingdetails[InternationalShippingServiceOption][3][ShipToLocation]_4"
												value="MX" disabled=""> <label
													id="shippingdetails[internationalshippingserviceoption][3][shiptolocation]_4_label"
													name="shippingdetails[internationalshippingserviceoption][3][shiptolocation]_4_label"
													for="shippingdetails[InternationalShippingServiceOption][3][ShipToLocation]_4"
													class="">墨西哥</label></span> &nbsp; &nbsp; <span
											style="white-space: nowrap;"><input type="checkbox"
												name="shippingdetails[InternationalShippingServiceOption][3][ShipToLocation][]"
												id="shippingdetails[InternationalShippingServiceOption][3][ShipToLocation]_5"
												value="CA" disabled=""> <label
													id="shippingdetails[internationalshippingserviceoption][3][shiptolocation]_5_label"
													name="shippingdetails[internationalshippingserviceoption][3][shiptolocation]_5_label"
													for="shippingdetails[InternationalShippingServiceOption][3][ShipToLocation]_5"
													class="">加拿大</label></span> &nbsp; &nbsp; &nbsp; &nbsp; <span
											style="white-space: nowrap;"><input type="checkbox"
												name="shippingdetails[InternationalShippingServiceOption][3][ShipToLocation][]"
												id="shippingdetails[InternationalShippingServiceOption][3][ShipToLocation]_7"
												value="DE" disabled=""> <label
													id="shippingdetails[internationalshippingserviceoption][3][shiptolocation]_7_label"
													name="shippingdetails[internationalshippingserviceoption][3][shiptolocation]_7_label"
													for="shippingdetails[InternationalShippingServiceOption][3][ShipToLocation]_7"
													class="">德国</label></span> &nbsp; &nbsp; <span
											style="white-space: nowrap;"><input type="checkbox"
												name="shippingdetails[InternationalShippingServiceOption][3][ShipToLocation][]"
												id="shippingdetails[InternationalShippingServiceOption][3][ShipToLocation]_8"
												value="Europe" disabled=""> <label
													id="shippingdetails[internationalshippingserviceoption][3][shiptolocation]_8_label"
													name="shippingdetails[internationalshippingserviceoption][3][shiptolocation]_8_label"
													for="shippingdetails[InternationalShippingServiceOption][3][ShipToLocation]_8"
													class="">欧洲</label></span> &nbsp; &nbsp; <span
											style="white-space: nowrap;"><input type="checkbox"
												name="shippingdetails[InternationalShippingServiceOption][3][ShipToLocation][]"
												id="shippingdetails[InternationalShippingServiceOption][3][ShipToLocation]_9"
												value="GB" disabled=""> <label
													id="shippingdetails[internationalshippingserviceoption][3][shiptolocation]_9_label"
													name="shippingdetails[internationalshippingserviceoption][3][shiptolocation]_9_label"
													for="shippingdetails[InternationalShippingServiceOption][3][ShipToLocation]_9"
													class="">英国</label></span> &nbsp; &nbsp; <span
											style="white-space: nowrap;"><input type="checkbox"
												name="shippingdetails[InternationalShippingServiceOption][3][ShipToLocation][]"
												id="shippingdetails[InternationalShippingServiceOption][3][ShipToLocation]_10"
												value="JP" disabled=""> <label
													id="shippingdetails[internationalshippingserviceoption][3][shiptolocation]_10_label"
													name="shippingdetails[internationalshippingserviceoption][3][shiptolocation]_10_label"
													for="shippingdetails[InternationalShippingServiceOption][3][ShipToLocation]_10"
													class="">日本</label></span> &nbsp; &nbsp; </td>
								</tr>
								<tr class="tobecost"></tr>
								<tr id="tobecost44">
									<th>运输方式2</th>
									<td><select
										id="shippingdetails[InternationalShippingServiceOption][4][ShippingService]"
										name="shippingdetails[InternationalShippingServiceOption][4][ShippingService]"
										class="val_SD_ISSO2_SHIPPINGSERVICE"
										onchange="changeshipservice(4)">
											<option value="" selected="selected">选择境外物流</option>
											<?php echo $PublishUtils->getShippingService2()?>
									</select> &nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="免运费"
										onclick="freeship(2,4)"></td>
								</tr>
								<tr class="tobecost4" id="tobecost4" style="display: none;">
									<th id="wai">首件运费</th>
									<td>
										<div id="ntr24_ucurrencyinput">
											<input type="text" class="pinput val_SD_ISSO2_SHIPPINGSERVICECOST"
												name="shippingdetails[InternationalShippingServiceOption][4][ShippingServiceCost]"
												id="ntr24" value="0.0" size="6"> USD &nbsp;&nbsp; <span
												class="upinput" style="display: none;"> <input
													type="text" value="0" size="6"> CNY </span> <span
												class="upview" style=""> <span>0</span> CNY
											</span>
										</div> <script language="javascript">
										<?php echo $PublishUtils->getShippingServiceCost("ntr24")?>
</script>
									</td>
								</tr>
								<tr class="tobecost4" id="tobecost28" style="display: none;">
									<th>续件运费</th>
									<td>
										<div id="ntr224_ucurrencyinput">
											<input type="text" class="pinput val_SD_ISSO2_SHIPPINGSERVICEADDITIONALCOST"
												name="shippingdetails[InternationalShippingServiceOption][4][ShippingServiceAdditionalCost]"
												id="ntr224" value="0.0" size="6"> USD &nbsp;&nbsp; <span
												class="upinput" style="display: none;"> <input
													type="text" value="0" size="6"> CNY </span> <span
												class="upview" style=""> <span>0</span> CNY
											</span>
										</div> <script language="javascript">
										<?php echo $PublishUtils->getShippingServiceCost("ntr224")?>
										</script>
									</td>
								</tr>
								<tr id="tobecost52" style="display: none;">
									<th>可运至的国家</th>
									<td style="border-bottom: 1px #CCC solid"><span
										style="white-space: nowrap;">
										<input type="checkbox"
											name="shippingdetails[InternationalShippingServiceOption][4][ShipToLocation][]"
											id="shippingdetails[InternationalShippingServiceOption][4][ShipToLocation]_16"
											value="Worldwide" checked="checked"> <label
												id="shippingdetails[internationalshippingserviceoption][4][shiptolocation]_16_label"
												name="shippingdetails[internationalshippingserviceoption][4][shiptolocation]_16_label"
												for="shippingdetails[InternationalShippingServiceOption][4][ShipToLocation]_16"
												class="">全球</label></span><br> <span
											style="white-space: nowrap;"><input type="checkbox"
												name="shippingdetails[InternationalShippingServiceOption][4][ShipToLocation][]"
												id="shippingdetails[InternationalShippingServiceOption][4][ShipToLocation]_11"
												value="Americas" disabled=""> <label
													id="shippingdetails[internationalshippingserviceoption][4][shiptolocation]_11_label"
													name="shippingdetails[internationalshippingserviceoption][4][shiptolocation]_11_label"
													for="shippingdetails[InternationalShippingServiceOption][4][ShipToLocation]_11"
													class="">美洲</label></span> &nbsp; &nbsp; <span
											style="white-space: nowrap;"><input type="checkbox"
												name="shippingdetails[InternationalShippingServiceOption][4][ShipToLocation][]"
												id="shippingdetails[InternationalShippingServiceOption][4][ShipToLocation]_12"
												value="Asia" disabled=""> <label
													id="shippingdetails[internationalshippingserviceoption][4][shiptolocation]_12_label"
													name="shippingdetails[internationalshippingserviceoption][4][shiptolocation]_12_label"
													for="shippingdetails[InternationalShippingServiceOption][4][ShipToLocation]_12"
													class="">亚洲</label></span> &nbsp; &nbsp; <span
											style="white-space: nowrap;"><input type="checkbox"
												name="shippingdetails[InternationalShippingServiceOption][4][ShipToLocation][]"
												id="shippingdetails[InternationalShippingServiceOption][4][ShipToLocation]_13"
												value="AU" disabled=""> <label
													id="shippingdetails[internationalshippingserviceoption][4][shiptolocation]_13_label"
													name="shippingdetails[internationalshippingserviceoption][4][shiptolocation]_13_label"
													for="shippingdetails[InternationalShippingServiceOption][4][ShipToLocation]_13"
													class="">澳大利亚</label></span> &nbsp; &nbsp; <span
											style="white-space: nowrap;"><input type="checkbox"
												name="shippingdetails[InternationalShippingServiceOption][4][ShipToLocation][]"
												id="shippingdetails[InternationalShippingServiceOption][4][ShipToLocation]_14"
												value="MX" disabled=""> <label
													id="shippingdetails[internationalshippingserviceoption][4][shiptolocation]_14_label"
													name="shippingdetails[internationalshippingserviceoption][4][shiptolocation]_14_label"
													for="shippingdetails[InternationalShippingServiceOption][4][ShipToLocation]_14"
													class="">墨西哥</label></span> &nbsp; &nbsp; <span
											style="white-space: nowrap;"><input type="checkbox"
												name="shippingdetails[InternationalShippingServiceOption][4][ShipToLocation][]"
												id="shippingdetails[InternationalShippingServiceOption][4][ShipToLocation]_15"
												value="CA" disabled=""> <label
													id="shippingdetails[internationalshippingserviceoption][4][shiptolocation]_15_label"
													name="shippingdetails[internationalshippingserviceoption][4][shiptolocation]_15_label"
													for="shippingdetails[InternationalShippingServiceOption][4][ShipToLocation]_15"
													class="">加拿大</label></span> &nbsp; &nbsp; &nbsp; &nbsp; <span
											style="white-space: nowrap;"><input type="checkbox"
												name="shippingdetails[InternationalShippingServiceOption][4][ShipToLocation][]"
												id="shippingdetails[InternationalShippingServiceOption][4][ShipToLocation]_17"
												value="DE" disabled=""> <label
													id="shippingdetails[internationalshippingserviceoption][4][shiptolocation]_17_label"
													name="shippingdetails[internationalshippingserviceoption][4][shiptolocation]_17_label"
													for="shippingdetails[InternationalShippingServiceOption][4][ShipToLocation]_17"
													class="">德国</label></span> &nbsp; &nbsp; <span
											style="white-space: nowrap;"><input type="checkbox"
												name="shippingdetails[InternationalShippingServiceOption][4][ShipToLocation][]"
												id="shippingdetails[InternationalShippingServiceOption][4][ShipToLocation]_18"
												value="Europe" disabled=""> <label
													id="shippingdetails[internationalshippingserviceoption][4][shiptolocation]_18_label"
													name="shippingdetails[internationalshippingserviceoption][4][shiptolocation]_18_label"
													for="shippingdetails[InternationalShippingServiceOption][4][ShipToLocation]_18"
													class="">欧洲</label></span> &nbsp; &nbsp; <span
											style="white-space: nowrap;"><input type="checkbox"
												name="shippingdetails[InternationalShippingServiceOption][4][ShipToLocation][]"
												id="shippingdetails[InternationalShippingServiceOption][4][ShipToLocation]_19"
												value="GB" disabled=""> <label
													id="shippingdetails[internationalshippingserviceoption][4][shiptolocation]_19_label"
													name="shippingdetails[internationalshippingserviceoption][4][shiptolocation]_19_label"
													for="shippingdetails[InternationalShippingServiceOption][4][ShipToLocation]_19"
													class="">英国</label></span> &nbsp; &nbsp; <span
											style="white-space: nowrap;"><input type="checkbox"
												name="shippingdetails[InternationalShippingServiceOption][4][ShipToLocation][]"
												id="shippingdetails[InternationalShippingServiceOption][4][ShipToLocation]_20"
												value="JP" disabled=""> <label
													id="shippingdetails[internationalshippingserviceoption][4][shiptolocation]_20_label"
													name="shippingdetails[internationalshippingserviceoption][4][shiptolocation]_20_label"
													for="shippingdetails[InternationalShippingServiceOption][4][ShipToLocation]_20"
													class="">日本</label></span> &nbsp; &nbsp; </td>
								</tr>
								<tr class="tobecost"></tr>
								<tr id="tobecost55">
									<th>运输方式3</th>
									<td><select
										id="shippingdetails[InternationalShippingServiceOption][5][ShippingService]"
										name="shippingdetails[InternationalShippingServiceOption][5][ShippingService]"
										class="val_SD_ISSO3_SHIPPINGSERVICE"
										onchange="changeshipservice(5)">
											<option value="" selected="selected">选择境外物流</option>
											<?php echo $PublishUtils->getShippingService2()?>
									</select> &nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="免运费"
										onclick="freeship(2,5)"></td>
								</tr>
								<tr class="tobecost5" id="tobecost5" style="display: none;">
									<th id="wai">首件运费</th>
									<td>
										<div id="ntr25_ucurrencyinput">
											<input type="text" class="pinput val_SD_ISSO3_SHIPPINGSERVICECOST"
												name="shippingdetails[InternationalShippingServiceOption][5][ShippingServiceCost]"
												id="ntr25" value="0.0" size="6"> USD &nbsp;&nbsp; <span
												class="upinput" style="display: none;"> <input
													type="text" value="0" size="6"> CNY </span> <span
												class="upview" style=""> <span>0</span> CNY
											</span>
										</div> <script language="javascript">
										<?php echo $PublishUtils->getShippingServiceCost("ntr25")?>
</script>
									</td>
								</tr>
								<tr class="tobecost5" id="tobecost35" style="display: none;">
									<th>续件运费</th>
									<td>
										<div id="ntr225_ucurrencyinput">
											<input type="text" class="pinput val_SD_ISSO3_SHIPPINGSERVICEADDITIONALCOST"
												name="shippingdetails[InternationalShippingServiceOption][5][ShippingServiceAdditionalCost]"
												id="ntr225" value="0.0" size="6"> USD &nbsp;&nbsp; <span
												class="upinput" style="display: none;"> <input
													type="text" value="0" size="6"> CNY </span> <span
												class="upview" style=""> <span>0</span> CNY
											</span>
										</div> <script language="javascript">
										<?php echo $PublishUtils->getShippingServiceCost("ntr225")?>
</script>
									</td>
								</tr>
								<tr id="tobecost65" style="display: none;">
									<th>可运至的国家</th>
									<td style="border-bottom: 1px #CCC solid"><span
										style="white-space: nowrap;"><input type="checkbox"
											name="shippingdetails[InternationalShippingServiceOption][5][ShipToLocation][]"
											id="shippingdetails[InternationalShippingServiceOption][5][ShipToLocation]_26"
											value="Worldwide" checked="checked"> <label
												id="shippingdetails[internationalshippingserviceoption][5][shiptolocation]_26_label"
												name="shippingdetails[internationalshippingserviceoption][5][shiptolocation]_26_label"
												for="shippingdetails[InternationalShippingServiceOption][5][ShipToLocation]_26"
												class="">全球</label></span><br> <span
											style="white-space: nowrap;"><input type="checkbox"
												name="shippingdetails[InternationalShippingServiceOption][5][ShipToLocation][]"
												id="shippingdetails[InternationalShippingServiceOption][5][ShipToLocation]_21"
												value="Americas" disabled=""> <label
													id="shippingdetails[internationalshippingserviceoption][5][shiptolocation]_21_label"
													name="shippingdetails[internationalshippingserviceoption][5][shiptolocation]_21_label"
													for="shippingdetails[InternationalShippingServiceOption][5][ShipToLocation]_21"
													class="">美洲</label></span> &nbsp; &nbsp; <span
											style="white-space: nowrap;"><input type="checkbox"
												name="shippingdetails[InternationalShippingServiceOption][5][ShipToLocation][]"
												id="shippingdetails[InternationalShippingServiceOption][5][ShipToLocation]_22"
												value="Asia" disabled=""> <label
													id="shippingdetails[internationalshippingserviceoption][5][shiptolocation]_22_label"
													name="shippingdetails[internationalshippingserviceoption][5][shiptolocation]_22_label"
													for="shippingdetails[InternationalShippingServiceOption][5][ShipToLocation]_22"
													class="">亚洲</label></span> &nbsp; &nbsp; <span
											style="white-space: nowrap;"><input type="checkbox"
												name="shippingdetails[InternationalShippingServiceOption][5][ShipToLocation][]"
												id="shippingdetails[InternationalShippingServiceOption][5][ShipToLocation]_23"
												value="AU" disabled=""> <label
													id="shippingdetails[internationalshippingserviceoption][5][shiptolocation]_23_label"
													name="shippingdetails[internationalshippingserviceoption][5][shiptolocation]_23_label"
													for="shippingdetails[InternationalShippingServiceOption][5][ShipToLocation]_23"
													class="">澳大利亚</label></span> &nbsp; &nbsp; <span
											style="white-space: nowrap;"><input type="checkbox"
												name="shippingdetails[InternationalShippingServiceOption][5][ShipToLocation][]"
												id="shippingdetails[InternationalShippingServiceOption][5][ShipToLocation]_24"
												value="MX" disabled=""> <label
													id="shippingdetails[internationalshippingserviceoption][5][shiptolocation]_24_label"
													name="shippingdetails[internationalshippingserviceoption][5][shiptolocation]_24_label"
													for="shippingdetails[InternationalShippingServiceOption][5][ShipToLocation]_24"
													class="">墨西哥</label></span> &nbsp; &nbsp; <span
											style="white-space: nowrap;"><input type="checkbox"
												name="shippingdetails[InternationalShippingServiceOption][5][ShipToLocation][]"
												id="shippingdetails[InternationalShippingServiceOption][5][ShipToLocation]_25"
												value="CA" disabled=""> <label
													id="shippingdetails[internationalshippingserviceoption][5][shiptolocation]_25_label"
													name="shippingdetails[internationalshippingserviceoption][5][shiptolocation]_25_label"
													for="shippingdetails[InternationalShippingServiceOption][5][ShipToLocation]_25"
													class="">加拿大</label></span> &nbsp; &nbsp; &nbsp; &nbsp; <span
											style="white-space: nowrap;"><input type="checkbox"
												name="shippingdetails[InternationalShippingServiceOption][5][ShipToLocation][]"
												id="shippingdetails[InternationalShippingServiceOption][5][ShipToLocation]_27"
												value="DE" disabled=""> <label
													id="shippingdetails[internationalshippingserviceoption][5][shiptolocation]_27_label"
													name="shippingdetails[internationalshippingserviceoption][5][shiptolocation]_27_label"
													for="shippingdetails[InternationalShippingServiceOption][5][ShipToLocation]_27"
													class="">德国</label></span> &nbsp; &nbsp; <span
											style="white-space: nowrap;"><input type="checkbox"
												name="shippingdetails[InternationalShippingServiceOption][5][ShipToLocation][]"
												id="shippingdetails[InternationalShippingServiceOption][5][ShipToLocation]_28"
												value="Europe" disabled=""> <label
													id="shippingdetails[internationalshippingserviceoption][5][shiptolocation]_28_label"
													name="shippingdetails[internationalshippingserviceoption][5][shiptolocation]_28_label"
													for="shippingdetails[InternationalShippingServiceOption][5][ShipToLocation]_28"
													class="">欧洲</label></span> &nbsp; &nbsp; <span
											style="white-space: nowrap;"><input type="checkbox"
												name="shippingdetails[InternationalShippingServiceOption][5][ShipToLocation][]"
												id="shippingdetails[InternationalShippingServiceOption][5][ShipToLocation]_29"
												value="GB" disabled=""> <label
													id="shippingdetails[internationalshippingserviceoption][5][shiptolocation]_29_label"
													name="shippingdetails[internationalshippingserviceoption][5][shiptolocation]_29_label"
													for="shippingdetails[InternationalShippingServiceOption][5][ShipToLocation]_29"
													class="">英国</label></span> &nbsp; &nbsp; <span
											style="white-space: nowrap;"><input type="checkbox"
												name="shippingdetails[InternationalShippingServiceOption][5][ShipToLocation][]"
												id="shippingdetails[InternationalShippingServiceOption][5][ShipToLocation]_30"
												value="JP" disabled=""> <label
													id="shippingdetails[internationalshippingserviceoption][5][shiptolocation]_30_label"
													name="shippingdetails[internationalshippingserviceoption][5][shiptolocation]_30_label"
													for="shippingdetails[InternationalShippingServiceOption][5][ShipToLocation]_30"
													class="">日本</label></span> &nbsp; &nbsp; </td>
								</tr>
								<script type="text/javascript">
								     //境外运输国家选择Worldwide时的操作
								     $(':checkbox[id^=shippingdetails[InternationalShippingServiceOption]][value=Worldwide]').each(function (){
								    	 if($(this).attr('checked'))
								         {
								             $(this).parent().parent().children().children(':checkbox[value!=Worldwide]').attr('disabled','disabled').attr('checked','');
								         }
								    	 $(this).parent().parent().prepend('<br/>').prepend($(this).parent());
								     });
								     $(':checkbox[id^=shippingdetails[InternationalShippingServiceOption]][value=Worldwide]').click(function (){
								         if($(this).attr('checked'))
								         {
								             $(this).parent().parent().children().children(':checkbox[value!=Worldwide]').attr('disabled','disabled').attr('checked','');
								         }
								         else
								         {
								        	 $(this).parent().parent().children().children(':checkbox[value!=Worldwide]').attr('disabled','').attr('checked','');
								         }
								      });
								     </script>
								<tr>
									<th class="lh">备货时间</th>
									<td><select id="dispatchtime" name="dispatchtime" class="val_DISPATCHTIME">
											<option value="0">0</option>
											<option value="1">1</option>
											<option value="2" selected="selected">2</option>
											<option value="3">3</option>
											<option value="20">20</option>
											<option value="4">4</option>
											<option value="5">5</option>
											<option value="10">10</option>
											<option value="30">30</option>
											<option value="15">15</option>
									</select> 天</td>
								</tr>
								<tr>
									<th>Tax</th>
									<td><select id="shippingdetails[SalesTax][SalesTaxState]"
										name="shippingdetails[SalesTax][SalesTaxState]" class="val_SD_SALESTAXSTATE">
											<?php  echo $PublishUtils->getTaxStates() ;?>
									</select> <input id="shippingdetails[SalesTax][SalesTaxPercent]" 
									type="text"
									name="shippingdetails[SalesTax][SalesTaxPercent]"   class="val_SD_SALESTAXPERCENT"
										value="" size="4">% &nbsp; <input
											id="ShippingIncludedInTax" type="checkbox"
											name="shippingdetails[SalesTax][ShippingIncludedInTax]"
											value="1"> <label for="ShippingIncludedInTax">运费加税</label></td>
								</tr>
							</tbody>
						</table>
					</div>
					<table id="detail_location" class="ebay_table">
						<caption>
							商品所在地与退货
							<div style="float: right;">
									<a href="#" class="profile_save">保存为范本</a> 
									<select id="detail_location_profile" name="detail_location_profile" class="profile">
										<option value=""></option>
									</select> 
									<a href="#" class="profile_load">读取</a> <a href="#"  class="profile_del">删除</a>
								</div>
						</caption>
						<tbody>
							<tr>
								<th class="lh">商品地址<span class="needstar">*</span></th>
								<td><input type="text" name="location" value="" class="val_LOCATION"></td>
								<th class="lh">邮编</th>
								<td><input type="text" name="postalcode" value="" class="val_POSTALCODE"></td>
							</tr>
							<tr>
								<th class="lh">国家<span class="needstar">*</span></th>
								<td colspan="3"><select id="country" name="country" class="val_COUNTRY">
										<?php  echo $PublishUtils->getPublishCountrys() ;?>
								</select></td>
							</tr>
							<!-- 
  <tr><th class="lh">地区</th><td><select  id="region" name="region" >
</select>
  </td></tr>
 -->

							<tr>
								<th width="150" class="lh">接受退货</th>
								<td colspan="3"><select
									id="return_policy[ReturnsAcceptedOption]"
									name="return_policy[ReturnsAcceptedOption]"
									onchange="return_policy_trigger(this)"  class="val_RP_RETURNSACCEPTEDOPTION">
										<option value="ReturnsAccepted">Returns Accepted</option>
										<option value="ReturnsNotAccepted">No returns
											accepted</option>
								</select> <script type="text/javascript">
        function return_policy_trigger(obj){
            if($(obj).val()=='ReturnsAccepted'){
                $('.return_accepted_only').show();
            }else{
                $('.return_accepted_only').hide();
            }
        }
        </script></td>
							</tr>
							<tr class="return_accepted_only" style="">
								<th>退货方式</th>
								<td colspan="3"><select id="return_policy[RefundOption]"
									name="return_policy[RefundOption]"   class="val_RP_REUNDOPTION">
										<option value="MoneyBack">Money Back</option>
										<option value="MoneyBackOrReplacement">Money back or
											replacement (buyer's choice)</option>
										<option value="MoneyBackOrExchange">Money back or
											exchange (buyer's choice)</option>
								</select></td>
							</tr>
							<tr class="return_accepted_only" style="">
								<th>接受退货期限</th>
								<td colspan="3"><select
									id="return_policy[ReturnsWithinOption]"
									name="return_policy[ReturnsWithinOption]" class="val_RP_RETURNSWITHINOPTION">
										<option value="Days_14">14 Days</option>
										<option value="Days_30">30 Days</option>
										<option value="Days_60">60 Days</option>
								</select></td>
							</tr>
							<tr class="return_accepted_only" style="">
								<th>退货邮费承当</th>
								<td colspan="3"><select
									id="return_policy[ShippingCostPaidByOption]"
									name="return_policy[ShippingCostPaidByOption]" class="val_RP_SHIPPINGCOSTPAIDBYOPTION">
										<option value="Buyer">Buyer</option>
										<option value="Seller">Seller</option>
								</select></td>
							</tr>
							<tr class="return_accepted_only" style="">
								<th>退货说明</th>
								<td colspan="3"><textarea rows="5"
										name="return_policy[Description]" cols="60" class="val_RP_DESCRIPTION"></textarea></td>
							</tr>
						</tbody>
					</table>
					<script type="text/javascript">
    //默认根据设置切换退还选项显示
    $('select[name=return_policy\\[ReturnsAcceptedOption\\]]').trigger('change');
</script>
					<table id="detail_return" class="ebay_table">
						<caption>
							收款选项
							<div style="float: right;">
									<a href="#" class="profile_save">保存为范本</a> 
									<select id="detail_return_profile" name="detail_return_profile" class="profile">
										<option value=""></option>
									</select> 
									<a href="#" class="profile_load">读取</a> <a href="#"  class="profile_del">删除</a>
								</div>
						</caption>
						<tbody>
							<tr>
								<th class="lh">收款方式</th>
								<td><span style="white-space: nowrap;"><input
										type="checkbox" name="paymentmethods[]"
										id="paymentmethods_259" value="AmEx"> <label
											id="paymentmethods_259_label" name="paymentmethods_259_label"
											for="paymentmethods_259" class="">American Express</label></span>
									&nbsp; &nbsp; <span style="white-space: nowrap;"><input
										type="checkbox" name="paymentmethods[]"
										id="paymentmethods_260" value="CashOnPickup"> <label
											id="paymentmethods_260_label" name="paymentmethods_260_label"
											for="paymentmethods_260" class="">Cash On Pickup
												Accepted</label></span> &nbsp; &nbsp; <span style="white-space: nowrap;"><input
										type="checkbox" name="paymentmethods[]"
										id="paymentmethods_261" value="Discover"> <label
											id="paymentmethods_261_label" name="paymentmethods_261_label"
											for="paymentmethods_261" class="">Discover Card</label></span>
									&nbsp; &nbsp; <span style="white-space: nowrap;"><input
										type="checkbox" name="paymentmethods[]"
										id="paymentmethods_262" value="IntegratedMerchantCreditCard">
											<label id="paymentmethods_262_label"
											name="paymentmethods_262_label" for="paymentmethods_262"
											class="">Integrated Merchant Credit Card</label></span> &nbsp;
									&nbsp; <span style="white-space: nowrap;"><input
										type="checkbox" name="paymentmethods[]"
										id="paymentmethods_263" value="MOCC"> <label
											id="paymentmethods_263_label" name="paymentmethods_263_label"
											for="paymentmethods_263" class="">Money order /
												Cashier's check</label></span> &nbsp; &nbsp; <span
									style="white-space: nowrap;"><input type="checkbox"
										name="paymentmethods[]" id="paymentmethods_264" value="PayPal"
										checked="checked"> <label
											id="paymentmethods_264_label" name="paymentmethods_264_label"
											for="paymentmethods_264" class="">PayPal</label></span> &nbsp;
									&nbsp; <span style="white-space: nowrap;"><input
										type="checkbox" name="paymentmethods[]"
										id="paymentmethods_265" value="PaymentSeeDescription">
											<label id="paymentmethods_265_label"
											name="paymentmethods_265_label" for="paymentmethods_265"
											class="">Other - See item description</label></span> &nbsp; &nbsp; <span
									style="white-space: nowrap;"><input type="checkbox"
										name="paymentmethods[]" id="paymentmethods_266"
										value="PersonalCheck"> <label
											id="paymentmethods_266_label" name="paymentmethods_266_label"
											for="paymentmethods_266" class="">Personal check</label></span>
									&nbsp; &nbsp; <span style="white-space: nowrap;"><input
										type="checkbox" name="paymentmethods[]"
										id="paymentmethods_267" value="VisaMC"> <label
											id="paymentmethods_267_label" name="paymentmethods_267_label"
											for="paymentmethods_267" class="">Visa or Master Card</label></span>
									&nbsp; &nbsp;</td>
							</tr>
							<tr>
								<th>收款Paypal帐号</th>
								<td><input size="60" name="paypal" value=""  class="val_PAYPAL"></td>
							</tr>
							<tr>
								<th>立即付款</th>
								<td>要求买家立即付款<input type="checkbox" name="autopay"
									value="true">&nbsp;&nbsp;&nbsp;&nbsp;(请确认您的PayPal帐号为商业帐号)</td>
							</tr>
						</tbody>
					</table>


					<table class="ebay_table" id="pos_other">
						<caption>其他</caption>
						<tbody>
							<tr>
								<th>橱窗展示(Gallery)图片 <span class="lableebayfee"
									title="部分选项 需付费">($)</span></th>
								<td><span style="white-space: nowrap;"><input
										type="radio" name="gallery" id="gallery_1" value="0"
										title="部分选项 需付费" checked="checked"> <label
											id="gallery_1_label" name="gallery_1_label" for="gallery_1"
											class="">不使用</label></span> &nbsp; &nbsp; <span
									style="white-space: nowrap;"><input type="radio"
										name="gallery" id="gallery_2" value="Featured"
										title="部分选项 需付费"> <label id="gallery_2_label"
											name="gallery_2_label" for="gallery_2" class="">Featured</label></span>
									&nbsp; &nbsp; <span style="white-space: nowrap;"><input
										type="radio" name="gallery" id="gallery_3" value="Gallery"
										title="部分选项 需付费"> <label id="gallery_3_label"
											name="gallery_3_label" for="gallery_3" class="">Gallery</label></span>
									&nbsp; &nbsp; <span style="white-space: nowrap;"><input
										type="radio" name="gallery" id="gallery_4" value="Plus"
										title="部分选项 需付费"> <label id="gallery_4_label"
											name="gallery_4_label" for="gallery_4" class="">Plus</label></span>
									&nbsp; &nbsp; <a
									href="http://www.ibay365.com/index.php/muban/editmuban#"
									class="cebayfee">查看Ebay费用</a></td>
							</tr>
							<tr>
								<th class="lh">计数器</th>
								<td id="hitcounter"><span style="white-space: nowrap;"><input
										type="radio" name="hitcounter" id="hitcounter_5"
										value="NoHitCounter"> <label id="hitcounter_5_label"
											name="hitcounter_5_label" for="hitcounter_5" class="">不用计数器</label></span>
									&nbsp; &nbsp; <span style="white-space: nowrap;"><input
										type="radio" name="hitcounter" id="hitcounter_6"
										value="BasicStyle" checked="checked"> <label
											id="hitcounter_6_label" name="hitcounter_6_label"
											for="hitcounter_6" class="">BasicStyle</label></span> &nbsp; &nbsp;
									<span style="white-space: nowrap;"><input type="radio"
										name="hitcounter" id="hitcounter_7" value="RetroStyle">
											<label id="hitcounter_7_label" name="hitcounter_7_label"
											for="hitcounter_7" class="">RetroStyle</label></span> &nbsp; &nbsp;
									<span> &nbsp; <a
										style="padding: 3px;; background: black; color: #5F8F8F; text-decoration: none;">BasicStyle</a>
										&nbsp; <a
										style="padding: 3px; background: black; color: lime; text-decoration: none;">RetroStyle</a>
								</span></td>
							</tr>
							<tr>
								<th>样式<span class="lableebayfee">($)</span></th>
								<td><span style="white-space: nowrap;"><input
										type="checkbox" name="listingenhancement[]"
										id="listingenhancement_268" value="BoldTitle"> <label
											id="listingenhancement_268_label"
											name="listingenhancement_268_label"
											for="listingenhancement_268" class="">BoldTitle</label></span>
									&nbsp; &nbsp; <a
									href="http://www.ibay365.com/index.php/muban/editmuban#"
									class="cebayfee">查看Ebay费用</a></td>
							</tr>
							<tr>
								<th>私人物品(Private Listing)</th>
								<td><span style="white-space: nowrap;"><input
										type="radio" name="privatelisting" id="privatelisting_8"
										value="true"> <label id="privatelisting_8_label"
											name="privatelisting_8_label" for="privatelisting_8" class="">使用</label></span>
									&nbsp; &nbsp; <span style="white-space: nowrap;"><input
										type="radio" name="privatelisting" id="privatelisting_9"
										value="false" checked="checked"> <label
											id="privatelisting_9_label" name="privatelisting_9_label"
											for="privatelisting_9" class="">不使用</label></span> &nbsp; &nbsp;
									该功能会使其他buyer不能看到您刊登的被购买记录</td>
							</tr>
							<tr>
								<th class="lh">SKU(Custom Lable)</th>
								<td><input name="sku" id="sku" value=""> <input
										type="checkbox"
										onclick="if(!$(this).attr('checked')){$('#sku').val($('#bindproductsku_val').val());$('#divbindconstantsku').hide();}else{if(!confirm('确认使用变量吗？')){$(this).attr('checked',''); return true;}$('#sku').val('{-BINDPRODUCTSKU-}');$('#divbindconstantsku').show();}"
										id="isusevarsku">使用关联商品变量 <a class="helplink"
											href="javascript:showVariables()"></a> <span
											id="divbindconstantsku" class="divbindconstan"></span></td>
							</tr>
							<tr>
								<th class="lh">备注</th>
								<td><input type="text" name="desc" size="30" value=""></td>
							</tr>
						</tbody>
					</table>

					<table class="ebay_table" id="pos_buyer_limit">
						<caption>买家限制</caption>
						<tbody>
							<tr>
								<th>买家必须拥有paypal账户</th>
								<td>是 <input type="radio" value="true"
									name="buyerrequirementdetails[LinkedPayPalAccount]"> 否
										<input type="radio" value="false"
										name="buyerrequirementdetails[LinkedPayPalAccount]"></td>
							</tr>
							<tr>
								<th class="lh">买家政策违反相关</th>
								<td>&nbsp;违 反 次 数: <select
									id="buyerrequirementdetails[MaximumBuyerPolicyViolations][Count]"
									class="val_BRD_MBPV_COUNT"
									name="buyerrequirementdetails[MaximumBuyerPolicyViolations][Count]">
										<option value=""></option>
										<option value="4">4</option>
										<option value="5">5</option>
										<option value="6">6</option>
										<option value="7">7</option>
								</select> &nbsp;&nbsp;&nbsp;&nbsp; 违规时段: <select
									id="buyerrequirementdetails[MaximumBuyerPolicyViolations][Period]"
									class="val_BRD_MBPV_PERIOD"
									name="buyerrequirementdetails[MaximumBuyerPolicyViolations][Period]">
										<option value=""></option>
										<option value="Days_30">30天内</option>
										<option value="Days_180">180天内</option>
										<option value="Days_360">360天内</option>
								</select> 注:2个需组合了一起填写使用
								</td>
							</tr>
							<tr>
								<th>买家不付款订单相关</th>
								<td>未付款次数: <select
									id="buyerrequirementdetails[MaximumUnpaidItemStrikesInfo][Count]"
									class="val_BRD_MUIS_COUNT"
									name="buyerrequirementdetails[MaximumUnpaidItemStrikesInfo][Count]">
										<option value=""></option>
										<option value="2">2</option>
										<option value="3">3</option>
										<option value="4">4</option>
										<option value="5">5</option>
								</select> &nbsp;&nbsp;&nbsp;&nbsp; 违规时段: <select
									id="buyerrequirementdetails[MaximumUnpaidItemStrikesInfo][Period]"
									class="val_BRD_MUIS_PERIOD"
									name="buyerrequirementdetails[MaximumUnpaidItemStrikesInfo][Period]">
										<option value=""></option>
										<option value="Days_30">30天内</option>
										<option value="Days_180">180天内</option>
										<option value="Days_360">360天内</option>
								</select> 注:2个需组合了一起填写使用
								</td>
							</tr>
							<tr>
								<th>10天期间限制拍卖次数</th>
								<td>≤ <select
									class="val_BRD_MIR_MIC"
									id="buyerrequirementdetails[MaximumItemRequirements][MaximumItemCount]"
									name="buyerrequirementdetails[MaximumItemRequirements][MaximumItemCount]">
										<option value=""></option>
										<option value="1">1</option>
										<option value="2">2</option>
										<option value="3">3</option>
										<option value="4">4</option>
										<option value="5">5</option>
										<option value="6">6</option>
										<option value="7">7</option>
										<option value="8">8</option>
										<option value="9">9</option>
										<option value="10">10</option>
										<option value="25">25</option>
										<option value="50">50</option>
										<option value="75">75</option>
										<option value="100">100</option>
								</select>
								</td>
								
							</tr>
						</tbody>
					</table>
					<br> <input type="button" value="保存新模板"
						onclick="doaktion('savenew')"> <input
							type="hidden" name="aktion" value=""> <input
								type="hidden" name="muban_id" value=""> <input
									type="hidden" name="goods_id" id="goods_id" value="0">
										<input type="hidden" name="languageid" id="languageid"
										value=""> <input type="hidden"
											name="shippingdetails[ShippingType]"
											id="shippingdetails[ShippingType]" value="Flat">
				</form>
			</div>
		</div>
		<br style="clear: both;" />
	</div>
	</div>

	<br style="clear: both;" />
	</div>

	<script type="text/javascript">
		
	
		  function loadCondition(accountId,categoryId,selected){
			var f1 = false ;
			var f2 = false ;
			  $.block() ;
			  $.ajax({
		          url:'<?php  echo $Utils->buildUrlByAccountId($accountId, "eBay/getCategoryFeathers") ; ?>/'+categoryId,
		          dataType:"jsonp",
		          jsonp:"jsonpcallback",
		          success:function(data){
					 
		             if( data && data != 'null' ){ //需要条件哦按
							$("#itemCondition").html("<select id='conditionid' name='conditionid'></select>") ;
							$(data).each(function(){
									var s ="" ;
									if(this.id == selected) s="selected" ; 
									$("#conditionid").append('<option value="'+this.id+'" '+s+'>'+this.name+'</option>') ;
							}) ;
			            }else{
			            	$("#itemCondition").html("该分类不需要设置Condition！") ;
				        }
		             f1 = true ;
		        	 
		             if( f1 && f2 ){
							$.unblock() ;
						}
		          }
		     });
			
			  $.ajax({
		          url:'<?php  echo $Utils->buildUrlByAccountId($accountId, "eBay/getCategorySpecials") ; ?>/'+categoryId,
		          dataType:"jsonp",
		          jsonp:"jsonpcallback",
		          success:function(data){
		        	  var itemSpecials = templateJson.ITEM_SPECIALS||"{}" ;
		        	  itemSpecials  = $.parseJSON(itemSpecials) ;
				      //  alert(itemSpecials);//{"Brand":"Alemite","Model":"123","MPN":"123","Country of Manufacture":"Albania"}
						$("#speci").empty();
						$( data.NameRecommendation ).each(function(){
							var name = this.Name ;
							var ValidationRules = this.ValidationRules ;
							var ValueRecommendation = this.ValueRecommendation||[] ;
							var MaxValues = ValidationRules.MaxValues ;
							var MinValues  = ValidationRules.MinValues||0 ;
							var VariationSpecifics =ValidationRules.VariationSpecifics ;

							var required = MinValues>0?" data-validator='required' ":"" ;
							
							var tr  = $("<tr></tr>").appendTo("#speci") ;
							var td = $("<td></td>").appendTo(tr) ;
							
							td.append(name) ;
							var td = $("<td></td>").appendTo(tr) ;
							$("<input type='checkbox' style='display:none;' checked name='itemspecials[]'>").appendTo(td).val(name) ;
							td.append("") ;
							if( ValueRecommendation.length >0  ){
								var select = $("<select><option value=''></option></select>").appendTo(td) ;
								select.attr("name","itemspecial_"+name) ;

								var isSelectValue = false ;
								$( ValueRecommendation ).each(function(index , item){
										var val = this.Value ;
										var selected = "" ;
										if( itemSpecials[name] == this.Value ){
											selected = "selected" ;
											isSelectValue = true ;
										}
										select.append("<option value='"+this.Value+"'  "+selected+">"+this.Value+"</option>") ;
								}) ;
								if( VariationSpecifics != "Disabled" ){
									var input = $("<input type='text'/>").appendTo(td) ;
									input.attr("name","itemspecial_"+name+"_input") ;
									if(!isSelectValue)input.val(   itemSpecials[name] ||"" ) ;
								}
							}else{
								var input = $("<input type='text'/>").appendTo(td) ;
								input.attr("name","itemspecial_"+name+"_input") ;
								input.val(   itemSpecials[name] ||"" ) ;
							}
						}) ;
						f2 = true ;
						
						if( f1 && f2 ){
							$.unblock() ;
						}
		          }
		     });
	  }
	
	var templateJson = <?php echo $templateJson;?>||{}  ;

	if( templateJson ){
		for(var o in templateJson ){
			$(".val_"+o).val( templateJson[o] ) ;
		}
		//处理分类
		if( templateJson.PRIMARYCATEGORY ){
			loadCondition('<?php echo $accountId;?>' , templateJson.PRIMARYCATEGORY,templateJson.CONDITIONID) ;
		}

		//初始化图片
		var url0 = templateJson.URL0 ;
		var url1 = templateJson.URL1 ;
		var url2 = templateJson.URL2 ;
		var url3 = templateJson.URL3 ;
		var url4 = templateJson.URL4 ;

		if( url0 ){
			$("[name='imgurl[0]']").val(url0) ;
			$("[name='imgurl[0]']").prev().attr("src",url0) ;
		}
		
	}
	
	
	var categoryTreeSelect = {
			title:'Ebay产品分类选择',
			valueField:"#primarycategory",
			labelField:"#primarycategorytext",
			key:{value:'id',label:'fullname'},//对应value和label的key
			multi:false ,
			tree:{
				title:"Ebay产品分类选择",
				method : 'post',
				asyn : true, //异步
				rootId  : 'root',
				rootText : '产品分类',
				CommandName : 'sqlId:sql_ebay_category_list',
				recordFormat:true,
				params : {}
			},
			after:function(result){
				if( !(result && result.value) ) return ;
				//获取condition
				loadCondition('<?php echo $accountId;?>' , result.value) ;
			}
	   } ;
	   
	$(".select-category").listselectdialog( categoryTreeSelect) ;
	</script>

<?php
		echo $this->Html->script('../ebay_publish_files/page');
	?>
</body>
</html>