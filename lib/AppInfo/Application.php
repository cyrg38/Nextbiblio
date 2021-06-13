<?php

namespace OCA\Nextbiblio\AppInfo;

use OCP\AppFramework\App;

class Application extends App {
	public const APP_ID = 'nextbiblio';

	public function __construct() {
		parent::__construct(self::APP_ID);
	}
}
