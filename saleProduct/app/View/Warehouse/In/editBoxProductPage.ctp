<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>包装箱编辑</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('../js/listselectdialog/jquery.listselectdialog');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('grid/query');
		echo $this->Html->script('validator/jquery.validation');	
		echo $this->Html->script('listselectdialog/jquery.listselectdialog');
		echo $this->Html->script('calendar/WdatePicker');
		
	?>
	<script type="text/javascript">
   	var boxId = '<?php echo $params['arg1'] ;?>' ;	 
   </script>
	<script>
	$(function(){
		$(".btn-save").click(function(){
			if( !$.validation.validate('#personForm').errorInfo ) {
				var json = $("#personForm").toJson() ;
				$.dataservice("model:Warehouse.In.doSaveBoxProduct",json,function(result){
					window.opener.openCallback('boxProduct') ;
						window.close();
				});

			};
			return false ;
		}) ;
		
		var productGridSelect = {
				title:'货品选择界面',
				defaults:[],//默认值
				key:{value:'ID',label:'REAL_SKU'},//对应value和label的key
				multi:false ,
				grid:{
					title:"用户选择",
					params:{
						sqlId:"sql_saleproduct_list"
					},
					ds:{type:"url",content:contextPath+"/grid/query"},
					pagesize:10,
					columns:[//显示列
						{align:"center",key:"REAL_SKU",label:"SKU",sort:true,width:"100"},
						{align:"center",key:"NAME",label:"NAME",sort:true,width:"100"}
					]
				}
		   } ;
		   
		$(".btn-product").listselectdialog( productGridSelect,function(){
			var args = jQuery.dialogReturnValue() ;
			var value = args.value ;
			var label = args.label ;
			var selectReocrds = args.selectReocrds ;
			
			$("#REAL_PRODUCT_ID").val(value) ;
			$("#SKU").val(label) ;
			$("#NAME").val(selectReocrds[value]['NAME']) ;
			return false;
		}) ;
	});
   </script>
</head>

<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page">
		<!-- 页面标题 -->
	
		<div class="container-fluid">

	        <form id="personForm" action="#" data-widget="validator" class="form-horizontal" >
	        <input type="hidden" id="id" value="<?php echo $result['ID'];?>"/>
	        <input type="hidden" id="BOX_ID" value="<?php echo $params['arg1'] ;?>"/>
				<!-- panel 头部内容  此场景下是隐藏的-->
				<div class="panel apply-panel">
					<!-- panel 中间内容-->
					<div class="panel-content">
						<!-- 数据列表样式 -->
						<table class="form-table col2" >
							<tbody>	
								<tr>
									<th>货品SKU：</th>
									<td>
									    <input data-validator="required"  type="hidden" id="REAL_PRODUCT_ID"
										      value="<?php echo $result['REAL_PRODUCT_ID'];?>"/>
									    <input data-validator="required" readonly type="text" id="SKU"
										      value="<?php echo $result['SKU'];?>"/>
										<button class="btn btn-product">选择</button>
									</td>
								</tr>									   
								<tr>
									<th>货品名称：</th>
									<td><input type="text" data-validator="required" readonly id="NAME" value="<?php echo $result['NAME'];?>"/></td>
								</tr>
								<tr>
									<th>入库类型：</th><td>
									普通库存<input type="radio"  name="inventoryType"   data-validator="required" 
									<?php echo ($result['INVENTORY_TYPE']==1)?"checked":"" ?>
									value="1" style="margin-top:1px;"/>  
									FBA库存<input type="radio"   name="inventoryType"   data-validator="required" 
									<?php echo ($result['INVENTORY_TYPE']== 2)?"checked":"" ?>
									value="2"  style="margin-top:1px;"/>
									</td>
								</tr>
								<tr>
									<th>货品数量：</th><td><input data-validator="required" type="text" id="QUANTITY"
										value="<?php echo $result['QUANTITY'];?>"/></td>
								</tr>
								<tr>
									<th>供货时间：</th><td><input data-validator="required"  data-widget="calendar" 
										type="text" id="DELIVERY_TIME"
										value="<?php echo $result['DELIVERY_TIME'];?>"/></td>
								</tr>
								<tr>
									<th>货品跟踪码：</th><td><input data-validator="required" type="text" id="PRODUCT_TRACKCODE"
										value="<?php echo $result['PRODUCT_TRACKCODE'];?>"/></td>
								</tr>
					
						
								<tr>
									<th>备注：</th><td>
										<textarea name="MEMO" id="MEMO" style="width:90%;height:100px;"><?php echo $result['MEMO'];?></textarea>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					
					<!-- panel脚部内容-->
                    <div class="panel-foot">
						<div class="form-actions col2">
							<button type="button" class="btn btn-primary btn-save">提&nbsp;交</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</body>
</html>