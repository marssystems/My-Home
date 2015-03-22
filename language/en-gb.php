<?php
// All Pages - Globals
// --------------------------------------------------------------------------------------------------
$currencySym 				= "£";
$amountReflectRefund 		= "Amount reflects a Refund";
$accessErrorH3				= "Access Error";
$permissionDenied			= "Permission Denied. You can not access this page.";

// Tenant login
// --------------------------------------------------------------------------------------------------
$tenantLogin 				= "Tenant Login";
$loginWelcomeMsg 			= "Welcome to ".$set['siteName'];
$newAccountNav 				= "Create a New Account";
$resetPasswordNav 			= "Reset Password";
$loginInstructions 			= "Enter your account information and click Sign In.";

// New Account Activation
// --------------------------------------------------------------------------------------------------
$loginNav					= 'Login';
$accountActivateQuip		= 'Thank you for verifying your email address and activating your account.';
$accountActivatedMsg		= 'Your account has been activated, and you can now log in.';
$accountSigninBtn			= 'All Set! Go ahead and Sign In';
$accountAllReadyActiveQuip	= 'You have all ready verified your email address and activated your account.';
$accountAllReadyActiveInst	= 'Your account has been activated, and you can now log in.';
$noDirectAccessQuip			= 'Please check your email for the Account Activation Link.';
$noDirectAccessMsg			= 'You cannot directly access this page. please use the link that has been sent to your email.';

// Tenant Header
// --------------------------------------------------------------------------------------------------
$welcomeMsg 				= "Welcome";
$todayMsg 					= "Today is";
$signoutConf 				= "Are you sure you want to sign out?";

// Tenant Navigation
// --------------------------------------------------------------------------------------------------
$dashboardNav 				= "Dashboard";
$propertyNav 				= "My Property";
$paymentHistoryNav 			= "Payment History";
$myAccountNav 				= "My Account";
$myProfileNav 				= "My Profile";
$tenantDocsNav 				= "My Documents";
$serviceRequestsNav 		= "Service Requests";
$availablePropertiesNav		= 'Available Properties';
$contactRequestsNav 		= "Contact Us";

// Form Labels
// --------------------------------------------------------------------------------------------------
$emailAddressField 			= "Email Address";
$passwordField 				= "Password";
$rpasswordField 			= "Retype Password";
$firstNameField 			= "First Name";
$lastNameField 				= "Last Name";
$resetPassEmailField 		= "The email address that is registered to your account.";
$phoneField 				= "Phone";
$altPhoneField 				= "Alternate Phone";
$addressField 				= "Mailing Address";
$currentPassField 			= "Current Password";
$newPassField 				= "New Password";
$confirmNewPassField 		= "Confirm New Password";
$tenantsPets				= "Tenant Pets";

$serviceRequestField		= "Request Title";
$serviceRequestDesc			= "Request Description";

$tenantNameField			= "Your Name";
$subjectField				= "Subject";
$commentsField				= "Comments";

$editNoteField				= "Edit Note";
$notesField					= "Notes";

$paymentAmountField			= "Payment Amount";
$totalAmountField			= "Total PayPal Amount";

// Button labels
// --------------------------------------------------------------------------------------------------
$signInBtn 					= "Sign In";
$signoutBtn 				= "Sign Out";
$cancelBtn	 				= "Cancel";
$createAccountBtn	 		= "Create Account";
$resetPasswordBtn	 		= "Reset My Password";
$uploadBtn	 				= "Upload";
$yesBtn 					= "Yes";
$updateBtn	 				= "Update";
$saveBtn					= "Save";
$sendBtn					= "Send";
$editBtn					= "Edit";
$saveChangesBtn				= "Save Changes";
$paypalBtn					= "Pay With PayPal";

