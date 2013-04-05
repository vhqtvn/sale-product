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
			key:{value:'id',label:'text'},//对应value和label的key
			multi:false ,
			tree:{
				title:"产品分类选择页面",
				method : 'post',
				asyn : true, //异步
				rootId  : 'root',
				rootText : '根节点',
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
});

function uploadSuccess(){
	if(window.opener){
		window.opener.location.reload() ;
		window.close() ;
	}else{
		window.location.reload();
	}
}