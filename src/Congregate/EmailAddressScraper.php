<?php

namespace Congregate;

class EmailAddressScraper implements ScraperInterface
{
	public static function scrape(\DOMDocument $doc) {
		$emailAddresses = [];

		$xpath = new \DOMXpath($doc);

		$textNodes = $xpath->query('//text()');

		$text = '';

		foreach ( $textNodes as $textNode ) {
			$text .= $textNode->textContent . "\n";
		}

		$emailAddresses = array_merge($emailAddresses, self::testEmail($text));
		$emailAddresses = array_merge($emailAddresses, self::testProtoEmail($doc));

		$emailAddresses = array_unique($emailAddresses);

		return $emailAddresses;
	}

	private static function testProtoEmail(\DOMDocument $doc) {
		$results = [];

		foreach ( $doc->getElementsByTagName('a') as $anchor ) {
			if ( $link = $anchor->getAttribute('href') ) {
				if ( preg_match('/^mailto:(.+$)/', $link, $match) && isset($match[1]) ) {
					$results[] = $match[1];
				}
			}
		}

		return array_unique($results);
	}

	private static function testEmail($string) {
		$results = [];

		$pattern = '/\b[^@\n]+@[^@\n]+\b/';

		if ( preg_match_all($pattern, $string, $matches) ) {
			foreach ( $matches[0] as $match ) {
				if ( !in_array($match, $results) && filter_var($match, FILTER_VALIDATE_EMAIL) ) {
					$results[] = $match;
				}
			}
		}

		return $results;
	}
}
