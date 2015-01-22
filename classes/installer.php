<?php
class installerSwr {
	static public $update_to_version_method = '';
	static private $_firstTimeActivated = false;
	static public function init() {
		global $wpdb;
		$wpPrefix = $wpdb->prefix; /* add to 0.0.3 Versiom */
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		$current_version = get_option($wpPrefix. SWR_DB_PREF. 'db_version', 0);
		if(!$current_version)
			self::$_firstTimeActivated = true;
		/**
		 * modules 
		 */
		if (!dbSwr::exist("@__modules")) {
			dbDelta(dbSwr::prepareQuery("CREATE TABLE IF NOT EXISTS `@__modules` (
			  `id` smallint(3) NOT NULL AUTO_INCREMENT,
			  `code` varchar(32) NOT NULL,
			  `active` tinyint(1) NOT NULL DEFAULT '0',
			  `type_id` tinyint(1) NOT NULL DEFAULT '0',
			  `label` varchar(64) DEFAULT NULL,
			  `ex_plug_dir` varchar(255) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE INDEX `code` (`code`)
			) DEFAULT CHARSET=utf8;"));
			dbSwr::query("INSERT INTO `@__modules` (id, code, active, type_id, label) VALUES
				(NULL, 'adminmenu',1,1,'Admin Menu'),
				(NULL, 'options',1,1,'Options'),
				(NULL, 'user',1,1,'Users'),
				(NULL, 'pages',1,1,'Pages'),
				(NULL, 'templates',1,1,'templates'),
				(NULL, 'supsystic_promo',1,1,'supsystic_promo'),

				(NULL, 'admin_nav',1,1,'admin_nav'),
				(NULL, 'blacklist',1,1,'blacklist'),
				(NULL, 'secure_files',1,1,'secure_files'),
				(NULL, 'secure_login',1,1,'secure_login'),
				(NULL, 'secure_hide',1,1,'secure_hide'),
				
				(NULL, 'htaccess',1,1,'htaccess'),
				(NULL, 'firewall',1,1,'firewall'),
				(NULL, 'system',1,1,'system'),
				(NULL, 'statistics',1,1,'statistics'),
				(NULL, 'mail',1,1,'mail');");
		}
		/**
		 *  modules_type 
		 */
		if(!dbSwr::exist("@__modules_type")) {
			dbDelta(dbSwr::prepareQuery("CREATE TABLE IF NOT EXISTS `@__modules_type` (
			  `id` smallint(3) NOT NULL AUTO_INCREMENT,
			  `label` varchar(32) NOT NULL,
			  PRIMARY KEY (`id`)
			) AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;"));
			dbSwr::query("INSERT INTO `@__modules_type` VALUES
				(1,'system'),
				(6,'addons');");
		}
		/**
		* Plugin usage statistics
		*/
		if(!dbSwr::exist("@__usage_stat")) {
			dbDelta(dbSwr::prepareQuery("CREATE TABLE `@__usage_stat` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `code` varchar(64) NOT NULL,
			  `visits` int(11) NOT NULL DEFAULT '0',
			  `spent_time` int(11) NOT NULL DEFAULT '0',
			  `modify_timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			  UNIQUE INDEX `code` (`code`),
			  PRIMARY KEY (`id`)
			) DEFAULT CHARSET=utf8"));
			dbSwr::query("INSERT INTO `@__usage_stat` (code, visits) VALUES ('installed', 1)");
		}
		/**
		 * Blacklist
		 */
		if (!dbSwr::exist("@__blacklist")) {
			dbDelta(dbSwr::prepareQuery("CREATE TABLE IF NOT EXISTS `@__blacklist` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `ip` varchar(16) NOT NULL,
			  `type` TINYINT(2) NOT NULL DEFAULT '0',
			  `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			  PRIMARY KEY (`id`),
			  KEY (`ip`)
			) DEFAULT CHARSET=utf8;"));
		}
		/**
		 * Blacklist Country
		 */
		if (!dbSwr::exist("@__blacklist_countries")) {
			dbDelta(dbSwr::prepareQuery("CREATE TABLE IF NOT EXISTS `@__blacklist_countries` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `country_id` SMALLINT(3) DEFAULT NULL,
			  `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			  PRIMARY KEY (`id`)
			) DEFAULT CHARSET=utf8;"));
		}/**
		 * Blacklist Browser
		 */
		if (!dbSwr::exist("@__blacklist_browsers")) {
			dbDelta(dbSwr::prepareQuery("CREATE TABLE IF NOT EXISTS `@__blacklist_browsers` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `browser_name` varchar(64) NOT NULL,
			  `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			  PRIMARY KEY (`id`)
			) DEFAULT CHARSET=utf8;"));
		}
		/**
		 * Files snapshots
		 */
		if (!dbSwr::exist("@__files_snapshot")) {
			dbDelta(dbSwr::prepareQuery("CREATE TABLE IF NOT EXISTS `@__files_snapshot` (
			  `filepathMd5` BINARY(16) NOT NULL,
			  `filepath` varchar(1000) NOT NULL,
			  `md5` BINARY(16) NOT NULL,
			  `md5_old` BINARY(16) NOT NULL,
			  `version` varchar(12) NOT NULL,
			  PRIMARY KEY (`filepathMd5`)
			) DEFAULT CHARSET=utf8;"));
		}
		/**
		 * Files issues
		 */
		if (!dbSwr::exist("@__files_issues")) {
			dbDelta(dbSwr::prepareQuery("CREATE TABLE IF NOT EXISTS `@__files_issues` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `filepathMd5` BINARY(16) NOT NULL,			  
			  `filepath` varchar(1000) NOT NULL,
			  `filename` varchar(255) NOT NULL,
			  `last_time_modified` int(11) NOT NULL,
			  `type` tinyint(1) NOT NULL DEFAULT '0',
			  `location_type` tinyint(1) NOT NULL DEFAULT '0',
			  `last_scan` tinyint(1) NOT NULL DEFAULT '1',
			  `date_found` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			  PRIMARY KEY (`id`)
			) DEFAULT CHARSET=utf8;"));
		}
		/**
		 * Countries
		 */
		if (!dbSwr::exist("@__countries")) {
			  dbDelta(dbSwr::prepareQuery("CREATE TABLE `@__countries` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`name` varchar(128) NOT NULL,
				`iso_code_2` varchar(2) DEFAULT NULL,
				`iso_code_3` varchar(3) DEFAULT NULL,
				PRIMARY KEY (`id`)
			  ) DEFAULT CHARSET=utf8;"));
			  self::_insertCountries();
		}
		/**
		 * Statistics
		 */
		if (!dbSwr::exist("@__statistics")) {
			  dbDelta(dbSwr::prepareQuery("CREATE TABLE `@__statistics` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`ip` varchar(16) NOT NULL,
				`type` TINYINT(2) NOT NULL DEFAULT '0',
				`url` varchar(255) NOT NULL,
				`date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (`id`)
			  ) DEFAULT CHARSET=utf8;"));
		}
		installerDbUpdaterSwr::runUpdate();
		if($current_version && !self::$_firstTimeActivated) {
			self::setUsed();
		}
		update_option($wpPrefix. SWR_DB_PREF. 'db_version', SWR_VERSION);
		add_option($wpPrefix. SWR_DB_PREF. 'db_installed', 1);
	}
	static public function setUsed() {
		update_option(SWR_DB_PREF. 'plug_was_used', 1);
	}
	static public function isUsed() {
		// No welcome page for now
		return true;
		return (int) get_option(SWR_DB_PREF. 'plug_was_used');
	}
	static public function delete() {
		global $wpdb;
		$wpPrefix = $wpdb->prefix;
		$wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.SWR_DB_PREF."modules`");
		$wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.SWR_DB_PREF."modules_type`");
		$wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.SWR_DB_PREF."usage_stat`");
		$wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.SWR_DB_PREF."countries`");
		delete_option($wpPrefix. 'db_version');
		delete_option($wpPrefix. 'db_installed');
	}
	static public function update() {
		global $wpdb;
		$wpPrefix = $wpdb->prefix; /* add to 0.0.3 Versiom */
		$currentVersion = get_option($wpPrefix. 'db_version', 0);
		if(!$currentVersion || version_compare(SWR_VERSION, $currentVersion, '>')) {
			self::init();
			update_option($wpPrefix. 'db_version', SWR_VERSION);
		}
	}
	private function _insertCountries() {
		dbSwr::query('INSERT INTO @__countries VALUES 
			(1, "Afghanistan", "AF", "AFG"),
			(2, "Albania", "AL", "ALB"),
			(3, "Algeria", "DZ", "DZA"),
			(4, "American Samoa", "AS", "ASM"),
			(5, "Andorra", "AD", "AND"),
			(6, "Angola", "AO", "AGO"),
			(7, "Anguilla", "AI", "AIA"),
			(8, "Antarctica", "AQ", "ATA"),
			(9, "Antigua and Barbuda", "AG", "ATG"),
			(10, "Argentina", "AR", "ARG"),
			(11, "Armenia", "AM", "ARM"),
			(12, "Aruba", "AW", "ABW"),
			(13, "Australia", "AU", "AUS"),
			(14, "Austria", "AT", "AUT"),
			(15, "Azerbaijan", "AZ", "AZE"),
			(16, "Bahamas", "BS", "BHS"),
			(17, "Bahrain", "BH", "BHR"),
			(18, "Bangladesh", "BD", "BGD"),
			(19, "Barbados", "BB", "BRB"),
			(20, "Belarus", "BY", "BLR"),
			(21, "Belgium", "BE", "BEL"),
			(22, "Belize", "BZ", "BLZ"),
			(23, "Benin", "BJ", "BEN"),
			(24, "Bermuda", "BM", "BMU"),
			(25, "Bhutan", "BT", "BTN"),
			(26, "Bolivia", "BO", "BOL"),
			(27, "Bosnia and Herzegowina", "BA", "BIH"),
			(28, "Botswana", "BW", "BWA"),
			(29, "Bouvet Island", "BV", "BVT"),
			(30, "Brazil", "BR", "BRA"),
			(31, "British Indian Ocean Territory", "IO", "IOT"),
			(32, "Brunei Darussalam", "BN", "BRN"),
			(33, "Bulgaria", "BG", "BGR"),
			(34, "Burkina Faso", "BF", "BFA"),
			(35, "Burundi", "BI", "BDI"),
			(36, "Cambodia", "KH", "KHM"),
			(37, "Cameroon", "CM", "CMR"),
			(38, "Canada", "CA", "CAN"),
			(39, "Cape Verde", "CV", "CPV"),
			(40, "Cayman Islands", "KY", "CYM"),
			(41, "Central African Republic", "CF", "CAF"),
			(42, "Chad", "TD", "TCD"),
			(43, "Chile", "CL", "CHL"),
			(44, "China", "CN", "CHN"),
			(45, "Christmas Island", "CX", "CXR"),
			(46, "Cocos (Keeling) Islands", "CC", "CCK"),
			(47, "Colombia", "CO", "COL"),
			(48, "Comoros", "KM", "COM"),
			(49, "Congo", "CG", "COG"),
			(50, "Cook Islands", "CK", "COK"),
			(51, "Costa Rica", "CR", "CRI"),
			(52, "Cote D\'Ivoire", "CI", "CIV"),
			(53, "Croatia", "HR", "HRV"),
			(54, "Cuba", "CU", "CUB"),
			(55, "Cyprus", "CY", "CYP"),
			(56, "Czech Republic", "CZ", "CZE"),
			(57, "Denmark", "DK", "DNK"),
			(58, "Djibouti", "DJ", "DJI"),
			(59, "Dominica", "DM", "DMA"),
			(60, "Dominican Republic", "DO", "DOM"),
			(61, "East Timor", "TP", "TMP"),
			(62, "Ecuador", "EC", "ECU"),
			(63, "Egypt", "EG", "EGY"),
			(64, "El Salvador", "SV", "SLV"),
			(65, "Equatorial Guinea", "GQ", "GNQ"),
			(66, "Eritrea", "ER", "ERI"),
			(67, "Estonia", "EE", "EST"),
			(68, "Ethiopia", "ET", "ETH"),
			(69, "Falkland Islands (Malvinas)", "FK", "FLK"),
			(70, "Faroe Islands", "FO", "FRO"),
			(71, "Fiji", "FJ", "FJI"),
			(72, "Finland", "FI", "FIN"),
			(73, "France", "FR", "FRA"),
			(74, "France, Metropolitan", "FX", "FXX"),
			(75, "French Guiana", "GF", "GUF"),
			(76, "French Polynesia", "PF", "PYF"),
			(77, "French Southern Territories", "TF", "ATF"),
			(78, "Gabon", "GA", "GAB"),
			(79, "Gambia", "GM", "GMB"),
			(80, "Georgia", "GE", "GEO"),
			(81, "Germany", "DE", "DEU"),
			(82, "Ghana", "GH", "GHA"),
			(83, "Gibraltar", "GI", "GIB"),
			(84, "Greece", "GR", "GRC"),
			(85, "Greenland", "GL", "GRL"),
			(86, "Grenada", "GD", "GRD"),
			(87, "Guadeloupe", "GP", "GLP"),
			(88, "Guam", "GU", "GUM"),
			(89, "Guatemala", "GT", "GTM"),
			(90, "Guinea", "GN", "GIN"),
			(91, "Guinea-bissau", "GW", "GNB"),
			(92, "Guyana", "GY", "GUY"),
			(93, "Haiti", "HT", "HTI"),
			(94, "Heard and Mc Donald Islands", "HM", "HMD"),
			(95, "Honduras", "HN", "HND"),
			(96, "Hong Kong", "HK", "HKG"),
			(97, "Hungary", "HU", "HUN"),
			(98, "Iceland", "IS", "ISL"),
			(99, "India", "IN", "IND"),
			(100, "Indonesia", "ID", "IDN"),
			(101, "Iran (Islamic Republic of)", "IR", "IRN"),
			(102, "Iraq", "IQ", "IRQ"),
			(103, "Ireland", "IE", "IRL"),
			(104, "Israel", "IL", "ISR"),
			(105, "Italy", "IT", "ITA"),
			(106, "Jamaica", "JM", "JAM"),
			(107, "Japan", "JP", "JPN"),
			(108, "Jordan", "JO", "JOR"),
			(109, "Kazakhstan", "KZ", "KAZ"),
			(110, "Kenya", "KE", "KEN"),
			(111, "Kiribati", "KI", "KIR"),
			(112, "Korea, Democratic People\'s Republic of", "KP", "PRK"),
			(113, "Korea, Republic of", "KR", "KOR"),
			(114, "Kuwait", "KW", "KWT"),
			(115, "Kyrgyzstan", "KG", "KGZ"),
			(116, "Lao People\'s Democratic Republic", "LA", "LAO"),
			(117, "Latvia", "LV", "LVA"),
			(118, "Lebanon", "LB", "LBN"),
			(119, "Lesotho", "LS", "LSO"),
			(120, "Liberia", "LR", "LBR"),
			(121, "Libyan Arab Jamahiriya", "LY", "LBY"),
			(122, "Liechtenstein", "LI", "LIE"),
			(123, "Lithuania", "LT", "LTU"),
			(124, "Luxembourg", "LU", "LUX"),
			(125, "Macau", "MO", "MAC"),
			(126, "Macedonia, The Former Yugoslav Republic of", "MK", "MKD"),
			(127, "Madagascar", "MG", "MDG"),
			(128, "Malawi", "MW", "MWI"),
			(129, "Malaysia", "MY", "MYS"),
			(130, "Maldives", "MV", "MDV"),
			(131, "Mali", "ML", "MLI"),
			(132, "Malta", "MT", "MLT"),
			(133, "Marshall Islands", "MH", "MHL"),
			(134, "Martinique", "MQ", "MTQ"),
			(135, "Mauritania", "MR", "MRT"),
			(136, "Mauritius", "MU", "MUS"),
			(137, "Mayotte", "YT", "MYT"),
			(138, "Mexico", "MX", "MEX"),
			(139, "Micronesia, Federated States of", "FM", "FSM"),
			(140, "Moldova, Republic of", "MD", "MDA"),
			(141, "Monaco", "MC", "MCO"),
			(142, "Mongolia", "MN", "MNG"),
			(143, "Montenegro", "ME", "MNE"),
			(144, "Montserrat", "MS", "MSR"),
			(145, "Morocco", "MA", "MAR"),
			(146, "Mozambique", "MZ", "MOZ"),
			(147, "Myanmar", "MM", "MMR"),
			(148, "Namibia", "NA", "NAM"),
			(149, "Nauru", "NR", "NRU"),
			(150, "Nepal", "NP", "NPL"),
			(151, "Netherlands", "NL", "NLD"),
			(152, "Netherlands Antilles", "AN", "ANT"),
			(153, "New Caledonia", "NC", "NCL"),
			(154, "New Zealand", "NZ", "NZL"),
			(155, "Nicaragua", "NI", "NIC"),
			(156, "Niger", "NE", "NER"),
			(157, "Nigeria", "NG", "NGA"),
			(158, "Niue", "NU", "NIU"),
			(159, "Norfolk Island", "NF", "NFK"),
			(160, "Northern Mariana Islands", "MP", "MNP"),
			(161, "Norway", "NO", "NOR"),
			(162, "Oman", "OM", "OMN"),
			(163, "Pakistan", "PK", "PAK"),
			(164, "Palau", "PW", "PLW"),
			(165, "Panama", "PA", "PAN"),
			(166, "Papua New Guinea", "PG", "PNG"),
			(167, "Paraguay", "PY", "PRY"),
			(168, "Peru", "PE", "PER"),
			(169, "Philippines", "PH", "PHL"),
			(170, "Pitcairn", "PN", "PCN"),
			(171, "Poland", "PL", "POL"),
			(172, "Portugal", "PT", "PRT"),
			(173, "Puerto Rico", "PR", "PRI"),
			(174, "Qatar", "QA", "QAT"),
			(175, "Reunion", "RE", "REU"),
			(176, "Romania", "RO", "ROM"),
			(177, "Russian Federation", "RU", "RUS"),
			(178, "Rwanda", "RW", "RWA"),
			(179, "Saint Kitts and Nevis", "KN", "KNA"),
			(180, "Saint Lucia", "LC", "LCA"),
			(181, "Saint Vincent and the Grenadines", "VC", "VCT"),
			(182, "Samoa", "WS", "WSM"),
			(183, "San Marino", "SM", "SMR"),
			(184, "Sao Tome and Principe", "ST", "STP"),
			(185, "Saudi Arabia", "SA", "SAU"),
			(186, "Senegal", "SN", "SEN"),
			(187, "Serbia", "RS", "SRB"),
			(188, "Seychelles", "SC", "SYC"),
			(189, "Sierra Leone", "SL", "SLE"),
			(190, "Singapore", "SG", "SGP"),
			(191, "Slovakia (Slovak Republic)", "SK", "SVK"),
			(192, "Slovenia", "SI", "SVN"),
			(193, "Solomon Islands", "SB", "SLB"),
			(194, "Somalia", "SO", "SOM"),
			(195, "South Africa", "ZA", "ZAF"),
			(196, "South Georgia and the South Sandwich Islands", "GS", "SGS"),
			(197, "Spain", "ES", "ESP"),
			(198, "Sri Lanka", "LK", "LKA"),
			(199, "St. Helena", "SH", "SHN"),
			(200, "St. Pierre and Miquelon", "PM", "SPM"),
			(201, "Sudan", "SD", "SDN"),
			(202, "Suriname", "SR", "SUR"),
			(203, "Svalbard and Jan Mayen Islands", "SJ", "SJM"),
			(204, "Swaziland", "SZ", "SWZ"),
			(205, "Sweden", "SE", "SWE"),
			(206, "Switzerland", "CH", "CHE"),
			(207, "Syrian Arab Republic", "SY", "SYR"),
			(208, "Taiwan", "TW", "TWN"),
			(209, "Tajikistan", "TJ", "TJK"),
			(210, "Tanzania, United Republic of", "TZ", "TZA"),
			(211, "Thailand", "TH", "THA"),
			(212, "Togo", "TG", "TGO"),
			(213, "Tokelau", "TK", "TKL"),
			(214, "Tonga", "TO", "TON"),
			(215, "Trinidad and Tobago", "TT", "TTO"),
			(216, "Tunisia", "TN", "TUN"),
			(217, "Turkey", "TR", "TUR"),
			(218, "Turkmenistan", "TM", "TKM"),
			(219, "Turks and Caicos Islands", "TC", "TCA"),
			(220, "Tuvalu", "TV", "TUV"),
			(221, "Uganda", "UG", "UGA"),
			(222, "Ukraine", "UA", "UKR"),
			(223, "United Arab Emirates", "AE", "ARE"),
			(224, "United Kingdom", "GB", "GBR"),
			(225, "United States", "US", "USA"),
			(226, "United States Minor Outlying Islands", "UM", "UMI"),
			(227, "Uruguay", "UY", "URY"),
			(228, "Uzbekistan", "UZ", "UZB"),
			(229, "Vanuatu", "VU", "VUT"),
			(230, "Vatican City State (Holy See)", "VA", "VAT"),
			(231, "Venezuela", "VE", "VEN"),
			(232, "Viet Nam", "VN", "VNM"),
			(233, "Virgin Islands (British)", "VG", "VGB"),
			(234, "Virgin Islands (U.S.)", "VI", "VIR"),
			(235, "Wallis and Futuna Islands", "WF", "WLF"),
			(236, "Western Sahara", "EH", "ESH"),
			(237, "Yemen", "YE", "YEM"),
			(238, "Zaire", "ZR", "ZAR"),
			(239, "Zambia", "ZM", "ZMB"),
			(240, "Zimbabwe", "ZW", "ZWE")');
	}
}
