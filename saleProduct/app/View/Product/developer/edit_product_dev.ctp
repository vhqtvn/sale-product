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
		echo $this->Html->css('../js/dialog/jquery.dialog');
		echo $this->Html->css('../js/listselectdialog/jquery.listselectdialog');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery-ui');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('tab/jquery.ui.tabs');
		echo $this->Html->script('dialog/jquery.dialog');
		echo $this->Html->script('validator/jquery.validation');
		echo $this->Html->script('modules/product/developer/edit_product_dev');
		echo $this->Html->script('listselectdialog/jquery.listselectdialog');
		echo $this->Html->script('grid/jquery.llygrid');
		

		echo $this->Html->css('../js/modules/tag/tagutil');
		echo $this->Html->script('modules/tag/tagutil');
		
		//$this->set('details', $details);
		//$this->set('images', $images);
		//$this->set('competitions', $competitions);
		//$this->set('rankings', $rankings);
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$security  = ClassRegistry::init("Security") ;
		$PurchaseService  = ClassRegistry::init("PurchaseService") ;
		
		
		$user = $this->Session->read("product.sale.user") ;
		$group=  $user["GROUP_CODE"] ;
		
		$devId = $params['arg1'] ;
		
		$productDev = $SqlUtils->getObject("sql_pdev_new_getByDevId",array("devId"=>$devId)) ;
		$product =$SqlUtils->getObject("select * from sc_product where asin= '{@#asin#}'",array("asin"=>$productDev['ASIN'])) ;
		$asin = $productDev['ASIN'];
		$username = $user["NAME"] ;
		$pdStatus = 10 ;
		$devStatus = 0 ;
		
		if(!empty( $productDev  )){
			$pdStatus = $productDev['FLOW_STATUS'] ;
			$devStatus = $productDev['DEV_STATUS'] ;
		}
		
		//debug($product) ;
		
		//询价负责人
		$charger = $productDev['INQUIRY_CHARGER'] ;
		$chargerName = $productDev['INQUIRY_CHARGER_NAME'] ;
		$isGlobal = "" ;
		if( empty($charger)  ){
			//获取默认询价负责人
			$cg = $PurchaseService->getDefaultInquiryCharger($asin) ;
			$charger = $cg['charger'] ;
			$chargerName= $cg['chargerName'] ;
			$isGlobal = $cg['isGlobal'] ;
		}

		$loginId 						= $user['LOGIN_ID'] ;
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
		$PD_INQUIRY				= $security->hasPermission($loginId , 'PD_INQUIRY') || (  $charger == $loginId ) ;//询价权限
		$PD_COST					=  $security->hasPermission($loginId , 'PD_COST') ;//成本权限
		$PD_FORCE					=  $security->hasPermission($loginId , 'PD_FORCE') ;//询价权限
		$PD_START_FQ				=  $security->hasPermission($loginId , 'PD_START_FQ') ;//启用废弃产品
		$PD_SALE_RPICE			=  $security->hasPermission($loginId , 'PD_SALE_RPICE') ;//销售限价
		$PD_SUPPLIER_MAX_PRICE	=  $security->hasPermission($loginId , 'PD_SUPPLIER_MAX_PRICE') ;//供应限价
		//样品检测权限
		$PD_SAMPLE_EVALUATE	=  $security->hasPermission($loginId , 'PD_SAMPLE_EVALUATE') ;
		$PD_SAMPLE_EVALUATE_AUDIT	=  $security->hasPermission($loginId , 'PD_SAMPLE_EVALUATE_AUDIT') ;
		$PD_SAMPLE_ORDER_ARREIVE		=  $security->hasPermission($loginId , 'PD_ORDER_ARREIVE') ;
		$PD_DOC		=  $security->hasPermission($loginId , 'PD_DOC') ;
		
		$PD_TRANSFER			=  $security->hasPermission($loginId , 'PD_TRANSFER') ;
		
		$PD_SXCG			=  $security->hasPermission($loginId , 'PD_SXCG') ;//试销采购
		$PD_KCDW			=  $security->hasPermission($loginId , 'PD_KCDW') ;//库存到位
		$PD_YXZK			=  $security->hasPermission($loginId , 'PD_YXZK') ;//营销展开
		$PD_KFZJ				=  $security->hasPermission($loginId , 'PD_KFZJ') ;//开发总结
	
		$Config  = ClassRegistry::init("Config") ;
		$websites = $Config->getAmazonConfig("PRODUCT_DEV_WEBSITE") ;
		
	
		$devStatusFlow = $productDev['DEV_STATUS_FLOW'] ;
		$devStatusFlow = json_decode($devStatusFlow) ;
		
		$isPurchaseSample = false ;
		if(!empty($devStatusFlow)){
			$devStatusFlow = get_object_vars($devStatusFlow) ;
			if( isset( $devStatusFlow['isPurchaseSample'] ) && $devStatusFlow['isPurchaseSample'] == 1 ){
				$isPurchaseSample = true ;
			}
		}
		
		ini_set('date.timezone','Asia/Shanghai');
		$now = date('Y-m-d H:i:s');
