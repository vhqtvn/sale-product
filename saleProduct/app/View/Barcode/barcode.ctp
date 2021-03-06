<html>
<head>
<?php 
include_once ('config/config.php');

//error_reporting(0);
$sellerSku   	=  $params['arg1']  ;
$accountId 	=  $params['arg2']  ;
$printNum 	=  $params['arg3']  ;
$paperType  =  $params['arg4']  ;
if( empty($paperType) ){
	$paperType = "A4" ;
}
//1MM=3.2Pixel
?>
 <style style="text/css">
<!--
	html,body{
		border: none;
		padding:0px;
		margin:0px;
		-webkit-text-size-adjust:none;	
		font-size:10px;	
	}
	<?php  if( $paperType=='A4' ){ ?>
	body{
		width:828px;height:1182px;
		background:white;
	}
	<?php	}else{ ?>
	body{
		width:600px;height:auto;
		background:white;
	}	
	<?php	} ?>
	

	.text-fnsku{
		text-align: center ;
		padding-top:1px;
		padding-bottom:1px;
		font-family:  Helvetica,Arial, sans-serif;
		font-size:5px;
	}
	
	.text-title{
		text-align:left;
		margin-left: 14px;
		width:185px;
		white-space:nowrap;
		overflow: hidden;
		font-size:11px;	
		font-family: Arial Narrow, sans-serif;
		padding-bottom:1px
	}
	
	.text-condition{
		text-align:left;
		margin-left: 14px;
		font-size:14px;	
		font-weight:bold;
		font-family: Arial , sans-serif;
		padding-bottom:1px
	}
	
	.text-sku{
		text-align:left;
		margin-left: 14px;
		font-size:10px;	
		font-family: Arial , sans-serif;
	}	
	
	<?php  if( $paperType=='A4' ){ ?>
	.barcode-img{
		width:170px;
		height:30px;
	}
	
	.label-item{
		height:108px;
		width:207px;
		border:none;
		padding:0px;
		margin:0px;
	}
	.label-item-last,.label-item-11{
		height:100px;
		width:209px;
		border:none;
		padding:0px;
		margin:0px;
	}	
	<?php	}else{ ?>
	.text-fnsku{
		padding-top:1px;
		padding-bottom:4px;
		font-size:15px;
	}
	
	.text-title{
		margin-left: 18px;
		width:280px;
		font-size:15px;	
		line-height:15px;
		height:15px;
		font-family:Arial Narrow,Verdana;
		padding-bottom:1px
	}
	
	.text-condition{
		text-align:left;
		margin-left: 18px;
		line-height:15px;
		height:15px;
		font-size:15px;		
	}
	
	.text-sku{
		text-align:left;
		line-height:15px;
		height:15px;	
		margin-left:18px;
		font-size:13px;	
	}	
	
	.barcode-img{
		width:240px;
		height:30px;
	}
		
	.label-item{
		height:139px;
		width:324px;
		border:none;
		padding-top:1px;
		margin:0px;
	}
	.label-item-last{
		height:132px;
		width:324px;
		border:none;
		padding-top:1px;
		margin:0px;
		padding:1px;
	}
	<?php	} ?>
	
	
-->
</style>
</head>
<body>
<center>
<?php 

$SqlUtils  = ClassRegistry::init("SqlUtils") ;
$sqlParams = array("accountId"=>$accountId,"sku"=>$sellerSku) ;

$sql = "select saap.*,saa.name as ACCOUNT_NAME from
			sc_amazon_account saa ,
			sc_amazon_account_product saap where
             saap.account_id = saa.id
			and saap.account_id = '{@#accountId#}' and saap.sku = '{@#sku#}'" ;
$item = $SqlUtils->getObject($sql,$sqlParams) ;
$title = $item['TITLE'] ;
$condition = "New" ;//$item['ITEM_CONDITION']==
$fnSku =  $item['FC_SKU'] ;

$_result = null ;

if( empty($title) ){
	$Amazonaccount  = ClassRegistry::init("Amazonaccount") ;
	$_result = $Amazonaccount->checkProductValid($accountId,$sellerSku)  ;
	try{
		$json = json_decode($_result) ;
		if( !empty( $json ) ){
			$json = get_object_vars($json) ;
			if(isset($json['Attributes']  )){
				$Attributes = $json['Attributes'] ;
				if( !empty($Attributes) ){
					$Attributes = get_object_vars($Attributes) ;
					$title = $Attributes['Title'] ;
				}
			}
		}
		
	}catch(Exception $e){
	}
}

if( empty( $fnSku  ) ){
	//检查该产品是否能够打印标签
	if( empty($_result) ){
		$Amazonaccount  = ClassRegistry::init("Amazonaccount") ;
		$_result = $Amazonaccount->checkProductValid($accountId,$sellerSku)  ;
	}
	
	$json = json_decode($_result) ;
	$json = get_object_vars($json) ;
	$status = $json['status'] ;
	$status = strtolower($status) ;
	$isError = false ;
	if( strpos($status,"error"  ) ===false ){
	}else{ ?>
		<div  style="color:red;font-size:20px;font-weight:bold;">该Listing目前无效，不能打印标签！</div>
	<?php 
		return ; 
	}
}

if(empty( $printNum ))
	$printNum = 44 ;

$row = ceil($printNum/4) ;
$nullRow = (11 -  ($row%11))%11  ;
if( $paperType == 'A2' ){
	$row = 1;//ceil($printNum/2) ;
	$nullRow = 0  ;
}
$basedir = dirname(__FILE__);

if( empty($fnSku) ){
	$result = $Amazonaccount->listInventorySupplyBySellerSKU($accountId,$sellerSku)  ;
	$item = $SqlUtils->getObject($sql,$sqlParams) ;
	$fnSku =  $item['FC_SKU'] ;
}

