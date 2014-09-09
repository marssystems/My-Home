<?php
	$fileId = $_GET['fileId'];

	// Get the File Uploads Folder from the Site Settings
	$uploadPath = $set['uploadPath'];

    // Get Property File Data
    $query  = "SELECT
                propertyfiles.fileId,
                propertyfiles.propertyId,
                propertyfiles.adminId,
				propertyfiles.fileName,
                propertyfiles.fileDesc,
				propertyfiles.fileUrl,
				DATE_FORMAT(propertyfiles.fileDate,'%M %d, %Y') AS fileDate,
				properties.propertyName,
				properties.propertyFolder,
				tenants.tenantId,
				admins.adminFirstName,
				admins.adminLastName
            FROM
                propertyfiles
				LEFT JOIN properties ON propertyfiles.propertyId = properties.propertyId
				LEFT JOIN tenants ON propertyfiles.propertyId = tenants.propertyId
				LEFT JOIN admins ON propertyfiles.adminId = admins.adminId
            WHERE
                propertyfiles.fileId = ".$fileId;
    $res = mysqli_query($mysqli, $query) or die('Error, retrieving Property File failed. ' . mysqli_error());
	$row = mysqli_fetch_assoc($res);
?>
<h3 class="primary"><?php echo $viewFileH3.' &mdash; '.$row['fileName']; ?></h3>
<p class="lead"><?php echo $viewDocumentQuip; ?></p>

<ul class="list-group padTop">
	<li class="list-group-item"><?php echo $tab_property; ?>: <a href="index.php?action=propertyInfo&propertyId=<?php echo $row['propertyId']; ?>"><?php echo clean($row['propertyName']); ?></a></li>
	<li class="list-group-item"><?php echo $tab_uploadedBy; ?>: <?php echo clean($row['adminFirstName']).' '.clean($row['adminLastName']); ?></li>
	<li class="list-group-item"><?php echo $tab_dateUploaded.': '.$row['fileDate']; ?></li>
	<li class="list-group-item"><?php echo $tab_description.': '.clean($row['fileDesc']); ?></li>
</ul>

<hr />

<?php
	//Get File Extension
	$ext = substr(strrchr($row['fileUrl'],'.'), 1);
	$imgExts = array('gif', 'GIF', 'jpg', 'JPG', 'jpeg', 'JPEG', 'png', 'PNG', 'tiff', 'TIFF', 'tif', 'TIF', 'bmp', 'BMP');

	if (in_array($ext, $imgExts)) {
		echo '<p><img src="../'.$uploadPath.$row['propertyFolder'].'/'.$row['fileUrl'].'" class="imgFrame" /></p>';
	} else {
		echo '
				<div class="alertMsg default"><i class="fa fa-info-circle"></i> No preview available for File: '.$row['fileName'].'</div>
				<p>
					<a href="../'.$uploadPath.$row['propertyFolder'].'/'.$row['fileUrl'].'" class="btn btn-success btn-icon" target="_blank">
					<i class="fa fa-download"></i> Download File: '.$row['fileName'].'</a>
				</p>
			';
	}
?>