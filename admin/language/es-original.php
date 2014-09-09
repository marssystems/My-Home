<?php
// All Pages - Globals
// --------------------------------------------------------------------------------------------------
$currencySym 				= "$";
$amountReflectRefund 		= "Amount reflects a Refund";
$accessErrorH3				= "Access Error";
$permissionDenied			= "Permission Denied. You can not access this page.";
$htmlNotAllowed				= "HTML is not allowed &amp; will be saved as plain text.";
$htmlNotAllowedAlt			= "HTML is not allowed.";
$numbersOnly				= "Numbers Only (ie. 99.00).";
$OptionNo					= "No";
$OptionYes					= "Yes";
$monthNoneSelect			= "None";
$monthJanuarySelect			= "Enero";
$monthFebruarySelect		= "Febrero";
$monthMarchSelect			= "Marzo";
$monthAprilSelect			= "Abril";
$monthMaySelect				= "Mayo";
$monthJuneSelect			= "Junio";
$monthJulySelect			= "Julio";
$monthAugustSelect			= "Agosto";
$monthSeptemberSelect		= "Septiembre";
$monthOctoberSelect			= "Octubre";
$monthNovemberSelect		= "Noviembre";
$monthDecemberSelect		= "Deciembre";

// Admin login
// --------------------------------------------------------------------------------------------------
$adminLogin 				= "Admin/Propietario Login";
$loginWelcomeMsg 			= "Bienvenido a ".$set['siteName'];
$resetPassword	 			= "Resetear Password";
$loginInstructions 			= "Ingrese su informacion de cuenta Administrador/Propietario y haga click en \"Ingresar\".";

$emailAddressMsg 			= "Please enter your Admin account email address.";
$emptyPasswordMsg 			= "The Password field can not be empty.";
$loginFailedMsg 			= "Log in failed, Please check your entries and try again.";
$inactiveAccountMsg 		= "Your account has been deactivated, and you can not log in.";
$passwordResetMsg 			= "Your password has been reset.";
$accountNotFoundMsg 		= "Account not found for that email address.";

// Admin Header
// --------------------------------------------------------------------------------------------------
$pageHeadTitle				= "Administration";
$welcomeMsg 				= "Howdy";
$todayMsg 					= "Hoy es";
$signoutConf 				= "are you sure you want to signout?";

$activeAccountHelper		= "Selecting No will require the Tenant to activate the Account via a link sent to the account email address.";
$tenantsEmailHelper			= "The Tenant's email. Used for logging in and notifications.";
$adminsEmailHelper			= "The Admin's email. Used for logging in and notifications.";
$passwordHelper				= "Set the new account password. <strong>Passwords are case-sensitive</strong>.";
$repeatPasswordHelper		= "Type the account password again. Passwords MUST Match.";
$propertyNameHelper			= "This is usually the Development or Sub-division Name";
$propertyDescHelper			= "A short description about the Property. Visible to the Tenant.";

$numberOnlyHelper			= "Numbers Only (ie. 1500.00)";
$latePeneltyHelper			= "The late payment penalty amount. Numbers Only (ie. 35.00)";
$propertyNotesHelper		= "Internal Notes about the Property.";

$normalSelect				= "Normal";
$importantSelect			= "Important";
$urgenSelect				= "Urgent";
$serviceTitleHelper			= "Please give your Service Request a Title.";
$servicePriorityHelper		= "Please choose a Priority Level for this Request.";
$beDescriptiveHelper		= "Please be as descriptive as possible.";

$adminsSetActive			= "New Accounts are automatically set to Active.";
$superuserNo				= "Normal";
$superuserYes				= "Superuser";
$superuserHelper			= "Normal: Cannot Add/Modify other Admins, Limited Payment System Access.<br />Superuser: Full Add/Modify &amp; Delete permissions.";
$adminRoleAdmin				= "Supervisor";
$adminRoleLandlord			= "Landlord";
$adminRoleTech				= "Service Technician";
$selectRoleHelper			= "Select the roll this Admin will fill.";

$tenantEmailReq				= "Please enter a valid email for the Tenant.";
$tenantPasswordReq			= "The Tenant will need a password.";
$passwordMismatch			= "Passwords do not match. Please check your entries.";
$tenantFirstNameReq			= "Please enter the Tenant's First Name.";
$tenantLastNameReq			= "Please enter the Tenant's Last Name.";
$duplicateEmail				= "There is all ready an account registered with that email address.";
$tenantAccountCreated		= "The New Tenant Account has been created and an email has been sent.";
$tenantAccountActivated 	= "The New Tenant Account has been created and has been set to Active.";
$accountEmailError			= "There was an error and the email could not be sent at this time.";

$propertyNameReq			= "Please enter the Property's name.";
$propertyDescReq			= "Please enter a short description of the Property.";
$monthlyRateReq				= "Please enter the Property's Monthly Rental Rate.";
$latePeneltyReq				= "Please enter the Late Rent payment Penalty amount.";
$depositAmountReq			= "Please enter the Deposit Amount Required.";
$newPropertyCreated			= "The New Property has been created.";

$leaseIdReq					= "Please select the Tenant this Service Request is for.";
$requestTitleReqMsg			= "Please enter a Title for the Service Request.";
$requestDescReqMsg			= "Please type the details of the Service Request.";
$requestCreated				= "The New Service Request has been created for the Tenant.";

$adminEmailReq				= "Please enter a valid email for the Admin.";
$adminPasswordReq			= "The new Admin will need a password.";
$adminFirstNameReq			= "Please enter the Admin's First Name.";
$adminLastNameReq			= "Please enter the Admin's Last Name.";
$adminAccountCreated		= "The New Admin Account has been created and has been set to Active.";

// Admin Navigation
// --------------------------------------------------------------------------------------------------
$dashboardNav 				= "Dashboard";

$tenantsNav					= "Tenants";
$activeTenantsNav			= "Active Tenants";
$inactiveTenantsNav			= "Inactive Tenants";
$newTenantNav				= "Add a New Tenant";
$archivedTenantsNav			= "Archived Tenants";

$propertiesNav				= "Properties";
$activePropertiesNav		= "Active Properties";
$newPropertyNav				= "Add a New Property";
$archivedPropertiesNav		= "Archived Properties";

$propertyLeasesNav			= "Property Leases";
$activeLeasesNav			= "Active Leases";
$archivedLeasesNav			= "Archived/Closed Leases";

$serviceRequestsNav			= "Service Requests";
$openServiceRequestsNav		= "Open/Active Requests";
$newServiceRequestNav		= "Create a New Service Request";
$closedServiceRequestsNav 	= "Completed/Closed Requests";
$archivedServiceRequestsNav	= "Archived Requests";

$aminsNav					= "Admin Accounts";
$viewAllAdminsNav			= "All Admins";
$newAdminNav				= "Add a New Admin";
$myProfileNav				= "My Profile";

$manageNav					= "Manage";
$siteAlertsNav				= "Site Alerts";
$reportsNav					= "Reports";
$formsNav					= "Forms &amp; Templates";
$emailAllTenantsNav			= "Email All Tenants";
$siteSettingsNav			= "Site Settings";

// Form Labels
// --------------------------------------------------------------------------------------------------
$emailAddressField 			= "Correo electronico";
$passwordField 				= "Clave";
$rpasswordField 			= "Retype Password";
$firstNameField 			= "First Name";
$tenantsFirstNameField		= "Tenant's First Name";
$lastNameField 				= "Last Name";
$tenantsLastNameField		= "Tenant's Last Name";
$resetPassEmailField 		= "The email address that is registered to your account.";
$phoneField 				= "Phone";
$altPhoneField 				= "Alternate Phone";
$addressField 				= "Mailing Address";
$currentPassField 			= "Current Password";
$newPassField 				= "New Password";
$confirmNewPassField 		= "Confirm New Password";
$setAsActiveField			= "Set the Account as Active?";
$tenantsEmailField			= "Tenant's Email";