?>
  
   <style>
html{-webkit-text-size-adjust: none;}
   		*{
   			font:12px "微软雅黑";
   		}
   		
   		.flow-node{
			font-size:11px;
   			/*width:60px;
   			word-wrap: break-word;*/
   			float:left;
   		}
   		
   		.flow-split{
   			float:left;
   			margin-top:-8px;
   		}
   		
   		.flow-bar{
			position:fixed;
   			top:30px;
   			padding-top:10px;
   			left:0px;
   			right:0px;
   			height:70px;
   			z-index:1000;
   			background:#EEE;
   			margin:0px;
   		}
   		
   		body{
			padding-top:85px!important;
   		}
   		
   		.flag_container li{
			float:left;
   		}
   		
   		.flag_container{
			display:block;
   			height:20px;
   		}
   		
   		.flag_container .alert{
			padding:2px 10px;
   			margin:0px 5px;
   		}
 </style>
 
 <script>
	$(function(){
		DynTag.listByEntity("productDevTag",'<?php echo  $asin;?>') ;
	}) ;
</script>
 
 <script>
    var taskId = '' ;
 	var asin 			= '<?php echo $asin;?>' ;
 	var devId  		= '<?php echo $productDev['DEV_ID'] ;?>' ;
 	var username 	= '<?php echo $username;?>' ;
 	var pdStatus 	= '<?php echo $pdStatus;?>' ;
 	var platformId 	= '<?php echo $product['PLATFORM_ID'] ;?>' ;
 	var devStatus 	= '<?php echo $productDev['DEV_STATUS'] ;?>' ;
 	var isPurchaseSample = <?php echo $isPurchaseSample?"true":"false"?> ;

 	jQuery.dialogReturnValue(false) ;

 	function  ForceAuditAction(status , statusLabel,fixParams, isFlow ){
			if(window.confirm("确认【"+statusLabel+"】吗？")){
				var json = $("#personForm").toJson() ;
				json = $.extend({},json,fixParams) ;

				json.ASIN = asin ;
				json.FLOW_STATUS = status;
				json.DEV_ID =devId ;
				//json.REAL_ID = $("#REAL_PRODUCT_ID").val() ;

				if( json.DEV_STATUS == 3 ){
					json.FLOW_STATUS = 15 ;
				}

				if(isFlow){
					json.isFlow = 1 ;
				}else{
					json.isFlow = 0 ;
				}
				
				var devStatusFlow = {} ;
				$(".dev_status_flow").each(function(){
					var key = $(this).attr("key") ;
					if( $(this).attr("checked") ){
						devStatusFlow[key] = 1 ;
					}
				}) ;
				if($(".dev_status_flow").length>0  ){
					json.DEV_STATUS_FLOW = $.json.encode(devStatusFlow)  ;
				}
				
				var memo = "("+statusLabel+")"+ ($(".memo").val()||"") ;
				json.trackMemo = memo ;
				
				$.dataservice("model:NewProductDev.doFlow",json,function(result){
						jQuery.dialogReturnValue(true) ;
						window.location.reload() ;
				});
			}
 	 }

 	//$.dataservice("model:PurchaseService.createPlanForProductDev",{},function(result){
		
	//});

 	function AuditAction(status , statusLabel,fixParams){
 		if( !$.validation.validate('#personForm').errorInfo ) {
 			ForceAuditAction(status , statusLabel,fixParams,true) ;
		}
 	 }

 	var flowData = [] ;
 	flowData.push( {status:10,label:"产品分析",memo:true ,
		actions:[ 
			 		<?php if( $PD_ANAYS ){ ?>
				 {label:"保存",action:function(){ ForceAuditAction('10',"保存") } },
				 {label:"询价",action:function(){ AuditAction('20',"询价") } },
				 {label:"成本利润",action:function(){ AuditAction('25',"成本利润分析") } },
				 {label:"提交审批",action:function(){ AuditAction('30',"提交审批") } }
		         <?php }?>
	     ]}
     ) ;
 	<?php if( $pdStatus ==15 ){ ?>
		 	flowData.push( {status:15,label:"废弃",memo:true ,
				actions:[ 
						<?php if( $PD_START_FQ ){ ?>
						{label:"启用",action:function(){ AuditAction('10',"启用废弃产品",{DEV_STATUS:'0'}) } }
						 <?php }?>
			     ]}
		     ) ;
 	<?php }?>
	<?php if( $pdStatus !=15 ){ ?>
			flowData.push( {status:20,label:"产品询价",memo:true ,
				actions:[ 
						<?php if( $PD_INQUIRY ){ ?>
						{label:"保存",action:function(){ ForceAuditAction('20',"保存") } },
						 {label:"开发分析",action:function(){ AuditAction('10',"开发分析") } },
						 {label:"成本利润",action:function(){ AuditAction('25',"成本利润分析") } },
						 {label:"提交审批",action:function(){ AuditAction('30',"提交审批") } }
						 <?php }?>
			     ]}
		     ) ;
			flowData.push( {status:25,label:"成本利润",memo:true ,
				actions:[ 
						<?php if( $PD_COST ){ ?>
						{label:"保存",action:function(){ ForceAuditAction('25',"保存") } },
						 {label:"开发分析",action:function(){ AuditAction('10',"开发分析") } },
						 {label:"询价",action:function(){ AuditAction('20',"询价") } },
						 {label:"提交审批",action:function(){ AuditAction('30',"提交审批") } }
						 <?php }?>
			     ]}
		     ) ;
		
			<?php 
				$aduitPassStatus = 50 ;
				$aduitPassText = "审批通过，录入货品" ;
				if( $productDev['DEV_STATUS'] == 1 && $isPurchaseSample  ){
					$aduitPassStatus = 41 ;
					$aduitPassText = "审批通过，进入样品下单环节" ;
				}else  if( $productDev['DEV_STATUS'] == 1    ){
					$aduitPassStatus = 46 ;
					$aduitPassText = "审批通过，进入资料准备环节" ;
				}
		    ?>
		    
			flowData.push( {status:30,label:"产品经理审批",memo:true ,
				actions:[ 
						<?php if( $PD_CPJLSP ){ ?>
			          {label:"保存",action:function(){ ForceAuditAction('30',"保存") } },
					  {label:"撤回分析",action:function(){ AuditAction('10',"审批不通过，撤回分析") } },
					  {label:"撤回询价",action:function(){ AuditAction('20',"审批不通过，撤回询价") } },
					  {label:"提交总监审批",action:function(){ AuditAction('40',"提交总监审批 " ) } },
					  {label:"审批通过",action:function(){ AuditAction('<?php echo $aduitPassStatus;?>',"<?php echo $aduitPassText;?>" ) } }
					  <?php }?>
			     ]}
		     ) ;

			flowData.push( {status:40,label:"总监审批",memo:true ,
				actions:[ 
						<?php if( $PD_ZJSP ){ ?>
				         {label:"保存",action:function(){ ForceAuditAction('40',"保存") } },
				         {label:"撤回分析",action:function(){ AuditAction('10',"审批不通过，撤回分析") } },
						 {label:"撤回询价",action:function(){ AuditAction('20',"审批不通过，撤回询价") } },
						 {label:"审批通过",action:function(){ AuditAction('<?php echo $aduitPassStatus;?>',"<?php echo $aduitPassText;?>") } }
						<?php }?>
			     ]}
		     ) ;
		
			<?php if( $productDev['DEV_STATUS'] == 1 && $isPurchaseSample ){//自有开发流程 ?>
				flowData.push( {status:41,label:"样品下单",memo:true ,
					actions:[ 
						//{label:"确认样品下单",action:function(){ AuditAction('42',"下单完成，等待样品到达",{"SAMPLE_ORDER_TIME":'<?php echo $now;?>'}) } }	
				     ]}
			    ) ;
				flowData.push( {status:42,label:"样品到达",memo:true ,
					actions:[ 
						//{label:"确认样品到达",action:function(){ AuditAction('43',"样品到达，产品资料准备",{"SAMPLE_ARRIVE_TIME":'<?php echo $now;?>'}) } }	
				     ]}
			     ) ;
				flowData.push( {status:43,label:"产品资料准备",memo:true ,
					actions:[ 
						<?php if($PD_DOC){?>
						{label:"保存",action:function(){ ForceAuditAction('43',"保存") } }	,
						{label:"完成产品资料准备",action:function(){ ForceAuditAction('44',"产品资料准备完成，进入样品检测") } }
						<?php }?>
				     ]}
			     ) ;
				flowData.push( {status:44,label:"样品检测",memo:true ,
					actions:[
								<?php if($PD_SAMPLE_EVALUATE){?>
								{label:"保存",action:function(){ ForceAuditAction('44',"保存") } }	,
								{label:"产品资料准备",action:function(){ ForceAuditAction('45',"样品检测完成，提交审批") } }
								<?php }?>
				     ]}
			     ) ;
				flowData.push( {status:45,label:"检测审批",memo:true ,
					actions:[ 
						<?php if( $PD_SAMPLE_EVALUATE_AUDIT ){ ?>
						{label:"保存",action:function(){ ForceAuditAction('45',"保存") } },
						{label:"重新检测",action:function(){ AuditAction('44',"审批不通过，重新检测") } },
						{label:"审批不通过",action:function(){ AuditAction('80',"审批不通过，终止开发") } },
						{label:"审批通过",action:function(){ AuditAction('46',"审批通过，上传资料准备") } }
						<?php }?>
				     ]}
			     ) ;
			<?php }?>
			<?php if( $productDev['DEV_STATUS'] == 1  ){//自有开发流程 ?>
				flowData.push( {status:46,label:"上传资料准备",memo:true ,
					actions:[ 
						{label:"保存",action:function(){ ForceAuditAction('44',"保存") } }	,
						{label:"完成产品上传资料准备",action:function(){ ForceAuditAction('45',"产品上传资料准备完成，进入录入货品") } }
				     ]}
			     ) ;
			<?php }?>
			flowData.push( {status:50,label:"录入货品",memo:true ,
				actions:[ 
							<?php if( $PD_HPLR ){ ?>
				         {label:"保存",action:function(){ ForceAuditAction('50',"保存") } },
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
			          {label:"保存",action:function(){ ForceAuditAction('60',"保存") } },
				      {label:"确认Listing制作完成",action:function(){ AuditAction('70',"确认Listing制作完成") },validate:function(){
				    	     var val = $("#LISTING_SKU").val() ;
				    	     var accountId = $("#ACCOUNT_ID").val() ;
				        	 if(!val || !accountId){
									alert("必须关联Listing SKU并且选择账户！") ;
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
			           {label:"保存",action:function(){ ForceAuditAction('70',"保存") } },
				       {label:"审批通过",action:function(){ AuditAction('72',"审批通过，进入试销采购，采购单将自动生成！") } }
			           <?php }?>
			     ]}
		     ) ;
		
			flowData.push( {status:72,label:"试销采购",memo:true ,
				actions:[ 
							<?php if( $PD_SXCG ){ ?>
				         {label:"保存",action:function(){ ForceAuditAction('72',"保存") } }
				         <?php if( $productDev['DEV_STATUS'] == 1  ){//自有开发流程 ?>
				        	 ,   {label:"下一步",action:function(){ AuditAction('74',"试销采购完成，进入下一步") } }
					      <?php }else{ ?>
					      ,   {label:"下一步",action:function(){ AuditAction('80',"试销采购完成，结束采购") } }
					      <?php   }?>
					//  ,   {label:"下一步",action:function(){ AuditAction('74',"试销采购完成，进入下一步") } }
				         <?php }?>
			     ]}
		     ) ;
			<?php if( $productDev['DEV_STATUS'] == 1  ){//自有开发流程 ?>
				flowData.push( {status:76,label:"营销展开",memo:true ,
					actions:[ 
						<?php if( $PD_YXZK ){ ?>
						{label:"保存",action:function(){ ForceAuditAction('76',"保存") } },
						{label:"下一步",action:function(){ AuditAction('78',"营销展开，进入下一步") } }
						<?php }?>
				     ]}
			     ) ;
			<?php }?>	
			<?php /*
			flowData.push( {status:74,label:"库存到达",memo:true ,
				actions:[ 
		                <?php if( $PD_KCDW ){ ?>
						{label:"保存",action:function(){ ForceAuditAction('74',"保存") } }
						,{label:"下一步",action:function(){ AuditAction('80',"库存到达，进入下一步") } }
						<?php }?>
			     ]}
		     ) ;
			
			flowData.push( {status:76,label:"营销展开",memo:true ,
				actions:[ 
		<?php if( $PD_YXZK ){ ?>
					{label:"保存",action:function(){ ForceAuditAction('76',"保存") } },
					{label:"下一步",action:function(){ AuditAction('78',"营销展开，进入下一步") } }
					<?php }?>
			     ]}
		     ) ;
		  
			flowData.push( {status:78,label:"开发总结",memo:true ,
				actions:[ 
		<?php if( $PD_KFZJ ){ ?>
						{label:"保存",action:function(){ ForceAuditAction('78',"保存") } },
						{label:"结束",action:function(){ AuditAction('80',"开发总结填写完成，结束开发流程") } }
						<?php }?>
			     ]}
		     ) ;
			*/   ?>
			flowData.push( {status:80,label:"结束"}) ;
			<?php } ?>
	$(function(){
		var flow = new Flow() ;
		flow.init(".flow-bar center",flowData) ;
		flow.drawDiv(<?php echo $pdStatus;?>) ;
	}) ;
 </script>

</head>
<body style="overflow-y:auto;padding:2px;">
	<table  style="position:absolute;left:2px;top:2px;">
		<tr>
		<td><button class="base-gather btn" >信息获取</button></td>
		<td>
			<select name="platformId"
				<?php  if(!empty($product["TITLE"])) echo "disabled";?>
			  style="margin:0px;padding:0px;height:25px;">
				<option value="">--选择平台--</option>
				<?php 
					$platformId = $product['PLATFORM_ID'] ;
					
					$strategys = $SqlUtils->exeSql("sql_platform_list",array()) ;
					foreach( $strategys as $s){
						$s = $SqlUtils->formatObject($s) ;
						$selected = '' ;
						if( $s['ID'] == $platformId ){
							$selected = "selected" ;
						}
						echo "<option $selected value='".$s['ID']."'>".$s['NAME']."</option>" ;
					}
				?>
			</select>
		</td>
		<td  style="text-align:center;padding-left:20px;">
		<center style="font-weight:bold; padding:0px 20px;margin:0px;font-size:15px;color:red;"  class="alert"><?php echo $productDev['TITLE'];?></center>
		</td>
		</tr>
	</table>
	
		
	<div  class="flow-bar"  style="padding-left:10px;">
		<center>
			<table class="flow-table"></table>
			<div class="flow-action"></div>
		</center>
	</div>
<form id="personForm" action="#" data-widget="validator" class="form-horizontal" >
	<div id="details_tab" style="border:0px;position:fixed;top:110px;left:0px;right:0px;">
	</div>	
	
	<div class="hide"  id="track-tab">
		<div class="grid-track" style="width:920px;"></div>
	</div>
	<div  id="ad-tab"  class="hide">
	<textarea id="SPREAD_STRATEGY"   class="input 10-input" 
								style="width:95%;height:277px;margin-top:10px;"><?php echo  $productDev['SPREAD_STRATEGY']?></textarea>
	</div>
	
	<?php if( $productDev['DEV_STATUS'] == 1    ){ ?>
	<div class="hide"  id="sample-check-tab">
			<table  class="table  from-table">
				<tbody>
				<tr>
						<th>样品下单时间：</th>
						<td>
								<?php echo $productDev['SAMPLE_ORDER_TIME']?>
						</td>
				</tr>
				<tr>
						<th>样品到达时间：</th>
						<td>
								<?php echo $productDev['SAMPLE_ARRIVE_TIME']?>
						</td>
				</tr>
				<tr>
						<th>样品总体评价：</th>
						<td>
							<select  name="SAMPLE_EVALUATE"   class="input 45-input 46-input"  <?php if($pdStatus==45 || $pdStatus==46){ echo ' data-validator="required" ' ; }?>>
									<option value="">选择</option>
									<option value="10"  <?php echo $productDev['SAMPLE_EVALUATE']==10?"selected":"";?>>优</option>
									<option value="20" <?php echo $productDev['SAMPLE_EVALUATE']==20?"selected":"";?>>良</option>
									<option value="30" <?php echo $productDev['SAMPLE_EVALUATE']==30?"selected":"";?>>一般</option>
									<option value="40" <?php echo $productDev['SAMPLE_EVALUATE']==40?"selected":"";?>>差</option>
							</select>
						</td>
				</tr>
				<tr>
						<th>样品检测明细：</th>
						<td><textarea name="SAMPLE_CHECK_DETAILS"   class="input 45-input 46-input"  <?php if($pdStatus==45 || $pdStatus==46){ echo ' data-validator="required" ' ; }?>
									style="width:500px;height:300px;"><?php echo $productDev['SAMPLE_CHECK_DETAILS']?></textarea></td>
				</tr>
				</tbody>
			</table>
	</div>
	<?php } ?>
	
	<div class="hide "  id="dev-tab">
		<input type="hidden"  id="isGlobal" value="<?php echo $isGlobal;?>"/>
		<input type="hidden"  id="devId" value="<?php echo $productDev['DEV_ID'];?>"/>
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
		<div style="clear:both;"></div>
	 <div style="height:480px;overflow:auto;"  class="apply-panel">	
		<table class="form-table"  style="margin:5px 2px;" id="flag1">
				<tbody>
					<tr>
						<th>开发标题：</th>
						<td colspan="3">
							<input type="text"  id="TITLE" class="input 10-input 30-input 40-input"
								 value="<?php echo $productDev['TITLE'];?>"
								 data-validator="required" style="width:90%;" placeHolder="输入开发标题"/>
						</td>
					</tr>
					<tr>
						<th>产品信息：</th>
						<td colspan="3">
							<?php 
							if( !empty($productDev['LOCAL_URL'])){
								echo "<img style='width:30px;height:30px;' src='/$fileContextPath/".$productDev['LOCAL_URL']."'></span>" ;
							}
							?>
							<a href="#"  offer-listing="<?php echo $productDev['ASIN'];?>"> 
							<?php echo $productDev['ASIN'];?>
							<?php 
								if( !empty($productDev['P_TITLE'])){
									echo '---'.$productDev['P_TITLE'] ;
								}
							?>
							</a>
						</td>
					</tr>
					<tr>
						<th>开发产品分类：</th>
						<td colspan="3">
							<input type="hidden"  id="categoryId" name="categoryId" value="<?php echo $productDev['CATEGORY_ID']?>"/>
							<input type="text" id="categoryName"  class="input 10-input"   
							  data-validator="required" 
							  name="categoryName" value="<?php echo $productDev['CATEGORY_NAME']?>"/>
							<button class="btn select-category">选择</button>
						</td>
					</tr>
					<tr>
						<th>开发标识：</th>
						<td>
							自有 <input type="radio"  class="input 10-input"   name="DEV_STATUS"  <?php echo $productDev['DEV_STATUS']==1?"checked":"";?>  value="1"/>&nbsp;&nbsp;&nbsp;
							跟卖 <input type="radio"  class="input 10-input"   name="DEV_STATUS"  <?php echo $productDev['DEV_STATUS']==2?"checked":"";?>   value="2"/>&nbsp;&nbsp;&nbsp;
							<?php /*
							自有兼跟卖<input type="radio"  class="input 10-input 30-input 40-input"   name="DEV_STATUS"  <?php echo $productDev['DEV_STATUS']==4?"checked":"";?>   value="4"/>&nbsp;&nbsp;&nbsp;
							*/?>
							废弃 <input type="radio"  class="input 10-input 30-input 40-input"   name="DEV_STATUS"  <?php echo $productDev['DEV_STATUS']==3?"checked":"";?>  value="3"/>
							
						</td>
						<td>
						<?php  if( $pdStatus >=10 && $pdStatus!=15  && $pdStatus!=80 &&$PD_FORCE ){  ?>
							<button class="btn btn-danger btn-force-15" onclick="AuditAction(15,'强制废弃',{DEV_STATUS:3})">强制废弃</button>
							<button class="btn btn-danger btn-force-10" onclick="AuditAction(10,'重新开发分析',{DEV_STATUS:'0'})">重新开发</button>
						<?php } ?>
						</td>
					</tr>
					<?php if( $pdStatus ==10 ){ ?>
					<tr class="dev_status_memo hide">
						<th style="width:20%;"></th>
						<td>是否采购样品：<input type="checkbox"  value="1"  <?php echo $isPurchaseSample?"checked='checked'":"";?>  class="dev_status_flow"  key="isPurchaseSample"/></td>
					</tr>
					<?php } ?>
					<tr>
						<th style="width:20%;">
							试销采购量
						</th>
						<td>
							<input type="text"  id="TRY_PURCHASE_NUM" 
								 class="input 10-input 30-input 40-input" 
								style="width:80%;" placeHolder="试销采购量" value="<?php echo $productDev['TRY_PURCHASE_NUM']?>" />
						</td>
						<td colspan="2">
						&nbsp;
						</td>
					</tr>
					<?php  if( ($pdStatus >=50) ||  !empty($productDev['REAL_PRODUCT_ID']) ){  ?>
					<tr>
						<th style="width:20%;">
							<?php  if( $pdStatus ==50||$pdStatus==60 ){  ?>
							<button class="btn btn-primary select-real-product">选择货品</button>
							<?php 	} ?>
							<input type="hidden" id="REAL_PRODUCT_ID" value="<?php echo $productDev['REAL_PRODUCT_ID'];?>"/>
							关联货品
						</th>
							<?php 
							if( !empty($productDev['REAL_PRODUCT_ID']) ){
								$sp = $SqlUtils->getObject("sql_saleproduct_getById",array("realProductId"=>$productDev['REAL_PRODUCT_ID'])) ;
								echo "<td colspan='4'>" ;
								echo $sp['NAME'] ;
								echo "<span   product-realsku='".$sp['REAL_SKU']."'>(".$sp['REAL_SKU'].")" ;
								echo "<img style='width:30px;height:30px;' src='/$fileContextPath".$sp['IMAGE_URL']."'></span>" ;
								echo "</td>" ;
							}	
							?>
					</tr>		
					<?php 	} ?>
					<?php  if( $pdStatus >=60 ){  ?>
					<tr>
						<th style="width:20%;">
							关联Listing SKU
						</th>
						<td colspan="3">
							<select id="ACCOUNT_ID"   class="input 60-input"   style="width:100px;">
				     		<option value="">--选择账号--</option>
					     	<?php
					     		 $amazonAccount  = ClassRegistry::init("Amazonaccount") ;
					     		 $accountId = $productDev['ACCOUNT_ID'] ;
				   				 $accounts = $amazonAccount->getAllAccounts(); 
					     		foreach($accounts as $account ){
					     			$account = $account['sc_amazon_account'] ;
					     			if( $account['ID'] ==  $accountId ){
					     				echo "<option value='".$account['ID']."'  selected>".$account['NAME']."</option>" ;
					     			}else{
					     				echo "<option value='".$account['ID']."'>".$account['NAME']."</option>" ;
					     			}
					     		} ;
					     	?>
							</select>
							<input type="text"  id="LISTING_SKU" 
								 class="input 60-input"  style="width:40%;" placeHolder="输入关联ListingSKU" value="<?php echo $productDev['LISTING_SKU']?>" />
						</td>
					</tr>		
					<?php 	} ?>
				</tbody>
			</table>
			
			<div class="row-fluid">
					<div class="span6">
						<table  class="form-table">
							<tr>
								<th>供应限价:</th>
								<td>
										<input type="text" class="input 25-input"  id="SUPPLIER_MAX_PRICE"  value="<?php echo $productDev['SUPPLIER_MAX_PRICE']?>" />
										<?php if( $PD_SUPPLIER_MAX_PRICE ){ 
											echo "<img src='/$fileContextPath/app/webroot/img/edit.png' class='reedit'>" ;
										}?>
								</td>
							</tr>
						</table>
					</div>
					<div class="span6">
						<table  class="form-table">
							<tr>
								<th>询价负责人:</th>
								<td>
										<input type="hidden"  id="INQUIRY_CHARGER"  value="<?php echo $charger;?>" />
										<input type="text"  disabled  id="INQUIRY_CHARGER_NAME"  value="<?php echo $chargerName;?>" />
										<button class="btn add-on add-on1 input 10-input">选择用户</button>
								</td>
							</tr>
						</table>
					</div>
				</div>
				
		<table class="form-table "   id="flag2">
				<caption>
				产品属性
				<?php if( $PD_REEDIT_BASE){ 
					echo "<img src='/$fileContextPath/app/webroot/img/edit.png' class='reedit'>" ;
				}?>
				</caption>
				<tbody>	
					<tr>
						<th>销售单元</th>
						<td><input type="text"    class="input 10-input"   id="P_SALE_UNIT"  value="<?php echo $productDev['P_SALE_UNIT'];?>" /></td>
						<th>包装</th>
						<td><input type="text"    class="input 10-input"   id="P_PACKAGE"  value="<?php echo $productDev['P_PACKAGE'];?>" /></td>
					</tr>
					<tr>
						<th>尺寸</th>
						<td colspan="3">
							<input type="text"    class="input 10-input"   id="P_SIZES"  value="<?php echo $productDev['P_SIZES'];?>" />
							<select id="P_SIZES_UNIT"   class="input 10-input"  style="width:60px;">
								<option value="cm"  <?php echo $productDev['P_SIZES_UNIT']=='cm'?"selected":"";?>>CM</option>
								<option value="inch"  <?php echo $productDev['P_SIZES_UNIT']=='inch'?"selected":"";?>>英寸</option>
							</select>
						</td>
					</tr>
					<tr>
						<th>颜色</th>
						<td><input type="text"    class="input 10-input"   id="P_COLOR"  value="<?php echo $productDev['P_COLOR'];?>" /></td>
						<th>材质</th>
						<td><input type="text"    class="input 10-input"   id="P_MATERIAL"  value="<?php echo $productDev['P_MATERIAL'];?>" /></td>
					</tr>
					<tr>
						<th>注意事项</th>
						<td colspan="3">
							<textarea  id="P_CAUTIONS"  class="input 10-input"  style="width:90%;height:60px;"><?php echo $productDev['P_CAUTIONS'];?></textarea>
						</td>
					</tr>
				</tbody>
		</table>		
		
		<table class="form-table "   id="flag2">
				<caption>
				基本信息分析
				<?php if( $PD_REEDIT_BASE){ 
					echo "<img src='/$fileContextPath/app/webroot/img/edit.png' class='reedit'>" ;
				}?>
				</caption>
				<tbody>	
					<tr>
						<th style="width:33%;">ASIN排名</th>
						<th style="width:33%;">估计流量</th>
						<th style="width:34%;">热销时段</th>
					</tr>
					<tr>
						<td style="width:20%;"><textarea  id="RANK"  class="input 10-input"  style="width:90%;height:60px;"><?php echo $productDev['RANK'];?></textarea></td>
						<td style="width:20%;"><textarea  id="ESTIMATE_TRAFFIC"  class="input 10-input"     style="width:90%;height:60px;"><?php echo $productDev['ESTIMATE_TRAFFIC'];?></textarea></td>
						<td style="width:20%;"><textarea  id="HOT_SELL_PERIOD" class="input 10-input"     style="width:90%;height:60px;"><?php echo $productDev['HOT_SELL_PERIOD'];?></textarea></td>
					</tr>
				</tbody>
			</table>
			
			<table class="form-table "   id="flag2">
				<caption>
				开发备注
				<?php if( $PD_REEDIT_BASE){ 
					echo "<img src='/$fileContextPath/app/webroot/img/edit.png' class='reedit'>" ;
				}?>
				</caption>
				<tbody>	
					<tr>
						<td>
						<textarea  id="DEV_MEMO"  class="input 10-input"  style="width:90%;height:160px;"><?php echo $productDev['DEV_MEMO'];?></textarea>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	
	<div>
	</div>
		</form>
</body>

<script type="text/javascript">
	$(".ae_key").keyup(function(){
		var val = $(this).val() ;
		var len = val.length;
		if(!$(this).parent().find("span").length){
			$(this).parent().append("<span></span>") ;
		}
		$(this).parent().find("span").html("剩余"+(50-len)) ;
	}).trigger("keyup") ;
</script>

</html>