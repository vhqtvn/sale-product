<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>产品详细信息</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>
	
	<style type="text/css">

</style>

   <?php
   include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->script('jquery');
		
		$user = $this->Session->read("product.sale.user") ;
		$group=  $user["GROUP_CODE"] ;
		$username = $user["NAME"] ;
		
	?>
	<?php
		$product = $details[0]['sc_product'] ;
		$competition = $details[0]['sc_sale_competition'] ;
		$potential = $details[0]['sc_sale_potential'] ;
		$fba       = $details[0]['sc_sale_fba'] ;
		$flow = "" ;
		
		if( isset($flows) ){
			if( !empty($flows) )
			{
				$flow = $flows[0]['sc_product_flow_details']["DAY_PAGEVIEWS"] ;
			}
		}
		
		$imgs = array() ;
		foreach( $images as $image ){
			$imgs[] = $image['sc_product_imgs'] ;
		} ;
		
		$comps = array() ;
		foreach( $competitions as $com ){
			$comps[] = $com['sc_sale_competition_details'] ;
		} ;
		
		$ranks = array() ;
		foreach( $rankings as $ranking ){
			$ranks[] = $ranking['sc_sale_potential_ranking'] ;
		} ;
		
		$fbs = array() ;
		foreach( $fbas as $fb ){
			$fbs[] = $fb['sc_sale_fba_details'] ;
		} ;
		
		
	?>
  
   <style type="text/css">
   		table tr td,table tr th{
   			font-size:3em;
   		}
   		
   	table{
		border-bottom:1px solid blue;
		border-right:1px solid blue;
	}
	
	table tr td,table tr th{
		border:1px solid blue;
		border-right:0px;
		border-bottom:0px;
	}
	
	.block-title{
		font-weight:bold;
		margin-top:30px;
		background:#AAA;
		padding:5px;
		font-size:3em;
	}

 </style>
 
 <script>
 	var asin = '<?php echo $asin;?>' ;

 	
 	$(function(){
			
			$("[supplier-id]").click(function(){
				var id = $(this).attr("supplier-id") ;
				viewSupplier(id) ;
				return false ;
			}) ;
		});
		
 </script>

