<?php

namespace Congregate;

error_reporting(-1);

chdir(dirname(__FILE__));

require 'vendor/autoload.php';

try {
	$results = new \StdClass;

	$html = '';

	while ( $line = fgets(STDIN) ) {
		$html .= $line;
	}

	if ( $html ) {
		$doc = new \DOMDocument();

		libxml_use_internal_errors(true);

		$doc->loadHTML($html);

		libxml_clear_errors();

		$phoneNumbers   = PhoneNumberScraper::scrape($doc);
		$emailAddresses = EmailAddressScraper::scrape($doc);

		if ( $phoneNumbers ) {
			$results->phone_numbers = $phoneNumbers;
		}

		if ( $emailAddresses ) {
			$results->email_addresses = $emailAddresses;
		}
	}

	echo json_encode($results) . "\n";
} catch ( \Exception $e ) {
	echo $e->getMessage() . "\n";

	exit(1);
}

exit(0);
