<?php
namespace DirtyHtmlTools;
exit;

$dir_iterator = new \RecursiveDirectoryIterator(dirname(__DIR__).'/src', \FilesystemIterator::SKIP_DOTS);
$iterator = new \RecursiveIteratorIterator($dir_iterator);

foreach ($iterator as $file) {
	include $file;
}

echo Sanitize::filterTags('<p onmouseover="pinto()">z</p><!-- asdf --><b>zxcv<i>qwer</i></b><p><a href="#" onclick="alert(1234)">zzzzzzzzz</a>', array(
	'p',
	'b',
	'i',
	'a' => array(
		'href',
	),
));