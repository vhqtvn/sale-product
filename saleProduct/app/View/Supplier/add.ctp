<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>供应商</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>
	
   <?php
   		include ('config/config.php');
   		include_once ('config/header.php');
		
		$id = "" ;
		$name = '' ;
		$address = '' ;
		$contactor  ="" ;
		$phone = '' ;
		$mobile = '' ;
		$fax = '' ;
		$zip_code = '' ;
		$url = "" ;
		$email ="" ;
		$qq ="" ;
		$products = "" ;
		$memo = '' ;
		$evaluate= '' ;
		$code = "" ;
		
		 if( $supplier !=null){
		 	$id =$supplier[0]['sc_supplier']["ID"] ;
			$name =$supplier[0]['sc_supplier']["NAME"] ;
			$code =$supplier[0]['sc_supplier']["CODE"] ;
			$address =$supplier[0]['sc_supplier']["ADDRESS"] ;
			$contactor =$supplier[0]['sc_supplier']["CONTACTOR"] ;
			$phone =$supplier[0]['sc_supplier']["PHONE"] ;
			$mobile =$supplier[0]['sc_supplier']["MOBILE"] ;
			$fax =$supplier[0]['sc_supplier']["FAX"] ;
			$zip_code =$supplier[0]['sc_supplier']["ZIP_CODE"] ;
			$url =$supplier[0]['sc_supplier']["URL"] ;
			$email  =$supplier[0]['sc_supplier']["EMAIL"] ;
			$qq  =$supplier[0]['sc_supplier']["QQ"] ;
			$products  =$supplier[0]['sc_supplier']["PRODUCTS"] ;
			$memo = $supplier[0]['sc_supplier']["MEMO"]; 
			$evaluate = $supplier[0]['sc_supplier']["EVALUATE"]; 
		 }
		 
		 $SqlUtils  = ClassRegistry::init("SqlUtils") ;
		 $categorys = $SqlUtils->exeSql("sql_saleproduct_categorytreeBySupplier",array('supplierId'=>$id) ) ;
		 
		// debug($categorys) ;
		$isView = false ;
		if( isset($view) ){
			$isView = true ;
		}
		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$defaultCode = $code;
		if( empty($code) ){
			$defaultCode = $SqlUtils->getDefaultCodeOnlyIndex("SUP") ;
		}
	?>
	
	<script type="text/javascript" src="/<?php echo  $fileContextPath;?>/app/webroot/js/modules/supplier/add.js"></script>
	
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
		
		.table-bordered tr td,.table-bordered tr th{
			padding:5px;
		}
		.WdateDiv, .ui-corner-all {
			border:none;
		}
		
		.eva-ul{
			list-style: none;
		}
		
		.eva-ul li{
			float:left;
			width:350px;
			padding:5px;
			margin:5px;
			border:1px solid #CCC;
		}
		
		.eva-ul li textarea{
			margin:5px 2px;
		}
		

   </style>

   <script>
		var supplierId = '<?php echo $id ;?>'
   
   	    var treeData = {id:"root",text:"产品分类",isExpand:true,childNodes:[]} ;
	    var treeMap  = {} ;
	  
	    <?php
	    	$ss = explode(",",$products) ;
	    	$Utils  = ClassRegistry::init("Utils") ;
	    	
	    	$Utils->echoTreeScript( $categorys ,null, function( $sfs, $index ,$ss ){
			    	$id   = $sfs['ID'] ;
	    			$name = $sfs['NAME']."(".$sfs['TOTAL'].")" ;
	    			$pid  = $sfs['PARENT_ID'] ;
	    			echo " var item$index = {id:'$id',text:'$name',isExpand:true} ;" ;
	    	} ) ;
		?>
   
   
   </script>

</head>
<body>
<form id="personForm" action="#" data-widget="validator,ajaxform">

