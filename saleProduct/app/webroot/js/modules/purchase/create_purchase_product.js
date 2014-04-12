$(function(){
	//初始化Tag 标签
	Tags.init( $(".btn-tags") , $(".tag-container") ,$("#tags")) ;
	
	//选择负责人
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
		$("#executor").val(value) ;
		$("#executorName").val(label) ;
		return false;
	}) ;
	
	//选择货品
	var productGridSelect = {
			title:'货品选择页面',
			defaults:[],//默认值
			key:{value:'ID',label:'NAME'},//对应value和label的key
			multi:false,
			width:600,
			height:560,
			grid:{
				title:"货品选择",
				params:{
					sqlId:"sql_purchase_realproduct_select"
				},
				ds:{type:"url",content:contextPath+"/grid/query"},
				pagesize:10,
				columns:[//显示列
					//{align:"center",key:"ID",label:"编号",width:"20%"},
					{align:"center",key:"IMAGE_URL",label:"",sort:true,width:"10%",format:{type:"img"}},
					{align:"center",key:"REAL_SKU",label:"SKU",sort:true,width:"36%",query:true},
					{align:"center",key:"NAME",label:"货品名称",sort:true,width:"36%",query:true}
				]
			}
	   } ;
	   
	$(".btn-real-product").listselectdialog( productGridSelect,function(){
		var args = jQuery.dialogReturnValue() ;
		var value = args.value ;
		var label = args.label ;
		$("#realId").val(value) ;
		$("#realName").val(label) ;
		return false;
	}) ;
	
	$(".btn-save").click(function(){
		if( !$.validation.validate('#personForm').errorInfo ) {
			var json = $(document.body).toJson() ;
			if(window.confirm("确认创建采购单吗？")){
				$.dataservice("model:NewPurchaseService.createNewPurchaseProduct",json,function(result){
					 alert(result);
				 });
			}
		}
	}) ;
}) ;