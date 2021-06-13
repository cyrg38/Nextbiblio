<?php

return [
	'resources' => [
		'nextbiblio' => ['url' => '/nextbiblio'],
		'nextbiblio_api' => ['url' => '/api/0.1/nextbiblio']
	],
	'routes' => [
		['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
		['name' => 'nextbiblio_api#preflighted_cors', 'url' => '/api/0.1/{path}',
			'verb' => 'OPTIONS', 'requirements' => ['path' => '.+']]
	]
];