// Form Helper Texts
// --------------------------------------------------------------------------------------------------
$htmlNotAllowedHelper		= "HTML is not allowed &amp; will be saved as plain text.";
$validEmailHelper 			= "A valid email address. Your account information will be sent to this address.";
$newPasswordHelper 			= "Type a Password for your new Account.";
$newPassword2Helper 		= "Type a new Password for your Account.";
$rnewPasswordHelper 		= "Please type your desired Password again. Passwords MUST Match.";
$accountEmailHelper 		= "Your email address is also used for your Account Log In.";
$currentPasswordHelper 		= "Your Current Account Password.";
$serviceTitleHelper			= "Please give your Service Request a Title.";
$beDescriptiveHelper		= "Please be as descriptive as possible.";
$servicePriorityHelper		= "Please choose a Priority Level for this Request.";
$paymentAmountHelper		= "Please enter the amount you would like to pay by PayPal. <strong>No currency symbols (Format: 500.00)</strong>.";
$totalAmountHelper			= "This amount reflects the ".$set['paypalFee']."% PayPal Fee and is the total for this rental payment.";

// Table Headers
// --------------------------------------------------------------------------------------------------
$tab_propName 				= "Property Name";
$tab_prop	 				= "Property";
$tab_monthlyRent 			= "Monthly Rent";
$tab_propertyDeposit		= 'Deposit Amount';
$tab_lateFee 				= "Fee if Late";
$tab_petsAllowed 			= "Pets Allowed?";
$tab_leaseTerm 				= "Lease Term";
$tab_leaseStarts 			= "Lease Started On";
$tab_leaseEnds 				= "Lease Ends On";
$tab_landlord 				= "Landlord";

$tab_paymentDate 			= "Payment Date";
$tab_paidBy 				= "Paid By";
$tab_amount 				= "Amount";
$tab_lateFeePaid 			= "Late Fee Paid";
$tab_for 					= "For";
$tab_rentalMonth 			= "Rental Month";
$tab_totalPaid 				= "Total Paid";

$tab_document				= "Document";
$tab_uploadedBy				= "Uploaded By";
$tab_description			= "Description";
$tab_dateUploaded			= "Date Uploaded";

$td_receipt 				= "Receipt";
$td_view 					= "View";

$tab_request 				= "Request";
$tab_dateRequested 			= "Date Requested";
$tab_priority 				= "Priority";
$tab_status 				= "Status";
$tab_lastUpdated 			= "Last Updated";

$tab_requestTitle			= "Request";

// PHP Form Validations
// --------------------------------------------------------------------------------------------------
$firstNameReqVal 			= "First Name is a Required Field.";
$lastNameReqVal 			= "Last Name is a Required Field.";
$emailReqVal 				= "Your Email Address is Required.";

$currentPassReqVal 			= "Please enter your current Account Password.";
$newPassReqVal 				= "Please enter your new Password.";
$typePassAgainReqVal		= "Please type your new Password again.";
$currentPassInvalidReqVal 	= "Your current password is incorrect. Please check your entry.";
$newPassNotMatchReqVal 		= "New Passwords do not match.";

$noteTextReqVal				= "Please enter your Note text.";

// Misc
// --------------------------------------------------------------------------------------------------
$createNewAccountMisc 		= "Create a New ".$set['siteName']." Account";
$resetPasswordMisc 			= "Reset Your ".$set['siteName']." Account Password";
$passwordResetMisc 			= "Your password has been reset.";
$passwordResetInstMisc 		= "Please check your email for your new password, and instructions on how to update your account.";

// MsgBox Messages
// --------------------------------------------------------------------------------------------------
$emailAddressMsg 			= "Please enter your account email address.";
$emptyPasswordMsg 			= "The Password field can not be empty.";
$loginFailedMsg 			= "Log in failed, Please check your entries and try again.";
$inactiveAccountMsg 		= "Your account has not been activated, and you can not log in. Please check your email for the activation link.";

$firstNameMsg 				= "Please enter your First Name.";
$lastNameMsg 				= "Please enter your Last Name.";
$validEmailMsg 				= "Please enter a valid email address.";
$passwordMismatchMsg 		= "Passwords do not match. Please check your entries.";
$newAccountErrorMsg 		= "There was an error, and the New Account could not be created at this time.";

