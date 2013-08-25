$(function(){
	$(".save-niche").click(function(){
		if( !$.validation.validate('#personForm').errorInfo ) {
			var json = $("#personForm").toJson() ;
			$.dataservice("model:Keyword.saveNiceDev",json,function(result){
					$(document.body).dialogReturnValue(true) ;
					window.close();
			});
		}
	}) ;
	
	$(".commit-niche").click(function(){
		if( !$.validation.validate('#personForm').errorInfo ) {
			var json = $("#personForm").toJson() ;
			json.status = 1 ;
			if(window.confirm("确认提交审批？")){
				$.dataservice("model:Keyword.saveNiceDev",json,function(result){
					$(document.body).dialogReturnValue(true) ;
					window.close();
				});
			}
		}
	}) ;
	
	$(".pass-niche").click(function(){
		if( !$.validation.validate('#personForm').errorInfo ) {
			var json = $("#personForm").toJson() ;
			json.status = 2 ;
			if(window.confirm("确认通过审批？")){
				$.dataservice("model:Keyword.saveNiceDev",json,function(result){
						$(document.body).dialogReturnValue(true) ;
						window.close();
				});
			}
		}
	}) ;
	
	$(".discart-niche").click(function(){
		if( !$.validation.validate('#personForm').errorInfo ) {
			var json = $("#personForm").toJson() ;
			json.status = 3 ;
			if(window.confirm("确认废弃？")){
				$.dataservice("model:Keyword.saveNiceDev",json,function(result){
						$(document.body).dialogReturnValue(true) ;
						window.close();
				});
			}
		}
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
	
}) ;