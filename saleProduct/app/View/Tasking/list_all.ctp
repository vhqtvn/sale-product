<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>llygrid demo</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('style-all');
		echo $this->Html->css('tab/jquery.ui.tabs');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery-ui');
		echo $this->Html->script('../grid/grid');
		echo $this->Html->script('../grid/query');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('tab/jquery.ui.tabs');
		
		$userId  = $_COOKIE["userId"] ; 
		App::import('Model', 'User') ;
		$u = new User() ;
		$user1 = $u->queryUserByUserName($userId) ;
		$user = $user1[0]['sc_user'] ;
		$group=  $user["GROUP_CODE"] ;	
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
 	.p-base{
 		border:1px solid #CCC;
 		padding:3px;
 		margin:3px;
 	}
 	
 	b,table th{
 		font-weight:bold;
 	}

 	.toolbar{
 		background-color:#EEE;
 		width:98%;
 		padding:3px;
 		
 		border:1px solid #CCC;
 		margin-bottom:3px;
 	}
 	
 	.alert{
 		margin-left:-18px;
 		margin-top:20px;
 		margin-bottom:3px;
 		font-weight:bold;
 		text-align:center;
 		padding:3px;
 		color:#000;
 	}
 	
 	.alert .btn{
 		padding-left:5px;
 		padding-right:5px;
 		margin-top:3px;
 	}
 	
 	div.alert-focus{
 		margin-top:2px;
 	}
 	
 	p{
 		text-indent:1em;
 		font-weight:bold;
 	}
 	
 	.description-container{
 		max-height:200px;
 		min-height:50px;
 		overflow:auto;
 	}
 	
 	.p-label{
 		margin:0px 10px;
 		font-weight:bold;
 	}
 </style>
 
</head>
<body style="overflow-y:auto;padding:2px;">
	
	<table class="table table-bordered">
		<tr>
			<th>任务类型</th>
			<th>开始时间</th>
			<th></th>
		</tr>
		<?php foreach( $taskings as $tasking ){
			
			$tasking1 = $tasking['sc_tasking'] ;
			$taskingType = $tasking['sc_tasking_type'] ;
			$type = $taskingType['NAME'] ;
			
			echo "<tr>
				<td>".$type."</td>
				<td>".$tasking1['START_TIME']."</td>
				<td>".$tasking1['ASIN']."</td>
			</tr> " ;
		}?>
		
	</table>
</body>

</html>