$dupEmailMsg 				= "There is all ready an account registered with that email address.";
$passwordResetMsg 			= "Your password has been reset.";
$accountNotFoundMsg 		= "Account not found for that email address.";

$avatarRemovedMsg 			= "Your Avatar Image has been removed.";
$avatarRemoveErrorMsg 		= "An Error was encountered &amp; your Avatar image could not be deleted at this time.";
$avatarNotAcceptedMsg 		= "The File was not an accepted Avatar type.";
$avatarUploadedMsg 			= "Your new Avatar has been uploaded.";
$avatarUploadErrorMsg 		= "There was an error uploading your Avatar, please check the file type &amp; try again.";

$personalInfoUpdatedMsg 	= "Your Personal Info has been updated.";
$emailAddyUpdatedMsg 		= "Your Account Email has been updated.";
$newPassSavedMsg 			= "Your new Password has been saved.";

$requestTitleMsg			= "Please enter a Title for the Service Request";
$requestDescMsg				= "Please include the Description of your Service Request.";
$requestAddedMsg			= "The new Service Request has been saved, and the Admins have been notified.";

$editServNoteUpdatedMsg 	= "Your Service Request Note has been updated.";
$newServNoteAddedMsg		= "The Service Request Note has been saved, and your Landlord has been notified.";

// **************************************************************************************************
// **************************************************************************************************

// Page Specific - Available Properties
// --------------------------------------------------------------------------------------------------
$availablePropertiesH3		= 'Available Properties for Rent';
$availablePropertiesQuip	= 'Listed below are the current properties available for rent.';
$availablePropertiesInst	= 'View more information about the property by clicking on the property name. If you are interested in renting one of the properties,
please download and complete the Application to Rent Form.';
$downloadApplicationBtn		= 'Download Application';
$noPropertiesAvailable		= 'There are not any Properties available to rent at this time.';
$noPropertiesAvailableQuip	= 'You can fill out and turn in an application, and when one of our properties becomes available, we will contact you.';

$feeForLateRent				= 'Late Rent Fee';
$sizeOfProperty				= 'Property Size';
$bedroomsBathrooms			= 'Bedrooms / Bathrooms';

// Page Specific - View an Available Property
// --------------------------------------------------------------------------------------------------
$viewPropertyH3				= 'Property Information';
$viewPropertyInst			= 'If you are interested in renting this property, please download and complete the Application to Rent Form.';
$petsAreAllowed				= 'Pets Allowed:';

// Page Specific - Dashboard
// --------------------------------------------------------------------------------------------------
$dashboardWelcome 			= "Welcome to your ".$set['siteName']." Dashboard";
$dashQuip 					= "The ".$set['siteName']." web portal allows you to view information & details relating to your current Leased Property.";
$currentLeaseH3 			= "Your Current Lease";
$leaseQuip 					= "The details about your current Property Lease.";
$lastpaymentH3 				= "Your Last Rent Payment";
$paymentQuip 				= "Thank you for your most recent payment.";
$openServReqH3 				= "Open Service Requests";
$openServReqQuip 			= "Below are your current Open Service Requests.";
$noLeasedProperty 			= "Either your Leased Property has not yet been set up in your ".$set['siteName']." Account, or you have not completed all
paperwork.<br />Please contact us if you have any questions or concerns.";

$noPropertyMsg 				= "You do not currently have a Leased Property.";
$noPaymentMsg 				= "You have not made any Payments.";

// Page Specific - Property Details
// --------------------------------------------------------------------------------------------------
$propertyDetailsH3 			= "My Rental Property";
$propertyPicturesH3 		= "Property Pictures";
$propertyAmenitiesH3 		= "Property Amenities";

$rentIsPastDueMsg			= "Your Monthly Rent Payment is Past Due.";
$rentIsPastDueQuip			= "Your rent is due by the 5th of each month. In addition, you owe late charges as outlined in your current lease.
Please submit your payment no later then close of business today.";

