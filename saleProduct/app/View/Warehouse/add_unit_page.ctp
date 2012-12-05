<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>仓库区编辑</title>
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
			
			$("button").click(function(){
				if( !$.validation.validate('#personForm').errorInfo ) {
					var json = $("#personForm").toJson() ;
					if(json.id){
						json.sqlId = "sql_warehouse_unit_update" ;
					}else{
						json.sqlId = "sql_warehouse_unit_insert" ;
					}
					
					$.ajax({
						type:"post",
						url:"/saleProduct/index.php/form/ajaxSave",
						data:json,
						cache:false,
						dataType:"text",
						success:function(result,status,xhr){
							window.opener.location.reload() ;
							window.close() ;
						}
					}); 
				}
			})
		})
   </script>

</head>

<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page">
		<!-- 页面标题 -->
		<div class="page-title">
			<h2>仓库基本信息</h2>
		</div>
		<div class="container-fluid">
	        <form id="personForm" action="#" data-widget="validator" class="form-horizontal" >
	        	<input type="hidden" id="id" value="<?php echo $id;?>"/>
	        	<input type="hidden" id="warehouseId" value="<?php echo $warehouseId;?>"/>
				<!-- panel 头部内容  此场景下是隐藏的-->
				<div class="panel apply-panel">
					<!-- panel 中间内容-->
					<div class="panel-content">
						<!-- 数据列表样式 -->
						<table class="form-table" >
							<caption>基本信息</caption>
							<tbody>										   
								<tr>
									<th>仓储区代码：</th>
									<td><input type="text" id="code" style="width:95%" data-validator="required" 
										value="<?php echo $item['CODE'];?>"/></td>
								</tr>
								<tr>
									<th>行：</th>
									<td><input type="text" id="row" style="width:95%" data-validator="required" value="<?php echo $item['ROW_VALUE'];?>"/></td>
								</tr>
								<tr>
									<th>层：</th>
									<td><input type="text" id="floor" style="width:95%" data-validator="required" value="<?php echo $item['FLOOR_VALUE'];?>"/></td>
								</tr>
								<tr>
									<th>列：</th>
									<td><input type="text" id="column" style="width:95%" data-validator="required" value="<?php echo $item['COLUMN_VALUE'];?>"/></td>
								</tr>
								<tr>
									<th>备注：</th><td>
									<textarea name="memo" rows="5"  style="width:95%"><?php echo $item['MEMO'];?></textarea>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					
					<!-- panel脚部内容-->
                    <div class="panel-foot">
						<div class="form-actions ">
							<button type="button" class="btn btn-primary">提&nbsp;交</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</body>
</html>