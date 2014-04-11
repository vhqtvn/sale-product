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
		$PurchaseService  = ClassRegistry::init("PurchaseService") ;
		
		
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
	var taskId = '<?php echo $task['ID'] ;?>';
	
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

</body>

</html>