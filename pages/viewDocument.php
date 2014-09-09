<?php
	$docId = $_GET['docId'];

	// Get the Tenant Documents Uploads Folder from the Site Settings
	$tenantDocsPath = $set['tenantDocsPath'];

    // Get Tenant Document Data
    $query  = "SELECT
                tenantdocs.docId,
                tenantdocs.tenantId,
                tenantdocs.adminId,
				tenantdocs.docTitle,
                tenantdocs.docDesc,
				tenantdocs.docUrl,
				DATE_FORMAT(tenantdocs.docDate,'%M %d, %Y') AS docDate,
				admins.adminFirstName,
				admins.adminLastName
            FROM
                tenantdocs
				LEFT JOIN admins ON tenantdocs.adminId = admins.adminId
            WHERE
                tenantdocs.docId = ".$docId;
    $res = mysqli_query($mysqli, $query) or die('Error, retrieving Tenant Document failed. ' . mysqli_error());
	$row = mysqli_fetch_assoc($res);

	// Check that the Tenant has not minipulated the URL
	$check = $row['tenantId'];
	if ($check == $tenantId) {
?>
<h3 class="primary"><?php echo $viewDocumentH3.' '.$row['docTitle']; ?></h3>
<p class="lead"><?php echo $viewDocumentQuip; ?></p>

<hr />

<ul class="list-group">
  <li class="list-group-item"><?php echo $tab_uploadedBy.': '.clean($row['adminFirstName']).' '.clean($row['adminLastName']); ?></li>
  <li class="list-group-item"><?php echo $tab_dateUploaded.': '.$row['docDate']; ?></li>
  <li class="list-group-item"><?php echo $tab_description.': '.clean($row['docDesc']); ?></li>
</ul>

<hr />

<?php
	//Get File Extension
	$ext = substr(strrchr($row['docUrl'],'.'), 1);
	$imgExts = array('gif', 'GIF', 'jpg', 'JPG', 'jpeg', 'JPEG', 'png', 'PNG', 'tiff', 'TIFF', 'tif', 'TIF', 'bmp', 'BMP');

	if (in_array($ext, $imgExts)) {
		echo '<p><img src="'.$tenantDocsPath.$row['docUrl'].'" /></p>';
	} else {
		echo '
				<div class="alertMsg default"><i class="fa fa-info-circle"></i> No preview available for File: '.$row['docTitle'].'</div>
				<p>
					<a href="'.$tenantDocsPath.$row['docUrl'].'" class="btn btn-success" target="_blank">
					<i class="fa fa-download"></i> Download File: '.$row['docTitle'].'</a>
				</p>
			';
	}
} else {
?>
<h3 class="primary"><?php echo $accessErrorH3; ?></h3>
<div class="alertMsg warning"><i class="fa fa-ban"></i> <?php echo $permissionDenied; ?></div>
<?php } ?>