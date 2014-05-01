<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>包装箱编辑</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   		include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('../js/dialog/jquery.dialog');
		echo $this->Html->css('default/style');
		echo $this->Html->css('../js/listselectdialog/jquery.listselectdialog');
		echo $this->Html->css('../js/tab/jquery.ui.tabs');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('grid/query');
		echo $this->Html->script('validator/jquery.validation');	
		echo $this->Html->script('listselectdialog/jquery.listselectdialog');
		echo $this->Html->script('calendar/WdatePicker');
		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$boxId = $params['arg1'] ;
		
		$boxInstance = $SqlUtils->getObject("sql_warehouse_box_getById",array('boxId'=>$boxId)) ;
		
		$inId = $boxInstance['IN_ID'] ;
		
		$warehoseIn = $SqlUtils->getObject("sql_warehouse_in_getById",array("id"=>$inId)) ;
		
		$inSourceType = $warehoseIn['IN_SOURCE_TYPE'] ;
		$sourceWarehouseId = $inSourceType =='warehouse'? $warehoseIn['SOURCE_WAREHOUSE_ID']:"" ;
		
	?>
	<script type="text/javascript">
   	var boxId = '<?php echo $params['arg1'] ;?>' ;	 
   	var sourceWarehouseId = '<?php echo $sourceWarehouseId ;?>'
   </script>
	<script>
	$(function(){
		var isSaved = false ;
		$(".btn-save").click(function(){
			if( !$.validation.validate('#personForm').errorInfo ) {
				if( isSaved ) return ;
				
				var purchaseDetails = formatPurchaseData() ;
				var json = $("#personForm").toJson() ;
				json.purchaseDetails = purchaseDetails ;
				
				isSaved = true ;
				$.dataservice("model:Warehouse.In.doSaveBoxProductNew",json,function(result){
					window.close() ;
				});

			};
			return false ;
		}) ;

		var sqlId = sourceWarehouseId?"sc_warehouse_in_product_select_warehouse":"sc_warehouse_in_product_select_out" ;
		var columns = [//显示列
						{align:"center",key:"REAL_SKU",label:"SKU",sort:true,width:"30%",query:true},
						{align:"center",key:"NAME",label:"名称",sort:true,width:"30%",query:true},
						{align:"center",key:"IMAGE_URL",label:"",sort:true,width:"10%",format:{type:'img'}}
				] ;
		if(sourceWarehouseId){
			columns.push( {align:"center",key:"WAREHOUSE_QUANTITY",label:"仓库库存",sort:true,width:"10%"} ) ;
		}
		//alert( sqlId+"     "+sourceWarehouseId ) ;
		var productGridSelect = {
				title:'货品选择界面',
				defaults:[],//默认值
				key:{value:'ID',label:'REAL_SKU'},//对应value和label的key
				multi:false ,
				width:700,
				height:600,
				grid:{
					title:"用户选择",
					params:{
						sqlId:sqlId,
						sourceWarehouseId : sourceWarehouseId
					},
					ds:{type:"url",content:contextPath+"/grid/query"},
					pagesize:10,
					columns:columns
				}
		   } ;
		   
		$(".btn-product").listselectdialog( productGridSelect,function(){
			var args = jQuery.dialogReturnValue() ;
			var value = args.value ;
			value = value[0] ;
			var label = args.label ;
			label = label[0] ;
			var selectReocrds = args.selectReocrds ;

			var warehouseQuantity = selectReocrds[value]['WAREHOUSE_QUANTITY'];

			/*
			if(warehouseQuantity){
				$("#QUANTITY").val("").attr("placeHolder","数量必须小于最大库存 "+warehouseQuantity+"") ;
				$("#QUANTITY").attr("data-validator","required,range[1,"+warehouseQuantity+"]")
			}*/
			$(".grid-content-details").llygrid("reload",{realId:value}) ;
			
			$("#REAL_PRODUCT_ID").val(value) ;
			$("#SKU").val(label) ;
			$("#NAME").val(selectReocrds[value]['NAME']) ;
			return false;
		}) ;

		$(".grid-content-details").llygrid({
			columns:[
				{align:"center",key:"LISTING_SKU",label:"",width:"3%",format:{type:"checkbox"}},
			    {align:"center",key:"IS_ANALYSIS",label:"供应需求", width:"6%",format:function(val,record){
					var html = [] ;
					if(val == 1){
						html.push('<a href="#" class="analysis" val="'+val+'">'+getImage("success.gif","可计算供应需求")+'</a>&nbsp;') ;
					}else{
						html.push('<a href="#" class="analysis" val="'+val+'">'+getImage("error.gif","不可计算供应需求")+'</a>&nbsp;') ;
					}
					return html.join("") ;
				}},
				{align:"center",key:"IS_RISK",label:"风险", width:"6%",format:function(val,record){
					var html = [] ;
					if(val == 1){
						html.push('<a href="#" class="risk" val="'+val+'">'+getImage("error.gif","存在风险")+'</a>&nbsp;') ;
					}else if(val == 2){
						html.push('<a href="#" class="risk" val="'+val+'">'+getImage("success.gif","不存在风险")+'</a>&nbsp;') ;
					}else{
						html.push('<a href="#" class="risk" title="未设置风险" val="'+val+'">-</a>&nbsp;') ;
					}
					return html.join("") ;
				}},
			 	{align:"center",key:"ACCOUNT_NAME",label:"账号",width:"10%"},
	           	{align:"center",key:"LISTING_SKU",label:"Listing SKU",width:"15%",forzen:false,align:"left",format:function(val,record){
	        		return "<a href='#'  offer-listing='"+record.ASIN+"'>"+val+"</a>" ;
	        	}},
	        	{align:"center",key:"FULFILLMENT_CHANNEL",label:"渠道",width:"10%",forzen:false,align:"left"},
	        	{align:"center",key:"SALES_FOR_THELAST14DAYS",label:"最近14天销售",width:"8%",sort:true },
	        	{align:"center",key:"SALES_FOR_THELAST30DAYS",label:"最近30天销售",width:"8%",sort:true },
	        	{align:"center",key:"TOTAL_SUPPLY_QUANTITY",label:"当前账户库存",width:"8%",sort:true },
	           	{align:"center",key:"PURCHASE_QUANTITY",label:"入库数量",width:"8%",format:function(val,record){
	           		return "<input type='text' class='edit-purchase-quantity' disabled='disabled' value='"+(val||"0")+"' style='width:100%;height:100%;padding:0px;border:none;'/>" ;
	           	}}
	           	/*,
	           	{align:"center",key:"URGENCY",label:"紧急程度",width:"8%",format:function(val,record){
	           		if(currentPlanProduct.P_STATUS == 1 || currentPlanProduct.P_STATUS ==0 ){
	           			return $.llygrid.format.editor.body(val,record,{align:"center",key:"URGENCY",label:"紧急程度",width:"10%",
			           		format:{type:'editor',renderType:'select',data:[{value:'A',text:'A'},{value:'B',text:'B'}]}})  ;
	           		}else{
	           			return val ;
	           		}
	           	}}*/
	         ],
	         ds:{type:"url",content:contextPath+"/grid/query"},
			 limit:20,
			 pageSizes:[20,10,20,30,40],
			 height:function(){
			 	return 130 ;
			 },
			 title:"货品Listing明细",
			// autoWidth:true,
			 indexColumn:false,
			  querys:{sqlId:"sql_supplychain_requirement_plan_product_details_list_ALL",realId:'-'},
			 loadMsg:"数据加载中，请稍候......",
			 loadAfter:function(records){ 
				 $(".grid-content-details").find('.edit-fix-quantity').blur(function(){
					 var record = $(this).closest("tr").data("record") ;
					 var fixQuatity = $(this).val() ;
					 var id = record.ID ;
				 }) ;
			 }
				
		}) ;

		$("[name='cb_LISTING_SKU']").live("click",function(){
			var listingSku = $(this).val() ;
			var checked = $(this).attr("checked") ;
			if(checked){
				$(this).closest("tr").find(".edit-purchase-quantity").removeAttr("disabled") ;
			}else{
				$(this).closest("tr").find(".edit-purchase-quantity").attr("disabled","true") ;
			}
			formatPurchaseData() ;
		}) ;
		
		$(".edit-purchase-quantity").live("keyup",function(){
			formatPurchaseData() ;
		}).live("blur",function(){
			formatPurchaseData() ;
		}) ;
		
		function formatPurchaseData(){
			var data = [] ;
			var purchaseQuantity = 0 ;
			$(".grid-content-details").find(".lly-grid-2-body  tr").each(function(){
				var checked = $(this).find(":checkbox").attr("checked") ;
				if( !checked ) return  ;
				var record = $(this).data("record");
				var pq = parseInt($(this).find(".edit-purchase-quantity").val()||0) ;
				 purchaseQuantity  += pq ;
				 data.push({sku:record.LISTING_SKU,accountId:record.ACCOUNT_ID,
					 quantity:pq,
					 asin:record.ASIN,
					 fulfillment:record.FULFILLMENT_CHANNEL,
					 supplyQuantity:record.TOTAL_SUPPLY_QUANTITY||'0'}) ;
			 }) ;
			//$("#QUANTITY").val(purchaseQuantity||"") ;
			return data ;
		}
	});
   </script>
