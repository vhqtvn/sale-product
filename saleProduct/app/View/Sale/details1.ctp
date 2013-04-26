<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>产品开发</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
  		 include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('default/style');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('../js/tab/jquery.ui.tabs');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('../js/listselectdialog/jquery.listselectdialog');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery-ui');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('tab/jquery.ui.tabs');
		echo $this->Html->script('validator/jquery.validation');
		echo $this->Html->script('modules/sale/details_dev');
		echo $this->Html->script('listselectdialog/jquery.listselectdialog');
		echo $this->Html->script('grid/jquery.llygrid');
		
		//$this->set('details', $details);
		//$this->set('images', $images);
		//$this->set('competitions', $competitions);
		//$this->set('rankings', $rankings);
		
		$user = $this->Session->read("product.sale.user") ;
		$group=  $user["GROUP_CODE"] ;

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
		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$security  = ClassRegistry::init("Security") ;
		
		$productDev = $SqlUtils->getObject("sql_pdev_findByAsin",array('ASIN'=>$asin)) ;
		
		$pdStatus = 10 ;
		$devStatus = 0 ;
		if(!empty( $productDev  )){
			$pdStatus = $productDev['FLOW_STATUS'] ;
			$devStatus = $productDev['DEV_STATUS'] ;
		}
		
		$loginId = $user['LOGIN_ID'] ;
		$PD_FLAG 					= $security->hasPermission($loginId , 'PD_FLAG') ;
		$PD_ANAYS 				= $security->hasPermission($loginId , 'PD_ANAYS') ;
		$PD_CPJLSP 				= $security->hasPermission($loginId , 'PD_CPJLSP') ;
		$PD_ZJSP						= $security->hasPermission($loginId , 'PD_ZJSP') ;
		$PD_HPLR					= $security->hasPermission($loginId , 'PD_HPLR') ;
		$PD_MADE_LISTING 	= $security->hasPermission($loginId , 'PD_MADE_LISTING') ;
		$PD_LISTING_SP 			= $security->hasPermission($loginId , 'PD_LISTING_SP') ;
		$PD_REEDIT_GM 			= $security->hasPermission($loginId , 'PD_REEDIT_GM') ;
		$PD_REEDIT_ZY 			= $security->hasPermission($loginId , 'PD_REEDIT_ZY') ;
		$PD_REEDIT_YX			= $security->hasPermission($loginId , 'PD_REEDIT_YX') ;
		$PD_REEDIT_BASE 		= $security->hasPermission($loginId , 'PD_REEDIT_BASE') ;
		
		$Config  = ClassRegistry::init("Config") ;
		$websites = $Config->getAmazonConfig("PRODUCT_DEV_WEBSITE") ;
	?>
  
   <style>
   		*{
   			font:12px "微软雅黑";
   		}
 </style>
 
 <script>
 	var filterId = '<?php echo $filterId;?>' ;
 	var asin = '<?php echo $asin;?>' ;
 	var type = '<?php echo $type;?>' ;
 	var status =  '<?php echo $status;?>' ;
 	var username = '<?php echo $username;?>' ;
 	var pdStatus = '<?php echo $pdStatus;?>' ;

 	function AuditAction(status , statusLabel,fixParams){
 		if( !$.validation.validate('#personForm').errorInfo ) {
			if(window.confirm("确认【"+statusLabel+"】吗？")){
				var json = $("#personForm").toJson() ;
				json = $.extend({},json,fixParams) ;
				json.ASIN = asin ;
				json.FLOW_STATUS = status;

				var memo = "("+statusLabel+")"+ ($(".memo").val()||"") ;
				json.trackMemo = memo ;
				
				$.dataservice("model:ProductDev.doFlow",json,function(result){
					window.location.reload() ;
				});
			}
		}
 	 }

 	var flowData = [] ;
 	flowData.push( {status:10,label:"标识状态",memo:true ,
		actions:[ 
			 		<?php if( $PD_FLAG ){ ?>
				 {label:"保存",action:function(){ AuditAction('10',"保存") } },
		         {label:"自有",action:function(){ AuditAction('20',"设置自有状态",{'DEV_STATUS':1}) } },
		         {label:"跟卖",action:function(){ AuditAction('20',"设置跟买状态",{'DEV_STATUS':2}) } },
		         {label:"废弃",action:function(){ AuditAction('15',"废弃结束",{'DEV_STATUS':3}) } }
		         <?php }?>
	     ]}
     ) ;
	flowData.push( {status:20,label:"产品分析",memo:true ,
		actions:[ 
				<?php if( $PD_ANAYS ){ ?>
				{label:"保存",action:function(){ AuditAction('20',"保存") } },
				{label:"结束分析，提交审批",action:function(){ AuditAction('30',"结束分析，提交审批") } }
				 <?php }?>
	     ]}
     ) ;
	flowData.push( {status:30,label:"产品经理审批",memo:true ,
		actions:[ 
				<?php if( $PD_CPJLSP ){ ?>
	          {label:"保存",action:function(){ AuditAction('30',"保存") } },
			  {label:"审批不通过，撤回分析",action:function(){ AuditAction('20',"审批不通过，撤回分析") } },
			  {label:"审批通过",action:function(){ AuditAction('40',"审批通过，提交总监审批") } }
			  <?php }?>
	     ]}
     ) ;
	flowData.push( {status:40,label:"总监审批",memo:true ,
		actions:[ 
				<?php if( $PD_ZJSP ){ ?>
		         {label:"保存",action:function(){ AuditAction('40',"保存") } },
				 {label:"审批不通过，撤回分析",action:function(){ AuditAction('20',"审批不通过，撤回分析") } },
				 {label:"审批通过",action:function(){ AuditAction('50',"审批通过，准备录入货品") } }
				 <?php }?>
	     ]}
     ) ;
	flowData.push( {status:50,label:"录入货品",memo:true ,
		actions:[ 
					<?php if( $PD_HPLR ){ ?>
		         {label:"保存",action:function(){ AuditAction('50',"保存") } },
		         {label:"确认货品录入",action:function(){ AuditAction('60',"确认货品录入完成") } ,validate:function(){
		        	 var val = $("#REAL_PRODUCT_ID").val() ;
		        	 if(!val){
							alert("必须关联货品！") ;
							return false ;
			        	} 
			        	return true ;
			      }}
		         <?php }?>
	     ]}
     ) ;
	flowData.push( {status:60,label:"制作Listing",memo:true ,
		actions:[ 
					<?php if( $PD_MADE_LISTING ){ ?>
	          {label:"保存",action:function(){ AuditAction('60',"保存") } },
		      {label:"确认Listing制作完成",action:function(){ AuditAction('70',"确认Listing制作完成") },validate:function(){
		    	  var val = $("#LISTING_SKU").val() ;
		        	 if(!val){
							alert("必须关联Listing SKU！") ;
							return false ;
			        	} 
			        	return true ;
			      }}
	          <?php }?>
	     ]}
     ) ;
	flowData.push( {status:70,label:"Listing审批",memo:true ,
		actions:[ 
					<?php if( $PD_LISTING_SP ){ ?>
	           {label:"保存",action:function(){ AuditAction('70',"保存") } },
		      {label:"审批通过",action:function(){ AuditAction('80',"审批通过，结束") } }
	           <?php }?>
	     ]}
     ) ;
	flowData.push( {status:80,label:"结束"}) ;

	$(function(){
		var flow = new Flow() ;
		flow.init(".flow-bar center",flowData) ;
		flow.draw(<?php echo $pdStatus;?>) ;
	}) ;
 </script>

