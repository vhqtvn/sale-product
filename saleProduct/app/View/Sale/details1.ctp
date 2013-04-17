<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>llygrid demo</title>
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
		
		//$this->set('details', $details);
		//$this->set('images', $images);
		//$this->set('competitions', $competitions);
		//$this->set('rankings', $rankings);
		
		$user = $this->Session->read("product.sale.user") ;
		$group=  $user["GROUP_CODE"] ;
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
		
		$username = $user["NAME"] ;
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
 	
 	.alert-success{
 		font-size:15px;
 		color:blue;
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
 </style>
 
 <script>
 	var filterId = '<?php echo $filterId;?>' ;
 	var asin = '<?php echo $asin;?>' ;
 	var type = '<?php echo $type;?>' ;
 	var status =  '<?php echo $status;?>' ;
 
 	$(function(){
 		createActionbar();
 		
 		$(".action").click(function(){
 			var status = $(this).attr("status") ;
 			
 			var _ = $.trim( $(this).text() ) ;
 			var val = getDescription(_) ;
 			
 			var strategy = $("#strategy").val() ;
 			
 			if(status == 3 && !(val && $.trim(val)) ){
 				alert("必须填写废弃理由！") ;
 				return false ;
 			}
 			
			if( window.confirm("确认执行该操作吗？") ){
				$.ajax({
					type:"post",
					url:contextPath+"/sale/productFlowProcess" ,
					data:{description:val,filterId:filterId,asin:asin,status:status,strategy:strategy},
					cache:false,
					dataType:"text",
					success:function(result,status,xhr){
						//window.opener.$(".grid-content-details").llygrid("reload") ;
						window.location.reload() ;
					}
				}); 
			}
			
 			return false ;
 		}) ;
 		
 	}) ;
 	
 	function createActionbar(){
 		var html = [] ;
 		var _status = '' ;
 		if(type == 1){
 			html.push('<button class="remove action btn" status="3">废弃</button>&nbsp;') ;
 			html.push('<button class="apply action btn btn-primary" status="4">提交产品经理审批</button>&nbsp;') ;
 			html.push('<button class="noapply action btn btn-primary" status="2">添加备注暂不提交审批</button>&nbsp;') ;
 			_status = 2 ;
 		}else if(type == 2){
 			html.push('<button class="remove action btn" status="3">废弃</button>&nbsp;') ;
 			html.push('<button class="apply action btn btn-primary" status="6">提交总经理审批</button>&nbsp;') ;
 			html.push('<button class="apply action btn btn-primary" status="5">审批通过</button>&nbsp;') ;
 			html.push('<button class="noapply action btn btn-primary" status="4">添加备注暂不提交审批</button>&nbsp;') ;
 			_status = 4 ;
 		}else if(type == 3){
 			html.push('<button class="remove action btn" status="3">废弃</button>&nbsp;') ;
 			html.push('<button class="apply action btn btn-primary" status="7">审批通过</button>&nbsp;') ;
 			html.push('<button class="noapply action btn btn-primary" status="6">添加备注暂不审批</button>&nbsp;') ;
 			_status = 6 ;
 		}
 		
 		if( status == '3' ){
 			 html = [] ;
 			 html.push('<button class="enable action btn" status="'+_status+'">启用</button>&nbsp;') ;
 		}
 		
 		$(".toobar-btns").html(html.join("")) ;
 	}
 	
 	$(function(){
			$(".base-gather").click(function(){
				$.ajax({
					type:"post",
					url:contextPath+"/gatherProduct/execute/<?php echo $product["ASIN"]?>",
					data:{},
					cache:false,
					dataType:"text",
					success:function(result,status,xhr){
						alert("采集完成");
						window.location.reload() ;
					}
				}); 
			}) ;
			
			$(".supplier").click(function(){
				openCenterWindow(contextPath+"/supplier/listsSelect/<?php echo $product["ASIN"]?>",800,600) ;
			}) ;
			
			
			$(".category").click(function(){
				openCenterWindow(contextPath+"/product/assignCategory/<?php echo $product["ASIN"]?>",400,500) ;
			}) ;
			
			$(".update-supplier").click(function(){
				var supplierId = $(this).attr("supplierId") ;
				openCenterWindow(contextPath+"/supplier/updateProductSupplierPage/<?php echo $product["ASIN"]?>/"+supplierId,650,600) ;
				return false;
			}) ;
			
			$("[testStatus]").click(function(){//下架
				
				var testStatus = $(this).attr("testStatus") ;
				
				var _ = $.trim( $(this).text() )  ;
				
				var val = getDescription(_) ;
	 			
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
			
			$("[supplier-id]").click(function(){
				var id = $(this).attr("supplier-id") ;
				viewSupplier(id) ;
				return false ;
			}) ;
			
		});
		
		function getDescription(action){
			//return "" ;
			var beforeDes = $("#description_hidden").val();
			var now       = $("#description").val()||"未填写备注信息" ;
			var username = '<?php echo $username;?>' ;
			return beforeDes+"<span>【"+username+"】"+new Date().format("yyyy-MM-dd hh:mm:ss") +"("+action+")</span><p><span>"+now+"</span></p>" ;
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
<body style="overflow-y:auto;padding:2px;">
	<div class="row-fluid">
		<div class="span11">
			<div class="toolbar">
				<div class="row-fluid" style="margin:5px;">
					<div class="span2">
						<button class="base-gather btn">信息采集</button>
					</div>
					<div class="span7 toobar-btns">
					</div>
				</div>
				<div>
					<select id="strategy" class="span6">
						<option value="">--选择策略--</option>
						<?php
							foreach($strategys as $strategy){
								$temp = "" ;
								if( $product["STRATEGY"] == $strategy['sc_config']['KEY']){
									$temp = " selected " ;
								} ;
								echo "<option $temp value='".$strategy['sc_config']['KEY']."'>".$strategy['sc_config']['LABEL']."</option>" ;
							} ;
						?>
					</select>
				</div>	
				<div>
					<textarea id="description" style="width:98%;height:40px;"></textarea>
					<div class="description-container"><pre><?php echo $product["COMMENT"]?></pre></div>
					<textarea id="description_hidden" style="display:none;"><?php echo $product["COMMENT"]?></textarea>
				</div>
			</div>
		</div>
		<div class="span1">
		<?php  
			$testStatus = $product['TEST_STATUS'] ;
			$userStatus = $product['USER_STATUS'] ;
			
			if( $group == 'manage' || $group== 'general_manager' || $group == 'sale_specialist' ){
				if( $testStatus == 'testing' ){
					echo "<div class='alert alert-info'>
							试销中(PV:$flow)
							<button class='btn normal-sell' testStatus='formal'>正式销售</button><div style='height:5px;'></div>
							<button class='btn uninstall-product' testStatus='uninstall'>下架</button>
					</div>" ;
				}else if( $testStatus == 'formal' ){
					echo "<div class='alert alert-warning'>
						正式销售(PV:$flow)
						<button class='btn testing-sell'  testStatus='testing'>试销</button><div style='height:5px;'></div>
						<button class='btn uninstall-product' testStatus='uninstall'>下架</button>
				</div>" ;
				}else if( $testStatus == 'uninstall' ){
					echo "<div class='alert alert-warning'>
						已下架
				</div>" ;
				}else{
					echo "<div class='alert alert-warning'>
						待开发
				</div>" ;
				}
				
				if($userStatus == "focus"){
					echo "<div class='alert alert-success alert-focus'>
						异常关注
						<button class='btn btn-small strong-focus' testStatus='unfocus'>取消关注</button>
				</div>" ;
				}else{
					echo "<div class='alert alert-info alert-focus'>
						<button class='btn btn-primary strong-focus' testStatus='focus'>添加关注</button>
				</div>" ;
				}
			}else{
				if( $testStatus == 'testing' ){
					if( empty($flow) ){
						echo "<div class='alert alert-info'>
								试销中(流量测试中)
						</div>" ;
					}else{
						echo "<div class='alert alert-info'>
								试销中(PV:$flow)
						</div>" ;
					}
				}else if( $testStatus == 'formal' ){
					echo "<div class='alert alert-warning'>
						正式销售(PV:$flow)
				</div>" ;
				}else if( $testStatus == 'uninstall' ){
					echo "<div class='alert alert-warning'>
						已下架
				</div>" ;
				}else{
					echo "<div class='alert alert-warning'>
						待开发
				</div>" ;
				}
				
				if($userStatus == "focus"){
					echo "<div class='alert alert-success alert-focus'>
						异常关注
				</div>" ;
				}
			}
		?>
		</div>
	</div>
	
	<script type="text/javascript">
		$(function(){
			var tab = $('#details_tab').tabs( {
				tabs:[
					{label:'基本信息',content:"baseinfo-tab"},
					{label:'竞争信息',content:"competetion-tab"},
					{label:'产品分类',url:contextPath+"/product/assignCategory/<?php echo $asin?>",iframe:true}
				] ,
				height:'500px'
			} ) ;
		}) ;
	</script>
	<div id="details_tab">
	</div>	
	
	<div>
		<div id="baseinfo-tab" class="ui-tabs-panel" style="height: 100px; display: block; ">
			<table class="table table-bordered">
				<tr>
					<th>标题：</th>
					<td><?php echo $product["TITLE"]?>(<?php echo $product["ASIN"]?>) </td>
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