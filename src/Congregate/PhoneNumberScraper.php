<?php

namespace Congregate;

class PhoneNumberScraper implements ScraperInterface
{
	private static $countryCodes = [
		'93', '355', '213', '1 684', '376', '244', '1 264', '672', '1 268', '54',
		'374', '297', '61', '43', '994', '1 242', '973', '880', '1 246', '375', '32',
		'501', '229', '1 441', '975', '591', '387', '267', '55', '1 284', '673',
		'359', '226', '95', '257', '855', '237', '1', '238', '1 345', '236', '235',
		'56', '86', '61', '61', '57', '269', '682', '506', '385', '53', '357', '420',
		'243', '45', '253', '1 767', '1 809', '593', '20', '503', '240', '291', '372',
		'251', '500', '298', '679', '358', '33', '689', '241', '220', '995', '49',
		'233', '350', '30', '299', '1 473', '1 671', '502', '224', '245', '592',
		'509', '39', '504', '852', '36', '354', '91', '62', '98', '964', '353', '44',
		'972', '39', '225', '1 876', '81', '962', '7', '254', '686', '965', '996',
		'856', '371', '961', '266', '231', '218', '423', '370', '352', '853', '389',
		'261', '265', '60', '960', '223', '356', '692', '222', '230', '262', '52',
		'691', '373', '377', '976', '382', '1 664', '212', '258', '264', '674', '977',
		'31', '599', '687', '64', '505', '227', '234', '683', '850', '1 670', '47',
		'968', '92', '680', '507', '675', '595', '51', '63', '870', '48', '351', '1',
		'974', '242', '40', '7', '250', '590', '290', '1 869', '1 758', '1 599',
		'508', '1 784', '685', '378', '239', '966', '221', '381', '248', '232', '65',
		'421', '386', '677', '252', '27', '82', '34', '94', '249', '597', '268', '46',
		'41', '963', '886', '992', '255', '66', '670', '228', '690', '676', '1 868',
		'216', '90', '993', '1 649', '688', '256', '380', '971', '44', '1', '598',
		'1 340', '998', '678', '58', '84', '681', '967', '260', '263'
	 	];

	public static function scrape(\DOMDocument $doc) {
		$phoneNumbers = [];
		$results      = [
			'href:tel'   => [],
			'href:skype' => [],
			'country:us' => []
			];

		$xpath = new \DOMXpath($doc);

		$textNodes = $xpath->query('//text()');

		foreach ( $textNodes as $textNode ) {
			$text = $textNode->textContent;

			if ( $result = self::testUS($text) ) {
				$results['country:us'] = array_merge($results['country:us'], $result);
			}
		}

		if ( $result = self::testTel($doc) ) {
			$results['href:tel'] = $result;
		}

		if ( $result = self::testSkype($doc) ) {
			$results['href:skype'] = $result;
		}

		foreach ( $results as $locality => $values ) {
			if ( $values ) {
				foreach ( $values as $value ) {
					if ( !isset($phoneNumbers[$value]) ) {
						$phoneNumbers[$value] = [];
					}

					$phoneNumbers[$value][] = $locality;
				}
			}
		}

		$phoneNumbers = self::filter($phoneNumbers);

		ksort($phoneNumbers);

		return $phoneNumbers;
	}

	private static function testTel(\DOMDocument $doc) {
		$results = [];

		foreach ( $doc->getElementsByTagName('a') as $anchor ) {
			if ( $link = $anchor->getAttribute('href') ) {
				if ( preg_match('/^tel:(.+$)/', $link, $match) && isset($match[1]) ) {
					$results[] = $match[1];
				}
			}
		}

		return $results;
	}

	private static function testSkype(\DOMDocument $doc) {
		$results = [];

		foreach ( $doc->getElementsByTagName('a') as $anchor ) {
			if ( $link = $anchor->getAttribute('href') ) {
				if ( preg_match('/^skype:(.+$)/', $link, $match) && isset($match[1]) ) {
					$participants = explode(';', preg_replace('/\?.*$/', '', $match[1]));

					foreach ( $participants as $participant ) {
						$results[] = $participant;
					}
				}
			}
		}

		return array_unique($results);
	}

