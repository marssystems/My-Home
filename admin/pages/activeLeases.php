<?php
	$stacktable = 'true';
	$jsFile = 'activeLeases';
	$datePicker = 'true';
	$count = 0;

	// Update Property Lease
    if (isset($_POST['submit']) && $_POST['submit'] == 'Update Lease') {
        // Validation
        if($_POST['leaseTerm'] == "") {
            $msgBox = alertBox($leaseTermReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['leaseStart'] == "") {
            $msgBox = alertBox($startDateReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['leaseEnd'] == "") {
            $msgBox = alertBox($endDateReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			// Set some variables
			$leaseId = $mysqli->real_escape_string($_POST['leaseId']);
			$leaseTerm = $mysqli->real_escape_string($_POST['leaseTerm']);
			$leaseStart = $mysqli->real_escape_string($_POST['leaseStart']);
			$leaseEnd = $mysqli->real_escape_string($_POST['leaseEnd']);
			$leaseNotes = htmlentities($_POST['leaseNotes']);
			$isClosed = $mysqli->real_escape_string($_POST['isClosed']);

			$stmt = $mysqli->prepare("
								UPDATE
									leases
								SET
									leaseTerm = ?,
									leaseStart = ?,
									leaseEnd = ?,
									leaseNotes = ?,
									isClosed = ?
								WHERE
									leaseId = ?");
			$stmt->bind_param('ssssss',
								$leaseTerm,
								$leaseStart,
								$leaseEnd,
								$leaseNotes,
								$isClosed,
								$leaseId
			);
			$stmt->execute();

			// If closing the Lease
			if ($isClosed == '1') {
				// Get the Property ID & Tenant ID
				$sql = "SELECT tenantId, propertyId FROM tenants WHERE leaseId = ".$leaseId;
				$result = mysqli_query($mysqli, $sql) or die(mysqli_error());
				$r = mysqli_fetch_assoc($result);
				$tenantId = $r['tenantId'];
				$propertyId = $r['propertyId'];

				$clearVal = '0';

				// Update the Tenant's Account
				$tenantstmt = $mysqli->prepare("UPDATE tenants
										SET
											propertyId = ?,
											leaseId = ?
										WHERE tenantId = ?");
				$tenantstmt->bind_param('sss', $clearVal, $clearVal, $tenantId);
				$tenantstmt->execute();
				$tenantstmt->close();

				// Set the Property to Unleased
				$propstmt = $mysqli->prepare("UPDATE properties
										SET
											isLeased = ?
										WHERE propertyId = ?");
				$propstmt->bind_param('ss', $clearVal, $propertyId);
				$propstmt->execute();
				$propstmt->close();

				// Remove the Assigned Landlord from the Property
				$delstmt = $mysqli->prepare("DELETE FROM assignedproperties WHERE propertyId = ".$propertyId);
				$delstmt->execute();
				$delstmt->close();
			}
			$msgBox = alertBox($leaseUpdatedMsg, "<i class='fa fa-check-square-o'></i>", "success");
			$stmt->close();
		}
	}

	// Get Active Lease Data
    $query = "SELECT
				leases.leaseId,
				leases.adminId,
				leases.propertyId,
				leases.leaseTerm,
				leases.leaseStart,
				DATE_FORMAT(leases.leaseStart,'%M %d, %Y') AS start,
				leases.leaseEnd,
				DATE_FORMAT(leases.leaseEnd,'%M %d, %Y') AS end,
				leaseNotes,
				leases.isClosed,
				properties.propertyName,
				tenants.tenantId,
				tenants.tenantFirstName,
				tenants.tenantLastName,
				admins.adminFirstName,
				admins.adminLastName
			FROM
				leases
				LEFT JOIN properties ON leases.propertyId = properties.propertyId
				LEFT JOIN tenants ON properties.propertyId = tenants.propertyId
				LEFT JOIN admins ON leases.adminId = admins.adminId
			WHERE
				leases.isClosed = 0";
    $res = mysqli_query($mysqli, $query) or die('Error, retrieving Active Lease Data failed. ' . mysqli_error());
?>
<h3 class="primary"><?php echo $activeLeasesH3; ?></h3>

<?php if ($msgBox) { echo $msgBox; } ?>

<?php if(mysqli_num_rows($res) < 1) { ?>
	<div class="alertMsg default">
		<i class="fa fa-minus-square-o"></i> <?php echo $noActiveLeasesMsg; ?>
	</div>
<?php } else { ?>
	<table id="responsiveTableTwo" class="large-only" cellspacing="0">
		<tr align="left">
			<th><?php echo $tab_property; ?></th>
			<th><?php echo $tab_tenant; ?></th>
			<th><?php echo $tab_createdBy; ?></th>
			<th><?php echo $tab_leaseTerm; ?></th>
			<th><?php echo $tab_leaseStartsOn; ?></th>
			<th><?php echo $tab_leaseEndsOn; ?></th>
			<th></th>
		</tr>
		<tbody class="table-hover">
		<?php while ($row = mysqli_fetch_assoc($res)) { ?>
			<tr>
				<td><a href="index.php?action=propertyInfo&propertyId=<?php echo $row['propertyId']; ?>"><?php echo clean($row['propertyName']); ?></a></td>
				<td><a href="index.php?action=tenantInfo&tenantId=<?php echo $row['tenantId']; ?>"><?php echo clean($row['tenantFirstName']).' '.clean($row['tenantLastName']); ?></a></td>
				<td><?php echo clean($row['adminFirstName']).' '.clean($row['adminLastName']); ?></td>
				<td><?php echo clean($row['leaseTerm']); ?></td>
				<td><?php echo $row['start']; ?></td>
				<td><?php echo $row['end']; ?></td>
				<td><a data-toggle="modal" href="#editLease<?php echo $row['leaseId']; ?>"><i class="fa fa-edit"></i> <?php echo $updateLeaseLink; ?></a></td>
			</tr>

			<!-- UPDATE PROPERTY LEASE MODAL -->
			<div class="modal fade" id="editLease<?php echo $row['leaseId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header modal-primary">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
							<h4 class="modal-title"><?php echo $editLeaseModalTitle.' '.clean($row['propertyName']); ?></h4>
						</div>
						<form action="" method="post">
							<div class="modal-body">
								<div class="form-group">
									<label for="leaseTerm"><?php echo $leaseTermField; ?></label>
									<input type="text" class="form-control" name="leaseTerm" value="<?php echo clean($row['leaseTerm']); ?>">
									<span class="help-block"><?php echo $leaseTermHelper; ?></span>
								</div>
								<div class="form-group">
									<label for="leaseStart"><?php echo $leaseStartField; ?></label>
									<input type="text" class="form-control" name="leaseStart" id="leaseStart[<?php echo $count; ?>]" value="<?php echo $row['leaseStart']; ?>">
									<span class="help-block"><?php echo $leaseStartHelper; ?></span>
								</div>
								<div class="form-group">
									<label for="leaseEnd"><?php echo $leaseEndField; ?></label>
									<input type="text" class="form-control" name="leaseEnd" id="leaseEnd[<?php echo $count; ?>]" value="<?php echo $row['leaseEnd']; ?>">
									<span class="help-block"><?php echo $leaseEndHelper; ?></span>
								</div>
								<div class="form-group">
									<label for="leaseNotes"><?php echo $leaseNotesField; ?></label>
									<textarea class="form-control" name="leaseNotes" rows="2"><?php echo clean($row['leaseNotes']); ?></textarea>
									<span class="help-block"><?php echo $leaseNotesHelper.' '.$htmlNotAllowed; ?></span>
								</div>
								<div class="form-group">
									<label for="isClosed"><?php echo $closeLeaseField; ?></label>
									<select class="form-control" name="isClosed">
										<option value="0"><?php echo $noBtn; ?></option>
										<option value="1"><?php echo $yesBtn; ?></option>
									</select>
									<span class="help-block"><?php echo $closeLeaseHelper; ?></span>
								</div>
							</div>
							<div class="modal-footer">
								<input type="hidden" name="leaseId" value="<?php echo $row['leaseId']; ?>">
								<button type="input" name="submit" value="Update Lease" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveChangesBtn; ?></button>
								<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
							</div>
						</form>
					</div>
				</div>
			</div>
		<?php
			$count++;
		}
		?>
		</tbody>
	</table>
<?php }	?>