$propertyField				= "Property";
$propertyNameField			= "Property Name";
$propertyDescField			= "Property Description";
$propertyAddressField		= "Property Address";
$propertyRateField			= "Monthly Rent Amount";
$latePeneltyField			= "Late Penalty Fee";
$propertyDepositField		= "Property Deposit Amount";
$petsAllowedField			= "Pets Allowed?";
$propertyNotesField			= "Property Notes";

$servicePriorityField		= "Priority";
$serviceRequestField		= "Request Title";
$serviceRequestDesc			= "Request Description";
$serviceStatusField			= "Status";

$adminAccountType			= "Account Type";
$adminsEmailField			= "Admin's Email";
$adminsFirstNameField		= "Admin's First Name";
$adminsLastNameField		= "Admin's Last Name";
$adminRoleField				= "Account Role";

// Button labels
// --------------------------------------------------------------------------------------------------
$signInBtn 					= "Ingresar";
$signoutBtn 				= "Salir";
$okBtn						= "OK";
$cancelBtn					= "Cancelar";
$closeBtn					= "Cerrar";
$resetPasswordBtn			= "Reinicia mi clave";
$uploadBtn					= "Upload";
$yesBtn 					= "Si";
$noBtn	 					= "No";
$updateBtn					= "Actualizar";
$saveBtn					= "Guardar";
$sendBtn					= "Enviar";
$editBtn					= "Edit";
$saveChangesBtn				= "Save Changes";
$addTenantBtn				= "Add Tenant";
$addPropertyBtn				= "Add Property";
$addAdminBtn				= "Add Admin/Landlord";
$receiptBtn					= "Receipt";

// Table Headers
// --------------------------------------------------------------------------------------------------
$tab_property				= "Property";
$tab_tenant					= "Tenant";
$tab_landlord				= "Landlord";
$tab_adminLandlord			= "Admin/Landlord";
$tab_name					= "Name";
$tab_rentAmount				= "Rent Amount";
$tab_lateFee				= "Late Fee";
$tab_totalDue				= "Total Due";
$tab_paymentDate			= "Payment Date";
$tab_paymentFor				= "Payment For";
$tab_paidBy 				= "Paid By";
$tab_rentalMonth			= "Rental Month";
$tab_amount					= "Amount";
$tab_lateFeePaid			= "Late Fee Paid";
$tab_totalPaid				= "Total Paid";
$tab_address				= "Address";
$tab_email					= "Email";
$tab_phone					= "Phone";
$tab_altPhone				= "Alternate Phone";
$tab_monthlyRate			= "Monthly Rate";
$tab_leaseStartsOn			= "Lease Starts On";
$tab_leaseEndsOn			= "Lease Ends On";
$tab_propertyType			= "Property Type";
$tab_petsAllowed			= "Pets Allowed";
$tab_propertySize			= "Property Size";
$tab_bedroomsBathrooms		= "Bedrooms / Bathrooms";
$tab_bathrooms				= "Bathrooms";
$tab_bedrooms				= "Bedrooms";
$tab_requestTitle			= "Request Title";
$tab_dateRequested			= "Date Requested";
$tab_priority				= "Priority";
$tab_status					= "Status";
$tab_lastUpdated			= "Last Updated";
$tab_Alert					= "Alert";
$tab_alertText				= "Alert Text";
$tab_createdBy				= "Created By";
$tab_printOnReceipt			= "On Receipt";
$tab_isActive				= "Active";
$tab_dateStarts				= "Date Starts";
$tab_dateEnds				= "Date Ends";
$tab_dateCreated			= "Date Created";
$tab_dateArchived			= "Date Archived";
$tab_leaseTerm				= "Lease Term";
$tab_superUser				= "Super User";
$tab_adminRole				= "Role";
$tab_uploadedBy				= "Uploaded By";
$tab_dateUploaded			= "Date Uploaded";
$tab_description			= "Description";
$tab_receivedBy				= "Received By";
$tab_isLeased				= "Leased";

// **************************************************************************************************
// **************************************************************************************************

// Page Specific: Dashboard
// --------------------------------------------------------------------------------------------------
$welcomeMessage				= "Welcome";
$welcomeMessageQuip			= "The ".$set['siteName']." web portal allows you to view/update information & details relating to your Rental Properties and Tenants.";

$lateRentH3					= "Tenants with late Rent for";
$rentReceivedMonthH3		= "Rent Received for";
$noRentReceived				= "No Rent has been received for this Month.";
$totalRentReceived			= "Total Received for";
$currentTenantsH3			= "Current Leased Tenants";
$availablePropertiesH3		= "Available Properties";
$openServRequestsH3			= "Open Service Requests";

$paymentDateField			= "Payment Date";
$paymentDateHelper			= "The Date the Payment was received from the Tenant";
$paymentAmountField			= "Payment Amount";
$paymentAmountHelper		= "The base amount of the Payment. Do not include any Late Fees paid here.";
$lateFeeField				= "Late Penalty Fee";
$lateFeeHelper				= "If the Payment was late and incurred the Late Fee Penalty.";
$paymentForField			= "Payment For";
$paymentForHelper			= "What this Payment is for. (ie. Deposit, Rent etc.)";
$paymentTypeField			= "Payment Type";
$paymentTypeHelper			= "What form the Payment was made in. (ie. Cash, Check etc.)";
$rentMonthField				= "Rent Month";
$rentMonthHelper			= "If this is a Monthly Rental Payment, otherwise leave at None";
$paymentNotesField			= "Payment Notes";
$paymentNotesHelper			= "Payment Notes WILL print on the Tenant's Receipt.";
$savePaymentBtn				= "Save Payment";

// Page Specific: Site Settings
// --------------------------------------------------------------------------------------------------
// Validation Error Msg Box
$payPalCurrencyCodwMsg		= "Please enter the PayPal Currency Code.";
$payPalAccountEmailMsg		= "Please enter your PayPal account Email.";
$payPalItemNameMsg			= "Please enter the Item Name for the PayPal payments.";
$paymentCompleteMsg			= "Please enter the Payment Message once a rental payment has been completed.";
$payPalFeeMsg				= "Please enter the additional PayPal Fee (if none, use 0.0).";
$installUrlMsg				= "Please enter your Installation URL (include the trailing slash).";
$siteNameMsg				= "Please enter a Site Name.";
$businessNameMsg			= "Please enter the the name of your Business.";
$businessAddressMsg			= "Please enter the your Address or the Address of your Business.";
$businessEmalMsg			= "Please enter your Business Email.";
$businessPhoneMsg			= "Please enter your Business Phone.";
$propertyUploadsMsg			= "Please enter the folder location where Property Uploads will be saved.";
$templateUploadsMsg			= "Please enter the folder location where Business Form Templates will be saved.";
$tenantDocUploadsMsg		= "Please enter the folder location where Tenant Documents will be saved.";
$fileTypesMsg				= "Please enter the File Type Extensions allowed to be uploaded.";
$avatarUploadsMsg			= "Please enter the folder location where Avatar images will be saved.";
$avatarFileTypesMsg			= "Please enter the Avatar File Type Extensions allowed to be uploaded.";
$propertyPicsUploadsMsg		= "Please enter the folder location where Property Pictures will be saved.";
$pictureFileTypesMsg		= "Please enter the Property Picture File Type Extensions allowed to be uploaded.";
$settingsSavedMsg			= "The global Site Settings has been saved.";

// Page elements
$updateSettingsH3			= "Update ".$set['siteName']." Global Site Settings";
$siteSetAccTitle			= "Global Site Settings";
$updateSettingsBtn			= "Update Site Settings";
$uploadsAccTitle			= "File/Image Upload Settings";
$uploadsNote				= "Please note: If you change any of the Upload Directory names, you must also change the actual folder's name.";
$updUploadSettingsBtn		= "Update Upload Settings";
$paymentsAccTitle			= "Rental Payment Settings";
$updPaymentSettingsBtn		= "Update Payment Settings";

