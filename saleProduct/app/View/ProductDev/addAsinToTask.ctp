<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>产品开发</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
  		 include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('default/style');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('../js/messagebox/jquery.messagebox');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery-ui');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('validator/jquery.validation');
		echo $this->Html->script('messagebox/jquery.messagebox');
		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$security  = ClassRegistry::init("Security") ;
		
		$taskId = $params['arg1'] ;
	?>
  
   <style>
   		*{
   			font:12px "微软雅黑";
   		}
   		
   		.message{
			z-index:1;
   			background: #CCC;
   			 -ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=50)"; /* ie8  */
		    filter:alpha(opacity=50);    /* ie5-7  */
		    -moz-opacity:0.5;    /* old mozilla browser like netscape  */
		    -khtml-opacity: 0.5;    /* for really really old safari */ 
		    opacity: 0.5;   
   			left:0px;
   			top:0px;
   			bottom:0px;
   			right:0px;
   			position:fixed;
   			display:none;
   		}
   		
   		.message >div{
   			position:absolute;;
			display:block;
   			background:#FFF;
   			z-index:2;
   			top:20px;
   		 	-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=100)"; /* ie8  */
		    filter:alpha(opacity=100);    /* ie5-7  */
		    -moz-opacity:1;    /* old mozilla browser like netscape  */
		    -khtml-opacity: 1;    /* for really really old safari */ 
		    opacity: 1;   
   		}
 </style>
 
 <script type="text/javascript">
	$(function(){
			$(".btn-save").click(function(){
				if( !$.validation.validate('#personForm').errorInfo ) {
					if(window.confirm("确认保存吗？")){
						var json = $("#personForm").toJson() ;
						
						$.dataservice("model:ProductDev.addAsinToTask",json,function(result){
							if(result){
								var items = [] ;
								$(result).each(function(){
									var asin = this.split("(")[0] ;
									items.push("<a target='_blank' href='"+contextPath+"/sale/details1/<?php echo $taskId;?>/"+asin+"'>"+this+"</a>") ;
								}) ;
								
								$.messageBox.info({message:"保存完成。<br/>以下产品为正在开发中的产品：<br/>"+items.join("&nbsp;&nbsp;&nbsp;&nbsp;"),callback:function(){
									jQuery.dialogReturnValue(true) ;
									window.close();
								} }) ;
								//alert("保存完成。\n以下产品为正在开发中的产品：\n"+result);
							}else{
								alert("保存成功！");
								jQuery.dialogReturnValue(true) ;
								window.close();
							}
							//jQuery.dialogReturnValue(true) ;
							//window.close();
							//window.location.reload();
						});
					}
				}
				
			}) ;
	}) ;
 </script>
 
</head>
<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page">
		<!-- 页面标题 -->
		<div class="page-title">
			<h2>添加ASIN</h2>
		</div>
		<div class="container-fluid">

	        <form id="personForm" action="#" data-widget="validator,ajaxform" class="form-horizontal" >
				<input type="hidden" id="taskId" value="<?php echo $taskId;?>"/>
				<!-- panel 头部内容  此场景下是隐藏的-->
				<div class="panel apply-panel">
					<!-- panel 中间内容-->
					<div class="panel-content">
						<!-- 数据列表样式 -->
						<table class="form-table" >
							<tbody>	
								<tr>
									<th>ASIN（逗号分隔）：</th>
									<td><textarea id="asins" data-validator="required" style="width:95%;height:200px;"></textarea></td>
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
	
	<div class="message">
		<div></div>
	</div>
</body>

</html>