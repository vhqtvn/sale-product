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
    	    $SqlUtils  = ClassRegistry::init("SqlUtils") ;
    
        	$index = 0 ;
    		foreach( $categorys as $Record ){
    			$sfs = $SqlUtils->formatObject($Record) ;
    			//debug($sfs) ;
    			
    			//$sfs = $Record['sc_product_category']  ;
    			
    			$id   = $sfs['ID'] ;
    			$name = $sfs['NAME'] ;
    			$pid  = $sfs['PARENT_ID'] ;
    			$charger = $sfs['PURCHASE_CHARGER'] ;
    			$chargerName = $sfs['PURCHASE_CHARGER_NAME'] ;
    			echo " var item$index = {id:'$id',text:'$name',memo:'".$sfs['MEMO']."',isExpand:true,purchaseCharger:'$charger',purchaseChargerName:'$chargerName'} ;" ;
    			
    			echo " treeMap['id_$id'] = item$index  ;" ;
    			$index++ ;
    		} ;
    		
    		$index = 0 ;
    		foreach( $categorys as $Record ){
    			$sfs = $SqlUtils->formatObject($Record) ;
    			$id   = $sfs['ID'] ;
    			$name = $sfs['NAME'] ;
    			$pid  = $sfs['PARENT_ID'] ;
    			
    			if(empty($pid)){
    				echo " item$index ['childNodes'] = item$index ['childNodes']||[] ;" ;
    				echo "treeData.childNodes.push( item$index ) ;" ;
    			}else{
    				echo " item$index ['childNodes'] = item$index ['childNodes']||[] ;" ;
    				echo " treeMap['id_$pid'].childNodes = treeMap['id_$pid'].childNodes||[] ;" ;
    				echo " treeMap['id_$pid'].childNodes.push( item$index ) ;" ;
    			}
    			$index++ ;
    		} ;
    		
    	?>
    
    <?php
    	/*$SqlUtils  = ClassRegistry::init("SqlUtils") ;
    
    	$index = 0 ;
		foreach( $categorys as $Record ){
			$sfs = $SqlUtils->formatObject($Record) ;
			//debug($sfs) ;
			
			//$sfs = $Record['sc_product_category']  ;
			
			$id   = $sfs['ID'] ;
			$name = $sfs['NAME'] ;
			$pid  = $sfs['PARENT_ID'] ;
			$charger = $sfs['PURCHASE_CHARGER'] ;
			$chargerName = $sfs['PURCHASE_CHARGER_NAME'] ;
			echo " var item$index = {id:'$id',text:'$name',memo:'".$sfs['MEMO']."',isExpand:true,purchaseCharger:'$charger',purchaseChargerName:'$chargerName'} ;" ;
			
			
			echo " treeMap['id_$id'] = item$index  ;" ;
			if(empty($pid)){
				echo " item$index ['childNodes'] = item$index ['childNodes']||[] ;" ;
				echo "treeData.childNodes.push( item$index ) ;" ;
			}else{
				echo " treeMap['id_$pid'].childNodes.push( item$index ) ;" ;
			}
			$index++ ;
		} ;*/
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

		var categoryTreeSelect = {
				title:'产品分类选择页面',
				valueField:"#up-category .parentId",
				labelField:"#up-category .parentName",
				key:{value:'ID',label:'NAME'},//对应value和label的key
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
				
				<label>分类备注:</label>
				<textarea id="memo" class="memo" style="height:50px;" class="span4"></textarea>
				<br/><br/>
				<button class="btn save-category">保存分类</button>
				
			</fieldset>
		
				
		</div>
		<div class="span4">
			<fieldset id="up-category">
				<legend>修改当前分类</legend>
				<input type="hidden" class="id" id="id"/>
			
				<label>分类名称:</label>
				<input type="text" class="name" id="name" class="span4"/>
				
				<label>上级分类:</label>
				<input type="text" readOnly class="parentName" id="parentName"/>
				<input type="hidden" class="parentId" id="parentId"/>
				<button class="btn add-on add-on-category">选择上级分类</button>
				
				<label>采购负责人:</label>
				<input type="hidden" class="purchaseCharger" id="purchaseCharger" class="span4"/>
				<input type="text" class="purchaseChargerName" id="purchaseChargerName" class="span4"/>
				<button class="btn add-on add-on2">选择用户</button>
				
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