	private static function testUS($string = '') {
		$areaCodes = [
			'205', '251', '256', '334', '938', '907', '480', '520', '602', '623',
			'928', '479', '501', '870', '209', '213', '310', '323', '408', '415',
			'424', '442', '510', '530', '559', '562', '619', '626', '628', '650',
			'657', '661', '669', '707', '714', '747', '760', '805', '818', '831',
			'858', '909', '916', '925', '949', '951', '303', '719', '720', '970',
			'203', '475', '860', '959', '302', '202', '239', '305', '321', '352',
			'386', '407', '561', '689', '727', '754', '772', '786', '813', '850',
			'863', '904', '941', '954', '229', '404', '470', '478', '678', '706',
			'762', '770', '912', '808', '208', '217', '224', '309', '312', '331',
			'618', '630', '708', '773', '779', '815', '847', '872', '219', '260',
			'317', '574', '765', '812', '930', '319', '515', '563', '641', '712',
			'316', '620', '785', '913', '270', '364', '502', '606', '859', '225',
			'318', '337', '504', '985', '207', '240', '301', '410', '443', '667',
			'339', '351', '413', '508', '617', '774', '781', '857', '978', '231',
			'248', '269', '313', '517', '586', '616', '734', '810', '906', '947',
			'989', '218', '320', '507', '612', '651', '763', '952', '228', '601',
			'662', '769', '314', '417', '573', '636', '660', '816', '406', '308',
			'402', '531', '702', '725', '775', '603', '201', '551', '609', '732',
			'848', '856', '862', '908', '973', '505', '575', '212', '315', '347',
			'516', '518', '585', '607', '631', '646', '716', '718', '845', '914',
			'917', '929', '252', '336', '704', '828', '910', '919', '980', '984',
			'701', '216', '234', '330', '419', '440', '513', '567', '614', '740',
			'937', '405', '539', '580', '918', '458', '503', '541', '971', '215',
			'267', '272', '412', '484', '570', '610', '717', '724', '814', '878',
			'787', '939', '401', '803', '843', '864', '605', '423', '615', '731',
			'865', '901', '931', '210', '214', '254', '281', '325', '346', '361',
			'409', '430', '432', '469', '512', '682', '713', '737', '806', '817',
			'830', '832', '903', '915', '936', '940', '956', '972', '979', '385',
			'435', '801', '802', '276', '434', '540', '571', '703', '757', '804',
			'206', '253', '360', '425', '509', '202', '304', '681', '262', '414',
			'534', '608', '715', '920', '307', '800', '844', '855', '866', '877',
		 	'888',
			];

		$country    = "(?:00|\+)1";
		$area       = "(?:" . implode('|', $areaCodes) . ")";
		$co         = "[2-9][0-9]{2}";
		$subscriber = "[0-9]{4}";

		$patterns = [
			"${country}-${area}-${co}-${subscriber}",
			"${country}.${area}.${co}.${subscriber}",
			"${country} ${area} ${co} ${subscriber}",
			"${area}-${co}-${subscriber}",
			"${area}.${co}.${subscriber}",
			"${area} ${co} ${subscriber}",
			"${co}-${subscriber}",
			"${co} ${subscriber}",
			];

		return self::evaluate($patterns, $string);
	}

	/**
	 * Evaluate multiple regular expressions
	 */
	private static function evaluate(array $patterns = [], $string = '') {
		$results = [];

		foreach ( $patterns as $pattern ) {
			if ( preg_match_all('/(?:^|\s|\()' . $pattern . '(?:\)|\s|$)/i', $string, $matches) ) {
				foreach ( $matches as $match ) {
					$result = rtrim(ltrim(trim($match[0]), '('), ')');

					if ( !in_array($result, $results) ) {
						$results[] = $result;
					}
				}
			}
		}

		return $results;
	}

	/**
	 * Remove match if a longer match exists
	 */
	private static function filter(array $array = []) {
		foreach ( $array as $self => $value ) {
			foreach ( $array as $key => $value ) {
				if ( strpos($key, $self) !== false && strlen($key) > strlen($self) ) {
					unset($array[$self]);
				}
			}
		}

		return $array;
	}
}
