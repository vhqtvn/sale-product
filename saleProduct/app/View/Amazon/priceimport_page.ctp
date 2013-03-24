<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>产品采集操作</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../grid/jquery.llygrid');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('../js/tree/jquery.tree');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('../grid/query');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('../grid/jquery.llygrid');
		echo $this->Html->script('validator/jquery.validation');
		echo $this->Html->script('tree/jquery.tree');
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
		
		input{
			width:300px;
		}
   </style>

   <script>
   		var accountId = '<?php echo $accountId;?>' ;
  
		$(".update-state").live("click",function(){
			$(this).addClass("disabled").html("状态获取中.....") ;
			var feedId = $(this).attr("feedSubmissionId") ;
			$.ajax({
				type:"post",
				url:contextPath+"/amazon/getFeedSubmissionResult/"+accountId+"/"+feedId,
				data:{},
				cache:false,
				dataType:"text",
				success:function(result,status,xhr){
					window.location.reload() ;
				}
			}); 
			return false ;
		});
		
		function uploadSuccess(){
			window.location.reload() ;
		}
   </script>

</head>
<body>
	<form action="<?php echo $contextPath;?>/amazonaccount/doUploadAmazonPrice" method="post" target="form-target" enctype="multipart/form-data">
	<table class="table table-bordered">
		<caption>价格更新</caption>
		<tr>
			
			  	<input name="accountId" value='<?php echo $accountId ;?>' type="hidden"/>
			    <td style="width:150px;">价格文件导入</td>
				<td>
					<input name="priceFile" type="file"/>
					<input type="submit" class="btn btn-primary" value="导入">
			 	</td>
		</tr>
		<tr>
			    <td colspan=2 class="alert-info">
			    	文件格式：sku,price作为一行，如: 20998,10.12 ,不需要文件头
			 	</td>
		</tr>
	</table>
	 </form>
	 <iframe style="width:0; height:0; border:0;display:none;" name="form-target"></iframe>

</html>