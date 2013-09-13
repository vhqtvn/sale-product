$(function(){
	var isView = window.parent.action == 'view' ;
	
	if( isView ){
		$(":input").attr("disabled",true);
		$("[type='button'],button,[type='file']").hide();
	}

	var categoryTreeSelect = {
			title:'产品分类选择页面',
			valueField:"#categoryId",
			labelField:"#categoryName",
			key:{value:'ID',label:'NAME'},//对应value和label的key
			multi:false ,
			tree:{
				title:"产品分类选择页面",
				method : 'post',
				nodeFormat:function(node){
					node.complete = false ;
				},
				asyn : true, //异步
				rootId  : 'root',
				rootText : '产品分类',
				CommandName : 'sqlId:sql_saleproduct_categorytree',
				recordFormat:true,
				params : {
				}
			}
	   } ;
	   
	$(".select-category").listselectdialog( categoryTreeSelect) ;
	
	var postageTreeGridSelect = {
			title:'物流服务选择页面',
			valueField:"#postageServiceId",
			labelField:"#postageServiceName",
			key:{value:'ID',label:'NAME'},//对应value和label的key
			multi:false ,
			tree:{
				title:"物流商",
				method : 'post',
				asyn : true, //异步
				rootId  : 'root',
				rootText : '物流商',
				gridKey:"vendorId",
				CommandName : 'sqlId:sql_postage_vendor_tree',
				recordFormat:true,
				params : {
				}
			},
			grid:{
				title:"物流服务",
				params:{
					sqlId:"sql_postage_service_grid"
				},
				ds:{type:"url",content:contextPath+"/grid/query"},
				pagesize:10,
				columns:[//显示列
					//{align:"center",key:"ID",label:"编号",width:"100"},
					{align:"left",key:"NAME",label:"名称",width:"15%"},
					{align:"left",key:"CODE",label:"代码",width:"15%"},
					{align:"left",key:"TAG",label:"TAG",width:"15%"},
					{align:"left",key:"COUNTRY",label:"国家",width:"15%"},
					{align:"left",key:"MEMO",label:"备注",width:"27%"}
				]
			}
	   } ;
	   
	$(".select-postage").listselectdialog( postageTreeGridSelect) ;
	
	//init keys
	var val = $("[name='keys']").val() ;
	if( val ){
		$(val.split("||")).each(function(){
			var self = this ;
			$("<li class='alert alert-success key-li' style='position:relative;padding:2px;margin:2px;'>"+this+"</li>").appendTo(".keys-container").mouseenter(function(){
				if(isView) return ;
				$("<a href='#' class='del-key' style='position:absolute;top:0px;right:0px;color:red;'>删除</a>").appendTo( $(this) ).click(function(){
					if( $.trim($(this).parent().text() || $(this).parent().find("input").val()) ){
						if(window.confirm("确认删除？")){
							$(this).parent().remove();
						}
					}
				}) ;
			}).mouseleave(function(){
				$(this).find(".del-key").remove();
			}).dblclick(function(){
				if(isView) return ;
				if( $(this).find("input").length <=0 ){
					var val = self ;
					$("<input class='key-input' type='text' placeHolder='输入关键字' value='"+val+"'/>").appendTo($(this).empty()).focus() ;
				}
			}) ;
		}) ;
	}
	
	$(".key-input").live("blur",function(){
		if(!$(this).val()){
			$(this).parent().remove() ;
		}else
		$(this).parent().html( $(this).val() ) ;
	});

	
	$(".addKey-btn").click(function(event){
		event.stopPropagation(); 
		var _val = "" ;
		$("<li class='alert alert-success key-li' style='position:relative;padding:2px;margin:2px;'><input class='key-input'  type='text' placeHolder='输入关键字'/></li>").appendTo(".keys-container").find("input").focus()
		.parent().mouseenter(function(){
			$("<a href='#' class='del-key' style='position:absolute;top:0px;right:0px;color:red;'>删除</a>").appendTo( $(this) ).click(function(){
				if( $.trim($(this).parent().text() || $(this).parent().find("input").val()) ){
					if(window.confirm("确认删除？")){
						$(this).parent().remove();
					}
				}
			}) ;
		}).mouseleave(function(){
			$(this).find(".del-key").remove();
		}).dblclick(function(){
			if( $(this).find("input").length <=0 ){
				$(this).find(".del-key").remove();
				var val = $.trim($(this).text()) ;
				$("<input class='key-input' type='text' placeHolder='输入关键字' value='"+val+"'/>").appendTo($(this).empty()).focus() ;
			}
		}) ; ; ;
		return false ;
	}) ;
	
	$(".btn-submit").click(function(){
		var keys = [] ;
		$(".keys-container li").each( function(){
			var _keys = $(this).find("input").length?$(this).find("input").val():$(this).text() ;
			keys.push( $.trim(_keys) ) ;
		}) ;
		$("[name='keys']").val(keys.join("||")) ;
		
		if( !$.validation.validate('#personForm').errorInfo ) {
			var json = $("#personForm").toJson() ;
			
			$.dataservice("model:SaleProduct.saveProduct",json,function(result){
				if(result){
					window.resizeTo(980,720) ;
					window.location.href = contextPath+"/saleProduct/details/"+result ;
				}else{
					window.lociton.reload() ;
				}
			});
		}
	}) ;
	
});

function uploadSuccess(){
	if(window.opener){
		window.opener.location.reload() ;
		window.close() ;
	}else{
		window.location.reload();
	}
}