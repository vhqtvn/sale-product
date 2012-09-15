<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>llygrid demo</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('../grid/redmond/ui');
		echo $this->Html->css('../grid/grid');
		echo $this->Html->css('style-all');

		echo $this->Html->script('jquery');
		echo $this->Html->script('../grid/query');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('../grid/grid');

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
			display:none;
			 border:1px solid #CCC;
			 width:90%;
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
   		
   		$(".save-product").live("click",function(){
   			var name  = $("[name='name']").val() ;
   			var asins = $("[name='asins']").val() ;
   			if(!name || !asins){
   				alert("名称和产品都必须输入！");
   				return false ;
   			}else{
   				$(this).parents("form").submit();
   			}
   		});
   </script>

   
</head>
<body>
  <div class="tab">
  	<div class="attachment active">附件上传</div>
  	<div class="inputin">文本输入</div>
  </div>
 <div style="clear:both;"></div>
  <div class="attachment-area tab-content" style="display:block">
	  <form action="/saleProduct/index.php/task/doUpload" method="post" target="form-target" enctype="multipart/form-data">
	  	<input name="groupId" value='<?php echo $id ;?>' type="hidden"/>
	   <table border=0 cellPadding=3 cellSpacing=4 width=80%>
	    <tr>
	     <td width=10% nowrap>任务组</td>
	     <td><?php echo $text ;?></td>
	    </tr>
	    <tr>
	     <td width=10% nowrap>产品附件</td>
	     <td><input name="productFile" type="file"/></td>
	    </tr>
	    <tr> 
	     <td colSpan=2 align=center><input type="submit" class="btn btn-primary" value="上传产品"></td> 
	    </tr>
	   </table>
	   </form>
	   <iframe style="width:0; height:0; border:0;display:none;" name="form-target"></iframe>
   </div>
   
   <div class="inputin-area tab-content">
	  <form action="/saleProduct/index.php/task/doUploadForInput" method="post" target="form-target">
	   <input name="groupId" value='<?php echo $id ;?>' type="hidden"/>
	   <table border=0 cellPadding=3 cellSpacing=4 width=80%>
	    <tr>
	     <td width=10% nowrap>任务组</td>
	     <td><?php echo $text ;?></td>
	    </tr>
	    <tr>
	     <td width=10% nowrap>产品名称</td>
	     <td><input name="name" type="text"/></td>
	    </tr>
	    <tr>
	     <td width=10% nowrap>产品ASIN(逗号分隔):</td>
	     <td><textarea style="width:350px;height:200px;" name="asins"></textarea></td>
	    </tr>
	    <tr> 
	     <td colSpan=2 align=center><button class="save-product btn btn-primary">保存产品</button></td> 
	    </tr>
	   </table>
	   </form>
   </div>
</body>

</html>