</head>

<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page">
		<!-- 页面标题 -->
	
		<div class="container-fluid">

	        <form id="personForm" action="#" data-widget="validator" class="form-horizontal" >
	        <input type="hidden" id="id" value="<?php echo $result['ID'];?>"/>
	        <input type="hidden" id="BOX_ID" value="<?php echo $params['arg1'] ;?>"/>
				<!-- panel 头部内容  此场景下是隐藏的-->
				<div class="panel apply-panel">
					<!-- panel 中间内容-->
					<div class="panel-content">
						<!-- 数据列表样式 -->
						<table class="form-table " >
							<tbody>	
								<tr>
									<th>货品SKU：</th>
									<td>
									    <input data-validator="required"  type="hidden" id="REAL_PRODUCT_ID"
										      value="<?php echo $result['REAL_PRODUCT_ID'];?>"/>
									    <input data-validator="required" readonly type="text" id="SKU"
										      value="<?php echo $result['SKU'];?>"/>
										<button class="btn btn-product">选择</button>
									</td>
								</tr>									   
								<tr>
									<th>货品名称：</th>
									<td><input type="text" data-validator="required" readonly id="NAME" value="<?php echo $result['NAME'];?>"/></td>
								</tr>
								<?php /*
								<tr>
									<th>入库类型：</th><td>
									普通库存<input type="radio"  name="inventoryType"   data-validator="required" 
									<?php echo ($result['INVENTORY_TYPE']==1)?"checked":"" ?>
									value="1" style="margin-top:1px;"/>  
									FBA库存<input type="radio"   name="inventoryType"   data-validator="required" 
									<?php echo ($result['INVENTORY_TYPE']== 2)?"checked":"" ?>
									value="2"  style="margin-top:1px;"/>
									</td>
								</tr>
								
								<tr>
									<th>Listing SKU：</th><td><input data-validator="required" type="text" id=LISTING_SKU  disabled
										value="<?php echo $result['LISTING_SKU'];?>"/></td>
								</tr>
								<tr>
									<th>货品数量：</th><td><input data-validator="required" type="text" id="QUANTITY"  disabled
										value="<?php echo $result['QUANTITY'];?>"/></td>
								</tr>
								*/?>
								<tr>
									<th>供货时间：</th><td><input data-validator="required"  data-widget="calendar" 
										type="text" id="DELIVERY_TIME"
										value="<?php echo $result['DELIVERY_TIME'];?>"/></td>
								</tr>
								<?php /*
								<tr>
									<th>货品跟踪码：</th><td><input data-validator="required" type="text" id="PRODUCT_TRACKCODE"
										value="<?php echo $result['PRODUCT_TRACKCODE'];?>"/></td>
								</tr>
								*/ ?>
						
								<tr>
									<th>备注：</th><td>
										<textarea name="MEMO" id="MEMO" style="width:90%;height:100px;"><?php echo $result['MEMO'];?></textarea>
									</td>
								</tr>
							</tbody>
						</table>
						<div class="grid-content-details"  ></div>
						</div>
					</div>
					
					<!-- panel脚部内容-->
                    <div class="panel-foot">
						<div class="form-actions">
							<button type="button" class="btn btn-primary btn-save">提&nbsp;交</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</body>
</html>