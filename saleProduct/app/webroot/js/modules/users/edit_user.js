$(function(){
	if( $("#login_id").val()  ){
		$("#login_id").attr("disabled",true) ;
	}
	
	$(".save-user").click(function(){
		if( !$.validation.validate('#personForm').errorInfo ) {
			var json = $("#personForm").toJson() ;
			
			$.dataservice("model:User.saveUser",json,function(result){
					window.opener.openCallback('') ;
					window.close();
			});
		}
	})
	
	$(".password-reset").click(function(){
		if( !$.validation.validate('#personForm').errorInfo ) {
			var json = $("#personForm").toJson() ;
			
			$.dataservice("model:User.passwordReset",json,function(result){
				if(typeof result == 'string'){
					alert(result);
				}else{
					window.location.reload();
				}
			});
		}
	});
	
	
	function loadUserGroup(){
		$.dataservice("sqlId:sql_user_group_list",{userId:userId},function(result){
			$(".group-container").empty() ;
			$(result).each(function(index,item){
				item = item.t ;
				$(".group-container").append("<li  userGroupId='"+item.ID+"'>"+item.GROUP_NAME+"&nbsp;&nbsp;&nbsp;<a href='#' title='删除' class='delete-user-group'><b style='color:red;'>X</b></a></li>") ;
			}) ;
		});
	}
	loadUserGroup();
	
	$(".delete-user-group").live("click",function(){
		var userGroupId = $(this).parent().attr("userGroupId") ;
		if(window.confirm("确认删除该用户组关系吗？")){
			$.dataservice("model:User.editUserGroup",{userGroupId:userGroupId,action:'del'},function(result){
				loadUserGroup() ;
			});
		}
		return false;
	}) ;
	
	
	var chargeGridSelect = {
			title:'用户组选择页面',
			defaults:[],//默认值
			key:{value:'ID',label:'NAME'},//对应value和label的key
			multi:false,
			width:600,
			height:560,
			grid:{
				title:"用户组选择",
				params:{
					sqlId:"sql_security_group_listForUserSelect",
					userId:userId
				},
				ds:{type:"url",content:contextPath+"/grid/query"},
				pagesize:10,
				columns:[//显示列
					{align:"center",key:"ID",label:"用户组编号",width:"20%"},
					{align:"center",key:"CODE",label:"用户组编码",width:"20%"},
					{align:"center",key:"NAME",label:"用户组名称",sort:true,width:"36%"}
				]
			}
	   } ;
	   
	$(".select-group").listselectdialog( chargeGridSelect,function(){
		var args = jQuery.dialogReturnValue() ;
		if(!args) return true;
		var value = args.value[0] ;
		//var label = args.label ;
		$.dataservice("model:User.editUserGroup",{userId:userId,groupId:value,action:'add'},function(result){
			loadUserGroup() ;
		});
		return true;
	}) ;
})