$(function(){
	$(".asyn-btn").click(function(){
		if( !$.validation.validate('.asyn-form').errorInfo ) {
			var json = $(".asyn-form").toJson() ;
			var url =contextPath+"/amazon/listOrders/" + json.accountId ;
			url= url +"?LastUpdatedAfter=" + json.LastUpdatedAfter+" 00:00:00" ;
			if( json.LastUpdatedBefore ){
				url= url +"&LastUpdatedBefore=" + json.LastUpdatedBefore+" 23:59:59" ;
			}
			if(window.confirm("确认同步？")){
				$.ajax({
					type:"post",
					url: url ,
					data:{},
					cache:false,
					dataType:"text",
					success:function(result,status,xhr){
						alert("执行结束") ;
					}
				});
			}
		}
	}) ;
}) ;