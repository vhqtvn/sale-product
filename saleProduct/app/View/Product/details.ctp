<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>产品基本信息</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   		include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('default/style');
		echo $this->Html->css('../js/tab/jquery.ui.tabs');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery-ui');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('tab/jquery.ui.tabs');
		
		
		$user = $this->Session->read("product.sale.user") ;
		$group=  $user["GROUP_CODE"] ;
		$username = $user["NAME"] ;
		
		$security  = ClassRegistry::init("Security") ;
		$hasSetSupplierPermission = $security->hasPermission($user['LOGIN_ID'] , 'SET_PRODUCT_SUPPLIER_FLAG') ;
		
	?>
	<?php
		$product = null ;
		$competition = null;
		$potential =null;
		$fba       =null;
		$flow = "" ;
		
		if( !empty($details) ){
			$product = $details[0]['sc_product'] ;
			$competition = $details[0]['sc_sale_competition'] ;
			$potential = $details[0]['sc_sale_potential'] ;
			$fba       = $details[0]['sc_sale_fba'] ;
		}
		
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
 	.p-base{
 		border:1px solid #CCC;
 		padding:3px;
 		margin:3px;
 	}
 	
 	b,table th{
 		font-weight:bold;
 	}

 	.toolbar{
 		background-color:#EEE;
 		width:98%;
 		padding:3px;
 		
 		border:1px solid #CCC;
 		margin-bottom:3px;
 	}
 	
 	.alert{
 		margin-left:-18px;
 		margin-top:20px;
 		margin-bottom:3px;
 		font-weight:bold;
 		text-align:center;
 		padding:3px;
 		color:#000;
 	}
 	
 	.alert .btn{
 		padding-left:5px;
 		padding-right:5px;
 		margin-top:3px;
 	}
 	
 	div.alert-focus{
 		margin-top:2px;
 	}
 	
 	p{
 		text-indent:1em;
 		font-weight:bold;
 	}
 	
 	.description-container{
 		max-height:200px;
 		min-height:50px;
 		overflow:auto;
 	}
 	
 	.p-label{
 		margin:0px 10px;
 		font-weight:bold;
 	}
 	
 	.used-clz{
		background:pink;
 	}
 </style>
 
 
 <script>
 	var asin = '<?php echo $asin;?>' ;

 	
 	$(function(){
			$(".base-gather").click(function(){
				var platformId = $("[name='platformId']").val() ;
				if(!platformId) {
					alert("必须选择平台！");
					return ;
				}
				$.ajax({
					type:"post",
					url:contextPath+"/gatherProduct/execute/<?php echo $asin?>/"+platformId,
					data:{},
					cache:false,
					dataType:"text",
					success:function(result,status,xhr){
						alert("获取完成");
						window.location.reload() ;
					}
				}); 
			}) ;
			
			$(".category").click(function(){
				openCenterWindow(contextPath+"/product/assignCategory/<?php echo $asin?>",400,500) ;
			}) ;
			
			$(".update-supplier").click(function(){
				var supplierId = $(this).attr("supplierId") ;
				openCenterWindow(contextPath+"/supplier/updateProductSupplierPage/<?php echo $asin?>/"+supplierId,650,600) ;
				return false;
			}) ;
			
			$("[testStatus]").click(function(){//下架
				var _ = $.trim( $(this).text() )  ;
				
				var val = getDescription(_) ;
				
				var testStatus = $(this).attr("testStatus") ;
	 			
				if( window.confirm("确认执行该操作吗？") ){
					$.ajax({
						type:"post",
						url:contextPath+"/sale/productTestStatus" ,
						data:{description:val,asin:asin,testStatus:testStatus},
						cache:false,
						dataType:"text",
						success:function(result,status,xhr){
							window.location.reload() ;
						}
					}); 
				}
				
	 			return false ;
			}) ;
			
			$(".add-product-knowlege").click(function(){
				var _ = $.trim( $(this).text() )  ;
				
				var val = getOnlyDescription(_) ;
				if(val === false ){
					alert("未填写产品知识信息！") ;
					return ;
				}
				
				if( window.confirm("确认执行该操作吗？") ){
					$.ajax({
						type:"post",
						url:contextPath+"/sale/productKnowlege" ,
						data:{description:val,asin:asin},
						cache:false,
						dataType:"text",
						success:function(result,status,xhr){
							window.location.reload() ;
						}
					}); 
				}
				
	 			return false ;
			}) ;
			
			
			$("[supplier-id]").click(function(){
				var id = $(this).attr("supplier-id") ;
				viewSupplier(id) ;
				return false ;
			}) ;
			
			$(".edit-content").live("click",function(){
				if($(this).text() == '[保存]'){
					$(this).removeClass("save-knowledge") ;
					$(this).html("[编辑]") ;
					$(this).prev().html($(this).prev().find("textarea").val()) ;
					$(this).parents("pre:first").find(".edit-content").remove() ;
					var content = $(".pre-knowledge").html() ;
					
					if( window.confirm("确认执行该操作吗？") ){
						$.ajax({
							type:"post",
							url:contextPath+"/sale/productKnowlege" ,
							data:{description:content,asin:asin},
							cache:false,
							dataType:"text",
							success:function(result,status,xhr){
								$(".pre-knowledge p").css("position","relative").each(function(){
									$(this).append('<a href="#" class="edit-content" style="position:absolute;right:0px;top:0px;">[编辑]</a>') ;
								}) ;
							}
						}); 
					}
				}else{
					var info = $(this).prev().html() ;
					$(this).prev().html("<textarea style='width:80%;min-height:50px;max-height:100px;'>"+info+"</textarea>") ;
					$(this).html("[保存]") ;
					$(this).addClass("save-knowledge") ;
				}
				
			})
		
			$(".pre-knowledge p").css("position","relative").each(function(){
				$(this).append('<a href="#" class="edit-content" style="position:absolute;right:0px;top:0px;">[编辑]</a>') ;
			}) ;
			//
		});
		
		function getEditableKnowledge(){
			$(".pre-knowledge textarea").each(function(){
				$(this).parent().html($(this).val()) ;
			}) ;
			
			$(".pre-knowledge").find(".edit-content").remove() ;
			
			var content = $(".pre-knowledge").html() ;
			
			$(".pre-knowledge p").css("position","relative").each(function(){
				$(this).append('<a href="#" class="edit-content" style="position:absolute;right:0px;top:0px;">[编辑]</a>') ;
			}) ;
			return content ;
		}

		function getDescription(action){
			//return "" ;
			var beforeDes = $("#description_hidden").val();
			var now       = $("#description").val()||"未填写备注信息" ;
			var username = '<?php echo $username;?>' ;
			return beforeDes+"<span>【"+username+"】"+new Date().format("yyyy-MM-dd hh:mm:ss") +"("+action+")</span><p><span>"+now+"</span></p>" ;
		}
		
		function getOnlyDescription(action){
			var beforeDes = getEditableKnowledge() ;
			var now       = $("#description").val() ;
			var username = '<?php echo $username;?>' ;
			if( !$.trim(now) )return false ;
			return $.trim(beforeDes)+"<span>【"+username+"】"+new Date().format("yyyy-MM-dd hh:mm:ss") +"("+action+")</span><p><span>"+now+"</span></p>" ;
		}
		
		Date.prototype.format = function(format){ 
			var o = { 
				"M+" : this.getMonth()+1, //month 
				"d+" : this.getDate(), //day 
				"h+" : this.getHours(), //hour 
				"m+" : this.getMinutes(), //minute 
				"s+" : this.getSeconds(), //second 
				"q+" : Math.floor((this.getMonth()+3)/3), //quarter 
				"S" : this.getMilliseconds() //millisecond 
			} 
			
			if(/(y+)/.test(format)) { 
				format = format.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length)); 
			} 
			
			for(var k in o) { 
				if(new RegExp("("+ k +")").test(format)) { 
					format = format.replace(RegExp.$1, RegExp.$1.length==1 ? o[k] : ("00"+ o[k]).substr((""+ o[k]).length)); 
				} 
			} 
			return format; 
		} 
 </script>