$installUrlField			= "Installation URL";
$installUrlHelper			= "Used in all File Uploads &amp; email notifications. Must include the trailing slash (ie. http://mysite.com/reside/).";
$localizationField			= "Localization";
$optionArabic				= "Arabic";
$optionBulgarian			= "Bulgarian";
$optionChechen				= "Chechen";
$optionCzech				= "Czech";
$optionDanish				= "Danish";
$optionEnglish				= "English";
$optionCanadianEnglish		= "Canadian English";
$optionBritishEnglish		= "British English";
$optionEspanol				= "Espanol";
$optionFrench				= "French";
$optionCroatian				= "Croatian";
$optionHungarian			= "Hungarian";
$optionArmenian				= "Armenian";
$optionIndonesian			= "Indonesian";
$optionItalian				= "Italian";
$optionJapanese				= "Japanese";
$optionKorean				= "Korean";
$optionDutch				= "Dutch";
$optionPortuguese			= "Portuguese";
$optionRomanian				= "Romanian";
$optionThai					= "Thai";
$optionVietnamese			= "Vietnamese";
$optionCantonese			= "Cantonese";
$localizationHelper			= "Choose the Language file to use throughout Reside.";
$siteNameField				= "Site Name";
$siteNameHelper				= "ie. Reside (Appears at the top of the browser, the header logo, in the footer and in other headings throughout the site).";
$businessNameField			= "Business Name";
$businessAddressField		= "Business Address";
$businessAddressHelper		= "Address & Phone. (Appears in Tenant's Receipt).";
$businessEmailField			= "Business Email";
$businessEmailHelper		= "Used in email notifications as the \"from/reply to\" email address.";
$businessPhoneField			= "Business Phone";
$contactPhoneField			= "Contact Phone";
$contactPhoneHelper			= "Phone Number Tenants can call for General Information, Questions etc.";

$propFileUploadField		= "Property Files Upload Directory";
$propFileUploadHelper		= "Where Property Files upload to (Admin Side ONLY). Must include the trailing slash (ie. uploads/).";
$templatesDirField			= "Templates Upload Directory";
$templatesDirHelper			= "Where Reside Forms &amp; Templates upload to (Admin Side ONLY). Must include the trailing slash (ie. templates/).";
$tenantDocUploadField		= "Tenant Documents Upload Directory";
$tenantDocsUploadHelper		= "Where Tenant documents upload to (Admin Side ONLY). Must include the trailing slash (ie. docs/).";
$uploadTypesField			= "Upload File Types Allowed";
$uploadTypesHelper			= "The file types you allow to be uploaded, NO spaces & each separated by a comma (Format: xls,pdf,doc,docx).";
$avatarUploadField			= "Avatar Upload Directory";
$avatarUploadHelper			= "Where both Admin &amp; Tenant Avatars upload to. Must include the trailing slash (ie. avatars/).";
$avatarTypesField			= "Avatar File Types Allowed";
$avatarTypesHelper			= "The file types you allow to be uploaded, NO spaces & each separated by a comma (Format: jpg,jpeg,png).";
$propPicUploadField			= "Property Pictures Upload Directory";
$propPicUploadHelper		= "Where Property Pictures upload to (Admin Side ONLY). Must include the trailing slash (ie. pictures/).";
$propPicTypesField			= "Property Pictures File Types Allowed";
$propPicTypesHelper			= "The file types you allow to be uploaded, NO spaces & each separated by a comma (Format: jpg,jpeg,png).";

$enablePayPalField			= "Enable rental payments through PayPal?";
$enablePayPalHelper			= "Set to Yes to allow Tenants to make rental payments via PayPal.";
$payPalCurrencyField		= "PayPal Payment Currency Code";
$paymentCompletedField		= "Payment Completed Message";
$paymentCompleteHelper		= "What the Tenant will see once they have completed a PayPal rental payment.";
$payPalEmailField			= "PayPal Account Email";
$payPalEmailHelper			= "Your PayPal Account's email &mdash; where PayPal payments will be sent to.";
$payPalItemField			= "PayPal Item Name";
$payPalItemHelper			= "The item name that appears on the PayPal payment screen.";
$payPalFeeField				= "PayPal Use Fee";
$payPalFeeHelper			= "Fee charged by PayPal. Do not include '%' symbol (ie. 0.5).";

// Page Specific: Site Alerts
// --------------------------------------------------------------------------------------------------
$noSiteAlertsMsg			= "No Site Alerts Found.";
$siteAlertsH3				= "Site Alerts";
$siteAlertsQuip				= "Manage your Site Alerts.";
$siteAlertsInst				= "You can delete or edit an existing Site Alert, or create a new one.";
$newSiteAlertBtn			= "Create a New Site Alert";

$alertDeletedMsg			= "The Site Alert has been Deleted.";
$alertTitleMsg				= "Please enter the Title for the Alert.";
$alertTextMsg				= "Please enter the Alert text.";
$newAlertSavedMsg			= "The new Site Alert has been saved.";
$alertUpdatedMsg			= "The Site Alert has been Updated.";

$editAlertModalTitle		= "Edit Site Alert";
$alertDatesInstructions		= "To use a Start Date and/or an Expire Date, set the new Alert as \"inactive\".<br />Site Alerts set to
Active will display regardless of what dates are set.";

$alertTitleField			= "Site Alert Title";
$alertStatusFeild			= "Alert Status";
$statusOptionInactive		= "Inactive";
$statusOptionActive			= "Active";
$alertStatusHelper			= "Selecting \"Active\" makes this Alert visible for ALL Tenants.";
$invoicePrintField			= "Print on Receipt";
$invoicePrintHelper			= "Setting this to \"Yes\" prints the alert in the Notes Section of the Tenant's Receipt.";
$alertStartField			= "Date Alert Starts";
$alertStarHelper			= "Leave blank if this Alert does not have a start date. Format: yyyy-mm-dd";
$alertEndField				= "Date Alert Expires";
$alertEndHelper				= "Leave blank if this Alert never expires. Format: yyyy-mm-dd";
$alertTextField				= "Site Alert Text";
$deleteAlertConf			= "Are you sure you wish to DELETE this Site Alert?";
$newAlertModalTitle			= "Create a New Site Alert";
$createAlertBtn				= "Create Alert";

// Page Specific: Active Tenants
// --------------------------------------------------------------------------------------------------
$deleteTenantConf			= "Are you sure you wish to DELETE this Tenant's Account?";
$tenantDeletedMsg			= "The Tenant's Account has been Deleted.";
$activeLeaseTenantsH3		= "Active Tenants with a Current Leased Property";
$activeTenantsQuip			= "Only Tenants that do not have a Leased Property can be deleted.";
$activeNoLeaseTenantsH3		= "Active Tenants without a Lease";
$noActiveTenantsMsg			= "No Active Tenants found.";

// Page Specific: Inactive Tenants
// --------------------------------------------------------------------------------------------------
$inactiveTenantsH3			= "Inactive Tenants";
$inactiveTenantsQuip		= "Tenants that have created an account, but have not activated or have been set to Inactive status by an admin.";
$noInactiveTenantsMsg		= "No Inactive Tenants Found.";
$dateAccountCreated			= "Date Account Created";
$resendActivationLink		= "Resend Activation Email";
$activationEmailSentMsg		= "The Activation email has been sent to the Tenant.";
$activationEmailError		= "There was an error, and the activation email could not be sent.";

// Page Specific: Archived Tenants
// --------------------------------------------------------------------------------------------------
$archivedTenantsH3			= "Archived Tenants";
$noArchedTenantsMsg			= "No Archived Tenants found.";

// Page Specific: Active Properties
// --------------------------------------------------------------------------------------------------
$deletePropertyConf			= "Are you sure you wish to DELETE this Property?";
$propertyDeletedMsg			= "The Property has been Deleted.";
$archivePropertyConf		= "Are you sure you wish to Archive this Property?";
$propertyArchivedMsg		= "The Property has been Archived.";
$activeLeasePropertyH3		= "Properties with a Current Lease";
$activePropertyQuip			= "Only Properties that do not have a Lease can be Archived or Deleted.";
$activeNoLeasePropertysH3 	= "Available Properties";
$noActivePropertiesMsg		= "No Active Properties found.";
$noAvailablePropertiesMsg	= "No Properties are available.";

