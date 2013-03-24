<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>仓库编辑</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('default/style');
		echo $this->Html->css('../js/tab/jquery.ui.tabs');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery-ui');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('validator/jquery.validation');
		echo $this->Html->script('tab/jquery.ui.tabs');
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
			
			$(".update").click(function(){
				if( !$.validation.validate('#personForm').errorInfo ) {
					var json = $("#personForm").toJson() ;
					json.sqlId = "sql_warehouse_update" ;
					$.ajax({
						type:"post",
						url:contextPath+"/form/ajaxSave",
						data:json,
						cache:false,
						dataType:"text",
						success:function(result,status,xhr){
							window.opener.$(".grid-content").llygrid("reload");
							window.location.reload();
						}
					}); 
				}
			}) ;
			
			var tab = $('#tabs-default').tabs( {//$this->layout="index";
				tabs:[
					{label:'基本信息',content:"base-info"},
					{label:'仓库管理员',url:contextPath+"/warehouse/managePage/<?php echo $id;?>",iframe:true},
					{label:'仓库单元',url:contextPath+"/warehouse/unitPage/<?php echo $id;?>",iframe:true}
				] ,
				height:'500px'
			} ) ;
		})
   </script>

</head>

<body class="container-popup">
	<div id="tabs-default"></div>
	<!-- apply 主场景 -->
	<div class="apply-page" id="base-info">
		<!-- 页面标题 -->
		<div class="page-title">
			<h2>仓库基本信息</h2>
		</div>
		<div class="container-fluid">
	        <form id="personForm" action="#" data-widget="validator" class="form-horizontal" >
	        <input type="hidden" id="id" value="<?php echo $id;?>"/>
				<!-- panel 头部内容  此场景下是隐藏的-->
				<div class="panel apply-panel">
					<!-- panel 中间内容-->
					<div class="panel-content">
						<!-- 数据列表样式 -->
						<table class="form-table" >
							<caption>基本信息</caption>
							<tbody>										   
								<tr>
									<th>仓库代码：</th>
									<td><input type="text" id="code" style="width:95%" data-validator="required" value="<?php echo $warehouse['CODE'];?>"/></td>
								</tr>
								<tr>
									<th>仓库名称：</th>
									<td><input type="text" id="name" style="width:95%" data-validator="required" value="<?php echo $warehouse['NAME'];?>"/></td>
								</tr>
								<tr>
									<th>仓库位置：</th>
									<td><input type="text" id="address" style="width:95%" data-validator="required" value="<?php echo $warehouse['ADDRESS'];?>"/></td>
								</tr>
								<tr>
									<th>仓库邮编：</th>
									<td><input type="text" id="zipcode" style="width:95%" value="<?php echo $warehouse['ZIPCODE'];?>"/></td>
								</tr>
								<tr>
									<th>备注：</th><td>
									<textarea name="memo" rows="5"  style="width:95%"><?php echo $warehouse['MEMO'];?></textarea>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					
					<!-- panel脚部内容-->
                    <div class="panel-foot">
						<div class="form-actions ">
							<button type="button update" class="btn btn-primary update">提&nbsp;交</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</body>
</html>