$propertyFiles_sb 			= "Property Files";
$noFilesUploaded 			= "No Files have been uploaded";
$viewDeatilsLink 			= "View Details";
$viewFileLink 				= "View File";

$propertyResidents_sb 		= "My Residents";
$relationToTenant 			= "Relation to Tenant:";

$propertyPayments_sb 		= "My Recent Payments";
$viewReciptLabel 			= "View Receipt";
$viewRentalPaymentsBtn 		= "View All Rental Payments";
$amountPaid_sb 				= "Amount Paid:";
$feesPaid_sb 				= "Fees Paid:";
$totalPaid_sb 				= "Total Paid:";
$paymentDate_sb 			= "Payment Date:";

$propertyType 				= "Property Type:";
$yearBuilt 					= "Year Built:";
$propertySize 				= "Property Size:";
$numberBedrooms 			= "Number of Bedrooms:";
$numberBathrooms 			= "Number of Bathrooms:";
$parking 					= "Parking:";
$heating 					= "Heating:";
$propertyAmenities 			= "Amenities:";
$hoa 						= "HOA:";
$hoaPhone 					= "HOA Phone:";
$hoaAddress 				= "HOA Address:";

// Page Specific - All Rental Payments
// --------------------------------------------------------------------------------------------------
$allPaymentsH3 				= "All Rental Payments";
$allPaymentsQuip 			= "All Rental Payments you have made for Your Current Lease.";
$newPaymentBtnLink			= "Make a Rental Payment";
$noPaymentsRecorded			= "No Payments have been recorded.";

// Page Specific - New Payment
// --------------------------------------------------------------------------------------------------
$newPaymentH3				= "Rental Payments";
$newPaymentQuipPaypal		= $set['siteName']." accepts Cash, Personal / Cashier's Checks, Money Orders or PayPal for Rental Payments.";
$newPaymentQuipNoPaypal		= $set['siteName']." accepts Cash, Personal / Cashier's Checks, or Money Orders for Rental Payments.";
$paymentIsOverdue			= "This month's rent payment is overdue.";

$payWithPaypal				= "Pay with PayPal";
$paymentAmount				= "Your Rent amount has been entered for you.";
$paymentAmountQuip			= "<small>You can change the amount if what you are paying differs. The Payment Amount will be converted to include the
additional ".$set['paypalFee']."% of the base payment amount to cover PayPal's transaction fees. Make sure the payment details below are correct
and click the Pay With PayPal button.<br />You will then be redirected to PayPal's secure site to complete your payment.</small>";

$payByOther					= "Pay by Cash, Personal / Cashier's Check or Money Order";
$payByOtherQuip				= "You can avoid paying the extra PayPal fees by paying with Cash, Check or a Money Order.";

$payableTo					= "Payable To:";
$mailTo						= "Mail or drop off at:";

$paymentQuestionsH3			= "Questions about Payments?";
$paymentQuestionsQuip		= "If you have any questions or concerns about Rental Payments, please <a href='index.php?page=contactRequests'>Contact Us</a>.";

$monthlyRate				= "Your Monthly Rent Amount is:";
$feeIfLate					= "Additional Fee if Rent Payment is Late:";

// Page Specific - Payment History
// --------------------------------------------------------------------------------------------------
$paymentHistoryH3 			= "My Payment History";
$paymentHistoryQuip 		= "All Payments you have made across all Leases &amp; Properties.";

// Page Specific - My Profile
// --------------------------------------------------------------------------------------------------
$myProfileH3 				= "My Account &amp; Profile";
$myProfileQuip 				= "Please keep your Profile information up to date.";

$listGroupTenantTitle 		= "Personal Account Information";
$listGroupTenantAvatarLink 	= "Profile Avatar";
$listGroupTenantUpdateInfo 	= "Update Personal Information";
$listGroupTenantUpdateEmail = "Update Account Email";
$listGroupTenantUpdatePassword 	= "Change Password";

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

