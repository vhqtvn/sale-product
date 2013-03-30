var downloadId = '' ;

var Page = {
	init: function(){
		$(function(){
			var tab = $('#details_tab').tabs( {
				tabs:[
					{label:'订单处理信息',content:"tab-content"},//9
					{label:'同步的订单处理信息',content:"tab-content"}
				] ,
				//height:'500px',
				select:function(event,ui){
					var index = ui.index ;
					if(index == 0){//拣货中
						$(".grid-content").llygrid("reload",{sqlId:"sql_order_can_do_ship"}) ;
					}else if(index == 1){//拣货完成
						//$(".save-btn").hide() ;
						$(".grid-content").llygrid("reload",{sqlId:"sql_order_has_do_ship"}) ;
					}
				}
			} ) ;
			
			Page.loadDownloadGrid() ;
			Page.loadOrderGrid() ;
			
			$(".download").click(function(){
				Page.download()
			}) ;
			
			$(".toamazon").click(function(){
				Page.asynTn2Amazon() ;
			}) ;
			
			$(".redownload").live("click",function(){
				var row =  $(this).parents("tr:first").data("record") ;
				Page.reDownload(row['ID']) ;
			}) ;
		}) ;
	},
	download:function(){
		window.location.href = contextPath+"/order/doDownloadOrder/"+accountId;
	},
	reDownload:function(id){ //重新下载
		window.location.href = contextPath+"/order/doDownloadOrder/"+accountId+"/"+id;
		return false ;
	},
	asynTn2Amazon:function(){
		if(window.confirm("确认同步到Amazon吗?")){
			$("[name='accountId']").find("option").each(function(){
				var accountId = $(this).attr("value") ;
				if(accountId){
						$.dataservice("model:NOrderService.synTn2Amazon",{accountId:accountId},function(result){
							
						}) ;
				}
			});
		}
		//getAllCount
			
	},
	loadDownloadGrid:function(){
		$("#picked-grid-content").llygrid({
				 columns:[
		           	{align:"center",key:"NAME",label:"名称",width:"20%",forzen:false,align:"left",format:function(val,record){
		           		if(record.STATUS == 1) return "&nbsp;&nbsp;&nbsp;"+val+"<strong></strong>" ;
		           		return '<a href="#" class="redownload">'+img+'</a>&nbsp;'+val+"<strong></strong>" ;
		           	}}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:function(){
				 	return $(window).height() - $(".toolbar-auto").height() -55 ;
				 },
				 autoWidth:true,
				 title:"",
				 pagerType:"simple",
				 querys:{sqlId:"sql_order_download_list",accountId:accountId},
				 loadMsg:"数据加载中，请稍候......",
				 rowClick:function(rowIndex , rowData){
				 	downloadId = rowData.ID ;
				 	$('#details_tab').tabs().active({'index' : 1})
				 	$(".grid-content").llygrid("reload",{sqlId:"sql_order_has_do_ship",downloadId:downloadId}) ;
				 	//renderBtn() ;
				 }

			}) ;
	},
	loadOrderGrid:function(){
		var sqlId = "sql_order_can_do_ship" ;
			//拣货单订单列表
		/*s1.ORDER_ID,
        s1.ORDER_ITEM_ID,
        s2.TRACK_NUMBER, 
		s2.SHIPPING_FEE, 
		s2.TRANSACTION_ID, 
		s2.MAIL_CLASS, 
		s2.POSTMARK_DATE, 
		s2.TRANSACTION_DATETIME, 
		s2.GROUP_CODE, 
		s2.INSURED_VALUE, 
		s2.INSURANCE_FEE, 
		s2.STATUS,
		s2.LENGTH, 
		s2.WIDTH, 
		s2.HEIGHT, 
		s2.BILLED_WEIGHT, 
		s2.ACTUAL_WEIGHT,
		sc_real_product.REAL_SKU,
		sc_real_product.NAME as REAL_NAME,
		sc_real_product.IMAGE_URL*/
			$(".grid-content").llygrid({
				columns:[
			        {align:"left",key:"ORDER_NUMBER",label:"内部订单号", width:"10%"},
			    	{align:"left",key:"ORDER_PRODUCTS",label:"订单货品", width:"10%",format:function(val,record){
			      		val = val||"" ;
			      		var html = [] ;
			      		$( val.split(";") ).each(function(index,item){
			      			var array = item.split("|") ;
			      			item&& html.push("<img src='/"+fileContextPath+""+array[0]+"' style='width:25px;height:25px;'>") ;
			      		})  ;
			      		return html.join("") ;
			      	}},
		           	{align:"center",key:"ORDER_ID",label:"ORDER_ID", width:"20%"},
		           	{align:"center",key:"TRACK_NUMBER",label:"TRACK_NUMBER", width:"20%"},
		           	{align:"center",key:"SHIPPING_FEE",label:"SHIPPING_FEE",sort:true, width:"20%"},
		           	{align:"center",key:"TRANSACTION_ID",label:"TRANSACTION_ID",sort:true, width:"20%"},
		           	{align:"center",key:"MAIL_CLASS",label:"MAIL_CLASS", width:"30%"}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query/"+accountId},
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:function(){
				 	return $(window).height() - $(".toolbar-auto").height() -160 ;
				 },
				 title:"",
				 indexColumn:false,
				 querys:{sqlId:sqlId,accountId:accountId,downloadId:downloadId},
				 loadMsg:"数据加载中，请稍候......"
			}) ;
	}
} ;

Page.init() ;