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
		

		echo $this->Html->css('../js/modules/tag/tagutil');
		echo $this->Html->script('modules/tag/tagutil');
		
		//$this->set('details', $details);
		//$this->set('images', $images);
		//$this->set('competitions', $competitions);
		//$this->set('rankings', $rankings);
		
		$user = $this->Session->read("product.sale.user") ;
		$group=  $user["GROUP_CODE"] ;

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
			if( !empty($flows) ){
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
		
		$task = $SqlUtils->getObject("sql_pdev_task_getById",array("id"=>$taskId)) ;
		
		$productDev = $SqlUtils->getObject("sql_pdev_findByAsinAndTaskId",array('ASIN'=>$asin,'TASK_ID'=>$taskId)) ;
		
		$pdStatus = 10 ;
		$devStatus = 0 ;
		if(!empty( $productDev  )){
			$pdStatus = $productDev['FLOW_STATUS'] ;
			$devStatus = $productDev['DEV_STATUS'] ;
		}
		
		//debug($product) ;
		
		//询价负责人
		$category = $SqlUtils->getObject("sql_getSingleProductCategoryByAsin" , array("asin"=>$asin)) ;
		$charger = $productDev['INQUIRY_CHARGER'] ;
		$chargerName = $productDev['INQUIRY_CHARGER_NAME'] ;
		if(  !empty($category) ){
			$charger = $category['PURCHASE_CHARGER'] ;
			$chargerName= $category['PURCHASE_CHARGER_NAME'] ;
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
		
		$PD_TRANSFER			=  $security->hasPermission($loginId , 'PD_TRANSFER') ;
		
		$PD_SXCG			=  $security->hasPermission($loginId , 'PD_SXCG') ;//试销采购
		$PD_KCDW			=  $security->hasPermission($loginId , 'PD_KCDW') ;//库存到位
		$PD_YXZK			=  $security->hasPermission($loginId , 'PD_YXZK') ;//营销展开
		$PD_KFZJ				=  $security->hasPermission($loginId , 'PD_KFZJ') ;//开发总结
	
		$Config  = ClassRegistry::init("Config") ;
		$websites = $Config->getAmazonConfig("PRODUCT_DEV_WEBSITE") ;
		
		//成本权限
		$COST_EDIT_PURCHASE  				= $security->hasPermission($loginId , 'COST_EDIT_PURCHASE') ;
		$COST_EDIT_LOGISTIC  					= $security->hasPermission($loginId , 'COST_EDIT_LOGISTIC') ;
		$COST_EDIT_PRODUCT_CHANNEL 	= $security->hasPermission($loginId , 'COST_EDIT_PRODUCT_CHANNEL') ;
		$COST_EDIT_FEE    							= $security->hasPermission($loginId , 'COST_EDIT_FEE') ;
		$COST_EDIT_OTHER   						= $security->hasPermission($loginId , 'COST_EDIT_OTHER') ;
		$COST_EDIT_SALEPRICE   				= $security->hasPermission($loginId , 'COST_EDIT_SALEPRICE') ;
		$COST_EDIT_PROFIT   						= $security->hasPermission($loginId , 'COST_EDIT_PROFIT') ;
		
		$COST_VIEW_TOTAL  						= $security->hasPermission($loginId , 'COST_VIEW_TOTAL') ;
		$COST_VIEW_PROFIT  						= $security->hasPermission($loginId , 'COST_VIEW_PROFIT') ||$COST_EDIT_PROFIT  ;
		$COST_VIEW_PURCHASE  				= ( $security->hasPermission($loginId , 'COST_VIEW_PURCHASE') )||$COST_EDIT_PURCHASE ;
		$COST_VIEW_LOGISTIC  					= ( $security->hasPermission($loginId , 'COST_VIEW_LOGISTIC') )|| $COST_EDIT_LOGISTIC ;
		$COST_VIEW_PRODUCT_CHANNEL =(  $security->hasPermission($loginId , 'COST_VIEW_PRODUCT_CHANNEL')  )|| $COST_EDIT_PRODUCT_CHANNEL ;
		$COST_VIEW_FEE  							= ( $security->hasPermission($loginId , 'COST_VIEW_FEE')  )|| $COST_EDIT_FEE ;
		$COST_VIEW_OTHER  						=(  $security->hasPermission($loginId , 'COST_VIEW_OTHER')  )|| $COST_EDIT_OTHER ;
		$COST_VIEW_SALEPRICE					= ( $security->hasPermission($loginId , 'COST_VIEW_SALEPRICE') )|| $COST_EDIT_SALEPRICE ;
		
		$COST_EDIT = $COST_EDIT_PURCHASE || $COST_EDIT_LOGISTIC || $COST_EDIT_PRODUCT_CHANNEL || $COST_EDIT_FEE||$COST_EDIT_OTHER||$COST_EDIT_SALEPRICE||$COST_EDIT_PROFIT ;
	?>
  
   <style>
html{-webkit-text-size-adjust: none;}
   		*{
   			font:12px "微软雅黑";
   		}
   		
   		.flow-node{
			font-size:11px;
   		}
   		
   		.flow-split{
			display:none;
   		}
   		
   		.flow-bar{
			position:fixed;
   			top:30px;
   			padding-top:10px;
   			left:0px;
   			right:0px;
   			height:50px;
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
		DynTag.listByEntity("productDevTag",'<?php echo $taskId.'$$'.$asin;?>') ;
	}) ;
</script>
 
 <script>
 var costColumns = [] ;

	<?php  if( $COST_EDIT ){?>
	costColumns.push({align:"center",key:"ID",label:"操作",width:"6%",forzen:true,format:function(val,record){
		var status = record.STATUS ;
		var html = [] ;
		html.push("<a href='#' class='edit-action' val='"+val+"'>编辑</a>&nbsp;") ;
		return html.join("") ;
	}}) ;
	<?php }?>
	costColumns.push( {align:"center",key:"TYPE",label:"成本类型", width:"6%" }) ;

	<?php  if($COST_VIEW_PROFIT){ ?>
	//利润率
	costColumns.push({align:"center",key:"PROFIT_MARGINS",label:"产品利润",sort:true,forzen:true,width:"7%",format:function(val ,record){
		var pn = record.PROFIT_NUM ;
		if(!pn) return "未算利润" ;

		var totalCost = record.TOTAL_COST ;
		var pl = pn/totalCost ;
		if(pl<=0){
			return "亏本" ;
		}

		if(pl<0.15) return "低利润" ;
		return "利润达标" ;
	}}) ;
	<?php }?>
	<?php  if($COST_VIEW_TOTAL){ ?>
	//总成本
	costColumns.push( {align:"center",key:"TOTAL_COST",label:"总成本",forzen:true,width:"6%",format:function(val,record){
			return val;
		}} ) ;
	<?php }?>
	<?php  if($COST_VIEW_PURCHASE){ ?>
	//采购成本
	costColumns.push( {align:"center",key:"PURCHASE_COST",label:"采购成本",width:"8%"}) ;
	<?php }?>
	<?php  if($COST_VIEW_LOGISTIC){ ?>
	//物流成本
	costColumns.push( {align:"center",key:"BEFORE_LOGISTICS_COST",label:"入库前物流费用",width:"8%",forzen:false,align:"left"}) ;
	costColumns.push(	{align:"center",key:"TARIFF",label:"关税",width:"6%",forzen:false,align:"left"}) ;
	costColumns.push(	{align:"center",key:"WAREHOURSE_COST",label:"仓储费用",width:"6%"}) ;
	costColumns.push( {align:"center",key:"USPS_COST",label:"USPS邮费",width:"6%"}) ;
	<?php }?>
	<?php  if($COST_VIEW_PRODUCT_CHANNEL){ ?>
	//产品渠道成本
	costColumns.push( {align:"center",key:"AMAZON_FEE",label:"amazon佣金",width:"8%"}) ;
	costColumns.push( {align:"center",key:"VARIABLE_CLOSURE_COST",label:"可变关闭费用",width:"8%"}) ;
	costColumns.push( {align:"center",key:"OORDER_PROCESSING_FEE",label:"订单处理费",width:"6%"}) ;
	costColumns.push( {align:"center",key:"TAG_COST",label:"标签费用",width:"8%"} ) ;
	costColumns.push( {align:"center",key:"PACKAGE_COST",label:"打包费",width:"6%"}) ;
	costColumns.push( {align:"center",key:"STABLE_COST",label:"称重费",width:"8%"}) ;
	<?php }?>
	<?php  if($COST_VIEW_FEE){ ?>
	//税费人工成本
	costColumns.push( {align:"center",key:"LOST_FEE",label:"当地税费",width:"6%"}) ;
	costColumns.push( {align:"center",key:"LABOR_COST",label:"人工成本",width:"6%"}) ;
	costColumns.push( {align:"center",key:"SERVICE_COST",label:"服务成本",width:"6%"}) ;
	<?php }?>
	<?php  if($COST_VIEW_OTHER){ ?>
	//其他成本
	costColumns.push(	{align:"center",key:"OTHER_COST",label:"其他成本",width:"8%"} ) ;
	<?php }?>
	costColumns.push(	{align:"center",key:"LAST_UPDATE_TIME",label:"更新时间",width:"15%"} ) ;

 
 	var taskId = '<?php echo $taskId;?>' ;
 	var asin = '<?php echo $asin;?>' ;
 	var username = '<?php echo $username;?>' ;
 	var pdStatus = '<?php echo $pdStatus;?>' ;
 	var platformId = '<?php echo $product['PLATFORM_ID'] ;?>' ;

 	jQuery.dialogReturnValue(false) ;

 	function  ForceAuditAction(status , statusLabel,fixParams){
			if(window.confirm("确认【"+statusLabel+"】吗？")){
				var json = $("#personForm").toJson() ;
				json = $.extend({},json,fixParams) ;
				json.ASIN = asin ;
				json.FLOW_STATUS = status;
				json.TASK_ID = taskId ;

				if( json.DEV_STATUS == 3 ){
					json.FLOW_STATUS = 15 ;
				}
				
				var memo = "("+statusLabel+")"+ ($(".memo").val()||"") ;
				json.trackMemo = memo ;
				
				$.dataservice("model:ProductDev.doFlow",json,function(result){
						jQuery.dialogReturnValue(true) ;
						window.location.reload() ;
				});
			}
 	 }

 	//$.dataservice("model:PurchaseService.createPlanForProductDev",{},function(result){
		
	//});

 	function AuditAction(status , statusLabel,fixParams){
 		if( !$.validation.validate('#personForm').errorInfo ) {
 			ForceAuditAction(status , statusLabel,fixParams) ;
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
    
	flowData.push( {status:30,label:"产品经理审批",memo:true ,
		actions:[ 
				<?php if( $PD_CPJLSP ){ ?>
	          {label:"保存",action:function(){ ForceAuditAction('30',"保存") } },
			  {label:"撤回分析",action:function(){ AuditAction('10',"审批不通过，撤回分析") } },
			  {label:"撤回询价",action:function(){ AuditAction('20',"审批不通过，撤回询价") } },
			  {label:"提交总监审批",action:function(){ AuditAction('40',"提交总监审批 " ) } },
			  {label:"审批通过",action:function(){ AuditAction('50',"审批通过，录入货品 " ) } }
			  <?php }?>
	     ]}
     ) ;
	flowData.push( {status:40,label:"总监审批",memo:true ,
		actions:[ 
				<?php if( $PD_ZJSP ){ ?>
		         {label:"保存",action:function(){ ForceAuditAction('40',"保存") } },
		         {label:"撤回分析",action:function(){ AuditAction('10',"审批不通过，撤回分析") } },
				 {label:"撤回询价",action:function(){ AuditAction('20',"审批不通过，撤回询价") } },
				 {label:"审批通过",action:function(){ AuditAction('50',"审批通过，准备录入货品") } }
				 <?php }?>
	     ]}
     ) ;
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
	           {label:"保存",action:function(){ ForceAuditAction('70',"保存") } },
		       {label:"审批通过",action:function(){ AuditAction('72',"审批通过，进入试销采购，采购单将自动生成！") } }
	           <?php }?>
	     ]}
     ) ;

	flowData.push( {status:72,label:"试销采购",memo:true ,
		actions:[ 
<?php if( $PD_SXCG ){ ?>
		         {label:"保存",action:function(){ ForceAuditAction('72',"保存") } }
			//  ,   {label:"下一步",action:function(){ AuditAction('74',"试销采购完成，进入下一步") } }
		         <?php }?>
	     ]}
     ) ;
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
		flow.draw(<?php echo $pdStatus;?>) ;
	}) ;
 </script>

