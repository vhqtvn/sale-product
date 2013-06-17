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

   		/*
   		$(".save-product").live("click",function(){
   			var name  = $("[name='name']").val() ;
   			var asins = $("[name='asins']").val() ;
   			if(!name || !asins){
   				alert("名称和产品都必须输入！");
   				return false ;
   			}else{
   				$(this).parents("form").submit();
   			}
   		});*/
   		
   $(function(){
  		var tab = $('#tabs-default').tabs( {
			tabs:[
				{label:'附件上传',content:"attachment"},
				{label:'文本输入',content:"inputs"}
			] ,
			height:'200px'
		} ) ;
  	})
   		
   </script>
   

   
</head>
<body>
<div id="tabs-default" class="view-source">
	</div>
	
  <div id="attachment" class="attachment-area tab-content" style="display:block">
	  <form action="<?php echo $contextPath;?>/gatherUpload/uploadAsins" method="post" 
	  		data-widget="validator"
	  		target="form-target" enctype="multipart/form-data">
	  	<input name="groupId" value='<?php echo $id ;?>' type="hidden"/>
	   <table class="table table-bordered">
	    <tr>
	     <th>任务组</th>
	     <td><?php echo $text ;?></td>
	    </tr>
	    <tr>
			<th>所属平台</th>
			<td>
			<select name="platformId"  data-validator="required"  class="input 10-input" >
				<option value="">--选择平台--</option>
				<?php 
					$SqlUtils  = ClassRegistry::init("SqlUtils") ;
					$strategys = $SqlUtils->exeSql("sql_platform_list",array()) ;
					foreach( $strategys as $s){
						$s = $SqlUtils->formatObject($s) ;
						$selected = '' ;
						if( $s['ID'] == $account[0]['sc_amazon_account']['PLATFORM_ID'] ){
							$selected = "selected" ;
						}
						echo "<option $selected value='".$s['ID']."'>".$s['NAME']."</option>" ;
					}
				?>
				</select>
			</td>
		</tr>
	    <tr>
	     <th width=10% nowrap>产品附件</th>
	     <td><input name="productFile" data-validator="required"  type="file"/></td>
	    </tr>
	    <tr> 
	     <td colSpan=2 align=center><input type="submit" class="btn btn-primary" value="上传产品"></td> 
	    </tr>
	   </table>
	   </form>
	   <iframe style="width:0; height:0; border:0;display:none;" name="form-target"></iframe>
   </div>
   
   <div id="inputs" class="inputin-area tab-content">
	  <form action="<?php echo $contextPath;?>/gatherUpload/inputAsins" method="post" target="form-target" data-widget="validator">
	   <input name="groupId" value='<?php echo $id ;?>' type="hidden"/>
	   <table class="table table-bordered">
	    <tr>
	     <th>任务组</th>
	     <td><?php echo $text ;?></td>
	    </tr>
	    <tr>
			<th>所属平台</th>
			<td>
			<select name="platformId"  data-validator="required"  class="input 10-input" >
				<option value="">--选择平台--</option>
				<?php 
					$SqlUtils  = ClassRegistry::init("SqlUtils") ;
					$strategys = $SqlUtils->exeSql("sql_platform_list",array()) ;
					foreach( $strategys as $s){
						$s = $SqlUtils->formatObject($s) ;
						$selected = '' ;
						if( $s['ID'] == $account[0]['sc_amazon_account']['PLATFORM_ID'] ){
							$selected = "selected" ;
						}
						echo "<option $selected value='".$s['ID']."'>".$s['NAME']."</option>" ;
					}
				?>
				</select>
			</td>
		</tr>
	    <tr>
	     <th width=10% nowrap>产品名称</th>
	     <td><input name="name" data-validator="required"  type="text"/></td>
	    </tr>
	    <tr>
	     <th width=10% nowrap>产品ASIN(逗号分隔):</th>
	     <td><textarea data-validator="required"  style="width:350px;height:200px;" name="asins"></textarea></td>
	    </tr>
	    <tr> 
	     <td colSpan=2 align=center><input type="submit" class="btn btn-primary" value="保存产品"></td> 
	    </tr>
	   </table>
	   </form>
   </div>
</body>

</html>