<html>
<head>
 <style style="text/css">
<!--
	html,body{
		border: none;
		padding:0px;
		margin:0px;
		-webkit-text-size-adjust:none;	
		font-size:10px;	
	}
	
	body{
		width:828px;height:1182px;
		background:white;
	}

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
		width:165px;
		white-space:nowrap;
		overflow: hidden;
		font-size:11px;	
		font-family: Helvetica,Arial Narrow, sans-serif;
		padding-bottom:1px
	}
	
	.text-condition{
		text-align:left;
		margin-left: 14px;
		font-size:14px;	
		font-weight:bold;
		font-family: Helvetica,Arial , sans-serif;
		padding-bottom:1px
	}
	
	.text-sku{
		text-align:left;
		margin-left: 14px;
		font-size:10px;	
		font-family:  Helvetica,Arial , sans-serif;
	}	
	
	.barcode-img{
		width:170px;
		height:30px;
	}
	
	.label-item{
		height:107.4px;
		width:207px;
	}
	
-->
</style>
</head>
<body>
<?php 
include_once ('config/config.php');

//error_reporting(0);
$printNum 	=  $params['arg1']  ;
$sellerSku   	=  $params['arg2']  ;
$accountId 	=  $params['arg3']  ;
$_fnsku 		=  $params['arg4']  ;

//检查该产品是否能够打印标签
$Amazonaccount  = ClassRegistry::init("Amazonaccount") ;
$result = $Amazonaccount->checkProductValid($accountId,$sellerSku)  ;
debug($result) ;

$row = ceil($printNum/4) ;
$nullRow = 11 -  $row%11  ;

$basedir = dirname(__FILE__);
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
$fnSku = empty($item['FNSKU'])?$item['FC_SKU']:$item['FNSKU'] ;
if(empty($fnSku)){
	$fnSku = $_fnsku ;
}

if(empty($title)){ //标题栏为空，则需要采集标题
	
}


$sku = $accountId.'-'.$sellerSku;//.'-'.$item['ASIN'] ;
//19

//echo strlen($title) ;

$arrays = explode(" ", $title) ;
$displayText = $arrays[0].' '.$arrays[1].'...'.$arrays[2].' '.$arrays[4] ;

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
?>
<center>

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

	<table border="0" style="width:100%;height:100%;">
		<tbody>
		<?php for($i=0 ;$i<$row ;$i++){ ?>
			<tr>
			<td   style="text-align:center;"  class="label-item">
					<img src="/<?php echo $fileContextPath;?>/barcode/<?php echo $fnSku;?>.png"  class="barcode-img"/>
					<div  class="text-fnsku"><?php echo $fnSku;?></div>
					<div class="text-title"><?php echo $displayText;?></div>
					<div class="text-condition"><?php echo $condition;?></div>
					<div class="text-sku"><?php echo $sku;?></div>
			</td>
			<td  class="label-item"  style="text-align:center;"><img src="/<?php echo $fileContextPath;?>/barcode/<?php echo $fnSku;?>.png"   class="barcode-img"/>
					<div  class="text-fnsku"><?php echo $fnSku;?></div>
					<div class="text-title"><?php echo $displayText;?></div>
					<div class="text-condition"><?php echo $condition;?></div>
					<div class="text-sku"><?php echo $sku;?></div>
			</td>
			<td  class="label-item"  style="text-align:center;"><img src="/<?php echo $fileContextPath;?>/barcode/<?php echo $fnSku;?>.png"  class="barcode-img"/>
					<div  class="text-fnsku"><?php echo $fnSku;?></div>
					<div class="text-title"><?php echo $displayText;?></div>
					<div class="text-condition"><?php echo $condition;?></div>
					<div class="text-sku"><?php echo $sku;?></div>
			</td>
			<td   class="label-item"  style="text-align:center;"><img src="/<?php echo $fileContextPath;?>/barcode/<?php echo $fnSku;?>.png"  class="barcode-img"/>
					<div  class="text-fnsku"><?php echo $fnSku;?></div>
					<div class="text-title"><?php echo $displayText;?></div>
					<div class="text-condition"><?php echo $condition;?></div>
					<div class="text-sku"><?php echo $sku;?></div>
			</td>
		 </tr>	
		<?php }?>
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
		<?php }?>
		</tbody>
	</table>
</body>
</html>