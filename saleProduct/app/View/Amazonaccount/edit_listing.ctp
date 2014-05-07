<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>Listing编辑</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   		include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('default/style');
		echo $this->Html->css('../js/dialog/jquery.dialog');
		echo $this->Html->css('../js/listselectdialog/jquery.listselectdialog');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('dialog/jquery.dialog');
		echo $this->Html->script('validator/jquery.validation');
		echo $this->Html->script('listselectdialog/jquery.listselectdialog');
		
		$listingId = $params['arg1'] ;
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$sql = "select * from sc_amazon_account_product where id ='{@#id#}'" ;
		$u = $SqlUtils->getObject($sql , array("id"=>$listingId)) ;
		
		$sql = "select * from sc_real_product_rel where account_id='{@#ACCOUNT_ID#}' and sku='{@#SKU#}'" ;
		$relProduct = $SqlUtils->getObject($sql,$u) ;
	?>
	<script>
		$(function(){
			$(".save-user").click(function(){
				if( !$.validation.validate('#personForm').errorInfo ) {
					var json = $("#personForm").toJson() ;
					
					$.dataservice("model:Listing.saveListing",json,function(result){
							window.close();
					});
				}
			});

			var productGridSelect = {
					title:'货品选择界面',
					defaults:[],//默认值
					key:{value:'ID',label:'REAL_SKU'},//对应value和label的key
					multi:false ,
					width:700,
					height:600,
					grid:{
						title:"用户选择",
						params:{
							sqlId:"sql_saleproduct_list"
						},
						ds:{type:"url",content:contextPath+"/grid/query"},
						pagesize:10,
						columns:[//显示列
							{align:"center",key:"REAL_SKU",label:"SKU",sort:true,width:"30%",query:true},
							{align:"center",key:"NAME",label:"NAME",sort:true,width:"30%",query:true},
							{align:"center",key:"IMAGE_URL",label:"",sort:true,width:"10%",format:{type:'img'}}
						]
					}
			   } ;
			   
			$(".select-real-product").listselectdialog( productGridSelect,function(){
				var args = jQuery.dialogReturnValue() ;
				if(!args)return ;
				var value = args.value[0] ;
				var label = args.label[0] ;
				var selectReocrds = args.selectReocrds ;
				var record= selectReocrds[value] ;

				var json = {} ;
				json.REAL_SKU = record.REAL_SKU ;
				json.SKU = '<?php echo $u['SKU'] ;?>' ;
				json.ACCOUNT_ID = '<?php echo $u['ACCOUNT_ID'] ;?>' ;
				json.REAL_ID = record.ID ;

				$.dataservice("model:Listing.relationProduct",json,function(result){
						window.location.reload() ;
				});
				return false;
			}) ;
		}) ;

	</script>
  
</head>

<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page">
		<!-- 页面标题 -->
		<div class="container-fluid">

	        <form id="personForm" action="#" data-widget="validator,ajaxform" class="form-horizontal" >
	        	<input type="hidden" id="ID" value="<?php echo $u['ID'];?>"/>
				<!-- panel 头部内容  此场景下是隐藏的-->
				<div class="panel apply-panel">
					<!-- panel 中间内容-->
					<div class="panel-content">
						<!-- 数据列表样式 -->
						<table class="form-table col2" >
							<caption>基本信息</caption>
							<tbody>	
								<tr>
									<th>Listing SKU：</th>
									<td>
									<?php echo  $u['SKU'];?>
									</td>
								</tr>	
								<tr>
									<th>ASIN：</th>
									<td>
									<?php echo  $u['ASIN'];?>
									</td>
								</tr>
								<tr>
									<th>当前价格：</th>
									<td>
										<?php echo $u['PRICE'];?>
									</td>
								</tr>
								<tr>
									<th>关联货品：</th>
									<td>
									<a href='#'  product-realsku='<?php echo $relProduct['REAL_SKU'];?>'><?php echo $relProduct['REAL_SKU'];?></a>
									<button class="btn btn-primary select-real-product">选择货品</button>
									</td>
								</tr>	
								<tr>
									<th>限价：</th>
									<td>
									<input type="text"   id="LIMIT_PRICE" value="<?php echo  $u['LIMIT_PRICE'];?>"
										/>
									</td>
								</tr>					   
								<tr>
									<th>供应周期(天)：</th>
									<td>
									<input type="text"  data-validator="required" id="SUPPLY_CYCLE" value="<?php echo  empty($u['SUPPLY_CYCLE'])?14:$u['SUPPLY_CYCLE']; ?>"/>
									</td>
								</tr>
								<tr>
									<th>需求调整系数：</th>
									<td>
									<input type="text"  data-validator="required" id="REQ_ADJUST" value="<?php echo  empty($u['REQ_ADJUST'])?1:$u['REQ_ADJUST'];?>"
										/>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					
					<!-- panel脚部内容-->
                    <div class="panel-foot">
						<div class="form-actions col2">
							<button type="button" class="btn btn-primary save-user">提&nbsp;交</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</body>
</html>