</head>
<body style="overflow-y:auto;padding:2px;padding-top:30px;">
	<div class="row-fluid" style="position:fixed;top:0px;left:0px;right:0px;height:30px;z-index:1;">
		<div class="span12">
			<div class="toolbar">
						<button class="base-gather btn">信息获取</button>
						<select name="platformId"
								<?php  if(!empty($product["TITLE"])) echo "disabled";?>
							  style="margin:0px;padding:0px;height:25px;">
								<option value="">--选择平台--</option>
								<?php 
									$SqlUtils  = ClassRegistry::init("SqlUtils") ;
									$platforms = $SqlUtils->exeSql("sql_platform_list",array()) ;
									foreach( $platforms as $s){
										$s = $SqlUtils->formatObject($s) ;
										$selected = '' ;
										if( $s['ID'] == $product['PLATFORM_ID'] ){
											$selected = "selected" ;
										}
										echo "<option $selected value='".$s['ID']."'>".$s['NAME']."</option>" ;
									}
								?>
							</select>
			</div>
		</div>
	</div>
	
	<script type="text/javascript">
		$(function(){
			var tab = $('#details_tab').tabs( {
				tabs:[
					{label:'基本信息',content:"baseinfo-tab"},
					{label:'竞争信息',content:"competetion-tab"},
					//{label:'供应商(询价)',content:"supplier-tab",iframe:true},
					{label:'产品分类',url:contextPath+"/product/assignCategory/<?php echo $asin?>",iframe:true},
					{label:'产品流量',url:contextPath+"/page/forward/Flow.flowAsin/<?php echo $asin?>",iframe:true},
					<?php  if( isset($sku) && !empty($sku) ){ ?>
					{label:'营销产品分类',url:contextPath+"/amazonaccount/assignCategory/<?php echo $asin;?>/<?php echo $accountId;?>/<?php echo $sku;?>",iframe:true}
					<?php } ?>
				] ,
				height:'500px'
			} ) ;

			$(".used").click(function(){
				var supplierId=$(this).attr("supplierId");
				var asin  ='<?php echo $asin?>';

				if(window.confirm("确认采用？")){
					$.dataservice("model:Product.setSupplierFlag",{supplierId:supplierId,asin:asin},function(result){
						window.location.reload();
					});
				}
				
				
			});
		}) ;
	</script>
	<div id="details_tab">
	</div>	
	
	<div>
		<div id="baseinfo-tab" class="ui-tabs-panel" style="height: 100px; display: block; ">
			<table class="table table-bordered">
				<tr>
					<th style="width:100px;">标题：</th>
					<td ><a href="#" offer-listing="<?php echo $asin?>"><?php echo $product["TITLE"]?>(<?php echo $asin?>) </a></td>
					<td rowspan="8">
						<?php
							$imgString = "" ;
							foreach( $imgs as $img ){
								$url = str_replace("%" , "%25",$img['LOCAL_URL']) ;
								$imgString = "<img src='/".$fileContextPath."/".$url."'>" ;
							} ;
							echo $imgString ;
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
					<td colspan="2"><?php echo htmlspecialchars_decode( $product["TECHDETAILS"] ); ?></td>
				</tr>
				<tr>
					<th>PRODUCT Description：</th>
					<td colspan="2"><?php echo htmlspecialchars_decode($product["DESCRIPTION"]);?> </td>
				</tr>
				<!--
				<tr>
					<th>PRODUCT Details：</th>
					<td colspan="2"><?php echo $product["PRODUCTDETAILS"]?></td>
				</tr>
				-->
			</table>
		</div>
		<div id="competetion-tab" class="ui-tabs-panel  ui-tabs-hide" style="height: 100px; display: none; ">
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
	</div>
	
</body>

</html>