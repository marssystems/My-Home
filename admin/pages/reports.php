<?php
	$jsFile = 'reports';
	$datePicker = 'true';

	// Check for any Tenants
	$check1  = "SELECT 'X' FROM tenants";
	$res1 = mysqli_query($mysqli, $check1) or die('-1' . mysqli_error());

	// Check for any Properties
	$check2  = "SELECT 'X' FROM properties";
	$res2 = mysqli_query($mysqli, $check2) or die('-2' . mysqli_error());

	// Check for any Service Requests
	$check3  = "SELECT 'X' FROM servicerequests";
	$res3 = mysqli_query($mysqli, $check3) or die('-3' . mysqli_error());

	// Check for any Payments
	$check4  = "SELECT 'X' FROM payments";
	$res4 = mysqli_query($mysqli, $check4) or die('-4' . mysqli_error());
	
	// Check for any Refunds
	$check5  = "SELECT 'X' FROM refunds";
	$res5 = mysqli_query($mysqli, $check5) or die('-5' . mysqli_error());

	// Check for any Leases
	$check6  = "SELECT 'X' FROM leases";
	$res6 = mysqli_query($mysqli, $check6) or die('-6' . mysqli_error());
?>
<h3 class="primary"><?php echo $reportsH3; ?></h3>
<p class="lead"><?php echo $reportsQuip; ?></p>
<p><?php echo $reportInst; ?></p>

<?php if ($msgBox) { echo $msgBox; } ?>

