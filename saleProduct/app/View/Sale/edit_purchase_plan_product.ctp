<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title></title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   		/*
		echo $this->Html->meta('icon');
		echo $this->Html->css('../grid/redmond/ui');
		echo $this->Html->css('../grid/grid');
		
		echo $this->Html->css('style-all');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('../grid/query');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('../grid/grid');
		*/
   include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('validator/jquery.validation');
		
		$id = $product[0]['sc_purchase_plan_details']["ID"] ;
		$asin = $product[0]['sc_purchase_plan_details']["ASIN"] ;
		$title = $product[0]['sc_product']["TITLE"] ;
		$planId = $product[0]['sc_purchase_plan_details']["PLAN_ID"] ;
		
		$cost = $product[0]['sc_purchase_plan_details']["COST"] ;
		$plan_num = $product[0]['sc_purchase_plan_details']["PLAN_NUM"] ;
		$quote_price = $product[0]['sc_purchase_plan_details']["QUOTE_PRICE"] ;
		$providor = $product[0]['sc_purchase_plan_details']["PROVIDOR"] ;
		$sample_code = $product[0]['sc_purchase_plan_details']["SAMPLE_CODE"] ;
		$sample  = $product[0]['sc_purchase_plan_details']["SAMPLE"] ;
		$area = $product[0]['sc_purchase_plan_details']["AREA"] ;
		
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

   <script type="text/javascript">
		$(function(){
			$("button").click(function(){
				var json = $("#personForm").toJson() ;
				$.dataservice("model:Sale.savePurchasePlanProduct",json,function(){
					window.opener.$(".grid-content-details").llygrid("reload",{planId:'<?php echo $planId;?>'}) ;
					window.close() ;
				}) ;
				/*$.ajax({
					type:"post",
					url:contextPath+"/sale/savePurchasePlanProduct",
					data:{
						id:$("#id").val(),
						plan_num:$("#plan_num").val(),
						quote_price:$("#quote_price").val(),
						cost:'',
						providor:$("#providor").val(),
						sample:$("#sample").val(),
						sample_code:$("#sample_code").val(),
						area:$("#area").val(),
						memo:$("#memo").val()
					},
					cache:false,
					dataType:"text",
					success:function(result,status,xhr){
						window.opener.$(".grid-content-details").llygrid("reload",{planId:'<?php echo $planId;?>'}) ;
						window.close() ;
					}
				}); */
			});
			
			$(".edit_supplier").click(function(){
				openCenterWindow(contextPath+"/supplier/listsSelect/<?php echo $asin ;?>",800,600) ;
				return false;
			}) ;
		})
   </script>

</head>


<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page">
		<div class="container-fluid">

	        <form id="personForm" action="#" data-widget="validator,ajaxform" class="form-horizontal" >
	        	<input id="id" type="hidden" value='<?php echo $id ;?>' />
				<!-- panel 头部内容  此场景下是隐藏的-->
				<div class="panel apply-panel">
					<!-- panel 中间内容-->
					<div class="panel-content">
						<!-- 数据列表样式 -->
						<table class="form-table" >
							<caption>采购产品信息</caption>
							<tbody>										   
								<tr>
									<th>编号：</th><td><?php echo $id ;?></td>
								</tr>
								<tr>
									<th>ASIN：</th><td><?php echo $asin ;?></td>
								</tr>
								<tr>
									<th>标题：</th><td><?php echo $title ;?></td>
								</tr>
								<tr>
									<th>采购数量：</th>
									<td><input id="plan_num" type="text" value='<?php echo $plan_num ;?>' /></td>
								</tr>
								<tr>
									<th>采购价：</th>
									<td><input id="quote_price" type="text" value='<?php echo $quote_price ;?>' /></td>
								</tr>
								<tr>
									<th>供应商：</th><td>
									<select id="providor">
										<option value="">--</option>
									<?php
										foreach($supplier as $suppli){
											$temp = "" ;
											if( $suppli['sc_supplier']['ID'] == $providor ){
												$temp = "selected" ;
											}
											echo "<option $temp value='".$suppli['sc_supplier']['ID']."'>".$suppli['sc_supplier']['NAME']."</option>" ;
										}
									?>
									</select>  
									
									<a href="javascript://" class="edit_supplier">编辑产品供应商</a>
									</td>
								</tr>
								<tr>
									<th>样品：</th><td>
									<select id="sample">
										<option value="0" <?php if($sample == 0 ) echo 'selected' ;?>>无</option>
										<option value="1" <?php if($sample == 1 ) echo 'selected' ;?> >准备中</option>
										<option value="2" <?php if($sample == 2 ) echo 'selected' ;?>>有</option>
									</select>
									</td>
								</tr>
								<tr>
									<th>样品编码：</th><td>
									<input type="text" id="sample_code" value='<?php echo $sample_code ;?>' />(位置码+产品码组成，中间以下划线连接)
									</td>
								</tr>
								<tr>
									<th>采购地区：</th><td>
									<select id="area">
										<option value="china" <?php if($area == 'china' ) echo 'selected' ;?>>大陆</option>
										<option value="taiwan" <?php if($area == 'taiwan' ) echo 'selected' ;?> >台湾</option>
										<option value="american" <?php if($area == 'american' ) echo 'selected' ;?>>美国</option>
									</select>
									</td>
								</tr>
								<tr>
									<th>采购原因：</th><td>
									<textarea style="width:500px;height:80px;" id="memo"><?php echo $product[0]['sc_purchase_plan_details']['MEMO'] ;?></textarea>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					
					<!-- panel脚部内容-->
                    <div class="panel-foot">
						<div class="form-actions">
							<button type="button" class="btn btn-primary">提&nbsp;交</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</body>

</html>