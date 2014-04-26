<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   		include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		
		$sku = $params['arg1'] ;
		$type = $params['arg2'] ;
		$taskId = $params['arg3'] ;
		$asin = "" ;
		if( $type == 'asin' ){
			$asin = $sku ;
			$sku = "" ;
			//通过ASIN查找对应的SKU
			$SqlUtils  = ClassRegistry::init("SqlUtils") ;
			$product = $SqlUtils->getObject("select srp.* from sc_real_product srp,
    	 					sc_product_dev spd where srp.id = spd.real_product_id and spd.asin='{@#asin#}'",array('asin'=>$asin )) ;
			$sku =$product['REAL_SKU'] ;
		}
		
		
		$security  = ClassRegistry::init("Security") ;
		$loginId 						= $user['LOGIN_ID'] ;
		$PD_INQUIRY	= $security->hasPermission($loginId , 'PD_INQUIRY')   ;//询价权限
		
	?>
  
   <script type="text/javascript">
   
   var taskId = '<?php echo $taskId;?>' ;

   <?php
			$url = "" ; 
			if(empty($sku)){
				$url = "/page/forward/Supplier.updateProductSupplierByAsinFrame/asin/".$asin."" ;//updateProductSupplierByAsinFrame -/$taskId
			}else{
				$url = "/page/forward/Supplier.updateProductSupplierByAsinFrame/sku/".$sku."" ;
			}
		?>
  
	$(function(){

			var querys = {sqlId:"sql_list_supplierInquiryHistory"} ;
			if( '<?php echo $sku;?>' ){
				querys['realSku'] = '<?php echo $sku;?>' ;
			}else{
				querys['asin'] = '<?php echo $asin;?>' ;
			}

			function  getShipFeeType(val){
				if(val == 1) return "包邮" ;
				if(val == 2) return "免邮" ;
				if(val == 3) return "卖家支付" ;
				return "-" ;
			}
			
			$(".grid-content-details").llygrid({
				columns:[
							//名称	产品重量	生产周期	包装方式	付款方式	产品尺寸	包装尺寸	报价1	报价2	报价3
							<?php if($PD_INQUIRY){ ?>
							{align:"center",key:"TASK_ID",label:"操作",width:"3%",format:function(val,record){
									var html = [] ;
									html.push( getImage("edit.png","修改","process-action") ) ;
									//if( record.DAY_NUM > 7 ) return "" ;
									return html.join("") ;
							}},
							 <?php }?>

							{align:"center",key:"IMAGE",label:"图片",width:"3%",forzen:false,align:"left",format:{type:'img'}},
							{align:"left",key:"CREATE_TIME",label:"询价信息",width:"15%",forzen:false,align:"left",format:function(val,record){
									var html=  [] ;
									html.push( record.CREATE_TIME ) ;
									html.push( record.USERNAME ) ;
									html.push( "<a href='#' supplier-id='"+record.SUPPLIER_ID+"'>"+record.NAME+"<a>" ) ;
									return html.join("<br/>") ;
							}},
						     {align:"left",key:"SAMPLE",label:"样品信息",width:"20%",forzen:false,align:"left",format:function(val,record){
							     	if( !record.SAMPLE_ORDER_TIME ) return "" ;
									return "下单时间："+record.SAMPLE_ORDER_TIME+"<br/>到达时间："+record.SAMPLE_ARRIVE_TIME+"<br/>样品报价："+record.SAMPLE_PRICE ;
						     }},
							 
				           	{align:"left",key:"NUM1",label:"报价",width:"15%",format:function(val,record){
				           			var prices = [] ;
				           			if(record.NUM1)prices.push("报价1："+record.NUM1+"/"+record.OFFER1+"/"+getShipFeeType(record.NUM1_SHIP_FEE)) ;
				           			if(record.NUM2)prices.push("报价2："+record.NUM2+"/"+record.OFFER2+"/"+getShipFeeType(record.NUM2_SHIP_FEE)) ;
				           			if(record.NUM3)prices.push("报价3："+record.NUM3+"/"+record.OFFER3+"/"+getShipFeeType(record.NUM3_SHIP_FEE)) ;
				           			
									return prices.join("<br/>");
					        }},
						   {align:"center",key:"WEIGHT",label:"产品重量(kg)",width:"6%",forzen:false,align:"left"},
					           	{align:"center",key:"PRODUCT_SIZE",label:"产品尺寸(cm)",width:"10%",format:function(val,record){
						           	if(!record.PRODUCT_LENGTH) return "-" ;
										return record.PRODUCT_LENGTH+"*"+record.PRODUCT_WIDTH+"*"+record.PRODUCT_HEIGHT ;
							 }},
						     {align:"center",key:"URL",label:"产品网址",width:"10%",forzen:false,align:"left",format:function(val,record){
									return "<a href='"+val+"' target='_blank'>"+val+"<a>" ;
						     }},
				           	{align:"center",key:"CYCLE",label:"生产周期",width:"6%",format:{type:"cycle"}},
				           	{align:"center",key:"PACKAGE",label:"包装方式",width:"6%",format:{type:"package"}},
				        	{align:"center",key:"PACKINGS_PECIFICATIONS",label:"装箱规格",width:"6%"}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:30,
				 pageSizes:[10,20,30,40],
				 height:function(){
					return $(window).height() - 130 ;
				 },
				 title:"",
				 indexColumn:true,
				 querys:querys,
				 loadMsg:"数据加载中，请稍候......",
				 loadAfter:function(){
				 	$(".grid-checkbox").each(function(){
						var val = $(this).attr("value") ;
						if( $(".product-list ul li[asin='"+val+"']").length ){
							$(this).attr("checked",true) ;
						}
					}) ;

					$(".process-action").click(function(){
						var record = $(this).parents("tr:first").data("record");
						openCenterWindow(contextPath+"<?php echo $url;?>/"+record.ID+"/"+taskId+"/pop",850,600,function(){
							setTimeout(function(){
								$(".grid-content-details").llygrid("reload",{},true);
							},300) ;
							}) ;
					}) ;
				 }
			}) ;

			$(".save-xj").click(function(){
				$("#supifr")[0].contentWindow.saveXj();
			}) ;

			$(".supplier-select").click(function(){
				if( $("#supifr").is(":visible") ){
					 $("#supifr").hide() ;
					 $(".save-xj").hide() ;
					 $(this).html("添加询价") ;
				}else{
					$(this).html("收起询价") ;
					 $("#supifr").show() ;
					 $(".save-xj").show() ;
				}
				/*
				openCenterWindow(contextPath+"<?php echo $url;?>",800,600,function(){
					$(".grid-content-details").llygrid("reload",{},true);
					}) ;
				*/
			}) ;

			 $("#supifr").attr("src",contextPath+"<?php echo $url;?>") ;
   	 });

	 function xjSuccess(){
		 $("#supifr").hide() ;
		 $("#supifr").attr("src",contextPath+"<?php echo $url;?>") ;
		 $(".save-xj").hide() ;
		 $(this).html("添加询价") ;
		 $(".grid-content-details").llygrid("reload",{},true);
	}

   </script>
   
   <style>
   		*{
   			font:12px "微软雅黑";
   		}
   		
		.lly-grid .lly-grid-row td.lly-grid-body-column {
				height: 52px;
				padding: 0 2px 0 2px;
				border-left: none;
				border-top: none;
				font-weight: normal;
				border-color: #A8CFEB;
				border-right: 1px solid #A8CFEB;
				position: relative;
		}
   </style>

</head>
<body>

	<div class="toolbar toolbar-auto">
				<table style="width:100%;" class="query-table">	
					<tr>
						<td>
						<?php if($PD_INQUIRY){ ?>
							<button class="supplier-select no-disabled btn">添加询价</button>
							<button class="save-xj btn btn-primary hide">保存询价</button>
							<?php }?>
						</td>
						<td>
							<?php 
			$Config  = ClassRegistry::init("Config") ;
			$websites = $Config->getAmazonConfig("PRODUCT_SEARCH_WEBSITE") ;
		
			echo '<b>相关网址：</b>' ;
			foreach ( explode(",", $websites) as $website ){
				$website = explode("||", $website) ;
				$name = $website[0] ;
				if( isset( $website[1] ) ){
					$url = $website[1] ;
					echo "<a href='$url' target='_blank'>$name</a>&nbsp;&nbsp;&nbsp;" ;
				}
			}
		?>
						</td>
					</tr>						
				</table>	
	 </div>
	 
				<iframe id="supifr" src=""  frameborder="0"  style="width:100%;display:none;height:450px;">
				
				</iframe>
		
	<div class="grid-content-details" style="margin-top:5px;">
	</div>

</body>
</html>