<div class="row padTop">
	<div class="col-md-6">
		<div class="panel-group" id="accordion">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#tenants"><i class="fa fa-group"></i> <?php echo $tenantReportsTitle; ?></a>
					</h4>
				</div>
				<div id="tenants" class="panel-collapse collapse">
					<div class="panel-body">
						<?php if(mysqli_num_rows($res1) < 1) { ?>
							<div class="alertMsg default">
								<i class="fa fa-minus-square-o"></i> <?php echo $noTenantsFoundMsg; ?>
							</div>
						<?php } else { ?>
							<div class="reportSet">
								<h5 class="primary"><?php echo $report1Title; ?></h5>
								<form action="index.php?action=tenantReport" method="post">
									<div class="form-group">
										<label for="includeInactive"><?php echo $includeInactiveField; ?></label>
										<select class="form-control" name="includeInactive">
											<option value="0"><?php echo $OptionNo; ?></option>
											<option value="1"><?php echo $OptionYes; ?></option>
										</select>
										<span class="help-block"><?php echo $includeInactiveHelper; ?></span>
									</div>
									<button type="input" name="submit" class="btn btn-success"><?php echo $runReportBtn; ?> <i class="fa fa-long-arrow-right"></i></button>
								</form>
							</div>
							
							<div class="reportSet padTop">
								<h5 class="primary"><?php echo $report2Title; ?></h5>
								<form action="index.php?action=tenantArchiveReport" method="post">
									<div class="form-group">
										<label><?php echo $includeArchivedHelper; ?></label>
									</div>
									<button type="input" name="submit" class="btn btn-success"><?php echo $runReportBtn; ?> <i class="fa fa-long-arrow-right"></i></button>
								</form>	
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>

		<div class="panel-group padTop" id="accordion1">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion1" href="#properties"><i class="fa fa-building"></i> <?php echo $propertyReportsTitle; ?></a>
					</h4>
				</div>
				<div id="properties" class="panel-collapse collapse">
					<div class="panel-body">
						<?php if(mysqli_num_rows($res2) < 1) { ?>
							<div class="alertMsg default">
								<i class="fa fa-minus-square-o"></i> <?php echo $noPropertiesFoundMsg; ?>
							</div>
						<?php } else { ?>
							<div class="reportSet">
								<h5 class="info"><?php echo $report3Title; ?></h5>
								<form action="index.php?action=propertyReport" method="post">
									<div class="form-group">
										<label for="propertyType"><?php echo $selectPropertyTypeField; ?></label>
										<select class="form-control" name="propertyType">
											<option value="0"><?php echo $propertyType1; ?></option>
											<option value="1"><?php echo $propertyType2; ?></option>
											<option value="2"><?php echo $propertyType3; ?></option>
											<option value="3"><?php echo $propertyType4; ?></option>
										</select>
										<span class="help-block"><?php echo $selectPropertyTypeHelper; ?></span>
									</div>
									<button type="input" name="submit" class="btn btn-success"><?php echo $runReportBtn; ?> <i class="fa fa-long-arrow-right"></i></button>
								</form>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
			
		<div class="panel-group padTop" id="accordion2">			
			<div class="panel panel-warning">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#service"><i class="fa fa-wrench"></i> <?php echo $serviceReportsTitle; ?></a>
					</h4>
				</div>
				<div id="service" class="panel-collapse collapse">
					<div class="panel-body">
						<?php if(mysqli_num_rows($res3) < 1) { ?>
							<div class="alertMsg default">
								<i class="fa fa-minus-square-o"></i> <?php echo $noRequestsFoundMsg; ?>
							</div>
						<?php } else { ?>
							<div class="reportSet">
								<h5 class="warning"><?php echo $report4Title; ?></h5>
								<form id="serviceReport" method="post">
									<div class="form-group">
										<label for="requestType"><?php echo $serviceIncludeField; ?></label>
										<select class="form-control" name="requestType">
											<option value="0"><?php echo $requestType1; ?></option>
											<option value="1"><?php echo $requestType2; ?></option>
											<option value="2"><?php echo $requestType3; ?></option>
										</select>
										<span class="help-block"><?php echo $serviceIncludeHelper; ?></span>
									</div>
									<div class="form-group">
										<?php
											$sql1 = "SELECT tenantId, tenantFirstName, tenantLastName FROM tenants WHERE isArchived = 0 AND leaseId != 0";
											$result1 = mysqli_query($mysqli, $sql1) or die('-7' . mysqli_error());
										?>
										<label for="tenantId"><?php echo $selectTenantField; ?></label>
										<select class="form-control" name="tenantId">
											<option value="all"><?php echo $allOption; ?></option>
											<?php while ($rows = mysqli_fetch_assoc($result1)) { ?>
												<option value="<?php echo $rows['tenantId']; ?>"><?php echo clean($rows['tenantFirstName']).' '.clean($rows['tenantLastName']); ?></option>
											<?php } ?>
										</select>
										<span class="help-block"><?php echo $allOptionHelper; ?></span>
									</div>
									<div class="form-group">
										<label for="serviceFromDate"><?php echo $fromDateField; ?></label>
										<div class="input-group">
											<input type="text" class="form-control" name="serviceFromDate" id="serviceFromDate" value="">
											<span class="input-group-addon tool-tip" title="Required Field"><i class="fa fa-asterisk requiredField"></i></span>
										</div>
										<span class="help-block"><?php echo $fromDateHelper; ?></span>
									</div>
									<div class="form-group">
										<label for="serviceToDate"><?php echo $toDateField; ?></label>
										<div class="input-group">
											<input type="text" class="form-control" name="serviceToDate" id="serviceToDate" value="">
											<span class="input-group-addon tool-tip" title="Required Field"><i class="fa fa-asterisk requiredField"></i></span>
										</div>
										<span class="help-block"><?php echo $toDateHelper; ?></span>
									</div>
									<button type="button" class="btn btn-success" id="servReportBtn"><?php echo $runReportBtn; ?> <i class="fa fa-long-arrow-right"></i></button>
								</form>
							</div>
							
							<div class="reportSet padTop">
								<h5 class="warning"><?php echo $report5Title; ?></h5>
								<form id="serviceCostsReport" method="post">
									<div class="form-group">
										<?php
											$sql2 = "SELECT tenantId, tenantFirstName, tenantLastName FROM tenants WHERE isArchived = 0 AND leaseId != 0";
											$result2 = mysqli_query($mysqli, $sql2) or die('-8' . mysqli_error());
										?>
										<label for="tenantId"><?php echo $selectTenantField; ?></label>
										<select class="form-control" name="tenantId">
											<option value="all"><?php echo $allOption; ?></option>
											<?php while ($rows = mysqli_fetch_assoc($result2)) { ?>
												<option value="<?php echo $rows['tenantId']; ?>"><?php echo clean($rows['tenantFirstName']).' '.clean($rows['tenantLastName']); ?></option>
											<?php } ?>
										</select>
										<span class="help-block"><?php echo $allOptionHelper; ?></span>
									</div>
									<div class="form-group">
										<label for="serviceCostsFromDate"><?php echo $fromDateField; ?></label>
										<div class="input-group">
											<input type="text" class="form-control" name="serviceCostsFromDate" id="serviceCostsFromDate" value="">
											<span class="input-group-addon tool-tip" title="Required Field"><i class="fa fa-asterisk requiredField"></i></span>
										</div>
										<span class="help-block"><?php echo $fromDateHelper; ?></span>
									</div>
									<div class="form-group">
										<label for="serviceCostsToDate"><?php echo $toDateField; ?></label>
										<div class="input-group">
											<input type="text" class="form-control" name="serviceCostsToDate" id="serviceCostsToDate" value="">
											<span class="input-group-addon tool-tip" title="Required Field"><i class="fa fa-asterisk requiredField"></i></span>
										</div>
										<span class="help-block"><?php echo $toDateHelper; ?></span>
									</div>
									<button type="button" class="btn btn-success" id="servCostsReportBtn"><?php echo $runReportBtn; ?> <i class="fa fa-long-arrow-right"></i></button>
								</form>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="panel-group" id="accordion3">
			<div class="panel panel-success">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion3" href="#accounting"><i class="fa fa-money"></i> <?php echo $accountingReportsTitle; ?></a>
					</h4>
				</div>
				<div id="accounting" class="panel-collapse collapse">
					<div class="panel-body">
						<?php if(mysqli_num_rows($res4) < 1) { ?>
							<div class="alertMsg default">
								<i class="fa fa-minus-square-o"></i> <?php echo $noPaymentsFoundMsg; ?>
							</div>
						<?php } else { ?>
							<div class="reportSet">
								<h5 class="success"><?php echo $report6Title; ?></h5>
								<form id="paymentsReport" method="post">
									<div class="form-group">
										<label for="paymentType"><?php echo $allPaymentsField; ?></label>
										<select class="form-control" name="paymentType">
											<option value="0"><?php echo $allPaymentsNoOption; ?></option>
											<option value="1"><?php echo $allPaymentsYesOption; ?></option>
										</select>
										<span class="help-block"><?php echo $allPaymentsHelper; ?></span>
									</div>
									<div class="form-group">
										<?php
											$sql3 = "SELECT tenantId, tenantFirstName, tenantLastName FROM tenants WHERE isArchived = 0 AND leaseId != 0";
											$result3 = mysqli_query($mysqli, $sql3) or die('-9' . mysqli_error());
										?>
										<label for="tenantId"><?php echo $selectTenantField; ?></label>
										<select class="form-control" name="tenantId">
											<option value="all"><?php echo $allOption; ?></option>
											<?php while ($rows = mysqli_fetch_assoc($result3)) { ?>
												<option value="<?php echo $rows['tenantId']; ?>"><?php echo clean($rows['tenantFirstName']).' '.clean($rows['tenantLastName']); ?></option>
											<?php } ?>
										</select>
										<span class="help-block"><?php echo $allOptionHelper; ?></span>
									</div>
									<div class="form-group">
										<label for="paymentsFromDate"><?php echo $fromDateField; ?></label>
										<div class="input-group">
											<input type="text" class="form-control" name="paymentsFromDate" id="paymentsFromDate" value="">
											<span class="input-group-addon tool-tip" title="Required Field"><i class="fa fa-asterisk requiredField"></i></span>
										</div>
										<span class="help-block"><?php echo $fromDateHelper; ?></span>
									</div>
									<div class="form-group">
										<label for="paymentsToDate"><?php echo $toDateField; ?></label>
										<div class="input-group">
											<input type="text" class="form-control" name="paymentsToDate" id="paymentsToDate" value="">
											<span class="input-group-addon tool-tip" title="Required Field"><i class="fa fa-asterisk requiredField"></i></span>
										</div>
										<span class="help-block"><?php echo $toDateHelper; ?></span>
									</div>
									<button type="button" class="btn btn-success" id="paymentsReportBtn"><?php echo $runReportBtn; ?> <i class="fa fa-long-arrow-right"></i></button>
								</form>
							</div>
							
							<div class="reportSet padTop">
								<h5 class="success"><?php echo $report7Title; ?></h5>
								<form id="refundsReport" method="post">
									<div class="form-group">
										<?php
											$sql4 = "SELECT tenantId, tenantFirstName, tenantLastName FROM tenants WHERE isArchived = 0 AND leaseId != 0";
											$result4 = mysqli_query($mysqli, $sql4) or die('-10' . mysqli_error());
										?>
										<label for="tenantId"><?php echo $selectTenantField; ?></label>
										<select class="form-control" name="tenantId">
											<option value="all"><?php echo $allOption; ?></option>
											<?php while ($rows = mysqli_fetch_assoc($result4)) { ?>
												<option value="<?php echo $rows['tenantId']; ?>"><?php echo clean($rows['tenantFirstName']).' '.clean($rows['tenantLastName']); ?></option>
											<?php } ?>
										</select>
										<span class="help-block"><?php echo $allOptionHelper; ?></span>
									</div>
									<div class="form-group">
										<label for="refundsFromDate"><?php echo $fromDateField; ?></label>
										<div class="input-group">
											<input type="text" class="form-control" name="refundsFromDate" id="refundsFromDate" value="">
											<span class="input-group-addon tool-tip" title="Required Field"><i class="fa fa-asterisk requiredField"></i></span>
										</div>
										<span class="help-block"><?php echo $fromDateHelper; ?></span>
									</div>
									<div class="form-group">
										<label for="refundsToDate"><?php echo $toDateField; ?></label>
										<div class="input-group">
											<input type="text" class="form-control" name="refundsToDate" id="refundsToDate" value="">
											<span class="input-group-addon tool-tip" title="Required Field"><i class="fa fa-asterisk requiredField"></i></span>
										</div>
										<span class="help-block"><?php echo $toDateHelper; ?></span>
									</div>
									<button type="button" class="btn btn-success" id="refundsReportBtn"><?php echo $runReportBtn; ?> <i class="fa fa-long-arrow-right"></i></button>
								</form>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
		
		<div class="panel-group padTop" id="accordion4">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion4" href="#leases"><i class="fa fa-file-o"></i> <?php echo $leaseReportsTitle; ?></a>
					</h4>
				</div>
				<div id="leases" class="panel-collapse collapse">
					<div class="panel-body">
						<?php if(mysqli_num_rows($res6) < 1) { ?>
							<div class="alertMsg default">
								<i class="fa fa-minus-square-o"></i> <?php echo $noLeasesFoundMsg; ?>
							</div>
						<?php } else { ?>
							<div class="reportSet">
								<h5 class="primary"><?php echo $report8Title; ?></h5>
								<form action="index.php?action=leaseReport" method="post">
									<div class="form-group">
										<label for="leaseType"><?php echo $closedLeasesField; ?></label>
										<select class="form-control" name="leaseType">
											<option value="0"><?php echo $closedLeasesNoOption; ?></option>
											<option value="1"><?php echo $closedLeasesYesOption; ?></option>
										</select>
										<span class="help-block"><?php echo $closedLeasesHelper; ?></span>
									</div>
									<button type="input" name="submit" class="btn btn-success"><?php echo $runReportBtn; ?> <i class="fa fa-long-arrow-right"></i></button>
								</form>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
		
		<?php if ($superuser == '1') { ?>
			<div class="panel-group padTop" id="accordion5">
				<div class="panel panel-danger">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion5" href="#admins"><i class="fa fa-male"></i> <?php echo $adminReportsTitle; ?></a>
						</h4>
					</div>
					<div id="admins" class="panel-collapse collapse">
						<div class="panel-body">
							<div class="reportSet">
								<h5 class="danger"><?php echo $report9Title; ?></h5>
								<form action="index.php?action=adminReport" method="post">
									<div class="form-group">
										<label for="adminType"><?php echo $inactiveAdminsField; ?></label>
										<select class="form-control" name="adminType">
											<option value="0"><?php echo $inactiveAdminsNoOption; ?></option>
											<option value="1"><?php echo $inactiveAdminsYesOption; ?></option>
										</select>
										<span class="help-block"><?php echo $inactiveAdminsHelper; ?></span>
									</div>
									<button type="input" name="submit" class="btn btn-success"><?php echo $runReportBtn; ?> <i class="fa fa-long-arrow-right"></i></button>
								</form>
							</div>
							
							<div class="reportSet padTop">
								<h5 class="danger"><?php echo $report0Title; ?></h5>
								<form action="index.php?action=assignedReport" method="post">
									<div class="form-group">
										<?php
											$sql5 = "SELECT adminId, adminFirstName, adminLastName FROM admins WHERE isActive = 1";
											$result5 = mysqli_query($mysqli, $sql5) or die('-10' . mysqli_error());
										?>
										<label for="adminId"><?php echo $selectAdminField; ?></label>
										<select class="form-control" name="adminId">
											<option value="all"><?php echo $allOption; ?></option>
											<?php while ($rows = mysqli_fetch_assoc($result5)) { ?>
												<option value="<?php echo $rows['adminId']; ?>"><?php echo clean($rows['adminFirstName']).' '.clean($rows['adminLastName']); ?></option>
											<?php } ?>
										</select>
										<span class="help-block"><?php echo $selectAdminHelper; ?></span>
									</div>
									<button type="input" name="submit" class="btn btn-success"><?php echo $runReportBtn; ?> <i class="fa fa-long-arrow-right"></i></button>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php } ?>
	</div>
</div>

<!-- ERROR NOTIFICATION MODAL -->
<div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<div class="errorMsg"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $okBtn; ?></button>
			</div>
		</div>
	</div>
</div>