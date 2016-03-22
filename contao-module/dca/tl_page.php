<?php

use Hofff\Contao\RootRelations\PageDCA;

$config = &$GLOBALS['TL_DCA']['tl_page']['config'];
foreach([ 'oncreate', 'onsubmit', 'onrestore', 'oncopy', 'oncut' ] as $callback) {
	$key = $callback . '_callback';
	$config[$key] = (array) $config[$key];
	array_unshift($config[$key], [ PageDCA::class, $callback . 'Page' ]);
}
$config['sql']['keys']['cca_rr_root'] = 'index';
unset($config);

$GLOBALS['TL_DCA']['tl_page']['fields']['cca_rr_root']['sql']
	= 'int(10) unsigned NOT NULL default \'0\'';

$GLOBALS['TL_DCA']['tl_page']['fields']['type']['save_callback'][]
	= [ PageDCA::class, 'saveType' ];
