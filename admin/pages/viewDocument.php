<?php
	$docId = $_GET['docId'];

	// Get Documents Folder from Site Settings
	$docUploadPath = $set['tenantDocsPath'];

    // Get Property File Data
    $query  = "SELECT
                tenantdocs.docId,
                tenantdocs.tenantId,
                tenantdocs.adminId,
				tenantdocs.docTitle,
                tenantdocs.docDesc,
				tenantdocs.docUrl,
				DATE_FORMAT(tenantdocs.docDate,'%M %d, %Y') AS docDate,
				tenants.tenantId,
				tenants.tenantDocsFolder,
				tenants.tenantFirstName,
				tenants.tenantLastName,
				admins.adminFirstName,
				admins.adminLastName
            FROM
                tenantdocs
				LEFT JOIN tenants ON tenantdocs.tenantId = tenants.tenantId
				LEFT JOIN admins ON tenantdocs.adminId = admins.adminId
            WHERE
                tenantdocs.docId = ".$docId;
    $res = mysqli_query($mysqli, $query) or die('Error, retrieving Property File failed. ' . mysqli_error());
	$row = mysqli_fetch_assoc($res);
?>
<h3 class="primary"><?php echo $viewDocumentH3.' &mdash; '.$row['docTitle']; ?></h3>
<p class="lead"><?php echo $viewDocumentQuip; ?></p>

<ul class="list-group padTop">
	<li class="list-group-item"><?php echo $tab_tenant; ?>: <a href="index.php?action=tenantInfo&tenantId=<?php echo $row['tenantId']; ?>"><?php echo clean($row['tenantFirstName']).' '.clean($row['tenantLastName']); ?></a></li>
	<li class="list-group-item"><?php echo $tab_uploadedBy; ?>: <?php echo clean($row['adminFirstName']).' '.clean($row['adminLastName']); ?></li>
	<li class="list-group-item"><?php echo $tab_dateUploaded.': '.$row['docDate']; ?></li>
	<li class="list-group-item"><?php echo $tab_description.': '.clean($row['docDesc']); ?></li>
</ul>

<hr />

<?php
	//Get File Extension
	$ext = substr(strrchr($row['docUrl'],'.'), 1);
	$imgExts = array('gif', 'GIF', 'jpg', 'JPG', 'jpeg', 'JPEG', 'png', 'PNG', 'tiff', 'TIFF', 'tif', 'TIF', 'bmp', 'BMP');

	if (in_array($ext, $imgExts)) {
		echo '<p><img src="../'.$docUploadPath.$row['tenantDocsFolder'].'/'.$row['docUrl'].'" class="imgFrame" /></p>';
	} else {
		echo '
				<div class="alertMsg default"><i class="fa fa-info-circle"></i> No preview available for File: '.$row['docTitle'].'</div>
				<p>
					<a href="../'.$docUploadPath.$row['tenantDocsFolder'].'/'.$row['docUrl'].'" class="btn btn-success btn-icon" target="_blank">
					<i class="fa fa-download"></i> Download File: '.$row['docTitle'].'</a>
				</p>
			';
	}
?>