// Page Specific: Archived Properties
// --------------------------------------------------------------------------------------------------
$archivedPropertiesH3		= "Archived Properties";
$noArchivedPropertiesMsg 	= "No Archived Properties found";

// Page Specific: Active Leases
// --------------------------------------------------------------------------------------------------
$activeLeasesH3				= "Active Leases";
$noActiveLeasesMsg			= "No Active Leases found";
$updateLeaseLink			= "Update";

// Page Specific: Active Leases
// --------------------------------------------------------------------------------------------------
$deleteLeaseConf			= "Are you sure you wish to DELETE this Archived/Closed Lease?";
$leaseDeletedMsg			= "The Lease has been Deleted.";
$archivedLeasesH3			= "Archived/Closed Leases";
$noArchivedLeasesMsg		= "No Archived/Closed Leases found";
$editLeaseModalTitle		= "Edit Property Lease for";
$leaseUpdatedMsg			= "The Lease has been updated.";
$closeLeaseField			= "Close this Lease?";
$closeLeaseHelper			= "Has the Lease ended? Select Yes to Close this Lease and Update the Tenant &amp; the Property.";

// Page Specific: Open Service Requests
// --------------------------------------------------------------------------------------------------
$noOpenRequestsMsg			= "No Open Service Requests found";

$deleteRequestConf			= "Are you sure you wish to DELETE this Service Request?";
$requestDeletedMsg			= "The Service Request has been Deleted.";

// Page Specific: Closed Service Requests
// --------------------------------------------------------------------------------------------------
$closedServRequestsH3		= "Closed/Completed Service Requests";
$noClosedRequestsMsg		= "No Closed/Completed Service Requests found";

// Page Specific: Archived Service Requests
// --------------------------------------------------------------------------------------------------
$archivedServRequestsH3		= "Archived Properties Service Requests";
$noArchivedRequestsMsg		= "No Archived Service Requests found";

// Page Specific - My Profile
// --------------------------------------------------------------------------------------------------
$avatarRemovedMsg 			= "Your Avatar Image has been removed.";
$avatarRemoveErrorMsg 		= "An Error was encountered &amp; your Avatar image could not be deleted at this time.";
$avatarNotAcceptedMsg 		= "The File was not an accepted Avatar type.";
$avatarUploadedMsg 			= "Your new Avatar has been uploaded.";
$avatarUploadErrorMsg 		= "There was an error uploading your Avatar, please check the file type &amp; try again.";

$personalInfoUpdatedMsg 	= "Your Personal Info has been updated.";
$emailAddyUpdatedMsg 		= "Your Account Email has been updated.";
$newPassSavedMsg 			= "Your new Password has been saved.";

$myProfileH3 				= "My Account &amp; Profile";
$myProfileQuip 				= "Please keep your Profile information up to date.";

$listGroupAdminTitle 		= "Personal Account Information";
$listGroupAdminAvatarLink 	= "Profile Avatar";
$listGroupAdminUpdateInfo 	= "Update Personal Information";
$listGroupAdminUpdateEmail 	= "Update Account Email";
$listGroupAdminUpdatePassword = "Change Password";

$safePersonalInfoH3 		= "Your Personal Information is secure.";
$safePersonalInfo 			= "We store your information in our database in an encrypted format. We do not sell or make your information available to any one
for any reason. We value your privacy and appreciate your trust in us. You can update your personal information easily by using any of the links in the sidebar.";

$profileAvatarTitle 		= "Your Profile Avatar";
$profileAvatarQuip 			= "You can remove your current Avatar, and use the default Avatar.<br /><small>To upload a new Avatar image you will need to first
remove your current Avatar.</small>";
$removeAvatarBtn 			= "Remove Current Avatar Image";

$newAvatarUpload 			= "Upload a New Avatar Image";
$allowedAvatarTypesQuip 	= "Allowed Avatar File Types:";
$selectNewAvatar 			= "Select New Avatar";
$avatarMaxHight 			= "All Avatars will be displayed at a max-height of 85 pixels";
$removeAvatarConf 			= "Are you sure you want to remove your current Avatar?";

$personalInfoModalTitle 	= "Update your Account's Personal Information";
$updateEmailModalTitle 		= "Update your Account's Email Address";
$updatePasswordModalTitle 	= "Change your Account's Password";

$newPassword2Helper 		= "Type a new Password for your Account.";
$rnewPasswordHelper 		= "Please type your desired Password again. Passwords MUST Match.";
$accountEmailHelper 		= "Your email address is also used for your Account log In.";
$currentPasswordHelper 		= "Your Current Account Password.";

$firstNameReqVal 			= "First Name is a Required Field.";
$lastNameReqVal 			= "Last Name is a Required Field.";
$emailReqVal 				= "Your Email Address is Required.";

$currentPassReqVal 			= "Please enter your current Account Password.";
$newPassReqVal 				= "Please enter your new Password.";
$typePassAgainReqVal		= "Please type your new Password again.";
$currentPassInvalidReqVal 	= "Your current password is incorrect. Please check your entry.";
$newPassNotMatchReqVal 		= "New Passwords do not match.";

// Page Specific: All Admins
// --------------------------------------------------------------------------------------------------
$allAdminsH3				= "All Admin Accounts";
$deleteAdminConf			= "Are you sure you wish to DELETE this Admin's Account?";
$adminDeletedMsg			= "The Admin's Account has been Deleted.";
$adminDeleteFailMsg			= "You cannot delete the Primary Admin's Account.";

// Page Specific: Property Info
// --------------------------------------------------------------------------------------------------
$propertyDetailsH3 			= "Rental Property Information";
$propertyPicturesH3 		= "Rental Property Pictures";
$propertyPicturesQuip		= "Any pictures uploaded for this property are viewable by the Tenant.";
$propertyAmenitiesH3 		= "Rental Property Details &amp; Amenities";
$noPicsUploaded				= "No Pictures have been uploaded for this Property.";
$propertyListingH3			= "Rental Property Listing Text";

$rentIsPastDueMsg			= "The Tenant's Rent Payment is Past Due.";

$updatePropertyBtn			= "Update Property Information";
$uploadPicturesBtn			= "Upload a New Picture";
$updateAmenitiesBtn			= "Update Property Amenities";
$updatePropHoaInfoBtn		= "Update HOA Information";
$leasePropertyBtn			= "Lease Property";
$AssignPropertyBtn			= "Assign Landlord";
$recordPaymentBtn			= "Record a Payment Received";
$viewPaymentsBtn			= "View All Lease Payments";
$uploadPropFileBtn			= "Upload a Property File";
$deletePictureBtn			= "Delete Picture";
$updateListingTextBtn		= "Update Property Listing Text";

$rentalMonthyRateLi			= "Monthly Rate";
$rentalLateFeeLi			= "Late Fee";
$rentalDepositAmtLi			= "Rental Deposit Amount";
$propertyNoteLi				= "Notes:";
$propertyTypeLi 			= "Property Type:";
$propertyStyleLi			= "Property Style:";
$yearBuiltLi				= "Year Built:";
$propertySizeLi				= "Property Size:";
$numberBedroomsLi 			= "Number of Bedrooms:";
$numberBathroomsLi 			= "Number of Bathrooms:";
$parkingLi 					= "Parking:";
$heatingLi 					= "Heating:";
$allowPetsLi				= "Pets Allowed:";
$propAmenitiesLi			= "Amenities:";
$hoaLi	 					= "HOA:";
$hoaPhoneLi 				= "HOA Phone:";
$hoaAddressLi	 			= "HOA Address:";
$hoaFeeLi					= "HOA Fee:";
$hoaFeeScheduleLi			= "HOA Fee Schedule:";

$propertyLeaseH4			= "Rental Property Lease";
$noLeaseMsg					= "This Property is not currently Leased.";

$propertyArchivedH4			= "Rental Property Status";
$propertyArchivedMsg		= "This Property is currently Archived.";

