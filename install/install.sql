CREATE TABLE IF NOT EXISTS `admins` (
  `adminId` int(5) NOT NULL AUTO_INCREMENT,
  `superuser` int(1) NOT NULL DEFAULT '0',
  `adminRole` int(1) NOT NULL DEFAULT '1',
  `adminEmail` varchar(255) COLLATE utf8_bin NOT NULL,
  `password` varchar(255) COLLATE utf8_bin NOT NULL,
  `adminFirstName` varchar(255) COLLATE utf8_bin NOT NULL,
  `adminLastName` varchar(255) COLLATE utf8_bin NOT NULL,
  `adminPhone` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `adminAltPhone` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `adminAddress` longtext COLLATE utf8_bin,
  `adminAvatar` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT 'adminDefault.png',
  `createDate` date NOT NULL,
  `isActive` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`adminId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `assignedproperties` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `propertyId` int(5) NOT NULL,
  `adminId` int(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `leases` (
  `leaseId` int(5) NOT NULL AUTO_INCREMENT,
  `adminId` int(5) NOT NULL,
  `propertyId` int(5) NOT NULL,
  `leaseTerm` varchar(100) COLLATE utf8_bin NOT NULL,
  `leaseStart` date NOT NULL,
  `leaseEnd` date NOT NULL,
  `leaseNotes` longtext COLLATE utf8_bin,
  `isClosed` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`leaseId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `payments` (
  `paymentId` int(5) NOT NULL AUTO_INCREMENT,
  `adminId` int(5) NOT NULL,
  `tenantId` int(5) NOT NULL,
  `leaseId` int(5) NOT NULL,
  `hasRefund` int(1) DEFAULT NULL,
  `paymentDate` date NOT NULL,
  `paymentAmount` varchar(255) COLLATE utf8_bin NOT NULL,
  `paymentPenalty` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `paymentFor` varchar(255) COLLATE utf8_bin NOT NULL,
  `paymentType` varchar(255) COLLATE utf8_bin NOT NULL,
  `isRent` int(1) NOT NULL DEFAULT '1',
  `rentMonth` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `paymentNotes` longtext COLLATE utf8_bin,
  PRIMARY KEY (`paymentId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `properties` (
  `propertyId` int(5) NOT NULL AUTO_INCREMENT,
  `createdBy` int(5) NOT NULL,
  `propertyName` varchar(255) COLLATE utf8_bin NOT NULL,
  `propertyDesc` longtext COLLATE utf8_bin,
  `propertyAddress` longtext COLLATE utf8_bin NOT NULL,
  `isLeased` int(1) NOT NULL DEFAULT '0',
  `propertyRate` varchar(255) COLLATE utf8_bin NOT NULL,
  `latePenalty` varchar(255) COLLATE utf8_bin NOT NULL,
  `propertyDeposit` varchar(255) COLLATE utf8_bin NOT NULL,
  `petsAllowed` int(1) NOT NULL DEFAULT '0',
  `propertyNotes` longtext COLLATE utf8_bin,
  `propertyFolder` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `propertyAmenities` longtext COLLATE utf8_bin,
  `propertyListing` longtext COLLATE utf8_bin,
  `propertyType` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `propertyStyle` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `yearBuilt` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `propertySize` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `parking` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `heating` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `bedrooms` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `bathrooms` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `propertyHoa` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `hoaAddress` longtext COLLATE utf8_bin,
  `hoaPhone` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `hoaFeeAmount` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `hoaFeeSchedule` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `isArchived` int(1) NOT NULL DEFAULT '0',
  `dateArchived` date DEFAULT NULL,
  PRIMARY KEY (`propertyId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `propertyfiles` (
  `fileId` int(5) NOT NULL AUTO_INCREMENT,
  `propertyId` int(5) NOT NULL,
  `adminId` int(5) NOT NULL,
  `fileName` varchar(255) COLLATE utf8_bin NOT NULL,
  `fileDesc` longtext COLLATE utf8_bin NOT NULL,
  `fileUrl` varchar(255) COLLATE utf8_bin NOT NULL,
  `fileDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`fileId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `propertypictures` (
  `pictureId` int(5) NOT NULL AUTO_INCREMENT,
  `propertyId` int(5) NOT NULL,
  `adminId` int(5) NOT NULL,
  `pictureName` varchar(255) COLLATE utf8_bin NOT NULL,
  `pictureUrl` varchar(255) COLLATE utf8_bin NOT NULL,
  `pictureDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`pictureId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `refunds` (
  `refundId` int(5) NOT NULL AUTO_INCREMENT,
  `paymentId` int(5) NOT NULL,
  `propertyId` int(5) NOT NULL,
  `leaseId` int(5) NOT NULL,
  `tenantId` int(5) NOT NULL,
  `refundDate` date NOT NULL,
  `refundAmount` varchar(255) COLLATE utf8_bin NOT NULL,
  `refundFor` varchar(255) COLLATE utf8_bin NOT NULL,
  `refundedBy` int(5) NOT NULL,
  `refundNotes` longtext COLLATE utf8_bin,
  PRIMARY KEY (`refundId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `residents` (
  `residentId` int(5) NOT NULL AUTO_INCREMENT,
  `tenantId` int(5) NOT NULL,
  `residentName` varchar(255) COLLATE utf8_bin NOT NULL,
  `residentPhone` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `residentEmail` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `relation` varchar(255) COLLATE utf8_bin NOT NULL,
  `residentNotes` longtext COLLATE utf8_bin,
  `isArchived` int(1) NOT NULL DEFAULT '0',
  `archivedDate` date NOT NULL,
  PRIMARY KEY (`residentId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `serviceexpense` (
  `expenseId` int(5) NOT NULL AUTO_INCREMENT,
  `requestId` int(5) NOT NULL,
  `tenantId` int(5) NOT NULL,
  `leaseId` int(5) NOT NULL,
  `vendorName` varchar(255) COLLATE utf8_bin NOT NULL,
  `expenseName` varchar(255) COLLATE utf8_bin NOT NULL,
  `expenseDesc` longtext COLLATE utf8_bin NOT NULL,
  `expenseCost` varchar(255) COLLATE utf8_bin NOT NULL,
  `dateOfExpense` date NOT NULL,
  PRIMARY KEY (`expenseId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `servicenotes` (
  `noteId` int(5) NOT NULL AUTO_INCREMENT,
  `requestId` int(5) NOT NULL,
  `tenantId` int(5) NOT NULL,
  `adminId` int(5) NOT NULL,
  `noteText` longtext COLLATE utf8_bin NOT NULL,
  `noteDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`noteId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `servicerequests` (
  `requestId` int(5) NOT NULL AUTO_INCREMENT,
  `tenantId` int(5) NOT NULL,
  `leaseId` int(5) NOT NULL,
  `adminId` int(5) NOT NULL DEFAULT '0',
  `requestDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `requestPriority` int(5) NOT NULL,
  `requestStatus` int(5) NOT NULL,
  `requestTitle` varchar(255) COLLATE utf8_bin NOT NULL,
  `requestDesc` longtext COLLATE utf8_bin NOT NULL,
  `lastUpdated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`requestId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `serviceresolutions` (
  `resolutionId` int(5) NOT NULL AUTO_INCREMENT,
  `requestId` int(5) NOT NULL,
  `tenantId` int(5) NOT NULL,
  `adminId` int(5) NOT NULL,
  `resolutionText` longtext COLLATE utf8_bin NOT NULL,
  `resolutionDate` date NOT NULL,
  `needsFollowUp` int(1) NOT NULL DEFAULT '0',
  `followUpText` longtext COLLATE utf8_bin,
  `isComplete` int(1) NOT NULL DEFAULT '0',
  `completeDate` date NOT NULL,
  PRIMARY KEY (`resolutionId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `sitealerts` (
  `alertId` int(5) NOT NULL AUTO_INCREMENT,
  `adminId` int(5) NOT NULL,
  `isActive` int(1) NOT NULL DEFAULT '1',
  `onReceipt` int(1) NOT NULL DEFAULT '0',
  `alertTitle` varchar(255) COLLATE utf8_bin NOT NULL,
  `alertText` longtext COLLATE utf8_bin NOT NULL,
  `alertDate` date NOT NULL,
  `alertStart` date DEFAULT NULL,
  `alertExpires` date DEFAULT NULL,
  PRIMARY KEY (`alertId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `sitesettings` (
  `installUrl` varchar(255) COLLATE utf8_bin NOT NULL,
  `localization` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT 'en',
  `siteName` varchar(255) COLLATE utf8_bin NOT NULL,
  `businessName` varchar(255) COLLATE utf8_bin NOT NULL,
  `businessAddress` longtext COLLATE utf8_bin NOT NULL,
  `businessEmail` varchar(255) COLLATE utf8_bin NOT NULL,
  `businessPhone` varchar(255) COLLATE utf8_bin NOT NULL,
  `contactPhone` varchar(255) COLLATE utf8_bin NOT NULL,
  `uploadPath` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT 'uploads/',
  `templatesPath` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT 'templates/',
  `tenantDocsPath` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT 'docs/',
  `fileTypesAllowed` varchar(255) COLLATE utf8_bin NOT NULL,
  `avatarFolder` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT 'avatars/',
  `avatarTypes` varchar(255) COLLATE utf8_bin NOT NULL,
  `propertyPicsPath` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT 'pictures/',
  `propertyPicTypes` varchar(255) COLLATE utf8_bin NOT NULL,
  `enablePayments` int(1) NOT NULL DEFAULT '1',
  `paypalCurrency` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT 'USD',
  `paymentCompleteMsg` varchar(255) COLLATE utf8_bin NOT NULL,
  `paypalEmail` varchar(255) COLLATE utf8_bin NOT NULL,
  `paypalItemName` varchar(255) COLLATE utf8_bin NOT NULL,
  `paypalFee` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`installUrl`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `sitetemplates` (
  `templateId` int(5) NOT NULL AUTO_INCREMENT,
  `adminId` int(5) NOT NULL,
  `templateName` varchar(255) COLLATE utf8_bin NOT NULL,
  `templateDesc` longtext COLLATE utf8_bin NOT NULL,
  `templateUrl` varchar(255) COLLATE utf8_bin NOT NULL,
  `templateDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`templateId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `tenantdocs` (
  `docId` int(5) NOT NULL AUTO_INCREMENT,
  `tenantId` int(5) NOT NULL,
  `adminId` int(5) NOT NULL,
  `docTitle` varchar(255) COLLATE utf8_bin NOT NULL,
  `docDesc` longtext COLLATE utf8_bin NOT NULL,
  `docUrl` varchar(255) COLLATE utf8_bin NOT NULL,
  `docDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`docId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `tenants` (
  `tenantId` int(5) NOT NULL AUTO_INCREMENT,
  `propertyId` int(5) NOT NULL DEFAULT '0',
  `leaseId` int(5) NOT NULL DEFAULT '0',
  `tenantDocsFolder` varchar(255) COLLATE utf8_bin NOT NULL,
  `tenantEmail` varchar(255) COLLATE utf8_bin NOT NULL,
  `password` varchar(255) COLLATE utf8_bin NOT NULL,
  `tenantFirstName` varchar(255) COLLATE utf8_bin NOT NULL,
  `tenantLastName` varchar(255) COLLATE utf8_bin NOT NULL,
  `tenantAddress` longtext COLLATE utf8_bin,
  `tenantPhone` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `tenantAltPhone` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `tenantNotes` longtext COLLATE utf8_bin,
  `tenantPets` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `tenantAvatar` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT 'tenantDefault.png',
  `createDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `hash` varchar(32) COLLATE utf8_bin NOT NULL,
  `isActive` int(1) NOT NULL DEFAULT '0',
  `isArchived` int(1) NOT NULL DEFAULT '0',
  `archivedDate` date NOT NULL,
  PRIMARY KEY (`tenantId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;
