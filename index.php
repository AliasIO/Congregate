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

	$doc = new \DOMDocument();

	libxml_use_internal_errors(true);

	$doc->loadHTML($html);

	libxml_clear_errors();

	$results->phoneNumbers = PhoneNumberScraper::scrape($doc);

	var_dump($results);
} catch ( \Exception $e ) {
	echo $e->getMessage() . "\n";

	exit(1);
}

exit(0);