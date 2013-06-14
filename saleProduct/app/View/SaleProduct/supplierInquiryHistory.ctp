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
		
		$loginId = $user["GROUP_CODE"] ;//transfer_specialist cashier purchasing_officer general_manager product_specialist
		$sku = $params['arg1'] ;
		$type = $params['arg2'] ;
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
	?>
  
   <script type="text/javascript">
   
   var taskId = '' ;
  
	$(function(){

			var querys = {sqlId:"sql_list_supplierInquiryHistory"} ;
			if( '<?php echo $sku;?>' ){
				querys['realSku'] = '<?php echo $sku;?>' ;
			}else{
				querys['asin'] = '<?php echo $asin;?>' ;
			}
			
			$(".grid-content-details").llygrid({
				columns:[
							//名称	产品重量	生产周期	包装方式	付款方式	产品尺寸	包装尺寸	报价1	报价2	报价3
							{align:"center",key:"CREATE_TIME",label:"询价时间",width:"15%",forzen:false,align:"left"},
							{align:"center",key:"USERNAME",label:"提交人",width:"6%",forzen:false,align:"left"},
							{align:"center",key:"IMAGE",label:"图片",width:"4%",forzen:false,align:"left",format:{type:'img'}},
					     	{align:"center",key:"NAME",label:"供应商名称",width:"15%",forzen:false,align:"left",format:function(val,record){
									return "<a href='#' supplier-id='"+record.SUPPLIER_ID+"'>"+val+"<a>" ;
						     }},
						     {align:"left",key:"EVALUATE",label:"供应商评价",width:"10%",format:{type:'json',content:{1:'不推荐',2:'备选',3:'推荐',4:'优先推荐'}}},
				           	{align:"center",key:"NUM1",label:"报价1",width:"6%",format:function(val,record){
									return val+"/"+record.OFFER1 ;
					          }},
				           	{align:"center",key:"NUM2",label:"报价2",width:"6%",format:function(val,record){
								return val+"/"+record.OFFER2 ;
					          }},
				           	{align:"center",key:"NUM3",label:"报价2",width:"6%",format:function(val,record){
								return val+"/"+record.OFFER3 ;
					          }},
						     {align:"center",key:"URL",label:"产品网址",width:"10%",forzen:false,align:"left",format:function(val,record){
									return "<a href='"+val+"' target='_blank'>"+val+"<a>" ;
						     }},
				           	{align:"center",key:"WEIGHT",label:"产品重量",width:"6%",forzen:false,align:"left"},
				           	{align:"center",key:"CYCLE",label:"生产周期",width:"6%"},
				           	{align:"center",key:"PACKAGE",label:"包装方式",width:"6%"},
				           	{align:"center",key:"PAYMENT",label:"付款方式",width:"6%"},
				           	{align:"center",key:"PRODUCT_SIZE",label:"产品尺寸",width:"6%"},
				           	{align:"center",key:"PACKAGE_SIZE",label:"包装尺寸",width:"6%"},
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
				 }
			}) ;

			$(".supplier-select").click(function(){
				<?php
					$url = "" ; 
					if(empty($sku)){
						$url = "/page/forward/Supplier.updateProductSupplierByAsin/asin/".$asin ;
					}else{
						$url = "/page/forward/Supplier.updateProductSupplierByAsin/sku/".$sku ;
					}
				?>
				openCenterWindow(contextPath+"<?php echo $url;?>",800,600,function(){
					$(".grid-content-details").llygrid("reload",{},true);
					}) ;
			}) ;
   	 });
   </script>
   
   <style>
   		*{
   			font:12px "微软雅黑";
   		}
   </style>

</head>
<body>
	<div class="toolbar toolbar-auto">
				<table style="width:100%;" class="query-table">	
					<tr>
						<td>
							<button class="supplier-select btn">添加询价</button>
						</td>
					</tr>						
				</table>	
	 </div>
		<?php 
			$Config  = ClassRegistry::init("Config") ;
			$websites = $Config->getAmazonConfig("PRODUCT_SEARCH_WEBSITE") ;
		
			echo '<b>相关网址：</b>' ;
			foreach ( explode(",", $websites) as $website ){
				$website = explode("||", $website) ;
				$name = $website[0] ;
				$url = $website[1] ;
				
				echo "<a href='$url' target='_blank'>$name</a>&nbsp;&nbsp;&nbsp;" ;
			}
		?>
	<div class="grid-content-details" style="margin-top:5px;">
	</div>

</body>
</html>
