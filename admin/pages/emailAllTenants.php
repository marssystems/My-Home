<?php
	if (isset($_POST['submit']) && $_POST['submit'] == 'sendEmail') {
		// Validation
        if($_POST['emailSubject'] == "") {
            $msgBox = alertBox($emailSubjectReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['emailText'] == "") {
            $msgBox = alertBox($emailTextReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			// Send out the email in HTML
			$emailSubject = $mysqli->real_escape_string($_POST['emailSubject']);
			$emailText = htmlentities($_POST['emailText']);
			$installUrl = $set['installUrl'];
			$siteName = $set['siteName'];
			$businessEmail = $set['businessEmail'];

			$subject = $emailSubject;

			$message = '<html><body>';
			$message .= '<h3>'.$subject.'</h3>';
			$message .= '<hr>';
			$message .= '<p>'.$emailText.'</p>';
			$message .= '<hr>';
			$message .= '<p>Thank you,<br>'.$adminFirstName.' '.$adminLastName.'</p>';
			$message .= '<p>'.$siteName.'</p>';
			$message .= '</body></html>';

			$headers = "From: ".$siteName." <".$businessEmail.">\r\n";
			$headers .= "Reply-To: ".$businessEmail."\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

			if (mail($allTenants, $subject, $message, $headers)) {
				$msgBox = alertBox($emailAllTenantsSentMsg, "<i class='fa fa-check-square-o'></i>", "success");
				// Clear the form of Values
				$_POST['emailSubject'] = $_POST['emailText'] = '';
			} else {
				$msgBox = alertBox($emailSentError, "<i class='fa fa-times-circle'></i>", "danger");
			}

		}
	}
	
	if ($superuser != '1') {
?>
	<h3 class="danger"><?php echo $accessErrorH3; ?></h3>
	<div class="alertMsg danger">
		<i class="fa fa-minus-square-o"></i> <?php echo $permissionDenied; ?>
	</div>
<?php } else { ?>
	<h3 class="primary"><?php echo $sendMassEmailH3; ?></h3>
	<p class="lead"><?php echo $sendMassEmailQuip; ?></p>
	
	<?php if ($msgBox) { echo $msgBox; } ?>

	<hr />

	<form action="" method="post">
		<div class="form-group">
			<label for="emailSubject"><?php echo $subjectField; ?></label>
			<input type="text" class="form-control" name="emailSubject" id="emailSubject" value="<?php echo isset($_POST['emailSubject']) ? $_POST['emailSubject'] : ''; ?>" />
		</div>
		<div class="form-group">
			<label for="emailText"><?php echo $emailTextField; ?></label>
			<textarea class="form-control" name="emailText" id="emailText" rows="8"><?php echo isset($_POST['emailText']) ? $_POST['emailText'] : ''; ?></textarea>
		</div>
		<button type="input" name="submit" value="sendEmail" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $sendEmailBtn; ?></button>
	</form>
<?php } ?>