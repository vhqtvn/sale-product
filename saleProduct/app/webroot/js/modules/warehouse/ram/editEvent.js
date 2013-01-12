	$(function(){
			var isAdd = false ;

			$(".btn-save").click(function(){
				if(isAdd)return ;
				var length = $(".data-row").length ;
				
				if( !$.validation.validate('#personForm').errorInfo ) {
					if(window.confirm("确认保存吗?")){
						isAdd = true ;
						
						var json = $("#personForm").toJson() ;
						json.status = json.status||0 ;
						
						//保存基本信息
						$.dataservice("model:Warehouse.Ram.doSaveEvent",json,function(result){
								window.opener.openCallback('editPlan') ;
								window.close();
						});
					}
				};
				
				return false ;
			}) ;
			
			$(".btn-save-audit").click(function(){
				
				var length = $(".data-row").length ;
				
				if( !$.validation.validate('#personForm').errorInfo ) {
					if(window.confirm("确认保存并提交审批吗?")){
						
						var json = $("#personForm").toJson() ;
						json.status = 1 ;//等待shengpi
						
						//保存基本信息
						$.dataservice("model:Warehouse.Ram.doSaveAndAuditEvent",json,function(result){
								window.opener.openCallback('editPlan') ;
								window.close();
						});
					}
				};
				
				return false ;
			}) ;
			
			
			var orderGridSelect = {
				title:'订单选择',
				labelField:"#orderNo",
				valueField:"#orderId",
				key:{value:'ORDER_ID',label:'ORDER_NUMBER'},//对应value和label的key
				multi:false,
				grid:{
					title:"订单选择",
					params:{
						sqlId:"sql_order_list"
					},
					ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
					pagesize:10,
					columns:[//显示列
						{align:"center",key:"ORDER_ID",label:"订单编号",width:"150",query:true},
						{align:"center",key:"ORDER_NUMBER",label:"系统货号",sort:true,width:"150",query:true},
						{align:"center",key:"PRODUCT_NAME",label:"产品名称",sort:true,width:"150"},
						{align:"center",key:"IMAGE_URL",label:"图片",width:"5%",format:{type:'func',funcName:"window.opener.renderProductImg"}},
						{align:"center",key:"REAL_SKU",label:"货品SKU",sort:true,width:"50"}
					]
				}
		    } ;
		    
		    window.renderProductImg = function(val,record){
	       		if(val){
	       			val = val.replace(/%/g,'%25') ;
	       			return "<img src='/saleProduct/"+val+"' style='width:30px;height:30px;'>" ;
	       		}
	       		return "" ;
	       	}
		   
			$(".btn-order").listselectdialog( orderGridSelect ) ;
   		
   }) ;