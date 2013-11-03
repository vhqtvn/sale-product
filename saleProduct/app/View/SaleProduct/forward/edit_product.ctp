<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>货品编辑</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
  		include_once ('config/config.php');
  		include_once ('config/header.php');
  		echo $this->Html->script('modules/saleproduct/edit_product');

  		echo $this->Html->css('../js/modules/tag/tagutil');
  		echo $this->Html->script('modules/tag/tagutil');
  		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$security  = ClassRegistry::init("Security") ;
		$groups = $SqlUtils->exeSql("sql_package_group_list",array() ) ;
		
		$entityId = $item['ID'] ;
		$fixEntityId = "" ;
		$category = null ;
		if( empty( $entityId ) ){
			$fixEntityId = $SqlUtils->create_guid() ;
			$entityId = $fixEntityId ;
		}else{
			$sql = "SELECT GROUP_CONCAT(  spc.NAME SEPARATOR ',' ) AS CATEGORY_NAME,
					       GROUP_CONCAT(  spc.ID SEPARATOR ',' ) AS CATEGORY_ID
						                    	FROM sc_product_category spc, sc_real_product_category srpc 
						                    	WHERE spc.ID = srpc.CATEGORY_ID
						                    	AND srpc.PRODUCT_ID = '$entityId'" ;
			$category = $SqlUtils->getObject($sql,array()) ;
		}
		
		//获取分类名称
		
	?>
	
	<style type="text/css">
		ul li{
			list-style: none;
			float:left;
			margin:2px 5px;
		}
		
		input[disabled],textarea[disabled],select[disabled]{
			background:none!important;
			border:none!important;
			-webkit-border-radius: 0px!important;
			-moz-border-radius: 0px!important;
			border-radius: 0px!important;
			-webkit-box-shadow: none;;
			-moz-box-shadow: none;
			box-shadow: none;
			-webkit-transition: none;
			-moz-transition:none;
			-ms-transition: none;
			-o-transition: none;
			transition: none;
		}
	</style>

</head>

<script>
	$(function(){
		DynTag.listByEntity("productTag",'<?php echo $entityId;?>') ;
	}) ;
</script>

