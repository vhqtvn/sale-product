<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>产品上传</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
  		include_once ('config/config.php');
  		 
		echo $this->Html->meta('icon');
		echo $this->Html->css('default/style');
		echo $this->Html->css('../js/tab/jquery.ui.tabs');
		echo $this->Html->css('../js/validator/jquery.validation');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery-ui');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('tab/jquery.ui.tabs');
		echo $this->Html->script('validator/jquery.validation');	
		
		$keywordId = $params['arg1'] ;
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
		
		.tab div{
			float:left;
			width:100px;
			background:#CCC;
			margin:3px;
			padding:5px;
			font-weight:bolder;
		}
		
		.tab-content{
			padding-top:2px!important;
		}
		
		.tab .active{
			background:#AAEECC;
		}
   </style>
   
   <script>
   		function uploadSuccess(taskId){//uploadSuccess
   			window.opener.startGather(taskId) ;
   			window.close() ;
   		}
   		
   		$(".tab > div").live("click",function(){
   			$(".tab > div").removeClass("active");
   			$(this).addClass("active") ;
   			$(".tab-content").hide() ;
   			if( $(this).hasClass("attachment") ){
   				$(".attachment-area").show() ;
   			}else{
   				$(".inputin-area").show() ;
   			}
   		});


   </script>
   

   
</head>

<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page">
		<!-- 页面标题 -->
		<div class="container-fluid">

	        <form action="<?php echo $contextPath;?>/product/doDocUpload" method="post" 
	  			data-widget="validator"
	  			target="form-target" enctype="multipart/form-data">
	        	<input type="hidden" id="devId" name="devId" value="<?php echo  $params['arg1'];?>"/>
				<!-- panel 头部内容  此场景下是隐藏的-->
				<div class="panel apply-panel">
					<!-- panel 中间内容-->
					<div class="panel-content">
						
						
						<table class="form-table" >
							<caption>资料信息</caption>
							<tbody>
								<tr>
									<th>资料名称</th>
									<td>
										<input type="text"  name="name"  value="" data-validator="required" />
									</td>
								</tr>
								<!-- 
								<tr>
									<th>资料类型</th>
									<td>
										<select  name="type">
												<option value="">--选择类型--</option>
												<option value="text">文本信息</option>
												<option value="file">附件信息</option>
										</select>
									</td>
								</tr>
								 -->
								<tr>
									<th>文本内容</th>
									<td>
										<textarea  name="textContent" style="width:90%;height:250px;"></textarea>
									</td>
								</tr>
								<tr>
									<th>附件内容</th>
									<td><input name="filePath"  type="file"/>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					
					<!-- panel脚部内容-->
                    <div class="panel-foot">
						<div class="form-actions">
							<input type="submit" class="btn btn-primary" value="保存资料">
						</div>
					</div>
				</div>
			</form>
			 <iframe style="width:0; height:0; border:0;display:none;" name="form-target"></iframe>
		</div>
	</div>
</body>

</html>