<?php
    // Get Tenant Documents Data
    $query  = "SELECT
                tenantdocs.docId,
                tenantdocs.tenantId,
                tenantdocs.adminId,
				tenantdocs.docTitle,
                tenantdocs.docDesc,
				DATE_FORMAT(tenantdocs.docDate,'%M %d, %Y') AS docDate,
				admins.adminFirstName,
				admins.adminLastName
            FROM
                tenantdocs
				LEFT JOIN admins ON tenantdocs.adminId = admins.adminId
            WHERE
                tenantdocs.tenantId = ".$_SESSION['tenantId'];
    $res = mysqli_query($mysqli, $query) or die('Error, retrieving Tenant Documents failed. ' . mysqli_error());
?>
<h3 class="primary"><?php echo $myDocumentsH3; ?></h3>
<p class="lead"><?php echo $myDocumentsQuip; ?></p>

<?php if (mysqli_num_rows($res) < 1) { ?>
	<div class="alertMsg default">
		<i class="fa fa-minus-square-o"></i> <?php echo $noDocsUploaded; ?>
	</div>
<?php } else { ?>
	<table id="responsiveTable" class="large-only" cellspacing="0">
		<tr align="left">
			<th><?php echo $tab_document; ?></th>
			<th><?php echo $tab_uploadedBy; ?></th>
			<th><?php echo $tab_description; ?></th>
			<th><?php echo $tab_dateUploaded; ?></th>
			<th></th>
		</tr>
		<tbody class="table-hover">
		<?php while ($row = mysqli_fetch_assoc($res)) { ?>
				<tr>
					<td><?php echo clean($row['docTitle']); ?></td>
					<td><?php echo clean($row['adminFirstName']).' '.clean($row['adminLastName']); ?></td>
					<td><?php echo clean(ellipsis($row['docDesc'])); ?></td>
					<td><?php echo $row['docDate']; ?></td>
					<td><a href="index.php?page=viewDocument&docId=<?php echo $row['docId']; ?>"><?php echo $td_view; ?> <i class="fa fa-long-arrow-right"></i></a></td>
				</tr>
		<?php } ?>
		</tbody>
	</table>
<?php } ?>