$sku = $accountId.'-'.$sellerSku;//.'-'.$item['ASIN'] ;

//echo strlen($title) ;
$displayText = $title ;
$length = strlen($displayText) ;
if( $length <=40  ){
	//nothing
}else{
	$start = substr($displayText, 0,20) ;
	$end = substr($displayText, $length-20 , 20) ;
	$displayText = $start.'...'.$end ;
}

if( empty($displayText) ){
	$displayText = "no title" ;
}

$data_to_encode = $fnSku ;

//App::uses('BarcodeHelper', 'View/Helper');

//$barcode = new BarcodeHelper() ;

// Generate Barcode data
$this->Barcode->barcode();
$this->Barcode->setType('C128');
$this->Barcode->setCode($data_to_encode);
$this->Barcode->setSize(33,145);
$this->Barcode->hideCodeType() ;
$this->Barcode->setText("") ;

// Generate filename
//$file = 'img/barcode/code_'.$random.'.png';
//D:\DEVELOPER\PHP\saleProduct\barcode
$file = "$basedir/../../../barcode/$fnSku.png" ;
// Generates image file on server
$this->Barcode->writeBarcodeFile($file);

if( isset( $json['ProductCount'] ) && $json['ProductCount'] == 0 ){
	//错误
	$isError = true ;
}
?>
<style>
  body{
	<?php if($paperType == 'A2'){
		//echo "height:".(216*$row).'px' ;
	}?>
  }
</style>
<?php  if( empty( $fnSku ) ){  ?>
<b>
	<br/><br/><br/><br/>
	FNSKU为空，请手动输入FNSKU码 <br/>
	<br/><br/><br/>
	<input type="text" id="fnsku"  value="" style="width:200px;height:30px;" /> <button  onclick="doSetFNSKU()">确认</button>
</b>

<script>
	function doSetFNSKU(){
			var fnSku = document.getElementById("fnsku").value ;
			window.location.href = contextPath+"/page/forward/Barcode.barcode/<?php echo $printNum;?>/<?php echo $sellerSku;?>/<?php echo $accountId;?>/"+fnSku ;
	}
</script>
<?php
  return ;
} ?>
</center>
<script>
	function printLabel(){
		var pld = document.getElementById("printLabelDiv") ;
		pld.style.display = "none" ;
		window.print() ;
	}
	//document.oncontextmenu=function(e){return false;} 
</script>
	<div style="position:fixed;top:5px;right:10px;width:200px;height:20px;background:#FFF;" id="printLabelDiv">
	<center>
	<div style="color:red;font-weight:bold;font-size:15px;">必须设置为标签打印</div><br/>
	<button style="width:50px;height:30px;" onclick="printLabel()">打印</button>
	</center>
	</div>
	<table border="0" style="width:100%;border:0;">
		<tbody>
		<?php for($i=0 ;$i<$row ;$i++){ 
					$clazz = "label-item" ;
					
					if( ($i+1) % 11 == 0   && $paperType == 'A4'){
						$clazz = "label-item-11" ;
					}
					
					if( $i == $row -1 ){
						$clazz = "label-item-last" ;
					}
			?>
			<tr>
			<td   style="text-align:center;"  class="<?php echo $clazz;?>">
					<img src="/<?php echo $fileContextPath;?>/barcode/<?php echo $fnSku;?>.png"  class="barcode-img"/>
					<div  class="text-fnsku"><?php echo $fnSku;?></div>
					<div class="text-title"><?php echo $displayText;?></div>
					<div class="text-condition"><?php echo $condition;?></div>
					<div class="text-sku"><?php echo $sku;?></div>
			</td>
			<td  class="<?php echo $clazz;?>"  style="text-align:center;"><img src="/<?php echo $fileContextPath;?>/barcode/<?php echo $fnSku;?>.png"   class="barcode-img"/>
					<div  class="text-fnsku"><?php echo $fnSku;?></div>
					<div class="text-title"><?php echo $displayText;?></div>
					<div class="text-condition"><?php echo $condition;?></div>
					<div class="text-sku"><?php echo $sku;?></div>
			</td>
			<?php if( $paperType == 'A4' ){ ?>
			<td  class="<?php echo $clazz;?>"  style="text-align:center;"><img src="/<?php echo $fileContextPath;?>/barcode/<?php echo $fnSku;?>.png"  class="barcode-img"/>
					<div  class="text-fnsku"><?php echo $fnSku;?></div>
					<div class="text-title"><?php echo $displayText;?></div>
					<div class="text-condition"><?php echo $condition;?></div>
					<div class="text-sku"><?php echo $sku;?></div>
			</td>
			<td   class="<?php echo $clazz;?>"  style="text-align:center;"><img src="/<?php echo $fileContextPath;?>/barcode/<?php echo $fnSku;?>.png"  class="barcode-img"/>
					<div  class="text-fnsku"><?php echo $fnSku;?></div>
					<div class="text-title"><?php echo $displayText;?></div>
					<div class="text-condition"><?php echo $condition;?></div>
					<div class="text-sku"><?php echo $sku;?></div>
			</td>
			 <?php  }?>
		 </tr><?php }?>
			<?php for($i=0 ;$i<$nullRow ;$i++){ ?>
			<tr>
			<td   style="text-align:center;"  class="label-item">
					&nbsp;
			</td>
			<td  class="label-item"  style="text-align:center;">&nbsp;
			</td>
			<td  class="label-item"  style="text-align:center;">&nbsp;
			</td>
			<td   class="label-item"  style="text-align:center;">&nbsp;
			</td>
		 </tr>	
		<?php }?></tbody></table></body></html>