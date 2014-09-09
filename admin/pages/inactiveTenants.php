<?php
	$stacktable = 'true';

	// Delete Tenant Account
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteTenant') {
		$stmt = $mysqli->prepare("DELETE FROM tenants WHERE tenantId = ?");
		$stmt->bind_param('s', $_POST['deleteId']);
		$stmt->execute();
		$stmt->close();

		$msgBox = alertBox($tenantDeletedMsg, "<i class='fa fa-check-square-o'></i>", "success");
    }

	// Resend Activation Link to Tenant
	if (isset($_POST['submit']) && $_POST['submit'] == 'resendActivation') {
		$tenantEmail = htmlspecialchars($_POST['tenantEmail']);
		$hash = htmlspecialchars($_POST['hash']);

		// Send out the email in HTML
		$installUrl = $set['installUrl'];
		$siteName = $set['siteName'];
		$businessEmail = $set['businessEmail'];

		$subject = 'Your '.$siteName.' Tenant Account is waiting to be activated';

		$message = '<html><body>';
		$message .= '<h3>'.$subject.'</h3>';
		$message .= '<hr>';
		$message .= '<p>You must activate your account before you will be able to log in. Please click (or copy/paste) the following link to activate your account:<br>'.$installUrl.'activate.php?tenantEmail='.$tenantEmail.'&hash='.$hash.'</p>';
		$message .= '<hr>';
		$message .= '<p>Once you have activated your new Tenant account and logged in, please take the time to update your account profile details.</p>';
		$message .= '<p>You can log in to your account at '.$installUrl.'</p>';
		$message .= '<p>Thank you,<br>'.$siteName.'</p>';
		$message .= '</body></html>';

		$headers = 'From: '.$siteName.' <'.$businessEmail.'>\r\n';
		$headers .= 'Reply-To: '.$businessEmail.'\r\n';
		$headers .= 'MIME-Version: 1.0\r\n';
		$headers .= 'Content-Type: text/html; charset=ISO-8859-1\r\n';

		if (mail($tenantEmail, $subject, $message, $headers)) {
			$msgBox = alertBox($activationEmailSentMsg, "<i class='fa fa-check-square-o'></i>", "success");
		} else {
			$msgBox = alertBox($activationEmailError, "<i class='fa fa-warning'></i>", "danger");
		}
	}

	// Get Inactive Tenant Data
    $query = "SELECT
					tenantId,
					tenantEmail,
					tenantFirstName,
					tenantLastName,
					DATE_FORMAT(createDate,'%M %d, %Y') AS createDate,
					hash,
					isActive,
					isArchived
				FROM
					tenants
				WHERE
					adminId = ".$_SESSION['adminId']." AND
					isActive = 0 AND
					isArchived = 0";
    $res = mysqli_query($mysqli, $query) or die('Error, retrieving Inactive Tenant Data failed. ' . mysqli_error());
?>
<h3 class="primary"><?php echo $inactiveTenantsH3; ?></h3>
<p class="lead"><?php echo $inactiveTenantsQuip; ?></p>

<?php if ($msgBox) { echo $msgBox; } ?>

<?php if(mysqli_num_rows($res) < 1) { ?>
	<div class="alertMsg default">
		<i class="fa fa-minus-square-o"></i> <?php echo $noInactiveTenantsMsg; ?>
	</div>
<?php } else { ?>
	<table id="responsiveTable" class="large-only" cellspacing="0">
		<tr align="left">
			<th><?php echo $tab_tenant; ?></th>
			<th><?php echo $tab_email; ?></th>
			<th><?php echo $dateAccountCreated; ?></th>
			<th></th>
			<?php if ($superuser == '1') { ?>
				<th></th>
			<?php }	?>
		</tr>
		<tbody class="table-hover">
		<?php while ($row = mysqli_fetch_assoc($res)) { ?>
			<tr>
				<td><a href="index.php?action=tenantInfo&tenantId=<?php echo $row['tenantId']; ?>"><?php echo clean($row['tenantFirstName']).' '.clean($row['tenantLastName']); ?></a></td>
				<td><?php echo clean($row['tenantEmail']); ?></td>
				<td><?php echo clean($row['createDate']); ?></td>
				<td>
					<form action="" method="post">
						<input name="tenantEmail" type="hidden" value="<?php echo $row['tenantEmail']; ?>" />
						<input name="hash" type="hidden" value="<?php echo $row['hash']; ?>" />
						<button type="input" name="submit" value="resendActivation" class="btn btn-link btn-icon-alt"><?php echo $resendActivationLink; ?> <i class="fa fa-exchange"></i></button>
					</form>
				</td>
				<?php if ($superuser == '1') { ?>
					<td><a data-toggle="modal" href="#deleteTenant<?php echo $row['tenantId']; ?>" class="btn btn-xs btn-link tool-tip" title="Delete Tenant Account"><i class="fa fa-times"></i></a></td>
				<?php }	?>
			</tr>

			<?php if ($superuser == '1') { ?>
				<!-- Delete Tenant Account Confirm Modal -->
				<div class="modal fade" id="deleteTenant<?php echo $row['tenantId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<form action="" method="post">
								<div class="modal-body">
									<p class="lead"><?php echo $deleteTenantConf; ?>
									</p>
								</div>
								<div class="modal-footer">
									<input name="deleteId" type="hidden" value="<?php echo $row['tenantId']; ?>" />
									<button type="input" name="submit" value="deleteTenant" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
									<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
								</div>
							</form>
						</div>
					</div>
				</div>
			<?php
			}
		}
		?>
		</tbody>
	</table>
<?php }	?>