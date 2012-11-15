<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>货品编辑</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('validator/jquery.validation');

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
		$(function(){
			if( $("#login_id").val()  ){
				$("#login_id").attr("disabled",true) ;
			}
			
			/*$("button").click(function(){
				if( !$.validation.validate('#personForm').errorInfo ) {
					var json = $("#personForm").toJson() ;
					$.ajax({
						type:"post",
						url:"/saleProduct/index.php/saleProduct/saveProduct",
						data:json,
						cache:false,
						dataType:"text",
						success:function(result,status,xhr){
							if(window.opener){
								window.opener.location.reload() ;
								window.close() ;
							}else{
								window.location.reload();
							}
							
						}
					}); 
				};
			})*/
		})
		
		function uploadSuccess(){
			window.location.reload();
		}
   </script>

</head>

<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page">
		<!-- 页面标题 -->
		<div class="page-title">
			<h2>货品编辑</h2>
		</div>
		<div class="container-fluid">

	        <form id="personForm" action="/saleProduct/index.php/saleProduct/saveProduct"
	          method="post" target="form-target"
	         enctype="multipart/form-data" class="form-horizontal" >
	        <input type="hidden" id="id" value=""/>
				<!-- panel 头部内容  此场景下是隐藏的-->
				<div class="panel apply-panel">
					<!-- panel 中间内容-->
					<div class="panel-content">
						<!-- 数据列表样式 -->
						<table class="form-table col2" >
							<caption>基本信息</caption>
							<tbody>	
								<tr>
									<th>SKU：</th><td><input type="text"
										<?php if(!empty($item['REAL_SKU']))echo 'readonly';?>
										 data-validator="required" name="sku" value="<?php echo $item['REAL_SKU']?>"/></td>
								</tr>									   
								<tr>
									<th>名称：</th>
									<td><input type="text" data-validator="required" name="name" value="<?php echo $item['NAME']?>"/></td>
								</tr>
								<tr>
									<th>仓库位置：</th>
									<td><input type="text" name="position" value="<?php echo $item['POSITION']?>"/></td>
								</tr>
								<tr>
									<th>条形码：</th>
									<td><input type="text" name="barcode" value="<?php echo $item['BARCODE']?>"/></td>
								</tr>
								<tr>
									<th>产品图片：</th>
									<td><input type="file" name="imageUrl"/>
									<?php
									if( $item['IMAGE_URL'] ){
										echo "<img src='/saleProduct/".$item['IMAGE_URL']."' style='width:50px;height:40px;'>" ;
									}?>
									</td>
								</tr>
								<tr>
									<th>产品地址：</th>
									<td><input type="text" name="url" value="<?php echo $item['URL']?>"/></td>
								</tr>
							</tbody>
						</table>
					</div>
					
					<!-- panel脚部内容-->
                    <div class="panel-foot">
						<div class="form-actions col2">
							<button type="submit" class="btn btn-primary">提&nbsp;交</button>
						</div>
					</div>
				</div>
			</form>
			 <iframe style="width:0; height:0; border:0;display:none;" name="form-target"></iframe>
		</div>
	</div>
</body>
</html>