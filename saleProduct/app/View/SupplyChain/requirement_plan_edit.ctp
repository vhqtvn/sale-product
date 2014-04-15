<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>需求明细</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>
	<script>
		var deleteHtml = "" ;
	</script>
   <?php
		include_once ('config/config.php');
  		include_once ('config/header.php');
  		
		echo $this->Html->script('modules/supplychain/requirement_plan_edit');
		echo $this->Html->css('../js/modules/tag/tagutil');
		echo $this->Html->script('modules/tag/tagutil');
		
		$planId = $params['arg1'] ;
		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		//获取计划
		$sql = "select * from sc_supplychain_requirement_plan where id = '{@#id#}'" ;
		$plan = $SqlUtils->getObject($sql,array('id'=>$planId)) ;
		
		/*if( $plan['STATUS'] == '' || $plan['STATUS'] == 0 ){
			//更新计划到产品
			$Requirement  = ClassRegistry::init("ScRequirement") ;
			//debug($Requirement) ;
			$Requirement->transferPlanItem2Product($planId) ;
		}*/

		$security  = ClassRegistry::init("Security") ;
		
		$user = $this->Session->read("product.sale.user") ;
		$loginId = $user["LOGIN_ID"] ;
		
		$product_add = $security->hasPermission($loginId , 'product_add') ;
		$product_edit = $security->hasPermission($loginId , 'product_edit') ;
		$product_giveup = $security->hasPermission($loginId , 'product_giveup') ;
		$view_giveup_product = $security->hasPermission($loginId , 'view_giveup_product') ;
		$product_stock_quanity_assign = $security->hasPermission($loginId , 'product_stock_quanity_assign') ;
		//销售状态变更权限
		$product_onsale =  $security->hasPermission($loginId , 'product_onsale') ;

		$user = $this->Session->read("product.sale.user") ;
		$loginId = $user["GROUP_CODE"] ;
		if($loginId == 'general_manager'){
		?>
		<script>
			var deleteHtml = "<a href='#' class='action giveup btn'   type=3>删除</a>" ;
		</script>
		<?php
		}
	?>
	
	<script type="text/javascript">
		$product_edit = <?php echo $product_edit?'true':'false';?> ;
		$product_giveup = <?php echo $product_giveup?'true':'false';?> ;
		$view_giveup_product = <?php echo $view_giveup_product?'true':'false';?> ;
		$product_stock_quanity_assign = <?php echo $product_stock_quanity_assign?'true':'false';?> ;
		$product_onsale = <?php echo $product_onsale?'true':'false';?> ;
		var planId = '<?php echo $planId;?>' ;

		function loadTree(){
			$('#default-tree').tree({//tree为容器ID
				//source:'array',
				//data:treeData ,
				rootId  : 'root',
				rootText : '产品分类',
				expandLevel:2,
				asyn:false,
				CommandName : 'sqlId:sql_supplychain_requirement_category',
				recordFormat:true,
				dataFormat:function(data){
					data.push({id:'uncategory',text:'未分类产品',memo:'',isExpand:true});
					return data;
				},
				nodeFormat:function(record){
					if(record.id=='root' ||record.id == 'uncategory') return record ;
					record.text = record.text+"("+record.TOTAL+")"
					return record ;
				},
				params : {
					planId: '<?php echo $planId;?>'
				},
				onNodeClick:function(id,text,record){
					var uncategory = "" ;
					if(id == 'uncategory'){
						id="" ;
						uncategory = 1 ;
					}else{
						uncategory = "" ;
					}
					
					if( id == 'root' ){
						$(".grid-content").llygrid("reload",{categoryId:"",uncategory:uncategory}) ;
					}else{
						$(".grid-content").llygrid("reload",{categoryId:id,uncategory:uncategory}) ;
					}
				}
	       }) ;
		}
		$(function(){
			loadTree() ;
		});
   </script>
   
   <style>
   	.span1_5{
		width:100px;
   	}
   	
   	.track-list {
		position:absolute;
   	    right:30px;
   	    top:320px;
   	    max-height:150px;
   	    border:1px solid #CCC;
   	    background: #FFF;
   	}
   </style>

</head>
<body>
  <div  style="width:100%;height:100%;">
		<div region="center" split="true" border="true"  style="padding:2px;">
			<div class="toolbar toolbar-auto query-bar">
				<table class="query-table">	
					<tr>	
						<th>需求分类:</th>
						<td>
							<select name='reqType'  style="width:100px">
								<option value=''>全部</option>
								<option value='A'>销量需求</option>
								<option value='B'>流量需求</option>
								<option value='C'>成本不完善</option>
								<option value='D'>利润不达标</option>
								<option value='E'>其他需求</option>
							</select>
						</td>
						<th>需求状态:</th>
						<td>
							<select name='status'  style="width:100px">
								<option value=''>全部</option>
								<option value='0'>待审批</option>
								<option value='1'>审批通过</option>
								<option value='3'>采购中</option>
							</select>
						</td>
						<th>销售渠道:</th>
						<td>
							<select name='fulfillmentChannel'  style="width:100px">
								<option value=''>全部</option>
								<option value='AMAZON'>FBA</option>
								<option value='Merchant'>FBM</option>
							</select>
						</td>
						<td>
							<input type="text"  name="searchKey"  placeholder="SKU、NAME" value="" style="width:300px;"/>
 						</td>
						<td>
							<button class="btn btn-primary query query-btn" >查询</button>
						</td>
					</tr>						
				</table>
			</div>
			
			<div class="grid-content"></div>
					
			<div class="row-fluid">
					<div class="span9">
						<div class="grid-content-details"  ></div>
					</div>
					<div class="span3">
						<div style="padding:5px 5px;position:relative;"  class="action-panel">
							<div class="current-product" style="margin:5px;font-weight:bold;"></div>
							<img  src="/<?php echo $fileContextPath;?>/app/webroot/img/tabs.gif"  style="position:absolute;right:0px;top:10px;" alt="查看操作日志"  class="track-img"/>
							<br>
							<button class="btn  save" >保存</button>
							<button class="btn btn-success  save-pass" >审批通过</button>
							<button class="btn btn-danger save-nopass" >审批不通过</button>
							<?php 
								$sql= "SELECT * FROM sc_purchase_plan spp WHERE STATUS = 1 ORDER BY id DESC LIMIT 0,20" ;
								$items = $SqlUtils->exeSqlWithFormat($sql,array()) ;
								
							?>
							<button class="btn btn-primary add-purchaseplan hide" >加入采购计划单</button>
							<br>
							<select style="margin-top:2px;"  class="purchase-plan hide">
								<option value="">-选择采购计划-</option>
								<?php 
								foreach(  $items as $item){
									echo "<option value='".$item['ID']."'>".$item['NAME']."</option>";
								}
								?>
							</select>
							<br>
							<textarea  style="width:95%;height:150px;margin-top:10px;" class="audit-memo" placeHolder="输入备注"></textarea>
						</div>
					</div>
			</div>
			
		</div>
		<div region="west"  split="true" border="true" title="货品分类" style="width:180px;display:none;">
			<div id="tree-wrap">
				<div id="default-tree" class="tree" style="padding: 5px; "></div>
			</div>
		</div>
		
		<div  class="track-list hide">
		</div>
   </div>	
</body>
</html>
