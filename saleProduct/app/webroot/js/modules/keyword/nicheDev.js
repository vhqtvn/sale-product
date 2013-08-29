$(function(){
	var tabs = [{label:'基本信息',content:"base-info"}] ;
		tabs.push( {label:'search terms',content:"searchTerm"} ) ;
		tabs.push( {label:'处理轨迹',content:"tracks"} ) ;

	//widget init
	var tab = $('#details_tab').tabs( {
		tabs:tabs 
	} ) ;
	
	$(".grid-track").llygrid({
		columns:[
		    {align:"left",key:"description",label:"内容", width:"51%"},
           	{align:"center",key:"create_date",label:"操作时间",width:"24%" },
            {align:"left",key:"username",label:"操作人",width:"10%" },
         ],
         ds:{type:"url",content:contextPath+"/grid/query"},
		 limit:30,
		 pageSizes:[10,20,30,40],
		 height:function(){
		 	return $(window).height() - 370 ;
		 },
		 title:"",
		 indexColumn:false,
		 querys:{_data:"d_keyword_tracks",keywordId:keywordId},//sql_purchase_plan_details_listForSKU sql_purchase_plan_details_list
		 loadMsg:"数据加载中，请稍候......"
	}) ;
	
	$(".grid-term").llygrid({
		columns:[
		    {align:"left",key:"search_term",label:"内容", width:"51%"},
		    {align:"center",key:"platform",label:"平台",width:"14%" },
           	{align:"center",key:"create_date",label:"更新时间",width:"24%" }
         ],
         ds:{type:"url",content:contextPath+"/grid/query"},
		 limit:30,
		 pageSizes:[10,20,30,40],
		 height:function(){
		 	return $(window).height() - 270 ;
		 },
		 title:"",
		 indexColumn:true,
		 querys:{_data:"d_keyword_terms",keywordId:keywordId},//sql_purchase_plan_details_listForSKU sql_purchase_plan_details_list
		 loadMsg:"数据加载中，请稍候......"
	}) ;
	
	var chargeGridSelect = {
			title:'用户选择页面',
			defaults:[],//默认值
			key:{value:'LOGIN_ID',label:'NAME'},//对应value和label的key
			multi:false,
			width:600,
			height:560,
			grid:{
				title:"用户选择",
				params:{
					sqlId:"sql_user_list_forwarehouse"
				},
				ds:{type:"url",content:contextPath+"/grid/query"},
				pagesize:10,
				columns:[//显示列
					{align:"center",key:"ID",label:"编号",width:"20%"},
					{align:"center",key:"LOGIN_ID",label:"登录ID",sort:true,width:"30%"},
					{align:"center",key:"NAME",label:"用户姓名",sort:true,width:"36%"}
				]
			}
	   } ;
	   
	$(".btn-charger").listselectdialog( chargeGridSelect,function(){
		var args = jQuery.dialogReturnValue() ;
		var value = args.value ;
		var label = args.label ;
		$("#dev_charger").val(value) ;
		$("#dev_charger_name").val(label) ;
		return false;
	}) ;
	
	$(".getSearchTerm").click(function(){
		var json = {} ;
		json.key = keyword ;
		json.keywordId = keywordId ;
		$.dataservice("model:Keyword.getSearchTerm",json,function(result){
			$(".grid-term").llygrid("reload",{},true) ;
		});
	}) ;
}) ;