</head>
<body style="overflow-y:auto;padding:2px;">
	<div  class="flow-bar">
		<center>
			<table class="flow-table"></table>
			<div class="flow-action"></div>
		</center>
	</div>

	<button class="base-gather btn" style="position:absolute;left:2px;top:15px;">信息采集</button>
	
	<div id="details_tab" style="border:0px;">
	</div>	
	
	<div class="hide"  id="track-tab">
		<div class="grid-track" style="width:920px;"></div>
	</div>
	
	<div class="hide"  id="dev-tab">
		<form id="personForm" action="#" data-widget="validator" class="form-horizontal" >
		<div style="padding:4px 10px;margin-top:5px;margin-bottom:5px;" class="alert">
		<?php 
			echo '<b>相关网址：</b>' ;
			foreach ( explode(",", $websites) as $website ){
				$website = explode("||", $website) ;
				$name = $website[0] ;
				$url = $website[1] ;
				
				echo "<a href='$url' target='_blank'>$name</a>&nbsp;&nbsp;&nbsp;" ;
			}
		?>
		</div>
		<hr style="margin:0px;clear:both;padding-top:3px;"/>
		<table class="form-table " >
				<caption>
				基本信息
				<?php if( $PD_REEDIT_BASE){ 
					echo "<img src='/$fileContextPath/app/webroot/img/edit.png' class='reedit'>" ;
				}?>
				</caption>
				<tbody>	
					<tr>
						<th style="width:20%;">ASIN排名</th>
						<th style="width:20%;">估计流量</th>
						<th style="width:20%;">成本估算</th>
						<th style="width:20%;">利润估算</th>
						<th style="width:20%;">热销时段</th>
					</tr>
					<tr>
						<td style="width:20%;"><textarea  id="RANK"  class="input 10-input"  style="width:90%;height:60px;"><?php echo $productDev['RANK'];?></textarea></td>
						<td style="width:20%;"><textarea  id="ESTIMATE_TRAFFIC"  class="input 10-input"     style="width:90%;height:60px;"><?php echo $productDev['ESTIMATE_TRAFFIC'];?></textarea></td>
						<td style="width:20%;"><textarea  id="ESTIMATE_COST"   class="input 10-input"   style="width:90%;height:60px;"><?php echo $productDev['ESTIMATE_COST'];?></textarea></td>
						<td style="width:20%;"><textarea  id="ESTIMATE_PROFIT"  class="input 10-input"    style="width:90%;height:60px;"><?php echo $productDev['ESTIMATE_PROFIT'];?></textarea></td>
						<td style="width:20%;"><textarea  id="HOT_SELL_PERIOD" class="input 10-input"     style="width:90%;height:60px;"><?php echo $productDev['HOT_SELL_PERIOD'];?></textarea></td>
					</tr>
					<?php  if( $pdStatus >=50 ){  ?>
					<tr>
						<th style="width:20%;">
							<?php  if( $pdStatus ==50 ){  ?>
							<button class="btn btn-primary select-real-product">选择货品</button>
							<?php 	} ?>
							<input type="hidden" id="REAL_PRODUCT_ID" value="<?php echo $productDev['REAL_PRODUCT_ID'];?>"/>
							关联货品
						</th>
						<td colspan="4">
							<?php 
							if( !empty($productDev['REAL_PRODUCT_ID']) ){
								$sp = $SqlUtils->getObject("sql_saleproduct_getById",array("realProductId"=>$productDev['REAL_PRODUCT_ID'])) ;
								echo $sp['NAME'] ;
								echo "(".$sp['REAL_SKU'].")" ;
								echo "<img style='width:30px;height:30px;' src='/$fileContextPath".$sp['IMAGE_URL']."'>" ;
							}	
							?>
						</td>
					</tr>		
					<?php 	} ?>
					<?php  if( $pdStatus >=60 ){  ?>
					<tr>
						<th style="width:20%;">
							关联Listing SKU（逗号分隔）
						</th>
						<td colspan="4">
							<input type="text"  id="LISTING_SKU" 
								 class="input 60-input" 
								style="width:80%;" placeHolder="输入关联ListingSKU" value="<?php echo $productDev['LISTING_SKU']?>" />
						</td>
					</tr>		
					<?php 	} ?>
				</tbody>
			</table>
			
			<?php  if( $devStatus == 1 ){ //自有产品 ?>
			<table class="form-table " >
				<caption>
					自有产品分析
					<?php if( $PD_REEDIT_ZY){ 
					echo "<img src='/$fileContextPath/app/webroot/img/edit.png' class='reedit'>" ;
				}?>
				</caption>
				<tbody>	
					<tr>
						<th style="width:120px;"></th>
						<th>关键字</th>
						<th style="width:15%;">渠道有效竞争数</th>
						<th style="width:15%;">搜索量VOLUMN</th>
						<th style="width:15%;">竞争COM</th>
						<th style="width:15%;">竞价CPC</th>
					</tr>
					<tr>
						<th>核心关键字：</th>
						<td><input type="text" id="CORE_KEY"  data-validator="required"  class="input 20-input"   style="width:80%;" value="<?php echo $productDev['CORE_KEY']?>"/></td>
						<td>	<input type="text" id="CK_VALID_COMP"   data-validator="required"    class="input 20-input"  style="width:80%;"  value="<?php echo $productDev['CK_VALID_COMP']?>"/></td>
						<td>	<input type="text" id="CK_SR_SEARCH"  data-validator="required"     class="input 20-input"  style="width:80%;"  value="<?php echo $productDev['CK_SR_SEARCH']?>" /></td>
						<td>	<input type="text" id="CK_SR_COM"    data-validator="required"   class="input 20-input"  style="width:80%;"  value="<?php echo $productDev['CK_SR_COM']?>"/></td>
						<td>	<input type="text" id="CK_SR_CPC"  data-validator="required"     class="input 20-input"  style="width:80%;"  value="<?php echo $productDev['CK_SR_CPC']?>"/></td>
					</tr>
					<tr>
						<th>Amazon关键字1：</th>
						<td><input type="text" id="OP_KEY1"   data-validator="required"    class="input 20-input"  style="width:80%;"  value="<?php echo $productDev['OP_KEY1']?>"/></td>
						<td>	<input type="text" id="OK_VALID_COMP1"  data-validator="required"     class="input 20-input"  style="width:80%;"  value="<?php echo $productDev['OK_VALID_COMP1']?>"/></td>
						<td>	<input type="text" id="OK_SR_SEARCH1"   data-validator="required"    class="input 20-input"  style="width:80%;"  value="<?php echo $productDev['OK_SR_SEARCH1']?>"/></td>
						<td>	<input type="text" id="OK_SR_COM1"   data-validator="required"    class="input 20-input"  style="width:80%;"  value="<?php echo $productDev['OK_SR_COM1']?>"/></td>
						<td>	<input type="text" id="OK_SR_CPC1"  data-validator="required"     class="input 20-input"  style="width:80%;"  value="<?php echo $productDev['OK_SR_CPC1']?>"/></td>
					</tr>
					<tr>
						<th>Amazon关键字2：</th>
						<td><input type="text" id="OP_KEY2"   data-validator="required"    class="input 20-input"  style="width:80%;"  value="<?php echo $productDev['OP_KEY2']?>"/></td>
						<td>	<input type="text" id="OK_VALID_COMP2"   data-validator="required"    class="input 20-input"  style="width:80%;"  value="<?php echo $productDev['OK_VALID_COMP2']?>"/></td>
						<td>	<input type="text" id="OK_SR_SEARCH2"  data-validator="required"    class="input 20-input"   style="width:80%;" value="<?php echo $productDev['OK_SR_SEARCH2']?>" /></td>
						<td>	<input type="text" id="OK_SR_COM2"   data-validator="required"    class="input 20-input"  style="width:80%;"  value="<?php echo $productDev['OK_SR_COM2']?>"/></td>
						<td>	<input type="text" id="OK_SR_CPC2"  data-validator="required"     class="input 20-input"  style="width:80%;" value="<?php echo $productDev['OK_SR_CPC2']?>" /></td>
					</tr>
					<tr>
						<th>Amazon关键字3：</th>
						<td><input type="text" id="OP_KEY3"   data-validator="required"    class="input 20-input"  style="width:80%;" value="<?php echo $productDev['OP_KEY3']?>" /></td>
						<td>	<input type="text" id="OK_VALID_COMP3"   data-validator="required"    class="input 20-input"  style="width:80%;"  value="<?php echo $productDev['OK_VALID_COMP3']?>"/></td>
						<td>	<input type="text" id="OK_SR_SEARCH3"   data-validator="required"    class="input 20-input"  style="width:80%;"  value="<?php echo $productDev['OK_SR_SEARCH3']?>"/></td>
						<td>	<input type="text" id="OK_SR_COM3"  data-validator="required"    class="input 20-input"   style="width:80%;" value="<?php echo $productDev['OK_SR_COM3']?>" /></td>
						<td>	<input type="text" id="OK_SR_CPC3" data-validator="required"     class="input 20-input"   style="width:80%;"  value="<?php echo $productDev['OK_SR_CPC3']?>"/></td>
					</tr>
					<tr>
						<th>Amazon关键字4：</th>
						<td><input type="text" id="OP_KEY4"  data-validator="required"     class="input 20-input"  style="width:80%;"  value="<?php echo $productDev['OP_KEY4']?>"/></td>
						<td>	<input type="text" id="OK_VALID_COMP4"  data-validator="required"     class="input 20-input"  style="width:80%;" value="<?php echo $productDev['OK_VALID_COMP4']?>" /></td>
						<td>	<input type="text" id="OK_SR_SEARCH4"  data-validator="required"     class="input 20-input"  style="width:80%;"  value="<?php echo $productDev['OK_SR_SEARCH4']?>"/></td>
						<td>	<input type="text" id="OK_SR_COM4"  data-validator="required"     class="input 20-input"  style="width:80%;"  value="<?php echo $productDev['OK_SR_COM4']?>"/></td>
						<td>	<input type="text" id="OK_SR_CPC4" data-validator="required"     class="input 20-input"   style="width:80%;" value="<?php echo $productDev['OK_SR_CPC4']?>" /></td>
					</tr>
					<tr>
						<th>eBay关键字：</th><td colspan=5>
							<input  type="text" id="EBAY_KEY"  class="input 20-input"   style="width:90%;"  data-validator="required" 
							value="<?php echo $productDev['EBAY_KEY'];?>"/></td>
					</tr>
					<tr>
						<th>eBay销售数量：</th>
						<td  colspan=5><textarea id="EBAY_SALE_MEMO"   class="input 20-input"    data-validator="required" 
							style="width:90%;height:50px;"><?php echo $productDev['EBAY_SALE_MEMO'];?></textarea></td>
					</tr>
				</tbody>
			</table>
			<?php  }?>
			
			<?php  if( $devStatus == 2 ){ //跟卖产品 ?>
			<table class="form-table " >
				<caption>
				跟卖产品分析
				<?php if( $PD_REEDIT_GM){ 
					echo "<img src='/$fileContextPath/app/webroot/img/edit.png' class='reedit'>" ;
				}?>
				</caption>
				<tbody>
					<tr>
						<th>跟卖产品风险：</th>
						<td><textarea id="FOLLOW_RISK_PRODUCT"  data-validator="required"   class="input 20-input"  
							style="width:90%;height:40px;"><?php echo $productDev['FOLLOW_RISK_PRODUCT']?></textarea></td>
					</tr>
					<tr>
						<th>跟卖品牌风险：</th>
						<td><textarea id="FOLLOW_RISK_BRAND"   data-validator="required"    class="input 20-input"  
							style="width:90%;height:40px;"><?php echo  $productDev['FOLLOW_RISK_BRAND']?></textarea></td>
					</tr>
					<tr>
						<th>跟卖供应商风险：</th>
						<td><textarea id="FOLLOW_RISK_SUPPLIER"  data-validator="required"     class="input 20-input"  
							style="width:90%;height:40px;"><?php echo  $productDev['FOLLOW_RISK_SUPPLIER']?></textarea></td>
					</tr>
				</tbody>
			</table>
			<?php  }?>
			
			<?php  if( $devStatus == 1 || $devStatus == 2 ){ //跟卖产品 ?>
			<table class="form-table " >
				<caption>
				营销计划与策略
				<?php if( $PD_REEDIT_YX){ 
					echo "<img src='/$fileContextPath/app/webroot/img/edit.png' class='reedit'>" ;
				}?>
				</caption>
				<tbody>
					<tr>
						<th style="width:33%;">竞价排名策略</th>
						<th style="width:33%;">物流策略</th>
						<th style="width:34%;">推广策略</th>
					</tr>
					<tr>
						<td>
							<select id="PPC_STRATEGY"  data-validator="required"   style="width:97%;" class="input 20-input" >
								<option value="">--选择策略--</option>
							<?php 
								$strategys = $SqlUtils->exeSql("sql_rule_item_config",array('type'=>'devStrategy')) ;
								foreach( $strategys as $s){
									$s = $SqlUtils->formatObject($s) ;
									$selected = '' ;
									if( $s['ID'] == $productDev['PPC_STRATEGY'] ){
										$selected = "selected" ;
									}
									echo "<option $selected value='".$s['ID']."'>".$s['LABEL']."</option>" ;
								}
							?>
							</select>
							<textarea id="PPC_STRATEGY_MEMO" class="input 20-input"  style="margin-top:2px;width:95%;height:50px;"><?php echo  $productDev['PPC_STRATEGY_MEMO']?></textarea>
						</td>
						<td>
							<select id="LOGI_STRATEGY" style="width:97%;" data-validator="required"    class="input 20-input" >
								<option value="">--选择--</option>
								<option value="FBM"  <?php echo $productDev['LOGI_STRATEGY']=='FBM'?"selected":"" ?>>FBM</option>
								<option value="FBA"  <?php echo $productDev['LOGI_STRATEGY']=='FBA'?"selected":"" ?>>FBA</option>
								<option value="FBA_FBM"  <?php echo $productDev['LOGI_STRATEGY']=='FBA_FBM'?"selected":"" ?>>FBM和FMA</option>
							</select>
							<textarea id="LOGI_STRATEGY_MEMO"  class="input 20-input" 
								style="margin-top:2px;width:95%;height:50px;"><?php echo  $productDev['LOGI_STRATEGY_MEMO']?></textarea>
						</td>
						<td>
							<textarea id="SPREAD_STRATEGY"   class="input 20-input" 
								style="width:95%;height:77px;"><?php echo  $productDev['SPREAD_STRATEGY']?></textarea>
						</td>
					</tr>
				</tbody>
			</table>
			
			<?php  }?>
			</form>
	</div>
	
	<div>
		<div id="baseinfo-tab" class="ui-tabs-panel" style="height: 100px; display: block; ">
			<table class="table table-bordered">
				<tr>
					<th style="width:100px;">标题：</th>
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
								echo "<tr><td>".$rank['RANKING']."</td><td>".$rank['TYPE']."</td></tr>" ;
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