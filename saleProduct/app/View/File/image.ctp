<!DOCTYPE HTML>
<html lang="en">
<head>
	<?php echo $this->Html->charset(); ?>
	
	<title>图片上传</title>
	<meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
  		include_once ('config/config.php');
  		include_once ('config/header.php');
  		
  		echo $this->Html->css('../js/fileupload/css/jquery.fileupload-ui');
  		echo $this->Html->script('fileupload/js/vendor/jquery.ui.widget');
  		echo $this->Html->script('fileupload/js/vendor/tmpl.min');
  		echo $this->Html->script('fileupload/js/vendor/load-image.min');
  		echo $this->Html->script('fileupload/js/vendor/canvas-to-blob.min');
  		echo $this->Html->script('fileupload/js/vendor/bootstrap.min');
  		echo $this->Html->script('fileupload/js/vendor/jquery.blueimp-gallery.min');

		echo $this->Html->script('fileupload/js/jquery.iframe-transport');
		echo $this->Html->script('fileupload/js/jquery.fileupload');
		echo $this->Html->script('fileupload/js/jquery.fileupload-process');
		echo $this->Html->script('fileupload/js/jquery.fileupload-image');
		echo $this->Html->script('fileupload/js/jquery.fileupload-audio');
		echo $this->Html->script('fileupload/js/jquery.fileupload-video');
		echo $this->Html->script('fileupload/js/jquery.fileupload-validate');
		echo $this->Html->script('fileupload/js/jquery.fileupload-ui');
		echo $this->Html->script('fileupload/js/main');
		
		$entityType = $params['arg1'] ;
		$entityId     =  $params['arg2'] ;
	?>
</head>
<script type="text/javascript">
		var entityType = '<?php echo $entityType;?>' ;
		var entityId = '<?php echo $entityId;?>' ;
</script>
<body>

<div class="container-fluid">

    <form id="fileupload" action="" method="POST" enctype="multipart/form-data">
        <div class="row-fluid fileupload-buttonbar" style="padding-top:10px;">
            <div class="col-lg-7">
                <span class="btn btn-success fileinput-button">
                    <i class="glyphicon glyphicon-plus"></i>
                    <span>添加图片</span>
                    <input type="file" name="files[]" multiple>
                </span>
                <button type="submit" class="btn btn-primary start">
                    <span>开始上传</span>
                </button>
                <button type="reset" class="btn btn-warning cancel">
                    <span>取消上传</span>
                </button>
                <!--  
                <button type="button" class="btn btn-danger delete">
                    <span>删除</span>
                </button>
                <input type="checkbox" class="toggle">
                -->
                <!-- The loading indicator is shown during file processing -->
                <span class="fileupload-loading"></span>
            </div>
            <!-- The global progress information -->
            <div class="col-lg-5 fileupload-progress fade">
                <!-- The global progress bar -->
                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar progress-bar-success" style="width:0%;"></div>
                </div>
                <!-- The extended global progress information -->
                <div class="progress-extended">&nbsp;</div>
            </div>
        </div>
        <!-- The table listing the files available for upload/download -->
        <table role="presentation" class="table table-striped"><tbody class="files"></tbody></table>
    </form>
    <br>
</div>

<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td>
            <span class="preview"></span>
        </td>
        <td>
            <p class="name">{%=file.name%}</p>
            {% if (file.error) { %}
                <div><span class="label label-danger">Error</span> {%=file.error%}</div>
            {% } %}
        </td>
        <td>
            <p class="size">{%=o.formatFileSize(file.size)%}</p>
            {% if (!o.files.error) { %}
                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
            {% } %}
        </td>
        <td>
            {% if (!o.files.error && !i && !o.options.autoUpload) { %}
                <button class="btn btn-primary start">
                    <i class="glyphicon glyphicon-upload"></i>
                    <span>Start</span>
                </button>
            {% } %}
            {% if (!i) { %}
                <button class="btn btn-warning cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>Cancel</span>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade">
        <td>
            <span class="preview">
                {% if (file.thumbnailUrl) { %}
                    <a href="/<?php echo $fileContextPath;?>{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="/<?php echo $fileContextPath;?>{%=file.thumbnailUrl%}"></a>
                {% } %}
            </span>
        </td>
        <td>
            <p class="name">
                {% if (file.url) { %}
                    <a href="/<?php echo $fileContextPath;?>{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
                {% } else { %}
                    <span>{%=file.name%}</span>
                {% } %}
            </p>
            {% if (file.error) { %}
                <div><span class="label label-danger">Error</span> {%=file.error%}</div>
            {% } %}
        </td>
        <td>
            <span class="size">{%=o.formatFileSize(file.size)%}</span>
        </td>
        <td>
            {% if (file.deleteUrl) { %}
                <button class="btn btn-danger delete-img"   delete-url="<?php echo $contextPath;?>/{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                    <i class="glyphicon glyphicon-trash"></i>
                    <span>Delete</span>
                </button>
            {% } else { %}
                <button class="btn btn-warning cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>Cancel</span>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>


<!-- The XDomainRequest Transport is included for cross-domain file deletion for IE 8 and IE 9 -->
<!--[if (gte IE 8)&(lt IE 10)]>
<script src="js/cors/jquery.xdr-transport.js"></script>
<![endif]-->
</body> 

<script>
	$(function(){
			$(".delete-img").live("click",function(e){
				if(window.confirm("确认删除吗？")){
					var deleteUrl = $(this).attr("delete-url") ;
					var me = $(this) ;
					$.ajax({
					     type: "DELETE",
					     dataType: "json",
					     url: deleteUrl,
					     data: { Value: "testvalue" },
					     success:function(msg){
					    	 me.parents(".template-download:first").remove() ;
			            }
					});
				}
				return false ;
				
			}) ;
	}) ;
</script>
</html>
