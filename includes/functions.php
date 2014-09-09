<?php
	/*
     * Function to convert a number into readable Currency
     *
     * @param string $n   			The number
     * @param string $n_decimals	The decimal position
     * @return string           	The formated Currency Amount
	 *
	 * Returns string type, rounded number - same as php number_format()):
	 *
	 * Examples:
	 *		format_amount(54.377, 2) 	returns 54.38
	 *		format_amount(54.004, 2) 	returns 54.00
	 *		format_amount(54.377, 3) 	returns 54.377
	 *		format_amount(54.00007, 3) 	returns 54.00
     */
	function format_amount($n, $n_decimals) {
        return ((floor($n) == round($n, $n_decimals)) ? number_format($n).'.00' : number_format($n, $n_decimals));
    }

    /*
     * Function to show an Alert type Message Box
     *
     * @param string $message   The Alert Message
     * @param string $icon      The Font Awesome Icon
     * @param string $type      The CSS style to apply
     * @return string           The Alert Box
     */
    function alertBox($message, $icon = "", $type = "") {
        return "<div class=\"alertMsg $type\"><span>$icon</span> $message <a class=\"alert-close\" href=\"#\">x</a></div>";
    }

    /*
     * Function to ellipse-ify text to a specific length
     *
     * @param string $text      The text to be ellipsified
     * @param int    $max       The maximum number of characters (to the word) that should be allowed
     * @param string $append    The text to append to $text
     * @return string           The shortened text
     */
    function ellipsis($text, $max = 75, $append = '&hellip;') {
        if (strlen($text) <= $max) return $text;

        $replacements = array(
            '|<br /><br />|' => ' ',
            '|&nbsp;|' => ' ',
            '|&rsquo;|' => '\'',
            '|&lsquo;|' => '\'',
            '|&ldquo;|' => '"',
            '|&rdquo;|' => '"',
        );

        $patterns = array_keys($replacements);
        $replacements = array_values($replacements);

        // Convert double newlines to spaces.
        $text = preg_replace($patterns, $replacements, $text);
        // Remove any HTML.  We only want text.
        $text = strip_tags($text);
        $out = substr($text, 0, $max);
        if (strpos($text, ' ') === false) return $out.$append;
        return preg_replace('/(\W)&(\W)/', '$1&amp;$2', (preg_replace('/\W+$/', ' ', preg_replace('/\w+$/', '', $out)))).$append;
    }

    /*
     * Function to Encrypt user sensitive data for storing in the database
     *
     * @param string	$value		The text to be encrypted
	 * @param 			$encodeKey	The Key to use in the encrytion
     * @return						The encrypted text
     */
	function encryptIt($value) {
		// The encodeKey MUST match the decodeKey
		$encodeKey = 'DvHtl3CGp4QLuuOEtBQ2AS';
		$encoded = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($encodeKey), $value, MCRYPT_MODE_CBC, md5(md5($encodeKey))));
		return($encoded);
	}

    /*
     * Function to decrypt user sensitive data for displaying to the user
     *
     * @param string	$value		The text to be decrypted
	 * @param 			$decodeKey	The Key to use for decryption
     * @return						The decrypted text
     */
	function decryptIt($value) {
		// The decodeKey MUST match the encodeKey
		$decodeKey = 'DvHtl3CGp4QLuuOEtBQ2AS';
		$decoded = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($decodeKey), base64_decode($value), MCRYPT_MODE_CBC, md5(md5($decodeKey))), "\0");
		return($decoded);
	}

	/*
     * Function to strip slashes for displaying content to the user
     *
     * @param string	$value		The string to be stripped
     * @return						The stripped text
     */
	function clean($value) {
		$str = str_replace('\\', '', $value);
		return $str;
	}

	/*
     * Get all of the Admins emails for use in form submits from Tenants
     */
	$adminsql = "SELECT adminEmail FROM admins";
	$adminresult = mysqli_query($mysqli, $adminsql) or die('Error, retrieving Admin email list failed. ' . mysqli_error());

	// Set each admin email into a csv
	$emailAdmins = array();
	while ($admin = mysqli_fetch_assoc($adminresult)) {
		$emailAdmins[] = $admin['adminEmail'];
	}
	$allAdmins = implode(',',$emailAdmins);

	/*
     * Get all of the Tenant emails for use in form submits from Admins
     */
	$tenantsql = "SELECT tenantEmail FROM tenants";
	$tenantresult = mysqli_query($mysqli, $tenantsql) or die('Error, retrieving Tenant email list failed. ' . mysqli_error());

	// Set each Tenant email into a csv
	$emailTenants = array();
	while ($tenants = mysqli_fetch_assoc($tenantresult)) {
		$emailTenants[] = $tenants['tenantEmail'];
	}
	$allTenants = implode(',',$emailTenants);
?>