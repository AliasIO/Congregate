<?php

namespace Congregate;

interface ScraperInterface
{
	public static function scrape(\DOMDocument $doc);
}
