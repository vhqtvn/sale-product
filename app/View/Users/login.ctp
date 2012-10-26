<div style="width:470px; height:234px; position:absolute; top:50%; left:50%; margin-top:-117px; margin-left:-235px;">
<?php if ($error): ?>
<p>The login credentials you supplied could not be recognized. Please try again.</p>
<?php endif; ?>
<h1 style="font-size: 1.85em;color:#025A8D;">SmarteSeller Marketing System</h1>
<form action="<?php echo $this->Html->url('/users/login'); ?>" method="post" style="margin-left:50px; float:left; padding:25px 38px; width:300px;background-color: #F4F4F4;  border: 1px solid #CCCCCC; border-radius: 10px 10px 10px 10px;">
<div>
    <label for="username">User Name:</label>
   <div style="float:right;"> <?php echo $this->Form->username('username', array('size' => 20)); ?></div>
</div>
<div style="margin-top:15px;">
    <label for="password">Password:</label>
   <div style="float:right; "> <?php echo$this->Form->password('password', array('size' => 20)); ?></div>
</div>
<div style="float:right;margin-top:15px;">
    <?php echo $this->Form->submit('Login'); ?>
</div>
</form>
</div>