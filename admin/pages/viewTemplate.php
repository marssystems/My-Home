<?php
	$templateId = $_GET['templateId'];

	// Get Templates Folder from Site Settings
	$templatesPath = $set['templatesPath'];

	// Get Template Data
    $query  = "SELECT
                    sitetemplates.templateId,
                    sitetemplates.adminId,
                    sitetemplates.templateName,
					sitetemplates.templateDesc,
					sitetemplates.templateUrl,
                    DATE_FORMAT(sitetemplates.templateDate,'%M %d, %Y') AS templateDate,
					admins.adminFirstName,
					admins.adminLastName
                FROM
                    sitetemplates
					LEFT JOIN admins ON sitetemplates.adminId = admins.adminId
                WHERE
					sitetemplates.templateId = ".$templateId;
    $res = mysqli_query($mysqli, $query) or die('Error, retrieving Template Data failed. ' . mysqli_error());
	$row = mysqli_fetch_assoc($res);
?>
<h3 class="primary"><?php echo $viewTemplateH3.' '.$row['templateName']; ?></h3>
<p class="lead"><?php echo $viewTemplateQuip; ?></p>

<ul class="list-group padTop">
	<li class="list-group-item"><?php echo $tab_uploadedBy; ?>: <?php echo clean($row['adminFirstName']).' '.clean($row['adminLastName']); ?></li>
	<li class="list-group-item"><?php echo $tab_dateUploaded.': '.$row['templateDate']; ?></li>
	<li class="list-group-item"><?php echo $tab_description.': '.clean($row['templateDesc']); ?></li>
</ul>

<hr />

<?php
	//Get Template Extension
	$ext = substr(strrchr($row['templateUrl'],'.'), 1);
	$imgExts = array('gif', 'GIF', 'jpg', 'JPG', 'jpeg', 'JPEG', 'png', 'PNG', 'tiff', 'TIFF', 'tif', 'TIF', 'bmp', 'BMP');

	if (in_array($ext, $imgExts)) {
		echo '<p><img src="'.$templatesPath.$row['templateUrl'].'" class="imgFrame" /></p>';
	} else {
		echo '
				<div class="alertMsg default"><i class="fa fa-info-circle"></i> No preview available for Template: '.$row['templateName'].'</div>
				<p>
					<a href="'.$templatesPath.$row['templateUrl'].'" class="btn btn-success btn-icon" target="_blank">
					<i class="fa fa-download"></i> Download Template: '.$row['templateName'].'</a>
				</p>
			';
	}
?>