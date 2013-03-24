<html>
<head>
<?php 
include_once ('config/config.php');

?>
<style type="text/css">
   input{
   	font-size:70px;
   }
   
   body{
   	text-align:center;
   }
   
   body label{
   		text-align:left;
   }
</style>
</head>
<body>
<div style="width:100%;height:100%;;font-size:50px;left:30%;right:30%;">
<?php if ($error): ?>
<p>The login credentials you supplied could not be recognized. Please try again.</p>
<?php endif; ?>
<h1 style="font-size: 1.85em;color:#025A8D;">SmarteSeller Marketing System</h1>
<form action="<?php echo $this->Html->url('/users/login'); ?>" method="post" style="float:left; background-color: #F4F4F4;  border: 1px solid #CCCCCC; border-radius: 10px 10px 10px 10px;">
<div>
   <label for="username">User Name:</label><br/>
   <div style="float:right;"> <?php echo $this->Form->username('username', array('size' => 20)); ?></div>
</div>
<div style="margin-top:15px;">
    <label for="password">Password:</label><br/>
   <div style="float:right; "> <?php echo$this->Form->password('password', array('size' => 20)); ?></div>
</div>
<div style="float:right;margin-top:15px;">
    <?php echo $this->Form->submit('Login'); ?>
</div>
</form>
</div>
</body>
</html>