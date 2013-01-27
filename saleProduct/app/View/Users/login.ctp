<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        
        <title>LOGIN</title>
        <link rel="shortcut icon" href="">
        <meta name="author" content="Survs">
		<meta name="keywords" content="Survs, surveys, online surveys, collaborative, share">
		<meta name="description" content="Survs is a collaborative tool that enables you to create online surveys with simplicity and elegance.">
		<meta name="robots" content="noodp,noydir">
		 
    <script type="text/javascript" async="" src="./Survs - Login_files/ga.js"></script><script type="text/javascript" src="./Survs - Login_files/rot13.js"></script>
	<link rel="stylesheet" type="text/css" href="/saleProduct/app/webroot/css/home.css">
<script type="text/javascript" src="./Survs - Login_files/prototype.js"></script>
</head>
    <body class="small login">
    	<div id="wrapper">
	<div id="header"><h1><a href="https://www.survs.com/">Survs - Collaborative Online Survey Tool, Web Survey Software</a></h1></div>
    <div class="container">
    	<div id="topcorners"><div class="cleft"></div><div class="cright"></div></div>
    	<div id="content">
			
			<h1 style="font-size: 1.85em;color:#025A8D;margin-bottom:20px;">SmarteSeller Marketing System</h1>
			<form name="f_0_11_1_3_1" method="post" action="<?php echo $this->Html->url('/users/login'); ?>">
				
				<table class="login" style="margin-left: auto; margin-right: auto;">
					<tbody><tr>
						<td>
						<p class="mtop0 mbottom025"><strong><label for="email">Username</label></strong></p>
						<input id="username" tabindex="1" class="inputtext" type="text" name="username"></td>
					</tr>
					<tr>
						<td>
						<p class="mtop05 mbottom025"><strong><label for="password">Password</label></strong></p>
						<input tabindex="2" class="inputtext" type="password" name="password" id="password"></td>
					</tr>
					
					<?php if ($error): ?>
					<tr>
					<td style="color:red;">
					<p>The login credentials you supplied could not be recognized. Please try again.</p>
					</td>
					</tr>
					<?php endif; ?>
					
					<tr>
						<td style="padding-top: 10px;"><input class="bprimarypub80" type="submit" tabindex="4" value="Login"></td>
					</tr>
				</tbody></table>
			</form>
			
			
		</div>
		<div id="bottomcorners"><div class="cleft"></div><div class="cright"></div></div>
	</div>
</div>
    	<div id="footer">
	<p>
	</p>
	<p>Copyright © 2013  smarteseller.com . All rights reserved.</p>
</div>

</body></html>