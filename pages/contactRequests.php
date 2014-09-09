<?php
    if (isset($_POST['submit']) && $_POST['submit'] == 'contactRequest') {
        // Validation
        if($_POST['subject'] == "") {
            $msgBox = alertBox($emptySubjectField, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['comments'] == "") {
            $msgBox = alertBox($emptyCommentsField, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$tenantName = $mysqli->real_escape_string($_POST['tenantName']);
			$tenantEmail = $mysqli->real_escape_string($_POST['tenantEmail']);
			$subject = $mysqli->real_escape_string($_POST['subject']);
			$comments = htmlentities($_POST['comments']);

			// Send out the email in HTML
			$installUrl = $set['installUrl'];
			$siteName = $set['siteName'];
			$businessEmail = $set['businessEmail'];

			$message = '<html><body>';
			$message .= '<h3>'.$subject.'</h3>';
			$message .= '<hr>';
			$message .= '<p>From: '.$tenantName.'</p>';
			$message .= '<p>'.$tenantEmail.'</p>';
			$message .= '<hr>';
			$message .= '<p>'.$comments.'</p>';
			$message .= '<hr>';
			$message .= '<p>Thank you,<br>'.$siteName.'</p>';
			$message .= '</body></html>';

			$headers = "From: ".$siteName." <".$businessEmail.">\r\n";
			$headers .= "Reply-To: ".$businessEmail."\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

			if (mail($allAdmins, $subject, $message, $headers)) {
				$msgBox = alertBox($emailSentMsg, "<i class='fa fa-check-square-o'></i>", "success");
				// Clear the form of Values
				$_POST['subject'] = $_POST['comments'] = '';
			} else {
				$msgBox = alertBox($emailSentError, "<i class='fa fa-times-circle'></i>", "danger");
			}
		}
	}

	// Get Tenant Data
	$query = "SELECT tenantEmail, tenantFirstName, tenantLastName FROM tenants WHERE tenantId = ".$tenantId;
	$res = mysqli_query($mysqli, $query) or die('Error, retrieving Tenant Avatar failed. ' . mysqli_error());
	$row = mysqli_fetch_assoc($res);
?>
<h3 class="info"><?php echo $contactUsH3; ?></h3>
<p class="lead"><?php echo $contactUsQuip; ?></p>
<p><?php echo $contactUsInstructions; ?></p>

<?php if ($msgBox) { echo $msgBox; } ?>

<form action="" method="post" class="padTop">
	<div class="form-group">
		<label for="tenantName"><?php echo $tenantNameField; ?></label>
		<input type="text" class="form-control" name="tenantName" id="tenantName" value="<?php echo clean($row['tenantFirstName']).' '.clean($row['tenantLastName']); ?>"  readonly="readonly" />
	</div>
	<div class="form-group">
		<label for="tenantEmail"><?php echo $emailAddressField; ?></label>
		<input type="text" class="form-control" name="tenantEmail" id="tenantEmail" value="<?php echo clean($row['tenantEmail']); ?>"  readonly="readonly" />
	</div>
	<div class="form-group">
		<label for="subject"><?php echo $subjectField; ?></label>
		<input type="text" class="form-control" name="subject" id="subject" value="" />
	</div>
	<div class="form-group">
		<label for="comments"><?php echo $commentsField; ?></label>
		<textarea class="form-control" name="comments" id="comments" rows="4"></textarea>
	</div>
	<button type="input" name="submit" value="contactRequest" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $sendBtn; ?></button>
</form>