<?php
error_reporting(0);

$data_to_encode = 'X000JN9X11'; 

App::uses('BarcodeHelper', 'View/Helper');

$barcode = new BarcodeHelper() ;

// Generate Barcode data 
$barcode->barcode(); 
$barcode->setType('C128'); 
$barcode->setCode($data_to_encode); 
$barcode->setSize(48.5 ,25.4); 
$barcode->hideCodeType() ;

// Generate filename             
//$file = 'img/barcode/code_'.$random.'.png'; 
//D:\DEVELOPER\PHP\saleProduct\barcode
$file = "D:/DEVELOPER/PHP/saleProduct/barcode/X000JN9X11.png" ;

// Generates image file on server             
//$barcode->writeBarcodeFile($file); 

// Display image 
?>

<table border="0" style="width:100%;height:100%;">
	<tbody>
	<?php  for ( $i=0 ;$i<11;$i++){ ?>
	<tr>
		<td><img src="http://localhost/saleProduct/barcode/X000JN9X11.png"/></td>
		<td><img src="http://localhost/saleProduct/barcode/X000JN9X11.png"/></td>
		<td><img src="http://localhost/saleProduct/barcode/X000JN9X11.png"/></td>
		<td><img src="http://localhost/saleProduct/barcode/X000JN9X11.png"/></td>
	</tr>		
	<?php   } ?>
	</tbody>
</table>