// Page Specific - Service Requests
// --------------------------------------------------------------------------------------------------
$serviceRequestsH3			= "Service &amp; Maintenance Requests";
$serviceRequestsQuip		= "Your Service Requests for your current Property Lease.";
$newServiceRequestBtn		= "Create a New Service Request";
$serviceRequestsCompleted 	= "Completed Service Requests are shaded in gray.";
$noServiceRequestsMsg		= 'You do not have any active Service Requests.';

$normalSelect				= "Normal";
$importantSelect			= "Important";
$urgenSelect				= "Urgent";

// Page Specific - View Service Request
// --------------------------------------------------------------------------------------------------
$viewRequestH3				= "Viewing Service Request";
$viewRequestOpenQuip		= "You can add notes to this open Request.";
$viewRequestClosedQuip		= "This Request has been Closed/Completed.";

$serviceReqLiTitle 			= "Service Request Information";
$servReqLiDateRequested 	= "Date Requested:";
$servReqLiReqBy				= "Requested By:";
$servReqLiProperty			= "Property:";
$servReqLiPriority			= "Priority:";
$servReqLiStatus			= "Current Status:";
$servReqLiLastUpdate		= "Last Updated:";
$servReqLiRequest			= "Request:";

$servResolutionLiTitle  	= "Service Request Resolution";
$servResLiCompletedBy		= "Completed By";
$servResLiDateResolved		= "Date Resolved:";
$servResLiComments			= "Comments:";
$servResLiNeedsFollowup 	= "Needs a Follow-up?";
$servResLiFollowUpComments 	= "Follow-up Comments:";
$servResLiDateCompleted		= "Date Completed:";

$serviceReqNotesH3			= "Service Request Notes";
$editNoteModalTitle 		= "Edit Service Request Note";

$addNoteBtn					= "Add a Note to this Service Request";
$notesClosedMsg				= "Notes are closed for this Service Request.";

// Page Specific - Contact Us
// --------------------------------------------------------------------------------------------------
$contactUsH3				= "Have a Question? Need some Information?";
$contactUsQuip				= "You can send us an email by using the form below.";
$contactUsInstructions		= "Please use this form for general inquires only.<br />If you have a problem with your rental that requires a Service Call,
please use the <a href='index.php?page=serviceRequests'>Service Request</a> page.";
$emptySubjectField			= "Please enter a Subject for your email";
$emptyCommentsField			= "Please include your comments";
$emailSentMsg				= "Thank you, Your email has been sent";
$emailSentError				= 'There was an error, and the email could not be sent.';

// Page Specific - My Documents
// --------------------------------------------------------------------------------------------------
$myDocumentsH3 				= "My Documents";
$myDocumentsQuip 			= "Documents uploaded to your account.";
$noDocsUploaded				= "No Documents have been uploaded.";

// Page Specific - View Tenant Document
// --------------------------------------------------------------------------------------------------
$viewDocumentH3				= "Viewing Document &mdash;";
$viewDocumentQuip			= "Pictures/Images will be displayed. Any other file type will need to be downloaded to view.";

// Page Specific - View Property File
// --------------------------------------------------------------------------------------------------
$viewFileH3					= "Viewing File &mdash;";

// Page Specific - Payment Receipt
// --------------------------------------------------------------------------------------------------
$headTitle					= "Receipt of Payment";
$receivedFrom				= "Received From:";
$receiptDate				= "Receipt Date";
$paymentNum					= "Payment ID";
$dateReceived				= "Date Received";
$monthlyRent				= "Rent Month";
$descFor					= "Description/For";
$paymentNotes				= "Payment Notes";
$lateFeeDue					= "Late Fee Due";
$amountDue					= "Amount Due";
$totalAmountDue				= "Total Amount Due";
$totalAmountPaid			= "Amount Paid";
$receiptThankYou			= "Thank You For Your Trust in ".$set['siteName'];