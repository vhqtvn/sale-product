<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Amazon关键字获取</title>
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
		
		$key ="" ;
		$type = "" ;
		if(isset($_GET['key'])){
			$key = $_GET['key'] ;
			$type = $_GET['type'] ;
		}
		
		$content = "[]" ;
		if( !empty($key) ){
			try{
					$key = urlencode( $key ) ;
				
					$url = "" ;
					if($type == 'amazon'){
						$url = "http://completion.amazon.com/search/complete?method=completion&q=$key&search-alias=aps&client=amazon-search-ui&mkt=1" ;
						//urlencode($str)
						$content = file_get_contents( $url ) ;
					}else if($type=="ebay"){
						$url = "http://autosug.ebaystatic.com/autosug?kwd=$key" ;
						
						$content = file_get_contents($url) ;
						$content = str_replace("vjo.darwin.domain.finding.autofill.AutoFill._do", "", $content) ;
					}
					
			}catch(Exception $e){
				echo "获取关键字异常！" ;
			}
		}
	?>
  <script type="text/javascript">
  $content = <?php echo $content;?> ;
  $type = '<?php echo $type;?>' ;
  $(function(){
	  $("#type").val( $type ) ;
	  if( $type == 'amazon' ){
		  if( $content.length >=2 ){
				var key =  $content[0] ;
				var val  = $content[1] ;
				$("#key").val(key) ;
				$(val).each(function(){
					$("#result").append("<li><a target='_blank' href='http://www.amazon.com/s/ref=nb_sb_noss?field-keywords="+this+"'>"+this+"</a></li>") ;
				}) ;
			}
	  }else{
			var key = $content.prefix ;
			$("#key").val(key) ;
			var val  = $content.res.sug ;
			$(val).each(function(){
				$("#result").append("<li><a target='_blank' href='http://www.ebay.com/sch/i.html?&_nkw="+this+"'>"+this+"</a></li>") ;
			}) ;
	  }
		
})
  </script>
   
   <style>
   		*{
   			font:12px "微软雅黑";
   		}
   		
   		ul{
			margin-top:50px;
   		}
   		
   		ul li{
			list-style: none;
   			padding:5px;
   			font-weight:bold;
   		}
   </style>

</head>
<body>
<center>
	
		<form action="Sale.getAmazonSearchKey" method="get" style="margin-top:50px;">
			<select id="type" name="type" style="width:130px;">
					<option value="amazon">Amazon.com</option>
					<option value="ebay">Ebay.com</option>
			</select>
			<input type="text"   placeHolder="获取搜索关键字"  name="key"  id="key"/>
			
			&nbsp;&nbsp;
			<button type="submit" class="btn btn-primary">查询</button>
			<iframe src=""  id="sframe" name="sframe"  style="display:none"></iframe>
		</form>
	<div style="width:350px;text-align:left;">	
		<ul id="result"></ul>
	</div>
</center>
</body>
</html>
