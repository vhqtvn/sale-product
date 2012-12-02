function formatGridData(data){
		var records = data.record ;
 		var count   = data.count ;
 		
 		count = count[0][0]["count(*)"] ;
 		
		var array = [] ;
		$(records).each(function(){
			var row = {} ;
			for(var o in this){
				var _ = this[o] ;
				for(var o1 in _){
					row[o1] = _[o1] ;
				}
			}
			array.push(row) ;
		}) ;
	
		var ret = {records: array,totalRecord:count } ;
			
		return ret ;
	}
	var currentPickName = '' ;
	$(function(){
			var currentPickId = '' ;
			//拣货单列表
			$("#picked-grid-content").llygrid({
				 columns:[
		           	{align:"center",key:"NAME",label:"名称",width:"20%",forzen:false,align:"left",format:function(val,record){
		           		if(record.STATUS == 1) return "&nbsp;&nbsp;&nbsp;"+val+"<strong>（"+record.TOTAL+"）</strong>" ;
		           		return '<a href="#" class="select-product"  pickId="'+record.ID+'">'+img+'</a>&nbsp;'+val+"<strong>（"+record.TOTAL+"）</strong>" ;
		           	}}
		         ],
		         ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:function(){
				 	return $(window).height() - $(".toolbar-auto").height() -55 ;
				 },
				 autoWidth:true,
				 title:"",
				 pagerType:"simple",
				 querys:{sqlId:"sql_order_picked_list"},
				 loadMsg:"数据加载中，请稍候......",
				 rowClick:function(rowIndex , rowData){
				 	currentPickId = rowData.ID ;
				 	currentPickName = rowData.NAME ;
				 	$(".grid-content").llygrid("reload",{pickId:rowData.ID});
				 	renderBtn() ;
				 }

			}) ;
			
			$(".btn-outwarehouse").click(function(){
				openCenterWindow("/saleProduct/index.php/order/outWarehouse",850,450) ;
			}) ;
			
			$(".action").live("click",function(){
				var id = $(this).attr("val") ;
				if( $(this).hasClass("add") ){
					openCenterWindow("/saleProduct/index.php/order/editPicked",600,400) ;
				} 
				return false ;
			}) ;
			
			$(".action-btn").click(function(){
				var action = $(this).attr("action");
				var checkedRecords = $(".grid-content").llygrid("getSelectedRecords",{key:"ORDER_ID",checked:true},true) ;
				var status = $(this).attr("status");
				
				if( action == 4 ){//打印拣货单
					openCenterWindow("/saleProduct/index.php/order/printPicked/"+currentPickId,950,650) ;
					return ;
				}else if( action == 5 ){//二次分拣
					openCenterWindow("/saleProduct/index.php/order/rePrintPicked/"+currentPickId+"/1",950,550) ;
					return ;
				}else if( action == 6 ){//确认发货
					if( window.confirm("确认发货并更新TN到Amazon？") ){
						$.ajax({
							type:"post",
							url:"/saleProduct/index.php/order/saveTrackNumberToAamazon/"+currentPickId ,
							data:{},
							cache:false,
							dataType:"text",
							success:function(result,status,xhr){
								alert("保存成功!");
								$(".grid-content").llygrid("reload",{},true) ;
							}
						});
					}
					return ;
				}
			}) ;
			
			function renderBtn(){
				if(currentPickId){
					$(".action-can-disabled").removeAttr("disabled");
				}else{
					$(".action-can-disabled").attr("disabled",true);
				}
			}
			
			renderBtn() ;
			
			$(".select-product").live("click",function(){
				var pickId = $(this).attr("pickId") ;
				openCenterWindow("/saleProduct/index.php/order/selectPickedProduct/"+pickId,1000,600) ;
			})

			//var sqlId = "sql_order_doing_list" ;
			var sqlId = "sql_order_list_picked" ;
			//拣货单订单列表
			$(".grid-content").llygrid({
				columns:[
					{align:"left",key:"TRACK_NUMBER",label:"Tracking Number", width:"20%"},
		           	//{align:"center",key:"SHIP_SERVICE_LEVEL",label:"SHIP LEVEL", width:"10%"},
		           	{align:"left",key:"ASIN",label:"ASIN", width:"90",render:function(record){
							if(record.IS_PACKAGE || record.C > 1){
								$(this).find("td").css("background","#EEBBFF") ;
							}
						}
						,format:function(val,record){
			           		var memo = record.MEMO||"" ;
			           		return "<a href='#' class='product-detail' title='"+memo+"' asin='"+val+"' sku='"+record.SKU+"'>"+(val||'')+"</a>" ;
			        }},
			        {align:"left",key:"ORDER_NUMBER",label:"系统货号", width:"10%"},
		           	{align:"left",key:"REAL_SKU",label:"货品SKU", width:"10%"},
			        {align:"left",key:"NAME",label:"货品名称", width:"10%"},
		           	{align:"center",key:"IMAGE_URL",label:"货品图片",width:"4%",format:function(val,record){
		           		if(val){
		           			val = val.replace(/%/g,'%25') ;
		           		}else{
		           			return "" ;
		           		}
		           		return "<img src='/saleProduct/"+val+"' onclick='showImg(this)' style='width:25px;height:25px;'>" ;
		           	}},
		           	{align:"center",key:"ORDER_ID",label:"ORDER_ID", width:"15%"},
		           	{align:"center",key:"ORDER_ITEM_ID",label:"ORDER_ITEM_ID", width:"12%"},
		           	{align:"center",key:"SKU",label:"SKU",sort:true, width:"10%"},
		           	{align:"center",key:"PRODUCT_NAME",label:"PRODUCT_NAME", width:"20%"},
		           	{align:"center",key:"PURCHASE_DATE",label:"PURCHASE_DATE",sort:true, width:"20%"},
		           	{align:"center",key:"PAYMENTS_DATE",label:"PAYMENTS_DATE",sort:true, width:"20%"},
		           	{align:"center",key:"BUYER_EMAIL",label:"BUYER_EMAIL", width:"30%"},
		           	{align:"center",key:"BUYER_NAME",label:"BUYER_NAME", width:"10%"},
		           	{align:"center",key:"BUYER_PHONE_NUMBER",label:"BUYER_PHONE_NUMBER", width:"10%"},
		           	{align:"center",key:"QUANTITY_PURCHASED",label:"QUANTITY_PURCHASED", width:"10%"},
		           	{align:"center",key:"CURRENCY",label:"CURRENCY", width:"10%"},
		           	{align:"center",key:"ITEM_PRICE",label:"ITEM_PRICE", width:"10%"},
		           	{align:"center",key:"ITEM_TAX",label:"ITEM_TAX", width:"10%"}
		         ],
		         ds:{type:"url",content:"/saleProduct/index.php/grid/query/"+accountId},
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:function(){
				 	return $(window).height() - $(".toolbar-auto").height() -185 ;
				 },
				 title:"",
				 indexColumn:false,
				 querys:{sqlId:sqlId,accountId:accountId,status:'',trackNumberNull:"",pickStatus:"9"},
				 loadMsg:"数据加载中，请稍候......"
			}) ;
			
			$(".query").click(function(){
				var json = $(".query-table").toJson() ;
				//if(currentQueryKey)json.sqlId = currentQueryKey ;
				$(".grid-content").llygrid("reload",json,true) ;
			}) ;
   	 });
   	 
   	 var currentQueryKey = "" ;
     //{未审核订单：,合格订单：5，风险订单：2，待退单：3，外购订单：4，加急单：6，特殊单：7}
   	 $(function(){
			var tab = $('#details_tab').tabs( {
				tabs:[
					{label:'处理中',content:"tab-content"},//9
					{label:'拣货完成',content:"tab-content"},//12
					{label:'发货完成',content:"tab-content"},//10
					{label:'异常订单',content:"tab-content"}//10
				] ,
				//height:'500px',
				select:function(event,ui){
					var index = ui.index ;
					renderAction(index);
				}
			} ) ;
		}) ;
   	 
function renderAction(index){
	$(".save-btn").show() ;
	if(index == 0){//拣货中
		$(".save-btn").hide() ;
		$(".grid-content").llygrid("reload",{pickStatus:'9',status:''},true) ;
	}else if(index == 1){//拣货完成
		//$(".save-btn").hide() ;
		$(".grid-content").llygrid("reload",{pickStatus:'12',status:''},true) ;
	}else if(index == 2){//发货完成
		//$(".save-btn").hide() ;
		$(".grid-content").llygrid("reload",{pickStatus:'10',status:''},true) ;
	}else if(index == 3){//发货完成
		//$(".save-btn").hide() ;
		$(".grid-content").llygrid("reload",{pickStatus:'11',status:''},true) ;
	}
}