<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>My-Home Software Installation</title>

	<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>

	<link href="css/bootstrap.css" rel="stylesheet">
	<link href="css/custom.css" rel="stylesheet">
	<link href="css/extra.css" rel="stylesheet">
	<link href="css/font-awesome.min.css" rel="stylesheet">
	<!--[if lt IE 9]>
		<script src="js/html5shiv.js"></script>
		<script src="js/respond.js"></script>
	<![endif]-->
</head>

<body id="top">
	<div class="container">
		<div class="row">
			<div class="col-md-8">
				<div class="logo">
					<a href=""><img alt="My-Home Software Installation" src="images/logo.png" /></a>
				</div>
			</div>
			<div class="col-md-4 userInfo">
				<p class="textRight">Installation Instructions &amp;<br />Usage Documentation</p>
			</div>
		</div>

		<div class="navbar navbar-default">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
			</div>

			<div class="navbar-collapse collapse">
			</div>
		</div>

		<div class="content">

			<div class="row">
				<div class="col-md-4">
					<div class="list-group">
					<a href="" class="list-group-item active">Table of Contents</a>
					<a href="#preinstallation" class="list-group-item">A. Pre-installation</a>
					<a href="#installation" class="list-group-item">B. Installation</a>
					<a href="#settings" class="list-group-item">C. Settings &mdash; Explained</a>
					<a href="#advanced" class="list-group-item">D. Advanced &mdash; Custom Styling</a>
					<a href="usage.html" class="list-group-item">E. General My-Home Usage</a>
					<a href="#support" class="list-group-item">F. Help &amp; Support</a>
					<a href="#ftp" class="list-group-item">G. A Note About FTP</a>
				</div>
				</div>
				<div class="col-md-8">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title">My-Home Property Management<span class="floatRight">March 2015</span></h3>
						</div>
						<div class="panel-body">
							<p class="lead">
								Thank you for using <a href="http://marssystems.github.io/My-Home/">My-Home Property Management</a>.<br />
								If you have any questions that are beyond the scope of this help file, please do not hesitate to email me via my <a  href="http://marssystems.github.io/My-Home/">Github Profile</a>.<br>
								I am always happy to help if you have any questions relating to My-Home.
							</p>
						</div>
					</div>
					<p class="lead">Easy to get started, everything you need to grow your Rental Property Business. My-Home is simple and fun to use - just log in, and start adding in your Properties and Tenants.</p>
				</div>
			</div>

			<hr />

			<h3 id="preinstallation" class="primary">A. Pre-Installation<span class="floatRight"><i class="fa fa-arrow-up"></i> <a href="#top">top</a></span></h3>

			<h4 class="info">1. Unzip My-Home and have a look around.</h4>
			<p>Unzip the My-Home file and take a look at the file structure. You should see 13 folders and 3 files.</p>

			<h4 class="info padTop">2. Create a Database.</h4>
			<p>
				First step is to create a MySQL database, and if you do not have a default one, a database user. This process is explained in great detail on many websites and web hosts.
				Once you have the database setup, you will need:
			</p>
			<ul class="list-group">
				<li class="list-group-item">The hostname <span class="floatRight"><small>almost always "localhost"</small></span></li>
				<li class="list-group-item">The database name <span class="floatRight"><small>ie. Myhome</small></span></li>
				<li class="list-group-item">The database username <span class="floatRight"><small>ie: database_user</small></span></li>
				<li class="list-group-item">The database password  <span class="floatRight"><small>ie: database_password.</small></span></li>
			</ul>

			<h4 class="info">3. Upload My-Home</h4>
			<p>Upload all the files you unzipped to your webhost, keeping the file system intact.</p>
			<div class="alertMsg warning">
				<i class="fa fa-warning"></i> Be sure to cmod the avatars, docs, pictures, uploads & the admin/templates folders to 755
			</div>

			<p class="lead">You should now have the following folders/files on your host account:</p>
			<p><img src="images/file_structure.png" class="imgFrame" /></p>

			<hr />

			<h3 id="installation" class="primary">B. Installation<span class="floatRight"><i class="fa fa-arrow-up"></i> <a href="#top">top</a></span></h3>

			<h4 class="info">Run the Online Setup Wizard</h4>
			<p>Installation is quite easy and only takes a few minutes.</p>
			<p>
				Once My-Home has finished uploading, simply go to http://www.yoursite.com/My-Home/ (or wherever subfolder you uploaded it to) and follow the on-screen instructions.<br />
				Easy to do, just follow the defaults and plug in your specific information.
			</p>

			<h4 class="info padTop">1. Database Configuration</h4>

			<ul class="list-group">
				<li class="list-group-item">Hostname <span class="floatRight"><small>On most web hosts this usually defaults to "localhost"</small></span></li>
				<li class="list-group-item">Database Username <span class="floatRight"><small>Your username to access the database</small></span></li>
				<li class="list-group-item">Database Password <span class="floatRight"><small>Your database password</small></span></li>
				<li class="list-group-item">Database Name  <span class="floatRight"><small>The name of the database you plan to install My-Home to</small></span></li>
			</ul>
			<p>
				Once you have input your database information, click on the "On to Step 2" button.<br />
				If you run into any errors here, simply delete the config.php file from the folder you uploaded My-Home to, and try again.
			</p>

			<p><img src="images/step1.png" class="imgFrame" /></p>
			<p>If everything was configured correctly, you will see the following:</p>
			<p><img src="images/step2.png" class="imgFrame" /></p>

			<h4 class="info padTop">2. My-Home Settings</h4>
			<p>
				Again, follow the defaults and plug in your site's specific information. You can set your Administrator password and Site name to be anything you would like. Once completed, be sure to hit
				"On to Step 3" to save your settings.
			</p>

			<div class="alertMsg success">
				<i class="fa fa-check"></i>
				<strong>Installation URL</strong> This should be auto-filled for you*.
			</div>

			<p>
				*If the Installation URL is not auto-filled, you will need to enter it manually.<br />
				This is needed for email notifications to both Tenants &amp; Admins. Include any sub-folder My-Home may be installed in. (ie. http://www.mydomain.com/My-Home/)<br />
				Look at your browser's URL bar, and use that (remove the install/install.php from the end) as your Installation URL.
				<br /><br />
				If you have any questions on this, please do not hesitate to email me via my <a href="http://marssystems.github.io/My-Home/">Github Profile</a>.
			</p>

			<h4 class="info padTop">3. The Primary Admin Account</h4>
			<p><img src="images/one.png" class="imgFrame" /></p>
			<p>
				Complete the Primary Admin Account form. This is the main, "Superuser" for My-Home. This Admin Account cannot be deleted through the web UI, only from within the database (ie. PHPMyAdmin).<br />
				Once you have filled in the information for the Primary Admin, click the "Complete Install" button to complete the installation.
			</p>
			<p><img src="images/two.png" class="imgFrame" /></p>

			<p>Once installation is completed, you will need to go back into your FTP application, and either rename or Delete the installation folder before you can log in as Administrator.</p>
			<div class="alertMsg warning">
				<i class="fa fa-warning"></i> For security reasons and to stop any possible re-installations please, DELETE or RENAME the "install" folder, otherwise you will not be able to log in as Administrator.
			</div>

			<hr />

			<h3 id="settings" class="warning">C. Settings - Explained<span class="floatRight"><i class="fa fa-arrow-up"></i> <a href="#top">top</a></span></h3>

			<h4 class="info padTop">1. Settings</h4>
			<p>Once My-Home is installed, you can update the Site Settings after logging in as the Primary Admin. Go to Site Settings page from the Dashboard dropdown menu.</p>
			<p><img src="images/three.png" class="imgFrame" /></p>

			<ul class="list-group padTop">
				<a href="" class="list-group-item active">Global Site Settings</a>
				<li class="list-group-item">Installation URL <span class="floatRight"><small>Used in all File Uploads & email notifications. Must include the trailing slash (ie. http://mysite.com/My-Home/).</small></span></li>
				<li class="list-group-item">Localization <span class="floatRight"><small>Choose the Language file to use throughout My-Home. All Localization files need to be translated from English.</small></span></li>
				<li class="list-group-item">Site Name <span class="floatRight"><small>ie. My-Home (Appears at the top of the browser, the header logo, in the footer and in other headings throughout the site).</small></span></li>
				<li class="list-group-item">Business Name</li>
				<li class="list-group-item">Business Address <span class="floatRight"><small>Address & Phone. (Appears in Tenant's Receipt).</small></span></li>
				<li class="list-group-item">Business Email <span class="floatRight"><small>Used in email notifications as the "from/reply to" email address.</small></span></li>
				<li class="list-group-item">Business Phone</li>
				<li class="list-group-item">Contact Phone <span class="floatRight"><small>Phone Number Tenants can call for General Information, Questions etc.</small></span></li>
			</ul>

			<div class="alertMsg warning">
				<i class="fa fa-warning"></i> If you change any names of the Uploads, Avatar or Template folders, be sure you also update the Site Settings to reflect that change. Otherwise, Uploading will not work.
			</div>

			<ul class="list-group padTop">
				<a href="" class="list-group-item active">File/Image Upload Settings</a>
				<li class="list-group-item">Property Files Upload Directory <span class="floatRight"><small>Where Property Files upload to (Admin Side ONLY). Must include the trailing slash (ie. uploads/).</small></span></li>
				<li class="list-group-item">Templates Upload Directory <span class="floatRight"><small>Where My-Home Forms & Templates upload to (Admin Side ONLY). Must include the trailing slash (ie. templates/).</small></span></li>
				<li class="list-group-item">Tenant Documents Upload Directory <span class="floatRight"><small>Where Tenant documents upload to (Admin Side ONLY). Must include the trailing slash (ie. docs/).</small></span></li>
				<li class="list-group-item">Upload File Types Allowed <span class="floatRight"><small>The file types you allow to be uploaded, NO spaces & each separated by a comma (Format: xls,pdf,doc,docx).</small></span></li>
				<li class="list-group-item">Avatar Upload Directory <span class="floatRight"><small>Where both Admin & Tenant Avatars upload to. Must include the trailing slash (ie. avatars/).</small></span></li>
				<li class="list-group-item">Avatar File Types Allowed <span class="floatRight"><small>The file types you allow to be uploaded, NO spaces & each separated by a comma (Format: jpg,jpeg,png).</small></span></li>
				<li class="list-group-item">Property Pictures Upload Directory <span class="floatRight"><small>Where Property Pictures upload to (Admin Side ONLY). Must include the trailing slash (ie. pictures/).</small></span></li>
				<li class="list-group-item">Property Pictures File Types Allowed <span class="floatRight"><small>The file types you allow to be uploaded, NO spaces & each separated by a comma (Format: jpg,jpeg,png).</small></span></li>
			</ul>
			<ul class="list-group padTop">
				<a href="" class="list-group-item active">Rental Payment Settings</a>
				<li class="list-group-item">Enable rental payments through PayPal? <span class="floatRight"><small>Set to Yes to allow Tenants to make rental payments via PayPal.</small></span></li>
				<li class="list-group-item">PayPal Payment Currency Code</li>
				<li class="list-group-item">Payment Completed Message <span class="floatRight"><small>What the Tenant will see once they have completed a PayPal rental payment.</small></span></li>
				<li class="list-group-item">PayPal Account Email <span class="floatRight"><small>Your PayPal Account's email &mdash; where PayPal payments will be sent to.</small></span></li>
				<li class="list-group-item">PayPal Item Name <span class="floatRight"><small>The item name that appears on the PayPal payment screen.</small></span></li>
				<li class="list-group-item">PayPal Use Fee <span class="floatRight"><small>Fee charged by PayPal. Do not include '%' symbol (ie. 0.5).</small></span></li>
			</ul>

			<h4 class="info padTop">2. Primary Admin</h4>
			<p>The Primary Admin Account is a "Superuser", and has access to all settings, other Admins, Tenants, Properties and Leases.</p>
			<p>You can update the Primary Admin's Account from the Admin Accounts dropdown menu.</p>

			<hr />

			<h3 id="advanced" class="danger">D. Advanced - Custom Styling<span class="floatRight"><i class="fa fa-arrow-up"></i> <a href="#top">top</a></span></h3>

			<h4 class="info padTop">1. Custom Styling</h4>
			<p>
				While My-Home already has a modern flat interface based on <a href="http://getbootstrap.com/" target="_blank">Twitter's Bootstrap</a>, many of you will want to integrate it into your own design. This is very
				easy, however, you will need to be able to write HTML/CSS code. For those that aren't versed, here is a quick overview. The easiest way to change the look is by editing the default CSS files named "My-Home.css"
				and "custom.css". Next is to edit the page files, located in the "pages" folders in both the <em>main</em> folder and the <em>admin/</em>&nbsp; folder.
			</p>

			<h4 class="info padTop">2. Images</h4>
			<p>
				My-Home only has two images, bg.png (the page background for all pages) &amp; logo.png.<br />
				All icons are generated from an Icon Font: Font Awesome (version 4.3.0). Check out all of Font Awesome's icons &amp; usage at:<br />
				<a href="http://fontawesome.io/">http://fontawesome.io</a>.
			</p>
			<p>
				Icons &amp; Images can be changed to your liking. You may want to change the My-Home logo to that of your company logo.<br />
				The My-Home logo is 245px x 50px.
			</p>

			<hr />

			<h3 class="success">E. General My-Home Usage<span class="floatRight"><i class="fa fa-arrow-up"></i> <a href="#top">top</a></span></h3>

			<p>Great! Got all of that?</p>
			<p><a href="usage.html" class="btn btn-lg btn-success">Yup? Show me some Day to Day My-Home Usage <i class="fa fa-long-arrow-right"></i></a></p>

			<hr />

			<h3 id="support" class="primary">F. Help &amp; Support<span class="floatRight"><i class="fa fa-arrow-up"></i> <a href="#top">top</a></span></h3>

			<p>
				Please, if you have any questions, run into any issues or just need some help, do not hesitate to <a href="http://marssystems.github.io/My-Home/">contact me</a>.<br />
				I believe in providing the best support possible, and I monitor my email just about 24/7 (ok, not when I am sleeping).
			</p>
			<p>
				If you are thinking of giving My-Home anything less then 5 Stars, please tell me what I can do to make it a 5 Star experience for you.<br />
				I love to hear feedback, and welcome any suggestions you may have to make My-Home better.
			</p>
			<p><img src="images/rating.png" class="imgFrame" /></p>

			<hr />

			<h3 id="ftp" class="primary">G. A Note About FTP<span class="floatRight"><i class="fa fa-arrow-up"></i> <a href="#top">top</a></span></h3>

			<p>
				If you are having problems with My-Home after successfully installing and logging in the for the first time you should check your FTP software settings.
				I have found in many cases that numerous FTP programs are not uploading files correctly.
			</p>
			<p>
				Check your FTP program to see if it is uploading files in ASCII - which is causing the file code to be uploaded in one long line.<br />
				Try changing it to Binary and uploading the files again. Upload everything except the install folder and the config file (or better yet, if you have not started using My-Home,
				just wipe the install and the database and reinstall a fresh copy).<br />
				I use <a href="http://www.cuteftp.com">CuteFTP</a> for my uploads and ran into this problem a few months ago when all of the sudden everything I was working on just seemed to stop working as expected.
			</p>
			<p><img src="images/ftp.png" class="imgFrame" /></p>

		</div>

		<div class="footer">
			<p class="textCenter">
				&copy; <?php echo date('Y'); ?> <a href="http://rentmediterraneanapartments.com" target="_blank">My-Home Property Management</a>
					<span><i class="fa fa-plus"></i></span>
					Provided by <a href="http://bitcoin.bigmoney.biz" target="_blank">Mars Systems International</a>
			</p>
		</div>

	</div>

</body>
</html>