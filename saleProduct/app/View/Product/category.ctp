<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>货品分类</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   		include_once ('config/config.php');

		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/tree/jquery.tree');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('default/style');
		echo $this->Html->css('../js/listselectdialog/jquery.listselectdialog');
		
		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('tree/jquery.tree');
		echo $this->Html->script('validator/jquery.validation');
		echo $this->Html->script('calendar/WdatePicker');
		echo $this->Html->script('listselectdialog/jquery.listselectdialog');

	?>	
	
  
   <style>
   		*{
   			font:12px "微软雅黑";
   		}

		.rule-content-item{
			clear:both;
		}

		.item-label,.item-relation,.item-value,.item-value{
			float:left;
		}
   </style>

   <script>
    var treeData = {id:"root",text:"产品分类",isExpand:true,childNodes:[]} ;
    var treeMap  = {} ;

    <?php
    $Utils  = ClassRegistry::init("Utils") ;
    
    $Utils->echoTreeScript( $categorys ,null, function( $sfs, $index ,$ss ){
    	$id   = $sfs['ID'] ;
    			$name = $sfs['NAME'] ;
    			$pid  = $sfs['PARENT_ID'] ;
    			$charger = $sfs['PURCHASE_CHARGER'] ;
    			$chargerName = $sfs['PURCHASE_CHARGER_NAME'] ;
    			$supply = $sfs['SUPPLY_CHARGER'] ;
    			$supplyName = $sfs['SUPPLY_CHARGER_NAME'] ;
    			$inquiry = $sfs['INQUIRY_CHARGER'] ;
    			$inquiryName = $sfs['INQUIRY_CHARGER_NAME'] ;
    			echo " var item$index = {id:'$id',text:'$name',memo:'".$sfs['MEMO']."',isExpand:true,purchaseCharger:'$charger',purchaseChargerName:'$chargerName',
				supplyCharger:'$supply',supplyChargerName:'$supplyName',inquiryCharger:'$inquiry',inquiryChargerName:'$inquiryName'} ;" ;
    			
    } ) ;
    
    	?>
   
	$(function(){

		$('#default-tree').tree({//tree为容器ID
				source:'array',
				data:treeData ,
				onNodeClick:function(id, text, record,node){
					console.log(node);
					console.log(record) ;
					if(id == 'root'){
						$(".parentName").val("") ;
						$(".parentId").val("") ;
					}else{
						$(".parentName").val(text) ;
						$(".parentId").val(id) ;
					}

					var pid = (record.parent||{}).id ;
					var ptext =  (record.parent||{}).text ;
					pid = pid == 'root'?"":pid ;
					
					$("#up-category .id").val(id) ;
					$("#up-category .parentId").val( pid ) ;
					$("#up-category .parentName").val( ptext ) ;
					$("#up-category .name").val(text) ;
					$("#up-category .memo").val(record.memo) ;
					$("#up-category .purchaseChargerName").val(record.purchaseChargerName) ;
					$("#up-category .purchaseCharger").val(record.purchaseCharger) ;
					$("#up-category .supplyChargerName").val(record.supplyChargerName) ;
					$("#up-category .supplyCharger").val(record.supplyCharger) ;
					$("#up-category .inquiryChargerName").val(record.inquiryChargerName) ;
					$("#up-category .inquiryCharger").val(record.inquiryCharger) ;
				}
           }) ;
           
        $(".save-category").click(function(){
        	var ids = $('#xj-category').toJson() ;
        	
        	if(!ids.name){
        		alert("分类名称不能为空");
        		return ;
        	}

        	$.dataservice("model:Product.saveCategory",ids,function(){
				window.location.reload() ;
            }) ;

        }) ;
        
        $(".update-category").click(function(){
        	var ids = $('#up-category').toJson() ;
        	
        	if(!ids.name){
        		alert("分类名称不能为空");
        		return ;
        	}

        	$.dataservice("model:Product.saveCategory",ids,function(){
				window.location.reload() ;
            }) ;
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
		   
		$(".add-on1").listselectdialog( chargeGridSelect,function(){
			var args = jQuery.dialogReturnValue() ;
			var value = args.value ;
			var label = args.label ;
			$("#xj-category .purchaseCharger").val(value) ;
			$("#xj-category .purchaseChargerName").val(label) ;
			return false;
		}) ;

		$(".add-on2").listselectdialog( chargeGridSelect,function(){
			var args = jQuery.dialogReturnValue() ;
			var value = args.value ;
			var label = args.label ;
			$("#up-category .purchaseCharger").val(value) ;
			$("#up-category .purchaseChargerName").val(label) ;
			return false;
		}) ;

		$(".add-on11").listselectdialog( chargeGridSelect,function(){
			var args = jQuery.dialogReturnValue() ;
			var value = args.value ;
			var label = args.label ;
			$("#xj-category .supplyCharger").val(value) ;
			$("#xj-category .supplyChargerName").val(label) ;
			return false;
		}) ;

		$(".add-on22").listselectdialog( chargeGridSelect,function(){
			var args = jQuery.dialogReturnValue() ;
			var value = args.value ;
			var label = args.label ;
			$("#up-category .supplyCharger").val(value) ;
			$("#up-category .supplyChargerName").val(label) ;
			return false;
		}) ;

		$(".add-on11").listselectdialog( chargeGridSelect,function(){
			var args = jQuery.dialogReturnValue() ;
			var value = args.value ;
			var label = args.label ;
			$("#xj-category .inquiryCharger").val(value) ;
			$("#xj-category .inquiryChargerName").val(label) ;
			return false;
		}) ;

		$(".add-on222").listselectdialog( chargeGridSelect,function(){
			var args = jQuery.dialogReturnValue() ;
			var value = args.value ;
			var label = args.label ;
			$("#up-category .inquiryCharger").val(value) ;
			$("#up-category .inquiryChargerName").val(label) ;
			return false;
		}) ;

		$(".del-category").click(function(){
			var id = $("#up-category").find("#id").val() ;
			if(!id) {
				alert("未选择分类") ;
				return ;
			}
				if(window.confirm("确认删除该分类吗?")){
					$.dataservice("model:Product.delCategory",{id:id},function(result){
						if(result){
							result = $.parseJSON(result) ;
							if(result.type == 1){
									alert("该分类存在子分类，请先删除子分类") ;
									return ;
								}else if(result.type == 2){
										if( window.confirm("改分类存在货品关联，确认删除？") ){
											$.dataservice("model:Product.delCategory",{id:id,force:1},function(result){
												window.location.reload() ;
											});
										}
									}
						}else{
								window.location.reload() ;
						}
		            }) ;
				}
		}) ;

		var categoryTreeSelect = {
				title:'产品分类选择页面',
				valueField:"#up-category .parentId",
				labelField:"#up-category .parentName",
				key:{value:'ID',label:'TEXT'},//对应value和label的key
				width:500,
				height:500,
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
		   
		$(".add-on-category").listselectdialog( categoryTreeSelect) ;
		
	})
   </script>

</head>
<body>
<div id='content-default' class='demo' style="padding:10px;">
	<div class="row-fluid">
		<div id="default-tree" class="tree span3" style="padding: 5px; "></div>
		<div class="span4">
			<fieldset id="xj-category">
				<legend>添加下级分类</legend>
				
				<label>上级分类:</label>
				<input type="text" readOnly class="parentName" id="parentName"/>
				<input type="hidden" class="parentId" id="parentId"/>
			
				<label>分类名称:</label>
				<input type="text" class="name" id="name" class="span4"/>
				
				<label>采购负责人:</label>
				<input type="hidden" class="purchaseCharger" id="purchaseCharger" class="span4"/>
				<input type="text" class="purchaseChargerName" id="purchaseChargerName" class="span4"/>
				<button class="btn add-on add-on1">选择用户</button>
				
				<label>供应负责人:</label>
				<input type="hidden" class="supplyCharger" id="supplyCharger" class="span4"/>
				<input type="text" class="supplyChargerName" id="supplyChargerName" class="span4"/>
				<button class="btn add-on add-on11">选择用户</button>
				
				<label>询价负责人:</label>
				<input type="hidden" class="inquiryCharger" id="inquiryCharger" class="span4"/>
				<input type="text" class="inquiryChargerName" id="inquiryChargerName" class="span4"/>
				<button class="btn add-on add-on111">选择用户</button>
				
				<label>分类备注:</label>
				<textarea id="memo" class="memo" style="height:50px;" class="span4"></textarea>
				<br/><br/>
				<button class="btn save-category">保存分类</button>
				
			</fieldset>
		
				
		</div>
		<div class="span4">
			<fieldset id="up-category">
				<legend>修改当前分类 <button class="btn btn-danger  del-category">刪除</button></legend>
				<input type="hidden" class="id" id="id"/>
			
				<label>分类名称:</label>
				<input type="text" class="name" id="name" class="span4"/>
				
				<label>上级分类:</label>
				<input type="text" readOnly class="parentName" id="parentName"/>
				<input type="hidden" class="parentId" id="parentId"/>
				<button class="btn add-on add-on-category">上级分类</button>
				
				<label>采购负责人:</label>
				<input type="hidden" class="purchaseCharger" id="purchaseCharger" class="span4"/>
				<input type="text" class="purchaseChargerName" id="purchaseChargerName" class="span4"/>
				<button class="btn add-on add-on2">选择用户</button>
				
				<label>供应负责人:</label>
				<input type="hidden" class="supplyCharger" id="supplyCharger" class="span4"/>
				<input type="text" class="supplyChargerName" id="supplyChargerName" class="span4"/>
				<button class="btn add-on add-on22">选择用户</button>
				
				<label>询价负责人:</label>
				<input type="hidden" class="inquiryCharger" id="inquiryCharger" class="span4"/>
				<input type="text" class="inquiryChargerName" id="inquiryChargerName" class="span4"/>
				<button class="btn add-on add-on222">选择用户</button>
				
				<label>分类备注:</label>
				<textarea id="memo" class="memo" style="height:50px;" class="span4"></textarea>
				<br/><br/>
				<button class="btn update-category">修改分类</button>
				
			</fieldset>
		</div>
	</div>
	
</div>
</body>
</html>