$AssignedLandlord			= "Landlord:";
$currentTenant				= "Tenant:";
$leaseTerm					= "Lease Term:";
$leaseNotes					= "Lease Notes:";

$otherResidentsH4			= "Residents";
$noResidentsFound			= "No other Residents listed.";
$relationToTenant			= "Relation";
$newResidentBtn				= "Add a New Resident";
$updateResidentBtn			= "Update Resident";
$residentNameField			= "Resident's Name";
$residentNotesField			= "Notes";
$residentNotesHelper		= "Internal Only Notes.";
$archiveResidentField		= "Archive this Resident?";
$archiveResidentHelper		= "Selecting Yes removes the Resident from the Property.";
$residentNameReqMsg			= "The Resident's Name is Required";
$residentRelationReqMsg		= "Please enter the Resident's relation to the Tenant";
$residentUpdatedMsg			= "The Resident has been updated.";
$newResidentAddedMsg		= "The New Resident has been added.";

$propertyFilesH4 			= "Property Files";
$propertyFilesQuip			= "Property Files are documents &amp; files that relate to only the property.";
$noFilesUploaded 			= "No Files have been uploaded";
$viewDeatilsLink 			= "View Details";
$viewFileLink 				= "View File";
$dateUploaded				= "Date Uploaded";

$propertyAddressReq			= "Please enter the Property's Address.";
$propertyInfoUpdatedMsg 	= "The Properties Information has been updated.";
$allowedPictureTypesQuip 	= "Allowed Picture File Types:";
$maxUploadSize				= "Max File Size:";
$propPictureTitle			= "Title";
$propPictureTitleHelper		= "The Title is required and is used as the Picture's URL.";
$selectPictureField			= "Select Picture";
$pictureNotAcceptedMsg 		= "The File was not an accepted Picture type.";
$pictureUploadedMsg 		= "The new Property Picture has been uploaded.";
$pictureUploadErrorMsg 		= "There was an error uploading the Picture, please check the file type &amp; try again.";
$deletePictureConf 			= "Are you sure you want to permanently DELETE this Property Picture?";
$pictureRemovedMsg 			= "The Property Picture has been deleted.";
$pictureRemoveErrorMsg 		= "An Error was encountered &amp; the property picture could not be deleted at this time.";

$propertyTypeField			= "Property Type";
$propertyTypeHelper			= "Single Family, Multi-Family etc.";
$propertyStyleField			= "Property Style";
$propertyStylehelper		= "Detached Structure, Town Home, Apartment Building etc.";
$yearBuiltField				= "Year Built";
$propertySizeField			= "Property Size";
$propertySizeHelper			= "Total Size of Living Space in Sq Ft";
$parkingField				= "Parking";
$parkingHelper				= "Parking Type (ie. Private Drive, Shared Lot etc.)";
$heatingTypeField			= "Heating &amp; A/C Type";
$numBedroomsField			= "Number of Bedrooms";
$numBathroomsField			= "Number of Bathrooms";
$propertyAmenitiesText		= "Property Amenities Text";
$propAmenitiesUpdatedMsg 	= "The Property Amenities have been updated.";

$hoaNameField				= "HOA Name";
$hoaNameHelper				= "Home Owners Association";
$hoaPhoneField				= "HOA Contact Phone Number";
$hoaAddressField			= "HOA Mailing Address";
$hoaFeeField				= "HOA Fee";
$hoaFeeScheduleField		= "HOA Fee Schedule";
$hoaFeeScheduleHelper		= "Monthly, Yearly etc.";
$propHoaUpdatedMsg 			= "The Properties HOA Info has been updated.";

$propListingField			= "Property Listing Text";
$propListingHelper			= "Property Ad Text when advertising the property for rent.";
$propListingUpdatedMsg 		= "The Properties Listing Text has been updated.";

$selectLandlordField		= "Select Admin/Landlord";
$selectLandlordHelper		= "Assign this Property to an Admin/Landlord.";
$selectAdminReq				= "Please select and Admin/Landlord to Assign this Property to.";
$propAssignedMsg			= "The Property has been Assigned to the Admin/Landlord.";

$paymentDateReqMsg			= "Please select the Date the Payment was received.";
$paymentAmountReqMsg		= "Please enter the Amount of the Payment.";
$paymentForReqMsg			= "Please enter what this Payment was for (ie. Monthly Rent etc).";
$paymentTypeReqMsg			= "Please enter the type of Payment received (ie. Cash, Check etc).";
$paymentSavedMsg			= "The Tenants Payment has been recorded and saved.";

$allowedFileTypesQuip 		= "Allowed File Types:";
$propFileTitleField			= "File Title";
$propFileTitleHelper		= "The Title is required and is used as the Files's URL.";
$propFileDescField			= "File Description";
$propFileDescHelper			= "Short Description about the File.";
$selectPropFileField		= "Select the File to Upload";
$fileNotAcceptedMsg 		= "The Property Document was not an accepted File type.";
$fileUploadedMsg 			= "The new Property Document has been uploaded.";
$fileUploadErrorMsg 		= "There was an error uploading the Property Document, please check the file type &amp; try again.";

// Page Specific: Lease Property
// --------------------------------------------------------------------------------------------------
$propHasBeenLeasedH3		= "The Property has been Leased";
$propHasBeenLeasedMsg		= "The Property has been successfully Leased.";
$propAllReadyLeasedH3		= "The Property is all ready Leased to a Tenant";
$propAllReadyLeasedMsg		= "You can not Lease a Property that all ready has a Lease Assigned to it.";
$leasePropertyH3			= "Lease Property &mdash;";
$leasePropertyQuip			= "Create a New Lease for this Property.<br />
<small>Please Note: Tenant accounts must first be created before you can create a new Lease for any Property.</small>";
$leasePropertyInstructions	= "This is a 2-step process. Step 1: Create a New Lease. Step 2: Assign the New Lease to an available Tenant.<br />
Each step will be displayed in turn, after the previous step has been successfully completed.";

$step1Title					= "Step 1: Create a New Lease";
$noTenantsAvailableMsg		= "There are no available Tenants to Lease this Property to.";
$leaseTermField				= "Term of Lease";
$leaseTermHelper			= "The length of the Lease (ie. 6 Months, 12 Months etc).";
$leaseStartField			= "Lease Start Date";
$leaseStartHelper			= "The Date the Lease will begin on.";
$leaseEndField				= "Lease End Date";
$leaseEndHelper				= "The Date the Lease ends on.";
$leaseNotesField			= "Lease Notes";
$leaseNotesHelper			= "Notes (if any) for this Lease.";
$createLeaseBtn				= "Create Lease";

$step2Title					= "Step 2: Assign the Newly created Lease to a Tenant";
$step2Quip					= "Now that the New Lease for this Property has been created, you can now Assign the Lease and the Property to a Tenant.";
$selectTenantHelper			= "Select the Tenant this Property Lease is for. Only Active, Unleased Tenants are displayed.";
$assignLeaseBtn				= "Assign Lease";

$leaseTermReqMsg			= "Please enter the Term of this Lease.";
$startDateReqMsg			= "Please enter the Start Date for this Lease.";
$endDateReqMsg				= "Please enter the End Date for this Lease.";
$newLeaseCreatedMsg			= "The New Lease has been created, and is ready to be assigned to a Tenant.";

$tenantIdNameReqMsg			= "Please select the Tenant this Lease is for.";
$tenantUpdatedMsg			= "The Tenant has been Updated with the Property &amp; new Lease.";

// Page Specific - View Property File
// --------------------------------------------------------------------------------------------------
$viewFileH3					= "Viewing Property File";
$viewDocumentQuip			= "Pictures/Images will be displayed. Any other file type will need to be downloaded to view.";

// Page Specific - View Service Request
// --------------------------------------------------------------------------------------------------
$viewRequestH3				= "Viewing Service Request";
$viewRequestOpenQuip		= "You can add notes to this open Request.";
$viewRequestInstructions	= "Once the Request has been completed, you will be able to add the Service Resolution details,
and save any costs associated with the Request.";
$viewRequestClosedQuip		= "This Request has been Closed/Completed.";
$viewClosedRequestInstructions	= "This Service Request has been completed. You can add/update the Service Resolution details,
and save any costs associated with the Request.";

