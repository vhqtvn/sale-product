<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>llygrid demo</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('calendar/WdatePicker');
	?>
  
   <script type="text/javascript">

	$(function(){
		$(".message,.loading").hide() ;
			$(".grid-content").llygrid({
				columns:[
		           	{align:"center",key:"ID",label:"编号", width:"10%"},
		           	{align:"center",key:"NAME",label:"名称",width:"20%",forzen:false,align:"left",format:function(val,record){
		           		return "<a href='#' class='show-details' val='"+record.ID+"'>"+val+"</a>" ;
		           	}},
		           	{align:"center",key:"CREATE_TIME",label:"上传时间",width:"30%"},
		           	{align:"center",key:"USERNAME",label:"上传用户",width:"30%"}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:400,
				 title:"上传列表",
				 indexColumn:true,
				 querys:{sqlId:"sql_flow_list"},
				 loadMsg:"数据加载中，请稍候......"
			}) ;

	
			$(".show-details").live("click",function(){
				var taskId = $(this).attr("val") ;
				openCenterWindow(contextPath+"/flow/lists/"+taskId,900,600) ;
				return false ;
			}) ;
			
			$( "[name='startTime']" ).datepicker({dateFormat:"yy-mm-dd"});
			$( "[name='endTime']" ).datepicker({dateFormat:"yy-mm-dd"});
			
   	 });

	 function uploadSuccess(id){
		 $("[name='flowFile']").val("") ;
	}
   	 
		
		function validateForm(){
			if( !$("[name='flowFile']").val() ){
				alert("请选择上传文件！");
				return false ;
			}

			if( !$("[name='accountId']").val() ){
				alert("请选择账号！");
				return false ;
			}
			if( !$("[name='startTime']").val() ){
				alert("请选择开始时间！");
				return false ;
			}

			
			if( !$("[name='endTime']").val() ){
				alert("请选择结束时间！");
				return false ;
			}
			
			return true ;
			
		}
   </script>
   
   <style>
   		*{
   			font:12px "微软雅黑";
   		}
   		
   		.message{
   			width:600px;
   			border:1px solid #CCC;
   			overflow:auto;
   			margin:5px;
   			height:200px;
   			background:#000;
   			color:#FFF;
   			margin-bottom:0px;
   		}
   		
   		.loading{
   			width:600px;
   			background:#000;
   			color:#FFF;
   			margin-top:-1px;
   			display:hidden;
   			margin-left:6px;
   		}
   </style>

</head>
<body>

   <div style="border:1px solid #CCC;margin:3px;">
	    <form action="<?php echo $contextPath;?>/taskUpload/doFlowUpload" method="post" target="form-target" enctype="multipart/form-data" onsubmit="return validateForm()">
		   <table border=0 cellPadding=3 cellSpacing=4 >
		    <tr>
		     <td>流量文件：</td>
		     <td><input name="flowFile" type="file" class="span3"/></td>
		     <td>账号：</td>
		     <td>
		     	<select name="accountId" class="span2"   >
							     		<option value="">--选择--</option>
								     	<?php
								     		 $amazonAccount  = ClassRegistry::init("Amazonaccount") ;
							   				 $accounts = $amazonAccount->getAllAccounts(); 
								     		foreach($accounts as $account ){
								     			$account = $account['sc_amazon_account'] ;
								     			$checked = $account['ID'] == $result['ACCOUNT_ID']?"selected":"" ;
								     			echo "<option value='".$account['ID']."'  $checked>".$account['NAME']."</option>" ;
								     		} ;
								     	?>
					</select>
		     </td>
		     <td>开始时间：</td>
		     <td><input name="startTime" type="text" class="span2"  readonly="readonly"  data-widget="calendar"/></td>
		     <td>结束时间：</td>
		     <td><input name="endTime" type="text" class="span2"   readonly="readonly"  data-widget="calendar"/></td> 
		     <td colSpan=2 align=center><input type="submit" class="btn btn-primary" value="上传流量文件"></td> 
		    </tr>
		   </table>
	   </form>
	   <iframe style="width:0; height:0; border:0;display:none;" name="form-target"></iframe>
	</div>  
	<div class="grid-content">
	</div>
	
	<div class="message">
	</div>
	<div class="loading">
		处理中......
	</div>
</body>
</html>
