<?php

$config = &$GLOBALS['TL_DCA']['tl_page']['config'];
foreach(array('oncreate', 'onrestore', 'oncopy', 'oncut') as $callback) {
	$key = $callback . '_callback';
	$config[$key] = (array) $config[$key];
	array_unshift($config[$key], array('ContaoCommunityAlliance\\Contao\\RootRelations\\PageDCA', $callback . 'Page'));
}
unset($config);