$serviceReqLiTitle 			= "Service Request Information";
$servReqLiDateRequested 	= "Date Requested";
$servReqLiReqBy				= "Requested By";
$servReqLiProperty			= "Property";
$servReqLiPriority			= "Priority";
$servReqLiStatus			= "Current Status";
$servReqLiLastUpdate		= "Last Updated";
$servReqLiRequest			= "Request";

$updateRequestBtn			= "Update Request";
$statusSelectOpen			= "Open";
$statusSelectWIP			= "Work in Progress";
$statusSelectParts			= "Waiting for Parts";
$statusSelectNoRepair		= "Completed/No Repair Needed";
$statusSelectRepaired		= "Completed Repair";
$statusSelectClosed			= "Closed";

$servResolutionLiTitle  	= "Service Request Resolution";
$servResLiCompletedBy		= "Completed By";
$servResLiDateResolved		= "Date Resolved";
$servResLiComments			= "Comments";
$servResLiNeedsFollowup 	= "Needs a Follow-up";
$servResLiFollowUpComments 	= "Follow-up Comments";
$servResLiDateCompleted		= "Date Completed";

$servResolutionBtn			= "Add/Update Service Resolution";
$servCostsBtn				= "Add a New Service Costs";

$serviceReqNotesH3			= "Service Request Notes";
$editNoteModalTitle 		= "Edit Service Request Note";

$addNoteBtn					= "Add a Note to this Service Request";
$notesClosedMsg				= "Notes are closed for this Service Request.";

$editNoteField				= "Edit Note";
$notesField					= "Notes";

$noteTextReqVal				= "Please enter your Note text.";
$editServNoteUpdatedMsg 	= "The Service Request Note has been updated.";
$newServNoteAddedMsg		= "The Service Request Note has been saved, and the Tenant has been notified.";

$serviceRequestUpdatedMsg	= "The Service Request has been updated.";

$resolutionDateField		= "Date Resolved";
$resolutionDateHelper		= "The Date the Service Request was Resolved/Fixed.";
$resolutionTextField		= "Resolution";
$resolutionTextHelper		= "Please describe the details of the fix for this Service Request.";
$needsFollowupField			= "Follow Up on this Request?";
$needsFollowupHelper		= "If the Service Request needs any type of Follow Up.";
$followupDescField			= "Follow Up Description";
$followupDescHelper			= "Please describe the Follow Up needed.";
$closeRequestField			= "Close Request?";
$closeRequestHelper			= "Is the Service Request completed?";

$serviceRequestCostsH3		= "Service Request Costs";
$serviceRequestCostsQuip	= "You can add in any Service Related Costs.";
$serviceRequestCostsNote	= "You can add in as many Service Expenses as needed for this Request.";
$tab_expenseName			= "Expense Title";
$tab_vendorName				= "Vendor/Company";
$tab_expenseDesc			= "Expense Description";
$tab_expenseCost			= "Cost";
$tab_dateOfExpense			= "Date of Expense";

$expenseDateHelper			= "The Date of the Expense or Service.";
$vendorNameHelper			= "Company that provided the service/purchased from.";
$expenseNameHelper			= "Give the expense a Title (ie. Toilet Parts, Locksmith Service etc.).";
$expenseDescHelper			= "Short Description of the expense.";
$expenseCostHelper			= "The total Expense Cost.";

$expenseDateReqMsg			= "Please enter the Date of the Expense.";
$expenseNameReqMsg			= "Please give this Service Expense a Title.";
$expenseDescReqMsg			= "Please enter a short Description of this Expense.";
$expenseCostReqMsg			= "Please enter the Total Cost of this Expense.";
$serviceExpenseSavedMsg		= "The Service Request Expense has been saved.";

// Page Specific - View Tenant Info/Account
// --------------------------------------------------------------------------------------------------
$avatarRemovedMsg			= "The Tenant's Avatar Image has been removed.";
$avatarRemoveErrorMsg		= "An Error was encountered &amp; the Tenant's Avatar image could not be deleted at this time.";
$tenantPersonalInfoUpdMsg	= "The Tenant's Personal Info has been updated.";
$tenantNotesUpdatedMsg		= "The Tenant's internal Notes have been updated.";
$tenantEmailReqMsg			= "The Tenant's Email Address is Required.";
$tenantEmailUpdatedMsg		= "The Tenant's Account Email has been updated.";
$tenantsNewPassReqMsg		= "Please enter the Tenant's new Password.";
$tenantRepeatPassReqMsg		= "Please type the Tenant's new Password again.";
$passwordUpdatedMsg			= "The Tenant's new Password has been saved.";
$activeLeaseFoundMsg		= "Account Status Update failed. The Tenant currently has an Active Lease.";
$tenantAccountStatusUpdMsg	= "The Tenant's Account Status has been updated.";
$emailSubjectReqMsg			= "Please enter the Subject of your Email.";
$emailTextReqMsg			= "Please enter the text of the Email.";

$tenantAccountH3			= "Account Profile";
$tenantAccountQuip			= "You can update and/or archive this Tenant's Account.";
$tenantAccountStatusNote	= "The Tenant's Account Status can only be changed when they do NOT have an Active Property Lease.";

$tenantSidebarTitle			= "Update Account Information";
$updTenantAvatarLi			= "Tenant's Profile Avatar";
$updTenantInfoLi			= "Update Tenant's Personal Information";
$updTenantNotesLi			= "Update Tenant's Internal Notes";
$updTenantEmailLi			= "Update Tenant's Account Email";
$updTenantPasswordLi		= "Change Tenant's Password";
$updTenantStatusLi			= "Change Tenant's Account Status";

$tenantIsArchivedMsg		= "This Tenant is currently Archived.";
$tenantIsInactiveMsg		= "Tenant Account has not been Activated.";

$tenantsLeasedPropH3		= "'s Leased Property";
$tenantNoLeasedPropMsg		= "does not have a Leased Property";

$removeAvatarModalTitle		= "Remove Tenant's Avatar";
$removeAvatarQuip			= "You can remove the Tenant's current Avatar, and use the default Avatar. This is handy in the case of a Tenant uploading a questionable image.";
$removeAvatarBtn			= "Remove Current Avatar Image";
$noAvatarUploadedQuip		= "The Tenant does not have a custom Avatar uploaded at this time.";
$removeAvatarConfModal		= "Are you sure you want to remove the Avatar for";

$updTenantInfoModalTitle	= "Update Tenant's Personal Information";
$updTenantNotesModalTitle	= "Update Tenant's Internal Notes";
$tenantInternalNotesField	= "Internal Notes";
$updTenantEmailModalTitle	= "Update Tenant's Account Email Address";
$tenantEmailHelper			= "The Tenant's account email address is also used as their Account log In.";
$updPasswordModalTitle		= "Change the Tenant's Account Password";
$newTenantPasswordHelper	= "Type a new Password for the Tenant's Account.";
$newTenantPassRepeatHelper	= "Please type the new Password again. Passwords MUST Match.";
$updTenantStatusModalTitle	= "Change the Tenant's Account Status";
$updTenantStatusQuip		= "The Tenant's Account Status can only be changed when they do NOT have an Active Property Lease.";
$activeAccountField			= "Active Account?";
$accountStatusHelper		= "You can manually set the Tenant's account as Active. Inactive Tenants CANNOT access their accounts.";
$archiveAccountField		= "Archive the Tenant Account?";
$archiveAccountHelper		= "Active &amp; Archived Tenant's can still access their accounts.";
$sendEmailModalTitle		= "Send an Email to";
$subjectField				= "Email Subject";
$emailTextField				= "Email Text";
$tenantEmailSentMsg			= "The Email has been sent to the Tenant.";