</head>
<body style="overflow-y:auto;padding:2px;">
	<div class="row-fluid">
		<div class="span11">
			<div class="toolbar">
				<div>
						<?php
							foreach($strategys as $strategy){
								$temp = "" ;
								if( $product["STRATEGY"] == $strategy['sc_config']['KEY']){
									echo$strategy['sc_config']['LABEL'] ;
								} ;
							} ;
						?>
				</div>	
				<div><div class="description-container">
						<pre><?php echo $product["COMMENT"]?></pre>
						<pre style="position:relative;text-align:left;" class="pre-knowledge"><?php echo $product["KNOWLEDGE"]?>
						</pre>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="block-title">基本信息</div>
	<div>
		<div id="baseinfo-tab" class="ui-tabs-panel">
			<table class="table table-bordered">
				<tr>
					<th style="width:100px;">标题：</th>
					<td><?php echo $product["TITLE"]?>(<?php echo $asin?>) </td>
					<td rowspan="8">
						<?php
							foreach( $imgs as $img ){
								$url = str_replace("%" , "%25",$img['LOCAL_URL']) ;
								echo "<img src='/".$fileContextPath."/".$url."'>" ;
							} ;
						?>
					</td>
				</tr>
				<tr>
					<th>每日PV：</th>
					<td>
						<b><?php echo $flow?></b>
					</td>
				</tr>
				<tr>
					<th>Reviews：</th>
					<td>
						<b><?php echo $potential["REVIEWS_NUM"]?></b>
					</td>
				</tr>
				<tr>
					<th>Quality：</th>
					<td>
						<b><?php echo $potential["QUALITY_POINTS"]?></b>
					</td>
				</tr>
				<tr>
					<th>BRAND：</th>
					<td>
						<b><?php echo $product["BRAND"]?></b>
					</td>
				</tr>
				<tr>
					<th>DIMENSIONS：</th>
					<td>
						<b><?php echo $product["DIMENSIONS"]?></b>
					</td>
				</tr>
				<tr>
					<th>WEIGHT：</th>
					<td>
						<b><?php echo $product["WEIGHT"]?></b>
					</td>
				</tr>
				<tr>
					<th>Ranking：</th>
					<td>
						<table class="table table-bordered">
						<tr>
							<th>排名</th>
							<th>类型</th>
						</tr>
						<?php
							foreach( $ranks as $rank ){
								echo "<tr>
							<td>".$rank['RANKING']."</td>
							<td>".$rank['TYPE']."</td>
								</tr>" ;
							} ;
						?>
						</table>
					</td>
				</tr>
				<tr>
					<th>TECH Details：</th>
					<td colspan="2"><?php echo $product["TECHDETAILS"]?></td>
				</tr>
				<tr>
					<th>PRODUCT Description：</th>
					<td colspan="2"><?php echo $product["DESCRIPTION"]?> </td>
				</tr>
			</table>
		</div>
		
		<div class="block-title">竞争信息</div>
		<div id="competetion-tab" class="ui-tabs-panel  ui-tabs-hide" >
			<div class="p-comps">
				<div><b></b></div>
				<table class="table table-bordered">
				<tr>
					<th colspan="5" style="text-align:left;padding-left:30px;">
					<span class="p-label">产品竞争信息：</span>
					<span>FM:<?php echo $competition["FM_NUM"]?>  </span>
					<span>NM:<?php echo $competition["NM_NUM"]?> </span>
					<span>UP:<?php echo $competition["UM_NUM"]?></span>
					<span>FBA:<?php echo $fba["FBA_NUM"]?></span>
					<span class="p-label">每日PV:</span>
					<span><?php echo $flow?></span>
					</th>
				</tr>
				<tr>
					<th>类型</th>
					<th>商家名称</th>
					<th>商家图片</th>
					<th>价格</th>
					<th>运输价格</th>
					<th>总价格</th>
				</tr>
				<?php
					foreach( $comps as $comp ){
						$url = str_replace("%" , "%25",$comp['SELLER_IMG']) ;
						
						$sellerPrice = str_replace(",","",$comp['SELLER_PRICE']) ;
						
						$total = $sellerPrice + $comp['SELLER_SHIP_PRICE'] ;
						echo "<tr>
					<td>".$comp['TYPE']."</td>
					<td><a href='".$comp["SELLER_URL"]."' target='_blank'>".$comp['SELLER_NAME']."</a></td>
					<td><a href='".$comp["SELLER_URL"]."' target='_blank'><img src='/".$fileContextPath."/".$url."'></a></td>
					<td>".$sellerPrice."</td>
					<td>".$comp['SELLER_SHIP_PRICE']."</td>
					<td>".$total."</td>
						</tr>" ;
					} ;
					
					foreach( $fbs as $f ){
						$url = str_replace("%" , "%25",$f['SELLER_IMG']) ;
						$sellerPrice = str_replace(",","",$f['SELLER_PRICE']) ;
						
						$total = $sellerPrice + $f['SELLER_SHIP_PRICE'] ;
						echo "<tr>
						<td>".$f['TYPE']."</td>
						<td><a href='".$f["SELLER_URL"]."' target='_blank'>".$f['SELLER_NAME']."</a></td>
						<td><a href='".$f["SELLER_URL"]."' target='_blank'><img src='/".$fileContextPath."/".$url."'></a></td>
						<td>".$sellerPrice."</td>
						<td>".$f['SELLER_SHIP_PRICE']."</td>
			            <td>".$total."</td>
							</tr>" ;
					} ;
				?>
				</table>
			</div>
		</div>
		
		<div class="block-title">供应商信息</div>
		<div id="supplier-tab" class="ui-tabs-panel">
			<table class="table table-bordered">
				<tr>
					<th>供应商名称</th>
					<th>产品重量</th>
					<th>生产周期</th>
					<th>包装方式</th>
					<th>付款方式</th>
					<th>产品尺寸</th>
					<th>包装尺寸</th>
					<th>报价1</th>
					<th>报价2</th>
					<th>报价3</th>
					<th></th>
				</tr>
				<?php foreach($suppliers as $supplier){
					$urls = "" ;
					if( $supplier['sc_product_supplier']['URL'] != '' ){
						if( $supplier['sc_product_supplier']['IMAGE'] != "" ){
							$urls = "	<a href='".$supplier['sc_product_supplier']['URL']."' target='_blank'>
								<img src='/".$fileContextPath."/".$supplier['sc_product_supplier']['IMAGE']."' style='width:80px;height:50px;'>
							</a>" ;
						}else{
							$urls = "	<a href='".$supplier['sc_product_supplier']['URL']."' target='_blank'>产品网址</a>" ;
						}
					}else if($supplier['sc_product_supplier']['IMAGE'] != ""){
						$urls = "<img src='/".$fileContextPath."/".$supplier['sc_product_supplier']['IMAGE']."' style='width:80px;height:50px;'>" ;
					}
					
					echo "<tr>
						<td>".$supplier['sc_supplier']['NAME']."</td>
						<td>".$supplier['sc_product_supplier']['WEIGHT']."</td>
						<td>".$supplier['sc_product_supplier']['CYCLE']."</td>
						<td>".$supplier['sc_product_supplier']['PACKAGE']."</td>
						<td>".$supplier['sc_product_supplier']['PAYMENT']."</td>
						<td>".$supplier['sc_product_supplier']['PRODUCT_SIZE']."</td>
						<td>".$supplier['sc_product_supplier']['PACKAGE_SIZE']."</td>
						<td>".$supplier['sc_product_supplier']['NUM1']."/".$supplier['sc_product_supplier']['OFFER1']."</td>
						<td>".$supplier['sc_product_supplier']['NUM2']."/".$supplier['sc_product_supplier']['OFFER2']."</td>
						<td>".$supplier['sc_product_supplier']['NUM3']."/".$supplier['sc_product_supplier']['OFFER3']."</td>
						<td rowspan=2>
						  $urls
						</td>
					</tr><tr>
						<td colspan=10>".$supplier['sc_product_supplier']['MEMO']."
						</td>
						
					</tr> " ;
				}?>
				
			</table>
		</div>
		
	</div>
	
</body>

</html>