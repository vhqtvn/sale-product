var prdtServices={
	baseUrl:"/services/rest",
	query:function(params,callback){
		var url=prdtServices.baseUrl+"/product/product/query?_d="+new Date();
		$.ajax({
			url:url,
			type:"POST",
		    contentType:'application/json',
		    data:JSON.stringify(params),
		    dataType:"json",
			success:function(resp,status,xhr){
				if($.isFunction(callback)){
					callback(resp);
				}
			}
		})
	},
	getPrdtDetail:function(id,callback){
		//var url=prdtServices.baseUrl+"/product/product/"+id+"?_d="+new Date();
		var appDetail = Portal.services.httpproxy("load-app?appId="+id) ;
		callback( appDetail ) ;
		/*$.ajax({
			url:url,
			type:"GET",
		    contentType:'application/json',
		    dataType:"json",
			success:function(resp,status,xhr){
				if($.isFunction(callback)){
					callback(resp);
				}
			}
		})*/
	},
	prdtInstall:function(id,callback){
		//var url=prdtServices.baseUrl+"/product/product/"+id+"/install?_d="+new Date();
		var result=Portal.services.httpproxy("install-app?appId="+id);
		callback(result);
	},
	addToShopCart:function(chargeItemId,buyNum,buyOption,callback){
	    //var url=prdtServices.baseUrl+"/order/shoppingcart/additem?chargeItemId="+chargeItemId+"&_d="+new Date();
	    var orderObj=Portal.services.httpproxy("/addCartItem?chargeItemId="+chargeItemId+"&buyNum="+buyNum,buyOption) ;
        callback(orderObj);
	},
	removeItem:function(itemId,callback){
	    //var url=prdtServices.baseUrl+"/order/shoppingcart/removeItem?orderItemId="+itemId+"&_d="+new Date();
		var item=Portal.services.httpproxy("/removeitem?orderItemId="+itemId);
        callback(item);
	},
	getShopCart:function(callback){
	    //var url=prdtServices.baseUrl+"/order/shoppingcart/get?_d="+new Date();
	    var shopCart=Portal.services.httpproxy("get-shop-cart");
        callback(shopCart);
	},
	createOrderFromShopCart:function(callback){
	    //var url=prdtServices.baseUrl+"/order/order/createfromcart?_d="+new Date();
		var result=Portal.services.httpproxy("create-order-from-shopcart",null,null,function(){
                alert("创建订单失败");
            });
		callback(result);
        /*$.ajax({
            url:url,
            type:"GET",
            contentType:'application/json',
            dataType:"json",
            success:function(resp,status,xhr){
                if($.isFunction(callback)){
                    callback(resp);
                }
            },
            error:function(xhr,textStatus,errorThrown){
                alert("创建订单失败");
            }
        })*/
	},
	getUserInfo:function(callback){
		//var url=prdtServices.baseUrl+"/userinfo?_d="+new Date();
		var userinfo=Portal.services.httpproxy("/userinfo");
		callback(userinfo);
        /*$.ajax({
            url:url,
            type:"GET",
            contentType:'application/json',
            dataType:"json",
            success:function(resp,status,xhr){
                if($.isFunction(callback)){
                    callback(resp);
                }
            },
            error:function(xhr,textStatus,errorThrown){
            	if(xhr.status===403){
            		if($("#_ifrLoginSSO").length<1){
						//集成SSO登录
						//$("<iframe id='_ifrLoginSSO' width='0px' height='0px' src='/services/index.jsp?returnUrl="+escape(window.location)+"' style='display:none'></iframe>").appendTo("body");
            		}
					return;
				}
            }
        })*/
	},
	getMyOrder:function(pageSize,page,callback){
			var queryParams = {
					pager : {
						pageSize : pageSize,
						page : page
					}
			};
		//var url=prdtServices.baseUrl+"/order/order/myorders?_d="+new Date();
	    var orders=Portal.services.httpproxy("/myorder",queryParams);
		callback(orders);
	}

}