<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
 <title>采购执行列表</title>
<style type="text/css">
	table{
		font-size:5em;
	}
</style>
<head>
<body>
  <table style="width:100%;">
    <caption>采购执行列表</caption>
    <?php
    	foreach( $purchaseList as $record ){
    		$record = $record['sc_purchase_plan'] ;
    ?>
    <tr>
		<td><a href="/saleProduct/index.php/phone/purchasePlanDetails/<?php echo $record['ID'];?>"><?php echo $record['NAME'];?></a></td>
	</tr> 
    <?		
    	}
    ?>
	
</table>

</body>
</html>