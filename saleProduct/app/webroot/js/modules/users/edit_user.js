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
					//window.opener.openCallback('') ;
					//window.close();
			});
		}
	})
})