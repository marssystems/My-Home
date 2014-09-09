<?php
	$collapse = 'true';
	$jsFile = 'siteSettings';

	// Update the Site Settings
    if (isset($_POST['submit']) && $_POST['submit'] == 'Update Settings') {
        // Validation
        if($_POST['enablePayments'] == "1") {
			// Validation if PayPal Payments is Enabled
			if($_POST['paypalCurrency'] == "") {
				$msgBox = alertBox($payPalCurrencyCodwMsg, "<i class='fa fa-times-circle'></i>", "danger");
			} else if($_POST['paypalEmail'] == "") {
				$msgBox = alertBox($payPalAccountEmailMsg, "<i class='fa fa-times-circle'></i>", "danger");
			} else if($_POST['paypalItemName'] == "") {
				$msgBox = alertBox($payPalItemNameMsg, "<i class='fa fa-times-circle'></i>", "danger");
			} else if($_POST['paymentCompleteMsg'] == "") {
				$msgBox = alertBox($paymentCompleteMsg, "<i class='fa fa-times-circle'></i>", "danger");
			} else if($_POST['paypalFee'] == "") {
				$msgBox = alertBox($payPalFeeMsg, "<i class='fa fa-times-circle'></i>", "danger");
			}
        }
		if($_POST['installUrl'] == "") {
            $msgBox = alertBox($installUrlMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['siteName'] == "") {
            $msgBox = alertBox($siteNameMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['businessName'] == "") {
            $msgBox = alertBox($businessNameMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['businessAddress'] == "") {
            $msgBox = alertBox($businessAddressMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['businessEmail'] == "") {
            $msgBox = alertBox($businessEmalMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['businessPhone'] == "") {
            $msgBox = alertBox($businessPhoneMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['uploadPath'] == "") {
            $msgBox = alertBox($propertyUploadsMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['templatesPath'] == "") {
            $msgBox = alertBox($templateUploadsMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['tenantDocsPath'] == "") {
            $msgBox = alertBox($tenantDocUploadsMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['fileTypesAllowed'] == "") {
            $msgBox = alertBox($fileTypesMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['avatarFolder'] == "") {
            $msgBox = alertBox($avatarUploadsMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['avatarTypes'] == "") {
            $msgBox = alertBox($avatarFileTypesMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['propertyPicsPath'] == "") {
            $msgBox = alertBox($propertyPicsUploadsMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['propertyPicTypes'] == "") {
            $msgBox = alertBox($pictureFileTypesMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			// Add the trailing slash if there is not one
			$installUrl = $_POST['installUrl'];
			if(substr($installUrl, -1) != '/') {
				$install = $installUrl.'/';
			} else {
				$install = $installUrl;
			}

			$uploadPath = $_POST['uploadPath'];
			if(substr($uploadPath, -1) != '/') {
				$uploadFolder = $uploadPath.'/';
			} else {
				$uploadFolder = $uploadPath;
			}

			$templatesPath = $_POST['templatesPath'];
			if(substr($templatesPath, -1) != '/') {
				$templateFolder = $templatesPath.'/';
			} else {
				$templateFolder = $templatesPath;
			}
			
			$tenantDocsPath = $_POST['tenantDocsPath'];
			if(substr($tenantDocsPath, -1) != '/') {
				$documentsFolder = $tenantDocsPath.'/';
			} else {
				$documentsFolder = $tenantDocsPath;
			}

			$avatarFolder = $_POST['avatarFolder'];
			if(substr($avatarFolder, -1) != '/') {
				$avatarPath = $avatarFolder.'/';
			} else {
				$avatarPath = $avatarFolder;
			}
			
			$propertyPicsPath = $_POST['propertyPicsPath'];
			if(substr($propertyPicsPath, -1) != '/') {
				$propertyPicsFolder = $propertyPicsPath.'/';
			} else {
				$propertyPicsFolder = $propertyPicsPath;
			}
			
			$localization = $mysqli->real_escape_string($_POST['localization']);
			$siteName = $mysqli->real_escape_string($_POST['siteName']);
			$businessName = $mysqli->real_escape_string($_POST['businessName']);
			$businessAddress = htmlentities($_POST['businessAddress']);
			$businessEmail = $mysqli->real_escape_string($_POST['businessEmail']);
			$businessPhone = $mysqli->real_escape_string($_POST['businessPhone']);
			$contactPhone = $mysqli->real_escape_string($_POST['contactPhone']);
			$fileTypesAllowed = $mysqli->real_escape_string($_POST['fileTypesAllowed']);
			$avatarTypes = $mysqli->real_escape_string($_POST['avatarTypes']);
			$propertyPicTypes = $mysqli->real_escape_string($_POST['propertyPicTypes']);
			$enablePayments = $mysqli->real_escape_string($_POST['enablePayments']);
			$paypalCurrency = $mysqli->real_escape_string($_POST['paypalCurrency']);
			$paymentCompleteMsg = $mysqli->real_escape_string($_POST['paymentCompleteMsg']);
			$paypalEmail = $mysqli->real_escape_string($_POST['paypalEmail']);
			$paypalItemName = $mysqli->real_escape_string($_POST['paypalItemName']);
			$paypalFee = $mysqli->real_escape_string($_POST['paypalFee']);

            $stmt = $mysqli->prepare("
                                UPDATE
                                    sitesettings
                                SET
									installUrl = ?,
									localization = ?,
									siteName = ?,
									businessName = ?,
									businessAddress = ?,
									businessEmail = ?,
									businessPhone = ?,
									contactPhone = ?,
									uploadPath = ?,
									templatesPath = ?,
									tenantDocsPath = ?,
									fileTypesAllowed = ?,
									avatarFolder = ?,
									avatarTypes = ?,
									propertyPicsPath = ?,
									propertyPicTypes = ?,
									enablePayments = ?,
									paypalCurrency = ?,
									paymentCompleteMsg = ?,
									paypalEmail = ?,
									paypalItemName = ?,
									paypalFee = ?
			");
            $stmt->bind_param('ssssssssssssssssssssss',
                               $install,
							   $localization,
							   $siteName,
							   $businessName,
							   $businessAddress,
							   $businessEmail,
							   $businessPhone,
							   $contactPhone,
							   $uploadFolder,
							   $templateFolder,
							   $documentsFolder,
							   $fileTypesAllowed,
							   $avatarPath,
							   $avatarTypes,
							   $propertyPicsFolder,
							   $propertyPicTypes,
							   $enablePayments,
							   $paypalCurrency,
							   $paymentCompleteMsg,
							   $paypalEmail,
							   $paypalItemName,
							   $paypalFee					   
			);
            $stmt->execute();
			$msgBox = alertBox($settingsSavedMsg, "<i class='fa fa-check-square-o'></i>", "success");

            $stmt->close();
		}
	}

	$sqlStmt = "SELECT
					installUrl,
					localization,
					siteName,
					businessName,
					businessAddress,
					businessEmail,
					businessPhone,
					contactPhone,
					uploadPath,
					templatesPath,
					tenantDocsPath,
					fileTypesAllowed,
					avatarFolder,
					avatarTypes,
					propertyPicsPath,
					propertyPicTypes,
					enablePayments,
					paypalCurrency,
					paymentCompleteMsg,
					paypalEmail,
					paypalItemName,
					paypalFee
				FROM
					sitesettings";
	$res = mysqli_query($mysqli, $sqlStmt) or die('Error, retrieving Site Settings Data failed. ' . mysqli_error());
	$row = mysqli_fetch_assoc($res);

	if ($row['enablePayments'] == '1') { $paymentsystem = 'selected'; } else { $paymentsystem = ''; }
	if ($row['localization'] == 'ar') { $ar = 'selected'; } else { $ar = ''; }
	if ($row['localization'] == 'bg') { $bg = 'selected'; } else { $bg = ''; }
	if ($row['localization'] == 'ce') { $ce = 'selected'; } else { $ce = ''; }
	if ($row['localization'] == 'cs') { $cs = 'selected'; } else { $cs = ''; }
	if ($row['localization'] == 'da') { $da = 'selected'; } else { $da = ''; }
	if ($row['localization'] == 'en') { $en = 'selected'; } else { $en = ''; }
	if ($row['localization'] == 'en-ca') { $en_ca = 'selected'; } else { $en_ca = ''; }
	if ($row['localization'] == 'en-gb') { $en_gb = 'selected'; } else { $en_gb = ''; }
	if ($row['localization'] == 'es') { $es = 'selected'; } else { $es = ''; }
	if ($row['localization'] == 'fr') { $fr = 'selected'; } else { $fr = ''; }
	if ($row['localization'] == 'hr') { $hr = 'selected'; } else { $hr = ''; }
	if ($row['localization'] == 'hu') { $hu = 'selected'; } else { $hu = ''; }
	if ($row['localization'] == 'hy') { $hy = 'selected'; } else { $hy = ''; }
	if ($row['localization'] == 'id') { $id = 'selected'; } else { $id = ''; }
	if ($row['localization'] == 'it') { $it = 'selected'; } else { $it = ''; }
	if ($row['localization'] == 'ja') { $ja = 'selected'; } else { $ja = ''; }
	if ($row['localization'] == 'ko') { $ko = 'selected'; } else { $ko = ''; }
	if ($row['localization'] == 'nl') { $nl = 'selected'; } else { $nl = ''; }
	if ($row['localization'] == 'pt') { $pt = 'selected'; } else { $pt = ''; }
	if ($row['localization'] == 'ro') { $ro = 'selected'; } else { $ro = ''; }
	if ($row['localization'] == 'th') { $th = 'selected'; } else { $th = ''; }
	if ($row['localization'] == 'vi') { $vi = 'selected'; } else { $vi = ''; }
	if ($row['localization'] == 'yue') { $yue = 'selected'; } else { $yue = ''; }
	
	if ($superuser != '1') {
?>
<h3 class="danger"><?php echo $accessErrorH3; ?></h3>
<div class="alertMsg danger">
	<i class="fa fa-minus-square-o"></i> <?php echo $permissionDenied; ?>
</div>
<?php } else { ?>
	<h3 class="primary"><?php echo $updateSettingsH3; ?></h3>

	<?php if ($msgBox) { echo $msgBox; } ?>
	
	<form action="" method="post">
		<div class="panel-group" id="accordion">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h4 class="panel-title">
					<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne"><i class="fa fa-cogs"></i> <?php echo $siteSetAccTitle; ?></a>
					</h4>
				</div>
				<div id="collapseOne" class="panel-collapse in">
					<div class="panel-body">
						<div class="form-group">
							<label for="installUrl"><?php echo $installUrlField; ?></label>
							<input type="text" class="form-control" name="installUrl" id="installUrl" value="<?php echo $row['installUrl']; ?>" />
							<span class="help-block"><?php echo $installUrlHelper; ?></span>
						</div>
						<div class="form-group">
							<label for="localization"><?php echo $localizationField; ?></label>
							<select class="form-control" id="localization" name="localization">
								<option value="ar" <?php echo $ar; ?>>ar.php &mdash; <?php echo $optionArabic; ?></option>
								<option value="bg" <?php echo $bg; ?>>bg.php &mdash; <?php echo $optionBulgarian; ?></option>
								<option value="ce" <?php echo $ce; ?>>ce.php &mdash; <?php echo $optionChechen; ?></option>
								<option value="cs" <?php echo $cs; ?>>cs.php &mdash; <?php echo $optionCzech; ?></option>
								<option value="da" <?php echo $da; ?>>da.php &mdash; <?php echo $optionDanish; ?></option>
								<option value="en" <?php echo $en; ?>>en.php &mdash; <?php echo $optionEnglish; ?></option>
								<option value="en-ca" <?php echo $en_ca; ?>>en-ca.php &mdash; <?php echo $optionCanadianEnglish; ?></option>
								<option value="en-gb" <?php echo $en_gb; ?>>en-gb.php &mdash; <?php echo $optionBritishEnglish; ?></option>
								<option value="es" <?php echo $es; ?>>es.php &mdash; <?php echo $optionEspanol; ?></option>
								<option value="fr" <?php echo $fr; ?>>fr.php &mdash; <?php echo $optionFrench; ?></option>
								<option value="hr" <?php echo $hr; ?>>hr.php &mdash; <?php echo $optionCroatian; ?></option>
								<option value="hu" <?php echo $hu; ?>>hu.php &mdash; <?php echo $optionHungarian; ?></option>
								<option value="hy" <?php echo $hy; ?>>hy.php &mdash; <?php echo $optionArmenian; ?></option>
								<option value="id" <?php echo $id; ?>>id.php &mdash; <?php echo $optionIndonesian; ?></option>
								<option value="it" <?php echo $it; ?>>it.php &mdash; <?php echo $optionItalian; ?></option>
								<option value="ja" <?php echo $ja; ?>>ja.php &mdash; <?php echo $optionJapanese; ?></option>
								<option value="ko" <?php echo $ko; ?>>ko.php &mdash; <?php echo $optionKorean; ?></option>
								<option value="nl" <?php echo $nl; ?>>nl.php &mdash; <?php echo $optionDutch; ?></option>
								<option value="pt" <?php echo $pt; ?>>pt.php &mdash; <?php echo $optionPortuguese; ?></option>
								<option value="ro" <?php echo $ro; ?>>ro.php &mdash; <?php echo $optionRomanian; ?></option>
								<option value="th" <?php echo $th; ?>>th.php &mdash; <?php echo $optionThai; ?></option>
								<option value="vi" <?php echo $vi; ?>>vi.php &mdash; <?php echo $optionVietnamese; ?></option>
								<option value="yue" <?php echo $yue; ?>>yue.php &mdash; <?php echo $optionCantonese; ?></option>
							</select>
							<span class="help-block"><?php echo $localizationHelper; ?></span>
						</div>
						<div class="form-group">
							<label for="siteName"><?php echo $siteNameField; ?></label>
							<input type="text" class="form-control" name="siteName" id="siteName" value="<?php echo clean($row['siteName']); ?>" />
							<span class="help-block"><?php echo $siteNameHelper; ?></span>
						</div>
						<div class="form-group">
							<label for="businessName"><?php echo $businessNameField; ?></label>
							<input type="text" class="form-control" name="businessName" id="businessName" value="<?php echo clean($row['businessName']); ?>" />
						</div>
						<div class="form-group">
							<label for="businessAddress"><?php echo $businessAddressField; ?></label>
							<textarea class="form-control" name="businessAddress" id="businessAddress" rows="3"><?php echo clean($row['businessAddress']); ?></textarea>
							<span class="help-block"><?php echo $businessAddressHelper; ?></span>
						</div>
						<div class="errorNote"></div>
						<div class="form-group">
							<label for="businessEmail"><?php echo $businessEmailField; ?></label>
							<input type="text" class="form-control" name="businessEmail" id="businessEmail" value="<?php echo clean($row['businessEmail']); ?>" />
							<span class="help-block"><?php echo $businessEmailHelper; ?></span>
						</div>
						<div class="form-group">
							<label for="businessPhone"><?php echo $businessPhoneField; ?></label>
							<input type="text" class="form-control" name="businessPhone" id="businessPhone" value="<?php echo clean($row['businessPhone']); ?>" />
						</div>
						<div class="form-group">
							<label for="contactPhone"><?php echo $contactPhoneField; ?></label>
							<input type="text" class="form-control" name="contactPhone" id="contactPhone" value="<?php echo clean($row['contactPhone']); ?>" />
							<span class="help-block"><?php echo $contactPhoneHelper; ?></span>
						</div>
						<div class="form-group">
							<button type="input" name="submit" value="Update Settings" class="btn btn-success btn-icon"><i class="icon-check"></i> <?php echo $updateSettingsBtn; ?></button>
						</div>
					</div>
				</div>
			</div>

			<div class="panel panel-info">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo"><i class="fa fa-upload"></i> <?php echo $uploadsAccTitle; ?></a>
					</h4>
				</div>
				<div id="collapseTwo" class="panel-collapse collapse">
					<div class="panel-body">
						<div class="alertMsg warning">
							<i class="fa fa-warning"></i> <?php echo $uploadsNote; ?>
						</div>

						<div class="form-group">
							<label for="uploadPath"><?php echo $propFileUploadField; ?></label>
							<input type="text" class="form-control" name="uploadPath" id="uploadPath" value="<?php echo $row['uploadPath']; ?>" />
							<span class="help-block"><?php echo $propFileUploadHelper; ?></span>
						</div>
						<div class="form-group">
							<label for="templatesPath"><?php echo $templatesDirField; ?></label>
							<input type="text" class="form-control" name="templatesPath" id="templatesPath" value="<?php echo $row['templatesPath']; ?>" />
							<span class="help-block"><?php echo $templatesDirHelper; ?></span>
						</div>
						<div class="form-group">
							<label for="tenantDocsPath"><?php echo $tenantDocUploadField; ?></label>
							<input type="text" class="form-control" name="tenantDocsPath" id="tenantDocsPath" value="<?php echo $row['tenantDocsPath']; ?>" />
							<span class="help-block"><?php echo $tenantDocsUploadHelper; ?></span>
						</div>
						<div class="form-group">
							<label for="fileTypesAllowed"><?php echo $uploadTypesField; ?></label>
							<input type="text" class="form-control" name="fileTypesAllowed" id="fileTypesAllowed" value="<?php echo clean($row['fileTypesAllowed']); ?>" />
							<span class="help-block"><?php echo $uploadTypesHelper; ?></span>
						</div>
						<div class="form-group">
							<label for="avatarFolder"><?php echo $avatarUploadField; ?></label>
							<input type="text" class="form-control" name="avatarFolder" id="avatarFolder" value="<?php echo $row['avatarFolder']; ?>" />
							<span class="help-block"><?php echo $avatarUploadHelper; ?></span>
						</div>
						<div class="form-group">
							<label for="avatarTypes"><?php echo $avatarTypesField; ?></label>
							<input type="text" class="form-control" name="avatarTypes" id="avatarTypes" value="<?php echo clean($row['avatarTypes']); ?>" />
							<span class="help-block"><?php echo $avatarTypesHelper; ?></span>
						</div>
						<div class="form-group">
							<label for="propertyPicsPath"><?php echo $propPicUploadField; ?></label>
							<input type="text" class="form-control" name="propertyPicsPath" id="propertyPicsPath" value="<?php echo $row['propertyPicsPath']; ?>" />
							<span class="help-block"><?php echo $propPicUploadHelper; ?></span>
						</div>
						<div class="form-group">
							<label for="propertyPicTypes"><?php echo $propPicTypesField; ?></label>
							<input type="text" class="form-control" name="propertyPicTypes" id="propertyPicTypes" value="<?php echo clean($row['propertyPicTypes']); ?>" />
							<span class="help-block"><?php echo $propPicTypesHelper; ?></span>
						</div>
						<div class="form-group">
							<button type="input" name="submit" value="Update Settings" class="btn btn-success btn-icon"><i class="icon-check"></i> <?php echo $updUploadSettingsBtn; ?></button>
						</div>
					</div>
				</div>
			</div>

			<div class="panel panel-success">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseThree"><i class="fa fa-money"></i> <?php echo $paymentsAccTitle; ?></a>
					</h4>
				</div>
				<div id="collapseThree" class="panel-collapse collapse">
					<div class="panel-body">
						<div class="form-group">
							<label for="enablePayments"><?php echo $enablePayPalField; ?></label>
							<select class="form-control" id="enablePayments" name="enablePayments">
								<option value="0"><?php echo $OptionNo; ?></option>
								<option value="1" <?php echo $paymentsystem; ?>><?php echo $OptionYes; ?></option>
							</select>
							<span class="help-block"><?php echo $enablePayPalHelper; ?></span>
						</div>
						<div id="paymentSystem">
							<div class="form-group">
								<label for="paypalCurrency"><?php echo $payPalCurrencyField; ?></label>
								<input type="text" class="form-control" name="paypalCurrency" id="paypalCurrency" value="<?php echo clean($row['paypalCurrency']); ?>" />
							</div>
							<div class="form-group">
								<label for="paymentCompleteMsg"><?php echo $paymentCompletedField; ?></label>
								<input type="text" class="form-control" name="paymentCompleteMsg" id="paymentCompleteMsg" value="<?php echo clean($row['paymentCompleteMsg']); ?>" />
								<span class="help-block"><?php echo $paymentCompleteHelper; ?></span>
							</div>
							<div class="form-group">
								<label for="paypalEmail"><?php echo $payPalEmailField; ?></label>
								<input type="text" class="form-control" name="paypalEmail" id="paypalEmail" value="<?php echo clean($row['paypalEmail']); ?>" />
								<span class="help-block"><?php echo $payPalEmailHelper; ?></span>
							</div>
							<div class="form-group">
								<label for="paypalItemName"><?php echo $payPalItemField; ?></label>
								<input type="text" class="form-control" name="paypalItemName" id="paypalItemName" value="<?php echo clean($row['paypalItemName']); ?>" />
								<span class="help-block"><?php echo $payPalItemHelper; ?></span>
							</div>
							<div class="form-group">
								<label for="paypalFee"><?php echo $payPalFeeField; ?></label>
								<input type="text" class="form-control" name="paypalFee" id="paypalFee" value="<?php echo clean($row['paypalFee']); ?>" />
								<span class="help-block"><?php echo $payPalFeeHelper; ?></span>
							</div>
						</div>
						<div class="form-group">
							<button type="input" name="submit" value="Update Settings" class="btn btn-success btn-icon"><i class="icon-check"></i> <?php echo $updPaymentSettingsBtn; ?></button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
<?php } ?>