<div id="tabs-default" ></div>
	<div id="base-info">
			<input type="hidden" id="id" value="<?php echo $id;?>"/>
			<input type="hidden" id="sku" value="<?php echo $sku;?>"/>
			<input type="hidden" id="asin" value="<?php echo $asin;?>"/>
			<!-- panel 头部内容  此场景下是隐藏的-->
				<div class="panel apply-panel">
					<!-- panel 中间内容-->
					<div class="panel-content">
			<table  class="form-table" >
				<caption>供应商信息</caption>
				<tr>
					<th>供应商名称：</th>
					<td>
						<?php if($isView){
							echo $name;
						}else{ ?>
							<input   data-validator="required" type="text" id="name" value="<?php echo $name;?>"/>
						<?php }?>
					</td>
					<th>供应商编码：</th>
					<td>
						<?php if($isView){
							echo $defaultCode;
						}else{ ?>
							<input readonly='readOnly'   data-validator="required" type="text" id="code" value="<?php echo $defaultCode;?>"/>
						<?php }?>
					</td>
				</tr>
				<tr>
					<th>供应商地址：</th><td colspan="3">
						<?php if($isView){
							echo $address;
						}else{ ?>
							<input    data-validator="required" type="text" id="address" value="<?php echo $address;?>"/>
						<?php }?>
					</td>
				</tr>
				<tr>
					<th>联系人：</th><td>
					<?php if($isView){
							echo $contactor;
						}else{ ?>
							<input  type="text" id="contactor" value="<?php echo $contactor;?>"/>
						<?php }?>
						</td>
					<th>联系电话：</th><td>
					<?php if($isView){
							echo $phone;
						}else{ ?>
							<input  type="text" id="phone" value="<?php echo $phone;?>"/>
						<?php }?>
					</td>
				</tr>
				<tr>
					<th>手机：</th><td>
					<?php if($isView){
							echo $mobile;
						}else{ ?>
							<input  type="text" id="mobile" value="<?php echo $mobile;?>"/>
						<?php }?>
						</td>
					<th>传真：</th><td>
					<?php if($isView){
							echo $fax;
						}else{ ?>
							<input  type="text" id="fax" value="<?php echo $fax;?>"/>
						<?php }?>
						
						</td>
				</tr>
				<tr>
					<th>QQ/MSN/Skype：</th><td>
					<?php if($isView){
							echo $qq;
						}else{ ?>
							<input  type="text" id="qq" value="<?php echo $qq;?>"/>
						<?php }?>
						</td>
					<th>Email：</th><td>
					<?php if($isView){
							echo $email;
						}else{ ?>
							<input  type="text" id="email" value="<?php echo $email;?>"/>
						<?php }?>
						</td>
				</tr>
				<tr>
					<th>邮编：</th><td colspan="3">
					<?php if($isView){
							echo $zip_code;
						}else{ ?>
							<input  type="text" id="zip_code" value="<?php echo $zip_code;?>"/>
						<?php }?>
						</td>
				</tr>
				<tr>
					<th>网址：</th><td colspan="3">
					<?php if($isView){
							echo "<a href='$url' target='_blank'>$url</a>";
						}else{ ?>
							<input type="text" id="url" value="<?php echo $url;?>"/>
						<?php }?>
						</td>
				</tr>
				<tr>
					<th>备注：</th><td colspan="3">
					<?php if($isView){
							echo $memo;
						}else{ ?>
							<textarea     id="memo" style="width:300px;height:80px;"><?php echo $memo;?></textarea></td>
						<?php }?>
				</tr>
			</table>
			</div>
			</div>
	</div>

	<div id="evaluate">
		<div class="toolbar toolbar-auto">
				<table style="width:100%;" class="query-table  save-sale-price">
					<tr>
						<th>总体评价:</th>
						<td>
								<select name="evaluate"   <?php if($isView)echo "disabled='disabled'";?>>
										<option value="">--选择评价--</option>
										<option value="4" <?php if($evaluate==4)echo 'selected';?>>优先推荐</option>
										<option value="3" <?php if($evaluate==3)echo 'selected';?>>推荐</option>
										<option value="2" <?php if($evaluate==2)echo 'selected';?>>备选</option>
										<option value="1" <?php if($evaluate==1)echo 'selected';?>>不推荐</option>
								</select>
						</td>
					</tr>							
				</table>	
			</div>
			<ul class="eva-ul">
	<?php 
	$SqlUtils  = ClassRegistry::init("SqlUtils") ;
	$metas = $SqlUtils->exeSqlWithFormat("select * from sc_supplier_evaluate_meta",array()) ;
	$suppleEva = $SqlUtils->exeSqlWithFormat("select * from sc_supplier_evaluate where supplier_id = '{@#id#}' ",array('id'=>$id)) ;
	
	foreach($metas as $meta){
		$selected = "" ;
		if(!empty($suppleEva)){
			foreach($suppleEva as $se){
				if( $se['META_CODE'] ==$meta['CODE']  ){
					$selected = $se['SCORE'] ;
				}
			}
		}
		
		
		?>
		<li>
			<?php echo $meta['NAME']?>：
			<select  <?php if($isView)echo "disabled='disabled'";?>   id="<?php echo $meta['CODE']?>_select"></select>
				<script>
					var options = <?php echo $meta['OPTIONS'];?> ;
					var selectId = '<?php echo $meta['CODE']?>_select' ;
					$("#"+selectId).empty() ;
					$("#"+selectId).append("<option value=''>选择评价</option>")
					for(var o in options){
						var _s = "" ;
						if( o == '<?php echo $selected;?>'  ) _s ="selected" ;
						$("#"+selectId).append("<option value='"+o+"' "+_s+">"+options[o]+"</option>") ;
					}
				</script>
				
				<textarea <?php if($isView)echo "readonly='readOnly'";?>   id="<?php echo $meta['CODE']?>_memo" style="width:85%;;height:60px;"></textarea>
		</li>
<?php 	}
	
	?>
	</ul>
	</div>
	<?php  if( !$isView ){?>
					<div class="panel-foot" style="position:fixed;bottom:0px;left:0px;right:0px;" >
						<div class="form-actions col2">
							<button  type="submit" class="btn btn-primary commit">提&nbsp;交</button>
							
							<button onclick="$(this).dialogClose();" class="btn">关&nbsp;闭</button>
						</div>
					</div>
	<?php }?>
</form>
</body>
</html>