$tenantDocumentsH3			= "Documents Uploaded for";
$tenantDocQuip				= "Tenant Documents are files that relate to only the Tenant.";
$uploadTenantDocBtn			= "Upload a Tenant Document";
$noDocsFoundMsg				= "No Documents found.";

$documentUploadedMsg 		= "The new Document has been uploaded.";
$documentUploadErrorMsg 	= "There was an error uploading the Document, please check the file type &amp; try again.";
$tenantDocTitleReqMsg		= "The Tenant Document must have a Title.";
$tenantDocDescReqMsg		= "Please include a short Description for the Tenant Document.";

$tab_documentName			= "Document Name";
$documentDeletedMsg			= "The Tenant Document has been Deleted.";
$documentRemoveErrorMsg 	= "An Error was encountered &amp; the Tenant Document could not be deleted at this time.";
$deleteDocumentConf			= "Are you sure you wish to DELETE this Document?";

// Page Specific - View Tenant Document
// --------------------------------------------------------------------------------------------------
$viewDocumentH3				= "Viewing Tenant Document";

// Page Specific - View Admin/Landlord Account
// --------------------------------------------------------------------------------------------------
$adminAccountH3				= "Admin/Landlord Account Profile";
$adminAccountQuip			= "You can update and/or change the Active state of this Admin's Account.";
$adminAccountStatusNote		= "The Admin's Account Status can only be changed when they do NOT have a Leased Property assigned.";
$adminSidebarTitle			= "Update Account Information";

$updAdminAvatarLi			= "Admin's Profile Avatar";
$updAdminInfoLi				= "Update Admin's Personal Information";
$updAdminEmailLi			= "Update Admin's Account Email";
$updAdminPasswordLi			= "Change Admin's Password";
$updAdminStatusLi			= "Change Admin's Account Status";

$adminIsArchivedMsg			= "This Admin is currently Archived.";
$adminIsInactiveMsg			= "Admin Account is Inactive.";

$adminAssignedPropertiesH3	= "'s Current Assigned Properties";
$removeAvatarModalTitle		= "Remove Admin's Avatar";
$removeAvatarModalQuip		= "You can remove the Admin's current Avatar, and use the default Avatar. This is handy in the case of a Admin uploading a questionable image.";
$noAdminAvatar				= "The Admin does not have a custom Avatar uploaded at this time.";
$changeAdminTypeModalTitle	= "Change the Admin's Account Type";
$adminLevelField			= "Admin Level";
$adminLevelHelper			= "Superuser: Full Access &amp; Add/Modify &amp; Delete permissions.<br />Normal: Cannot Add/Modify other Admins, Limited Payment System and Site Settings Access.";
$adminRoleField				= "Admin Role";
$updateAdminPersInfoTitle	= "Update Admin's Personal Information";
$updateAdminEmailTitle		= "Update Admin's Account Email Address";
$adminEmailHelper			= "The Admin's account email address is also used as their Account log In.";
$changeAdminPasswordTitle	= "Change the Admin's Account Password";
$adminNewPasswordHelper		= "Type a new Password for the Admin's Account.";
$changeAdminStatusTitle		= "Change the Admin's Account Status";
$changeAdminStatusField		= "Account Status";
$changeAdminStatusQuip		= "Admin Accounts can only be deactivated when they do NOT have any active Projects assigned.";
$changeAdminStatusHelper	= "You can manually set the Admin's account as Active. Inactive Admins CANNOT access their accounts.";

$adminAvatarRemovedMsg		= "The Admin's Avatar Image has been removed.";
$adminAvatarRemoveErrorMsg	= "An Error was encountered &amp; the Admin's Avatar image could not be deleted at this time.";
$adminAccountUpdatedMsg		= "The Admin's Account Type has been updated.";
$adminPersonalInfoUpdatedMsg = "The Admin's Personal Info has been updated.";
$adminEmailReqMsg			= "The Admin's Email Address is Required.";
$adminEmailUpdatedMsg		= "The Admin's Account Email has been updated.";
$adminNewPasswordReqMsg		= "Please enter the Admin's new Password.";
$adminRetypePassReqMsg		= "Please type the Admin's new Password again.";
$newPasswordsNoMatchMsg		= "New Passwords do not match.";
$adminsPasswordSavedMsg		= "The Admin's new Password has been saved.";
$adminAccountStatusUpdMsg	= "The Admin's Account Status has been updated.";
$accountStatusUpdFailedMsg	= "The Admin currently has an assigned Property and can not be set to Inactive at this time.";
$adminEmailSentMsg			= "The Email has been sent to the Admin.";

// Page Specific - All Payments by Lease/Property
// --------------------------------------------------------------------------------------------------
$allPaymentsH3 				= "All Rental Payments";
$allPaymentsQuip 			= "All Rental Payments made for this Current Lease.";
$newPaymentBtnLink			= "Record a Rental Payment";
$noPaymentsRecorded			= "No Payments have been recorded.";
$viewPrintReceipt			= "View/Print Receipt";

$deletePaymentConf			= "Are you sure you wish to DELETE this Payment?";

// Page Specific - View Payment
// --------------------------------------------------------------------------------------------------
$paymentDetailsH3			= "Payment Details";
$refundQuip					= "You can Issue a Refund for this Payment. Refunds affect the total of the original payment amount and are indicated by an asterisk.
Refunds can only be issued once per payment.";
$issueRefundBtn				= "Issue a Refund";
$paymentInfoListHeader		= "Payment Information";

$updatePaymentBtn			= "Update Payment";
$emailReceiptBtn			= "Email Receipt";
$deletePaymentBtn			= "Delete Payment";
$issueTheRefundBtn			= "Issue Refund";
$sendEmailBtn				= "Send the Email";

$refundIssuedH3				= "A Refund has been Issued for this Payment";
$refundIssuedBy				= "Issued By";

$refundDateField			= "Refund Date";
$refundDateHelper			= "The Date the Refund was issued to the Tenant.";
$refundAmountField			= "Refund Amount";
$refundForField				= "Refund For";
$refundForHelper			= "What this Refund is for. (ie. Refund Security Deposit, Over Payment etc.)";
$refundNotesField			= "Refund Notes";
$refundDateReqMsg			= "Please enter the Date the Refund was issued";
$refundAmountReqMsg			= "Please enter the Amount of the Refund.";
$refundForReqMsg			= "Please enter what this Refund was for (ie. Security Deposit Refund et.).";
$refundIssuedMsg			= "The Refund has been saved, and the original Payment has been updated.";

$paymentUpdatedMsg			= "The Payment has been updated.";
$emailNotesField			= "Include a Note with the Receipt";
$emailNotesHelper			= "Not Required.";
$emailReceiptDefaultSubject	= "Payment Receipt from ".$set['siteName'];
$emailReceiptSentMsg		= "The Tenant's Receipt has been sent.";

// Page Specific - Payment Receipt
// --------------------------------------------------------------------------------------------------
$headTitle					= "Receipt of Payment";
$receivedFrom				= "Received From:";
$receiptDate				= "Receipt Date";
$paymentNum					= "Payment ID #";
$dateReceived				= "Date Received";
$monthlyRent				= "Rent Month";
$descFor					= "Description/For";
$payNotes					= "Payment Notes";
$lateFeeDue					= "Late Fee Due";
$amountDue					= "Amount Due";
$totalAmountDue				= "Total Amount Due";
$totalAmountPaid			= "Amount Paid";
$receiptThankYou			= "Thank You For Your Trust in ".$set['siteName'];

// Page Specific - Email All Tenants
// --------------------------------------------------------------------------------------------------
$sendMassEmailH3			= "Send an Email to All Active Tenants";
$sendMassEmailQuip			= "You can send a mass-email to all of your Current Active Tenants.";

$emailAllTenantsSentMsg		= "The Email has been sent to all current Active Tenants.";
$emailSentError				= "There was an error, and the email could not be sent.";

// Page Specific - Templates & Forms
// --------------------------------------------------------------------------------------------------
$templatesFormsH3			= "Uploaded Templates &amp; Forms";
$templatesFormsQuip			= "Below is a list of your uploaded Templates &amp; Forms.";

