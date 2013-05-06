
 	$(function(){
 		//init input
 		$(".input").attr("disabled","disabled");
 		$("."+pdStatus+"-input").removeAttr("disabled") ;
 		
 		$(".reedit").click(function(){
 			$(this).parents("table:first").find(".input").removeAttr("disabled") ;
 		}) ;
 		
 		
 		var taskSelect = {
				title:'开发任务选择界面',
				defaults:[],//默认值
				key:{value:'ID',label:'NAME'},//对应value和label的key
				multi:false ,
				width:800,
				height:600,
				grid:{
					title:"开发任务",
					params:{
						sqlId:"sql_pdev_filter_forTransfer"
					},
					ds:{type:"url",content:contextPath+"/grid/query"},
					pagesize:10,
					columns:[//显示列
						{align:"center",key:"CODE",label:"任务编码",sort:true,width:"30%",query:true},
						{align:"center",key:"NAME",label:"任务名称",sort:true,width:"40%",query:true}
					]
				},
				tree:{
					title:"开发计划",
					method : 'post',
					asyn : true, //异步
					gridKey:"planId",
					rootId  : 'root',
					rootText : '开发计划',
					CommandName : 'sqlId:sql_pdev_plan_listForLast10',
					recordFormat:true,
					params : {
					}
				}
		   } ;
		   
		$(".transfer-action").listselectdialog( taskSelect,function(){
			//taskId asin
			var args = jQuery.dialogReturnValue() ;
			var _taskId = args.value ;//
			var label = args.label ;
			
			if( _taskId && _taskId.length > 0 ){
				if(window.confirm("确认迁移?")){
					$.dataservice("model:ProductDev.taskProductTransfer",{taskId:taskId,asin:asin,toTaskId:_taskId[0]},function(result){
						window.close() ;
					});
				}
			}
			//var selectReocrds = args.selectReocrds ;
			
			
			
			return false;
		}) ;

 		
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
						sqlId:"sql_saleproduct_list"
					},
					ds:{type:"url",content:contextPath+"/grid/query"},
					pagesize:10,
					columns:[//显示列
						{align:"center",key:"REAL_SKU",label:"SKU",sort:true,width:"30%",query:true},
						{align:"center",key:"NAME",label:"NAME",sort:true,width:"30%",query:true},
						{align:"center",key:"IMAGE_URL",label:"",sort:true,width:"10%",format:{type:'img'}}
					]
				}
		   } ;
		   
		$(".select-real-product").listselectdialog( productGridSelect,function(){
			var args = jQuery.dialogReturnValue() ;
			var value = args.value ;
			var label = args.label ;
			var selectReocrds = args.selectReocrds ;
			
			$("#REAL_PRODUCT_ID").val(value) ;
		
			if( !$.validation.validate('#personForm').errorInfo ) {
					var json = $("#personForm").toJson() ;
					json = $.extend({},json) ;
					json.ASIN = asin ;
					$.dataservice("model:ProductDev.doFlow",json,function(result){
						window.location.reload() ;
					});
			} ;
			
			return false;
		}) ;
 	
 	}) ;
 	
 	
 	$(function(){
			$(".base-gather").click(function(){
				$.ajax({
					type:"post",
					url:contextPath+"/gatherProduct/execute/"+asin,
					data:{},
					cache:false,
					dataType:"text",
					success:function(result,status,xhr){
						alert("获取完成");
						window.location.reload() ;
					}
				}); 
			}) ;
			
			$(".supplier").click(function(){
				openCenterWindow(contextPath+"/supplier/listsSelect/"+asin,800,600) ;
			}) ;
			
			
			$(".category").click(function(){
				openCenterWindow(contextPath+"/product/assignCategory/"+asin,400,500) ;
			}) ;
			

			$("[testStatus]").click(function(){//下架
				
				var testStatus = $(this).attr("testStatus") ;
				
				var _ = $.trim( $(this).text() )  ;
				
				var val = getDescription(_) ;
	 			
				if( window.confirm("确认执行该操作吗？") ){
					$.ajax({
						type:"post",
						url:contextPath+"/sale/productTestStatus" ,
						data:{description:val,asin:asin,testStatus:testStatus},
						cache:false,
						dataType:"text",
						success:function(result,status,xhr){
							window.location.reload() ;
						}
					}); 
				}
				
	 			return false ;
			}) ;
			
			$("[supplier-id]").click(function(){
				var id = $(this).attr("supplier-id") ;
				viewSupplier(id) ;
				return false ;
			}) ;
			
		});
		
		function getDescription(action){
			//return "" ;
			var beforeDes = $("#description_hidden").val();
			var now       = $("#description").val()||"未填写备注信息" ;
			return beforeDes+"<span>【"+username+"】"+new Date().format("yyyy-MM-dd hh:mm:ss") +"("+action+")</span><p><span>"+now+"</span></p>" ;
		}
		
		$(function(){
			var tab = $('#details_tab').tabs( {
				tabs:[
					{label:'基本信息',content:"baseinfo-tab"},
					{label:'竞争信息',content:"competetion-tab"},
					{label:'产品分类',url:contextPath+"/product/assignCategory/"+asin,iframe:true},
					{label:'产品开发',content:"dev-tab"},
					{label:'询价',content:"supplier-tab",iframe:true},
					{label:'开发轨迹',content:"track-tab"}
				] ,
				height:'500px'
			} ) ;
			
			$(".supplier-select").click(function(){
				openCenterWindow(contextPath+"/page/forward/Supplier.updateProductSupplierByAsin/"+asin,800,600,function(){
					window.location.reload();
					}) ;
			}) ;
			
			$(".update-supplier").click(function(){
				var inquiryId = $(this).attr("inquiryId") ;
				openCenterWindow(contextPath+"/page/forward/Supplier.updateProductSupplierByAsin/"+asin+"/"+inquiryId,800,600,function(){
					window.location.reload();
				}) ;
				return false;
			}) ;
			
			$(".grid-track").llygrid({

				columns:[
				    {align:"left",key:"MEMO",label:"内容", width:"31%"},
		           	{align:"center",key:"CREATE_TIME",label:"操作时间",width:"24%" },
		            {align:"left",key:"USERNAME",label:"操作人",width:"10%" },
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:30,
				 pageSizes:[10,20,30,40],
				 height:function(){
				 	return $(window).height() - 370 ;
				 },
				 title:"",
				 indexColumn:false,
				 querys:{asin:asin,taskId:taskId,sqlId:"sql_pdev_listTracks"},//sql_purchase_plan_details_listForSKU sql_purchase_plan_details_list
				 loadMsg:"数据加载中，请稍候......"
			}) ;
		}) ;
		
		Date.prototype.format = function(format){ 
			var o = { 
				"M+" : this.getMonth()+1, //month 
				"d+" : this.getDate(), //day 
				"h+" : this.getHours(), //hour 
				"m+" : this.getMinutes(), //minute 
				"s+" : this.getSeconds(), //second 
				"q+" : Math.floor((this.getMonth()+3)/3), //quarter 
				"S" : this.getMilliseconds() //millisecond 
			} 
			
			if(/(y+)/.test(format)) { 
				format = format.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length)); 
			} 
			
			for(var k in o) { 
				if(new RegExp("("+ k +")").test(format)) { 
					format = format.replace(RegExp.$1, RegExp.$1.length==1 ? o[k] : ("00"+ o[k]).substr((""+ o[k]).length)); 
				} 
			} 
			return format; 
		} 
		