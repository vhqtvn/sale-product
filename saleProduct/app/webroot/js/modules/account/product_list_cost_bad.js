
	
	var currentCategoryId = "" ;
	var currentCategoryText = "" ;
	$(function(){

			function loadStatis(){
				$.dataservice("sqlId:sql_account_product_cost_bad_statics",{},function(result){
					$(".flow-node").find(".count").html("(0)") ;
					$(result).each(function(index,item){
						item = item.t ;
						$(".flow-node[status='"+item.STATUS+"']").find(".count").html("("+item.COUNT+")") ;
					});
				},{noblock:true}) ;
			}
			loadStatis() ;
	       
	       var gridConfig = {
					columns:[
					    {align:"center",key:"IMAGE_URL",label:"图片",width:"5%",forzen:false,align:"left",format:{type:'img'}},
						{align:"left",key:"REAL_SKU",label:"货品SKU",width:"8%",format:function(val,record){
							return "<a href='#' product-edit='"+record.ID+"'>"+(val||"")+"</a>" ;
						}},
			           	{align:"center",key:"NAME",label:"产品标题",width:"20%",forzen:false,align:"left"},
			           	{align:"center",key:"DECLARATION_NAME",label:" 报关名称",width:"20%",forzen:false,align:"left"},
			           	{align:"center",key:"DECLARATION_PRICE",label:" 报关价格",width:"10%",forzen:false,align:"left"},
			           	{align:"center",key:"PURCHASE_COST",label:"采购成本",width:"10%",forzen:false,align:"left"},
			           	{align:"center",key:"WEIGHT",label:"产品重量",width:"10%",forzen:false,align:"left"},
			         ],
			         ds:{type:"url",content:contextPath+"/grid/query"},
					 limit:30,
					 pageSizes:[15,20,30,40],
					 height:function(){
						 return $(window).height()-170;
					 },
					 title:"",
					 indexColumn:false,
					 querys:{status1:1,sqlId:"sql_account_product_list_cost_bad"},
					 loadMsg:"数据加载中，请稍候......",
					 loadAfter:function(records){
						 $(".grid-content").uiwidget();
						 
						 $realIds = [] ;
							$(records).each(function(){
								this.REAL_ID && $realIds.push(this.REAL_ID) ;
							}) ;
							
					 }
				} ;
	       
			setTimeout(function(){
				$(".grid-content").llygrid(gridConfig) ;
			},200) ;
			
			$(".flow-node").click(function(){
				var baseParams = {"status1":"","status2":"","status3":"","status4":"","status5":"","status6":"","status7":"","status8":"","status9":""} ;
				
				var status = $(this).attr("status");
				baseParams['status'+status] = 1 ;
				$(".flow-node").removeClass("active").addClass("disabled");
				$(this).removeClass("disabled").addClass("active");
				$(".grid-content").llygrid("reload",baseParams,true);
			}) ;
   	 });