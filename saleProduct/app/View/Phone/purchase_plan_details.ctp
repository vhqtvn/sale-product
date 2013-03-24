<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<?php 
include_once ('config/config.php');

?>
 <title>采购产品列表</title>
<style type="text/css">
	table{
		font-size:3em;
		border-bottom:1px solid blue;
		border-right:1px solid blue;
	}
	
	table tr td{
		border:1px solid blue;
		border-right:0px;
		border-bottom:0px;
	}
</style>
<head>
<body>
  <table style="width:100%;">
    <caption>采购产品列表</caption>
    <?php
    	$index = 0 ;
    	foreach( $purchaseList as $record ){
    		$index++ ;
    		//print_r($record);
    		$scproduct =  $record['t'] ;
    		$others = $record['t'] ;
    		$record = $record['t'] ;
    		
    		$title = $scproduct['TITLE'] ;
    		$localUrl = $others['LOCAL_URL'] ;
    		
    ?>
    <tr>
    	<td><?php echo $index;?></td>
		<td><a href="<?php echo $contextPath;?>/phone/productDetails/<?php echo $record['ASIN'];?>"><?php echo $record['ASIN'];?></a></td>
		<td><?php echo $title;?></td>
		<td>
		<?php if(empty($localUrl)){}else{
			$localUrl = str_replace("%" , "%25",$localUrl) ;
		?>
		<img src='<?php echo '/'.$fileContextPath.'/'.$localUrl;?>'>
		<?php
		}?>
		</td>
	</tr> 
    <?		
    	}
    ?>
	
</table>

</body>
</html>