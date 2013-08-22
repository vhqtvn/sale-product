$(function(){
	$(".plan-grid").llygrid({
		columns:[
		    {align:"left",key:"MEMO",label:"内容", width:"31%"},
           	{align:"center",key:"CREATE_TIME",label:"操作时间",width:"24%" },
            {align:"left",key:"USERNAME",label:"操作人",width:"10%" },
         ],
         ds:{type:"url",content:contextPath+"/grid/query"},
		 limit:30,
		 pageSizes:[10,20,30,40],
		 height:function(){
		 	return 150 ;
		 },
		 title:"",
		 indexColumn:false,
		 querys:{_data :"d_list_plan"},//sql_purchase_plan_details_listForSKU sql_purchase_plan_details_list
		 loadMsg:"数据加载中，请稍候......"
	}) ;
	
	$(".add-plan").click(function(){
		openCenterWindow(contextPath+"/page/forward/Keyword.editPlan",600,400,function(win,ret){
			
		}) ;
	}) ;
}) ;