</head>
<body style="overflow-y:auto;padding:2px;">
	<table  style="position:absolute;left:2px;top:2px;">
		<tr>
			<td><?php if($PD_TRANSFER && $pdStatus !=80 && $pdStatus!=15  ){?>
		<button class="transfer-action btn"  >转交</button>
		<?php }?></td>
		<td><button class="base-gather btn" >信息获取</button></td>
		<td>
			<select name="platformId"
				<?php  if(!empty($product["TITLE"])) echo "disabled";?>
			  style="margin:0px;padding:0px;height:25px;">
				<option value="">--选择平台--</option>
				<?php 
					$platformId = $product['PLATFORM_ID'] ;
					if(empty($platformId)){
						$platformId = $task['PLATFORM_ID'] ;
					}
					
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
		</tr>
	</table>
	
		
	<div  class="flow-bar">
		<center>
			<table class="flow-table"></table>
			<div class="flow-action"></div>
		</center>
	</div>
<form id="personForm" action="#" data-widget="validator" class="form-horizontal" >
	<div id="details_tab" style="border:0px;position:fixed;top:90px;left:0px;right:0px;">
	</div>	
	
	<div class="hide"  id="track-tab">
		<div class="grid-track" style="width:920px;"></div>
	</div>
	
	<div class="hide "  id="dev-tab">
		
		<input type="hidden"  id="TASK_ID" value="<?php echo $taskId;?>"/>
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
		
		
		
		<ul style="list-style: none;" class="flag_container">
			<li><a href="#flag1" class="alert alert-info">开发标题</a></li>
			<li><a href="#flag2" class="alert alert-info">基本信息分析</a></li>
			<li><a href="#flag3" class="alert alert-info">产品风险分析</a></li>
			<li><a href="#flag4" class="alert alert-info">产品关键字分析</a></li>
			<li><a href="#flag5" class="alert alert-info">产品方案</a></li>
			<li><a href="#flag6" class="alert alert-info">营销计划与策略</a></li>
		</ul>
		<div style="clear:both;"></div>
	 <div style="height:500px;overflow:auto;">	
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
						<th>开发标识：</th>
						<td>
							自有 <input type="radio"  class="input 10-input 30-input 40-input"   name="DEV_STATUS"  <?php echo $productDev['DEV_STATUS']==1?"checked":"";?>  value="1"/>&nbsp;&nbsp;&nbsp;
							跟卖 <input type="radio"  class="input 10-input 30-input 40-input"   name="DEV_STATUS"  <?php echo $productDev['DEV_STATUS']==2?"checked":"";?>   value="2"/>&nbsp;&nbsp;&nbsp;
							自有兼跟卖<input type="radio"  class="input 10-input 30-input 40-input"   name="DEV_STATUS"  <?php echo $productDev['DEV_STATUS']==4?"checked":"";?>   value="4"/>&nbsp;&nbsp;&nbsp;
							废弃 <input type="radio"  class="input 10-input 30-input 40-input"   name="DEV_STATUS"  <?php echo $productDev['DEV_STATUS']==3?"checked":"";?>  value="3"/>
							
						</td>
						<td>
						<?php  if( $pdStatus >=10 && $pdStatus!=15  && $pdStatus!=80 &&$PD_FORCE ){  ?>
							<button class="btn btn-danger btn-force-15" onclick="AuditAction(15,'强制废弃',{DEV_STATUS:3})">强制废弃</button>
							<button class="btn btn-danger btn-force-10" onclick="AuditAction(10,'重新开发分析',{DEV_STATUS:'0'})">重新开发</button>
						<?php } ?>
						</td>
					</tr>
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
							<?php 
									$devId = $asin.'_'.$taskId ;
									$existSql = "select * from sc_purchase_plan_details where dev_id = '{@#devId#}' " ;
									$item = $SqlUtils->getObject($existSql, array('devId'=>$devId)) ;
									if( !empty($item) ){
										echo "<a href='/$fileContextPath/index.php/page/forward/Sale.edit_purchase_plan_product/".$item['ID']."' target='_blank'>查看采购计划单</a>" ;
									}
							?>
						</td>
					</tr>
					<?php  if( ($pdStatus >=50) ||  !empty($productDev['REAL_PRODUCT_ID']) ){  ?>
					<tr>
						<th style="width:20%;">
							<?php  if( $pdStatus ==50 ){  ?>
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
							关联Listing SKU（逗号分隔）
						</th>
						<td colspan="3">
							<input type="text"  id="LISTING_SKU" 
								 class="input 60-input" 
								style="width:80%;" placeHolder="输入关联ListingSKU" value="<?php echo $productDev['LISTING_SKU']?>" />
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
								</td>
							</tr>
						</table>
					</div>
				</div>
		
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
			
			<table class="form-table "   id="flag3">
				<caption>
				产品风险分析
				<?php if( $PD_REEDIT_GM){ 
					echo "<img src='/$fileContextPath/app/webroot/img/edit.png' class='reedit'>" ;
				}?>
				</caption>
				<tbody>
					<tr>
						<th>产品风险：</th>
						<td><textarea id="FOLLOW_RISK_PRODUCT"    class="input 10-input"  
							style="width:90%;height:60px;"><?php echo $productDev['FOLLOW_RISK_PRODUCT']?></textarea></td>
					</tr>
					<tr>
						<th>品牌风险：</th>
						<td><textarea id="FOLLOW_RISK_BRAND"      class="input 10-input"  
							style="width:90%;height:60px;"><?php echo  $productDev['FOLLOW_RISK_BRAND']?></textarea></td>
					</tr>
					<tr>
						<th>供应商风险：</th>
						<td><textarea id="FOLLOW_RISK_SUPPLIER"      class="input 10-input"  
							style="width:90%;height:60px;"><?php echo  $productDev['FOLLOW_RISK_SUPPLIER']?></textarea></td>
					</tr>
				</tbody>
			</table>
			
			<table class="form-table "   id="flag4">
				<caption>
					产品关键字分析
					<?php if( $PD_REEDIT_ZY){ 
					echo "<img src='/$fileContextPath/app/webroot/img/edit.png' class='reedit'>" ;
				}?>
				</caption>
				<tbody>	
					<tr>
						<th style="width:15%;"></th>
						<th>关键字</th>
						<th style="width:9%;">渠道有效竞争数</th>
						<th style="width:9%;">搜索量VOLUMN</th>
						<th style="width:9%;">竞争COM</th>
						<th style="width:9%;">竞价CPC</th>
					</tr>
					<tr>
						<th>核心关键字：</th>
						<td><input type="text" id="CORE_KEY"   placeHolder="不能超过50个字符"  maxlength="50"   class=" ae_key input 10-input"   style="width:85%;" value="<?php echo $productDev['CORE_KEY']?>"/></td>
						<td>	<input type="text" id="CK_VALID_COMP"      class="input 10-input"  style="width:80%;"  value="<?php echo $productDev['CK_VALID_COMP']?>"/></td>
						<td>	<input type="text" id="CK_SR_SEARCH"      class="input 10-input"  style="width:80%;"  value="<?php echo $productDev['CK_SR_SEARCH']?>" /></td>
						<td>	<input type="text" id="CK_SR_COM"      class="input 10-input"  style="width:80%;"  value="<?php echo $productDev['CK_SR_COM']?>"/></td>
						<td>	<input type="text" id="CK_SR_CPC"      class="input 10-input"  style="width:80%;"  value="<?php echo $productDev['CK_SR_CPC']?>"/></td>
					</tr>
					<tr>
						<th>Amazon/Ebay关键字1：</th>
						<td><input type="text" id="OP_KEY1"  placeHolder="不能超过50个字符"  maxlength="50" class="ae_key input 10-input"  style="width:85%;"  value="<?php echo $productDev['OP_KEY1']?>"/></td>
						<td>	<input type="text" id="OK_VALID_COMP1"      class="input 10-input"  style="width:80%;"  value="<?php echo $productDev['OK_VALID_COMP1']?>"/></td>
						<td>	<input type="text" id="OK_SR_SEARCH1"      class="input 10-input"  style="width:80%;"  value="<?php echo $productDev['OK_SR_SEARCH1']?>"/></td>
						<td>	<input type="text" id="OK_SR_COM1"      class="input 10-input"  style="width:80%;"  value="<?php echo $productDev['OK_SR_COM1']?>"/></td>
						<td>	<input type="text" id="OK_SR_CPC1"      class="input 10-input"  style="width:80%;"  value="<?php echo $productDev['OK_SR_CPC1']?>"/></td>
					</tr>
					<tr>
						<th>Amazon/Ebay关键字2：</th>
						<td><input type="text" id="OP_KEY2"     placeHolder="不能超过50个字符"  maxlength="50"    class="ae_key input 10-input"  style="width:85%;"  value="<?php echo $productDev['OP_KEY2']?>"/></td>
						<td>	<input type="text" id="OK_VALID_COMP2"      class="input 10-input"  style="width:80%;"  value="<?php echo $productDev['OK_VALID_COMP2']?>"/></td>
						<td>	<input type="text" id="OK_SR_SEARCH2"     class="input 10-input"   style="width:80%;" value="<?php echo $productDev['OK_SR_SEARCH2']?>" /></td>
						<td>	<input type="text" id="OK_SR_COM2"      class="input 10-input"  style="width:80%;"  value="<?php echo $productDev['OK_SR_COM2']?>"/></td>
						<td>	<input type="text" id="OK_SR_CPC2"      class="input 10-input"  style="width:80%;" value="<?php echo $productDev['OK_SR_CPC2']?>" /></td>
					</tr>
					<tr>
						<th>Amazon/Ebay关键字3：</th>
						<td><input type="text" id="OP_KEY3"     placeHolder="不能超过50个字符"  maxlength="50"    class="ae_key input 10-input"  style="width:85%;" value="<?php echo $productDev['OP_KEY3']?>" /></td>
						<td>	<input type="text" id="OK_VALID_COMP3"      class="input 10-input"  style="width:80%;"  value="<?php echo $productDev['OK_VALID_COMP3']?>"/></td>
						<td>	<input type="text" id="OK_SR_SEARCH3"      class="input 10-input"  style="width:80%;"  value="<?php echo $productDev['OK_SR_SEARCH3']?>"/></td>
						<td>	<input type="text" id="OK_SR_COM3"     class="input 10-input"   style="width:80%;" value="<?php echo $productDev['OK_SR_COM3']?>" /></td>
						<td>	<input type="text" id="OK_SR_CPC3"     class="input 10-input"   style="width:80%;"  value="<?php echo $productDev['OK_SR_CPC3']?>"/></td>
					</tr>
					<tr>
						<th>Amazon/Ebay关键字4：</th>
						<td><input type="text" id="OP_KEY4"    placeHolder="不能超过50个字符"  maxlength="50"     class="ae_key input 10-input"  style="width:85%;"  value="<?php echo $productDev['OP_KEY4']?>"/></td>
						<td>	<input type="text" id="OK_VALID_COMP4"      class="input 10-input"  style="width:80%;" value="<?php echo $productDev['OK_VALID_COMP4']?>" /></td>
						<td>	<input type="text" id="OK_SR_SEARCH4"      class="input 10-input"  style="width:80%;"  value="<?php echo $productDev['OK_SR_SEARCH4']?>"/></td>
						<td>	<input type="text" id="OK_SR_COM4"      class="input 10-input"  style="width:80%;"  value="<?php echo $productDev['OK_SR_COM4']?>"/></td>
						<td>	<input type="text" id="OK_SR_CPC4"     class="input 10-input"   style="width:80%;" value="<?php echo $productDev['OK_SR_CPC4']?>" /></td>
					</tr>
					<tr>
						<th>其他关键字：</th><td colspan=5>
								<textarea id="EBAY_KEY"   class="input 10-input"    
									style="width:98%;height:50px;"><?php echo $productDev['EBAY_KEY'];?></textarea></td>
					</tr>
					<tr>
						<th>eBay销售数量：</th>
						<td  colspan=5><textarea id="EBAY_SALE_MEMO"   class="input 10-input"    
							style="width:98%;height:50px;"><?php echo $productDev['EBAY_SALE_MEMO'];?></textarea></td>
					</tr>
				</tbody>
			</table>
	
			<table class="form-table"  style="margin:5px 2px;"  id="flag5">
				<caption>产品方案<?php if( $PD_REEDIT_YX){ 
					echo "<img src='/$fileContextPath/app/webroot/img/edit.png' class='reedit'>" ;
				}?></caption>
				<tbody>
					<tr>
						<td>
							<textarea id="PRODUCTS_SOLUTIONS"  class="input 10-input 30-input 40-input" 
								style="margin-top:2px;width:95%;height:100px;"><?php echo  $productDev['PRODUCTS_SOLUTIONS']?></textarea>
						</td>
					</tr>
				</tbody>
			</table>
			
			<table class="form-table "   id="flag6">
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
							<select id="PPC_STRATEGY"    style="width:97%;" class="input 10-input" >
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
							<textarea id="PPC_STRATEGY_MEMO" class="input 10-input"  style="margin-top:2px;width:95%;height:50px;"><?php echo  $productDev['PPC_STRATEGY_MEMO']?></textarea>
						</td>
						<td>
							<select id="LOGI_STRATEGY" style="width:97%;"    class="input 10-input" >
								<option value="">--选择--</option>
								<option value="FBM"  <?php echo $productDev['LOGI_STRATEGY']=='FBM'?"selected":"" ?>>FBM</option>
								<option value="FBA"  <?php echo $productDev['LOGI_STRATEGY']=='FBA'?"selected":"" ?>>FBA</option>
								<option value="FBA_FBM"  <?php echo $productDev['LOGI_STRATEGY']=='FBA_FBM'?"selected":"" ?>>FBM和FMA</option>
								<option value="FROM_CHINA"  <?php echo $productDev['LOGI_STRATEGY']=='FROM_CHINA'?"selected":"" ?>>中国发货</option>
							</select>
							<textarea id="LOGI_STRATEGY_MEMO"  class="input 10-input" 
								style="margin-top:2px;width:95%;height:50px;"><?php echo  $productDev['LOGI_STRATEGY_MEMO']?></textarea>
						</td>
						<td>
							<textarea id="SPREAD_STRATEGY"   class="input 10-input" 
								style="width:95%;height:77px;"><?php echo  $productDev['SPREAD_STRATEGY']?></textarea>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	
	<div>
		<div id="baseinfo-tab" class="ui-tabs-panel" style="height: 100px; display: block; ">
			<table class="table table-bordered">
				<tr>
					<th style="width:100px;">标题：</th>
					<td><a href="#"  offer-listing="<?php echo $product["ASIN"]?>"><?php echo $product["TITLE"]?>(<?php echo $product["ASIN"]?>) </a></td>
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