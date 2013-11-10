
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
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:function(){
				 	return $(window).height() - $(".toolbar-auto").height() -55 ;
				 },
				 autoWidth:true,
				 title:"",
				 pagerType:"simple",
				 querys:{sqlId:"sql_sc_order_picked_list"},
				 loadMsg:"数据加载中，请稍候......",
				 rowClick:function(rowIndex , rowData){
				 	currentPickId = rowData.ID ;
				 	currentPickName = rowData.NAME ;
				 	$(".grid-content").llygrid("reload",{pickId:rowData.ID});
				 	renderBtn() ;
				 }

			}) ;
			
			$(".btn-outwarehouse").click(function(){
				openCenterWindow(contextPath+"/order/outWarehouse",850,450) ;
			}) ;
			
			$(".action").live("click",function(){
				var id = $(this).attr("val") ;
				if( $(this).hasClass("add") ){
					openCenterWindow(contextPath+"/order/editPicked",600,400) ;
				} 
				return false ;
			}) ;
			
			$(".action-btn").click(function(){
				var action = $(this).attr("action");
				var checkedRecords = $(".grid-content").llygrid("getSelectedRecords",{key:"ORDER_ID",checked:true},true) ;
				var status = $(this).attr("status");
				
				if( action == 4 ){//打印拣货单
					openCenterWindow(contextPath+"/order/printPicked/"+currentPickId,950,650) ;
					return ;
				}else if( action == 5 ){//二次分拣
					openCenterWindow(contextPath+"/order/rePrintPicked/"+currentPickId+"/1",950,550) ;
					return ;
				}else if( action == 10 ){//导出
					openCenterWindow(contextPath+"/order/exportPicked/"+currentPickId+"/1",950,550) ;
					return ;
				}else if( action == 11 ){//下载Endicia订单数据
					window.location.href= contextPath+"/excel/exportEndiciaOrder/" ;
					//openCenterWindow(contextPath+"/excel/exportEndiciaOrder/",950,550) ;
					return ;
				}else if( action == 6 ){//确认发货
					if( window.confirm("确认发货并更新TN到Amazon？") ){
						$.ajax({
							type:"post",
							url:contextPath+"/order/saveTrackNumberToAamazon/"+currentPickId ,
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
				openCenterWindow(contextPath+"/order/selectPickedProduct/"+pickId,1000,600) ;
			})

			//var sqlId = "sql_order_doing_list" ;
			var sqlId = "sql_sc_order_list_picked" ;
			//拣货单订单列表
			$(".grid-content").llygrid({
				columns:[
					/*{align:"center",key:"ID",label:"操作",width:"6%",format:function(val,record){
							var html = [] ;
							html.push("<a href='#' class='action-update' val='"+val+"'>编辑</a>&nbsp;") ;
							return html.join("") ;
					}},*/
					{align:"center",key:"IMAGE_URL",label:"",width:"4%",format:function(val,record){
		           		if(val){
		           			val = val.replace(/%/g,'%25') ;
		           		}else{
		           			return "" ;
		           		}
		           		return "<img src='/"+fileContextPath+"/"+val+"' onclick='showImg(this)' style='width:25px;height:25px;'>" ;
		           	}},
			        {align:"center",key:"RMA_RESHIP",label:"重发数量", width:"8%",format:function(val,record){
			        	//alert(record.RMA_STATUS+"  "+record.RMA_VALUE+"  "+record.RMA_RESHIP) ;
			        	//if(record.RMA_STATUS==1&& record.RMA_VALUE==10){
			        		return val ;
			        	//}
			        	},render:function(record){
			        		if( record.RMA_RESHIP >0 ){
			        			$(this).find("td[key='RMA_RESHIP']").css("background","red") ;
			        		}
							
						}},
						{align:"center",key:"ORDER_ID",label:"Order Id", width:"15%"},
				      	{align:"left",key:"ORDER_NUMBER",label:"内部订单号", width:"8%"},
				    	{align:"left",key:"ORDER_PRODUCTS",label:"订单货品", width:"10%",format:function(val,record){
				      		val = val||"" ;
				      		var html = [] ;
				      		$( val.split(";") ).each(function(index,item){
				      			var array = item.split("|") ;
				      			item&& html.push("<img src='/"+fileContextPath+""+array[0]+"' style='width:25px;height:25px;'>") ;
				      		})  ;
				      		return html.join("") ;
				      	}},
						{align:"center",key:"SHIPPED_NUM",label:"发货数量", width:"6%"},
						{align:"center",key:"UNSHIPPED_NUM",label:"未发货数量", width:"6%"},
						//{align:"center",key:"RMA_RESHIP",label:"待重发", width:"6%"},
						{align:"center",key:"AMOUNT",label:"金额", width:"4%" ,align:'right'},
						{align:"center",key:"PURCHASE_DATE",label:"Purchase Date",sort:true, width:"15%"},
				    	{align:"center",key:"LAST_UPDATE_DATE",label:"Last Update Date",sort:true, width:"15%"},
						{align:"center",key:"BUYER_EMAIL",label:"BUYER_EMAIL", width:"30%"},
						{align:"center",key:"BUYER_NAME",label:"BUYER_NAME", width:"10%"}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query/"+accountId},
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:function(){
				 	return $(window).height() - $(".toolbar-auto").height() -185 ;
				 },
				 title:"",
				 indexColumn:false,
				 querys:{sqlId:sqlId,accountId:'',status:'',trackNumberNull:"",pickStatus:"9"},
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