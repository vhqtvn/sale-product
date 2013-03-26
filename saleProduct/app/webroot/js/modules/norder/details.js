$(function(){
	$(".asyn-details").click(function(){
		 var json = $(".base-table").toJson() ;
		 
		 var url =contextPath+"/amazon/listOrderItems/" + json.accountId+"/"+json.orderId;
		// alert(url);
		 
		 if(window.confirm("确认同步订单明细信息？")){
				$.ajax({
					type:"post",
					url: url ,
					data:{},
					cache:false,
					dataType:"text",
					success:function(result,status,xhr){
						window.location.reload();
					}
				});
			}
		 
	}) ;
}) ;