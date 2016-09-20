<?php

$config = &$GLOBALS['TL_DCA']['tl_page']['config'];
foreach([ 'oncreate', 'onsubmit', 'onrestore', 'oncopy', 'oncut' ] as $callback) {
	$key = $callback . '_callback';
	$config[$key] = (array) $config[$key];
	array_unshift($config[$key], [ \Hofff\Contao\RootRelations\PageDCA::class, $callback . 'Page' ]);
}
$config['sql']['keys']['hofff_root_page_id'] = 'index';
unset($config);

$GLOBALS['TL_DCA']['tl_page']['fields']['hofff_root_page_id']['sql']
	= 'int(10) unsigned NOT NULL default \'0\'';

$GLOBALS['TL_DCA']['tl_page']['fields']['type']['save_callback'][]
	= [ \Hofff\Contao\RootRelations\PageDCA::class, 'saveType' ];
