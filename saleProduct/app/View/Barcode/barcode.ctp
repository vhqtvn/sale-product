<?php 
include_once ('config/config.php');

//error_reporting(0);
$sellerSku   =  $params['arg1']  ;
$accountId =  $params['arg2']  ;
$fnSku =  $params['arg3']  ;

$basedir = dirname(__FILE__);
$SqlUtils  = ClassRegistry::init("SqlUtils") ;
//获取FNSKU
$sql = "select * from sc_fba_supply_inventory where account_id = '{@#accountId#}' and seller_sku = '{@#sku#}'" ;
$sqlParams = array("accountId"=>$accountId,"sku"=>$sellerSku) ;
$item = $SqlUtils->getObject($sql,$sqlParams) ;
$fnSku = empty($item['FNSKU'])?$fnSku:$item['FNSKU'] ;

$sql = "select * from sc_amazon_account_product where account_id = '{@#accountId#}' and sku = '{@#sku#}'" ;
$item = $SqlUtils->getObject($sql,$sqlParams) ;
$title = $item['TITLE'] ;

$data_to_encode = $fnSku ;

//App::uses('BarcodeHelper', 'View/Helper');

//$barcode = new BarcodeHelper() ;

// Generate Barcode data
$this->Barcode->barcode();
$this->Barcode->setType('C128');
$this->Barcode->setCode($data_to_encode);
$this->Barcode->setSize(39 ,12);
$this->Barcode->hideCodeType() ;

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
	<input type="text" id="fnsku"  value="" style="width:200px;height:30px;"/> <button  onclick="doSetFNSKU()">确认</button>
</b>

<script>
	function doSetFNSKU(){
			var fnSku = document.getElementById("fnsku").value ;
			window.location.href = contextPath+"/page/forward/Barcode.barcode/<?php echo $sellerSku;?>/<?php echo $accountId;?>/"+fnSku ;
	}
</script>
<?php
  return ;
} ?>

<div style="width:794px;height:1123px;border:1px solid #000000;">
	<table border="0" style="width:100%;height:100%;">
		<tbody>
		<?php for($i=0 ;$i<11 ;$i++){ ?>
			<tr>
			<td   style="text-align:center;">
					<img src="http://localhost/saleProduct/barcode/<?php echo $fnSku;?>.png"/>
					<br/><?php echo $title;?>
			</td>
			<td   style="text-align:center;"><img src="http://localhost/saleProduct/barcode/<?php echo $fnSku;?>.png"  />
				<br/><?php echo $title;?></td>
			<td   style="text-align:center;"><img src="http://localhost/saleProduct/barcode/<?php echo $fnSku;?>.png" />
				<br/><?php echo $title;?></td>
			<td    style="text-align:center;"><img src="http://localhost/saleProduct/barcode/<?php echo $fnSku;?>.png" />
				<br/><?php echo $title;?></td>
		 </tr>	
		<?php }?>
			
		</tbody>
	</table>
</div>
</center>