$uploadNewTemplateBtn		= "Upload New Template/Form";
$noTemplatesFoundMsg		= "No Templates or Forms have been uploaded.";

$premadeFormsH3				= "Pre-Made Forms";
$premadeFormsQuip			= $set['siteName']." comes with some pre-made Forms & Templates.";
$premadeFormsInst			= "These are great to use with your Tenants, just fill in the PDF fields out with the Tenant's information.
These pre-made forms are designed to be filled out, printed and then given to the Tenant. If you have the full version of
<a href=\"http://www.adobe.com/products/acrobat.html\" target=\"_blank\">Adobe Acrobat</a>, you can also save the completed form, and then
upload it to the Tenant's account.";

$tab_formName				= "Form Name";
$tab_formDescription		= "Description";
$applicationFormTitle		= "Rental Application Form";
$applicationFormDesc		= "Basic Tenant Rental Application";
$rentIncreaseTitle			= "Rent Increase Notice";
$rentIncreaseDesc			= "Notice of Rental Rate Increase";
$moveOutReminderTitle		= "Move Out Reminder";
$moveOutReminderDesc		= "What's expected from a Tenant on Moving Out";
$petAgreementTitle			= "Pet Agreement";
$petAgreementDesc			= "Details of allowed pets";
$importantInfoTitle			= "Important Information for a New Tenant";
$importantInfoDesc			= "Helpful information relating to a newly Leased Property";
$returnedCheckTitle			= "Returned Check Notice";
$returnedCheckDesc			= "Notice of a Bank refused/Returned Check";
$vacateOrRenewTitle			= "Notice to Vacate or Renew Lease";
$vacateOrRenewDesc			= "Tenant's intentions beyond the current Lease";

$uniqueTemplateNames		= "Template/Form Names need to be Unique.<br />Do not use any slashes (ie. /) in the Template Name.";
$allowedTemplateTypesQuip 	= "Allowed File Types:";
$templateNameField			= "Template/Form Name";
$templateNameHelper			= "Please give the Template a Name.";
$templateDescriptionHelper	= "A short description about the Template.";

$templateNameReqMsg			= "Please give the Template/Form a Name";
$templateDescReqMsg			= "Please include a short Description of the Template/Form";
$templateDeletedMsg			= "The Template has been Deleted.";
$templateDeleteErrorMsg		= "An Error was encountered &amp; the Template could not be deleted at this time.";

$deleteTemplateConf			= "Are you sure you want to DELETE the Template?";

// Page Specific - View Uploaded Template/Form
// --------------------------------------------------------------------------------------------------
$viewTemplateH3				= "Viewing Template/Form &mdash;";
$viewTemplateQuip			= "Pictures/Images will be displayed. Any other Template/Form type will need to be downloaded to view/use.";

// Page Specific - Reports
// --------------------------------------------------------------------------------------------------
$reportsH3					= $set['siteName']." Reports";
$reportsQuip				= $set['siteName']." offers various reporting options to help manage your Properties &amp; Tenants.";
$reportInst					= "Additional Filter Options can be set for each Report. Most filter options are required for the report to run.";
$runReportBtn				= "Run Report";

$tenantReportsTitle			= "Tenant Reports";
$propertyReportsTitle		= "Property Reports";
$serviceReportsTitle		= "Service Reports";
$accountingReportsTitle		= "Accounting Reports";
$leaseReportsTitle			= "Lease Reports";
$adminReportsTitle			= "Admin / Landlord Reports";

$noTenantsFoundMsg			= "No Tenants found.";
$noPropertiesFoundMsg		= "No Properties found.";
$noRequestsFoundMsg			= "No Service Requests found.";
$noPaymentsFoundMsg			= "No Payments found.";
$noLeasesFoundMsg			= "No Leases found.";

$report1Title				= "Current Active &amp; Inactive Tenants";
$report2Title				= "Archived Tenants Report";
$report3Title				= "Properties Report";
$report4Title				= "Service Requests Report";
$report5Title				= "Total Costs by Service Request";
$report6Title				= "Payments Received Report";
$report7Title				= "Refunds Issued Report";
$report8Title				= "Leases Report";
$report9Title				= "Admin/Landlord Accounts Report";
$report0Title				= "Admin/Landlord Assigned Properties Report";

$includeInactiveField		= "Include Inactive Tenants";
$includeInactiveHelper		= "Choose whether to include Inactive Tenants in this report.";
$includeArchivedHelper		= "No options available, just hit Run Report.";

$reportTitleH3				= "Report:";
$noReportRecordsFoundMsg	= "No records found.";
$totalRecordsFound			= "Total Records:";
$reportTotals				= "Report Total:";
$reportCreatedOnDate		= "Report created on:";

$selectPropertyTypeField	= "Select a Property Report Type";
$selectPropertyTypeHelper	= "Select the Property status you would like included in the Report.";
$propertyType1				= "Only Leased Properties";
$propertyType2				= "Currently Leased &amp; Available to Rent Properties";
$propertyType3				= "Only Properties that are Available to Rent";
$propertyType4				= "Only Archived Properties";

$serviceIncludeField		= "Choose What to Include";
$serviceIncludeHelper		= "Select the status of the Service Requests you would like included in the Report.";
$requestType1				= "Only Active/Open Requests";
$requestType2				= "All Open/Active &amp; Completed Requests";
$requestType3				= "Only Closed/Completed Requests";
$selectTenantField			= "Select Tenant";
$allOption					= "All *";
$allOptionHelper			= "* Active &amp; Inactive only. Archived accounts are not included on the report.";

$fromDateField				= "Show Records From";
$fromDateHelper				= "Please select or type a Beginning Date.";
$toDateField				= "Show Records To";
$toDateHelper				= "Please select or type an End Date.";

$allPaymentsField			= "Include All Payments?";
$allPaymentsNoOption		= "No, Include only Rent Payments";
$allPaymentsYesOption		= "Yes, Show ALL Payments";
$allPaymentsHelper			= "Select Yes to include Any Fees &amp; Deposits Tenants have paid in this Report.";

$closedLeasesField			= "Include Closed Leases?";
$closedLeasesNoOption		= "No, Do NOT Include Closed Leases";
$closedLeasesYesOption		= "Yes, Include Both Open &amp; Closed Leases";
$closedLeasesHelper			= "Choose whether to include Closed Leases in this report.";

$inactiveAdminsField		= "Include Inactive Admins/Landlords?";
$inactiveAdminsNoOption		= "No, Do NOT Include Inactive";
$inactiveAdminsYesOption	= "Yes, Show Me All";
$inactiveAdminsHelper		= "Choose whether to include Inactive Admins/Landlords in this report.";

$selectAdminField			= "Select Admin/Landlord";
$selectAdminHelper			= "* Only Active Admins &amp; Landlords are included in the report.";

// Page Specific - reports - Tenant Reports
// --------------------------------------------------------------------------------------------------
$reportType1				= "All Active &amp; Inactive Tenants";
$reportType2				= "Active Tenants Only";
$archivedTenantReportName	= "Archived Tenants";

// Page Specific - reports - Properties Report
// --------------------------------------------------------------------------------------------------
$depositAmountReq			= "Deposit Amount";

// Page Specific - reports - Service Reports
// --------------------------------------------------------------------------------------------------
$tab_dateCompleted			= "Date Completed";
$tab_totalRepairCost		= "Total Repair Cost";

// Page Specific - reports - Accounting Reports
// --------------------------------------------------------------------------------------------------
$tab_originalPaymentFor		= "Original Payment For";
$tab_originalPaymentDate	= "Original Payment Date";

// Page Specific - reports - Lease Reports
// --------------------------------------------------------------------------------------------------
$leaseReport1				= "Only Open Leases";
$leaseReport2				= "All Open &amp; Closed Leases";

// Page Specific - reports - Admin Reports
// --------------------------------------------------------------------------------------------------
$adminReport1				= "All Active &amp; Inactive Admins";
$adminReport2				= "Active Admins Only";