<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page">
		<!-- 页面标题 -->
		<div class="container-fluid">

	        <form id="personForm" action="<?php echo $contextPath;?>/saleProduct/saveProduct"
	          method="post" target="form-target" data-widget="validator"
	         enctype="multipart/form-data" class="form-horizontal" >
	        	<input type="hidden" id="id" name="id" value="<?php echo $item['ID']?>"/>
				<!-- panel 头部内容  此场景下是隐藏的-->
				<div class="panel apply-panel" style="margin-bottom:50px;">
					<!-- panel 中间内容-->
					<div class="panel-content">
						<!-- 数据列表样式 -->
						<table class="form-table" >
							<caption>基本属性</caption>
							<tbody>
								<tr>
									<td colspan="2"  rowspan="4">
										<?php if( empty($item['ID']) ){
											echo "<center><b>保存后再上传图片！</b></center>" ;
										}else{?>
											
										<div class="image-container"  localUrl= '<?php echo $item['IMAGE_URL'] ;?>'   entityType="realProduct"  entityId="<?php echo $entityId?>"></div>
										
									 <?php	}?>
										<?php  /*
										<input type="file" name="imageUrl" class="span3"/>
										<?php
										if( $item['IMAGE_URL'] ){
											echo "<img src='/".$fileContextPath."/".$item['IMAGE_URL']."' style='width:140px;height:140px;'>" ;
										}?>*/ ?>
									</td>
										<th>类型：</th>
										<td>
											<input type="radio" <?php if($item['TYPE']=='base')echo 'checked';?> 
													<?php if($item['TYPE'])echo 'disabled';?> 
												data-validator="required" name="type" value="base" />基本货品
											<input type="radio" <?php if($item['TYPE']=='package')echo 'checked';?>
											<?php if($item['TYPE'])echo 'disabled';?>  
												data-validator="required" name="type" value="package"/>打包货品
										</td>
								</tr>
								<tr>
									<th>选择产品分类：</th>
									<td>
										<input type="hidden"  id="categoryId" name="categoryId" value="<?php echo $category['CATEGORY_ID']?>"/>
										<input type="text" id="categoryName" name="categoryName" value="<?php echo $category['CATEGORY_NAME']?>"/>
										<button class="btn select-category">选择</button>
									</td>
								</tr>
								<tr>
									<th>SKU：</th><td ><input type="text"
										 	data-validator="required" name="sku" 
										 	value="<?php if(isset($item['REAL_SKU']))echo $item['REAL_SKU']; else echo $realSku;?>"/></td>
								</tr>									   
								<tr>
									<th>名称：</th>
									<td ><input type="text" data-validator="required" name="name" value="<?php echo $item['NAME']?>"/></td>
								</tr>
								<tr>
									<th>预警库存：</th>
									<td><input type="text" data-validator="required" class="alert-danger"
										name="warningQuantity" value="<?php echo $item['WARNING_QUANTITY']?>"/></td>
									<th>安全库存：</th>
									<td><input type="text" data-validator="required"  class="alert-danger"
										name="securityQuantity" value="<?php echo $item['SECURITY_QUANTITY']?>"/></td>
								</tr>
								<tr>
									<th colspan=2 style="text-align:center">产品属性</th>
									<th colspan=2 style="text-align:center">备注</th>
									
								</tr>
								<tr>
									<td colspan=2>
										<textarea name="properties" style="width:90%;height:80px;"><?php echo $item['PROPERTIES']?></textarea>
									</td>
									<td colspan=2>
										<textarea name="memo" style="width:90%;height:80px;"><?php echo $item['MEMO']?></textarea>
										
									</td>
								</tr>
								<tr>
									<th><button class="btn addKey-btn no-disabled">添加</button>关键字：</th>
									<td colspan=3>
											<input type="hidden" name="keys" value="<?php echo $item['S_KEYS']?>"></input>
											<ul class="keys-container" style="margin:2px;">
											</ul>
											<hr style="margin:0px;clear:both;padding-top:3px;"/>
											<?php 
												$Config  = ClassRegistry::init("Config") ;
												$websites = $Config->getAmazonConfig("PRODUCT_SEARCH_WEBSITE") ;
											
												echo '<b>相关网址：</b>' ;
												foreach ( explode(",", $websites) as $website ){
													$website = explode("||", $website) ;
													$name = $website[0] ;
													$url = $website[1] ;
													
													echo "<a href='$url' target='_blank'>$name</a>&nbsp;&nbsp;&nbsp;" ;
												}
											?>
									</td>
								</tr>
							</tbody>
						</table>
						
						<table class="form-table" >
							<caption>物流属性</caption>
							<tbody>
								<tr>
									<th>重量：</th>
									<td><input type="text" name="weight" style="width:50px;" value="<?php echo $item['WEIGHT']?>"/>
									<select name="weightUnit" style="width:150px;">
										<option value="lb" <?php if($item['WEIGHT_UNIT']=='lb')echo 'selected';?>  >pound</option>
										<option value="oz" <?php if($item['WEIGHT_UNIT']=='oz')echo 'selected';?> >ounce</option>
									</select>
									</td>
									<th>长X宽X高(cm)：</th>
									<td><input type="text" name="length" style="width:50px;" value="<?php echo $item['LENGTH']?>"/>
									X<input type="text" name="width" style="width:50px;" value="<?php echo $item['WIDTH']?>"/>
									X<input type="text" name="height" style="width:50px;" value="<?php echo $item['HEIGHT']?>"/>
									
									</td>
								</tr>
								<tr>
									<th>默认物流服务：</th>
									<td>
										<input type="hidden"  id="postageServiceId" name="postageServiceId" value="<?php echo $item['POSTAGE_SERVICE_ID']?>"/>
										<input type="text" id="postageServiceName" name="postageServiceName" value="<?php echo $item['POSTAGE_SERVICE_NAME']?>"/>
										<button class="btn select-postage">选择</button>
									</td>
									<th>包装类型：</th>
									<td colspan=3>
										<select id="packageGroupId" name="packageGroupId">
											<option value="">--请选择--</option>
											<?php
												$defGroupId = $item['PACKAGE_GROUP_ID'] ;
												
												foreach( $groups as $group ){
													$group = $SqlUtils->formatObject($group) ;
													$val = $group['ID'] ;
													$label = $group['NAME'] ;
													$isSelected = $defGroupId == $val?"selected":"" ;
													echo "<option $isSelected value='$val'>$label</option>" ;
												}
											?>
										</select>
									</td>
								</tr>
								<tr>
									<th>报关名称：</th>
									<td>
											<input type="text" 
													name="declarationName" value="<?php echo $item['DECLARATION_NAME']?>"/>
									</td>
									<th>报关价格：</th>
									<td>
											<input type="text" 
													name="declarationPrice" value="<?php echo $item['DECLARATION_PRICE']?>"/>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					
					<!-- panel脚部内容-->
                    <div class="panel-foot" style="background-color:#FFF;">
						<div class="form-actions">
							<button   class="btn btn-primary btn-submit">提&nbsp;交</button>
							
							<button onclick="window.close();return false;" class="btn">关&nbsp;闭</button>
						</div>
					</div>
				</div>
			</form>
			 <iframe style="width:0; height:0; border:0;display:none;" name="form-target"></iframe>
		</div>
	</div>
</body>
</html>