<?php
exit;
namespace DirtyHtmlTools;

$dir_iterator = new \RecursiveDirectoryIterator(dirname(__DIR__).'/src', \FilesystemIterator::SKIP_DOTS);
$iterator = new \RecursiveIteratorIterator($dir_iterator);

foreach ($iterator as $file) {
	include $file;
}

$samples = array();

// $samples[] = '<a:asdf_asdf-asda.sdfasfasdf asdfasd             fasdfa sdfa sdf               >SDFAS</a>';
// $samples[] = '<<';
// $samples[] = '<>';
// $samples[] = '<';
// $samples[] = '<a';
// $samples[] = '<a href';
// $samples[] = '<a href=';
// $samples[] = '<a href="';
// $samples[] = '<a href="asdf';
// $samples[] = '<a href="asdf"';
// $samples[] = '<a href="asdf">';
// $samples[] = '<a href="asdf">z';
// $samples[] = '<a href="asdf">z<';
// $samples[] = '<a href="asdf">z</';
// $samples[] = '<a href="asdf">z</a';
// $samples[] = '<a href="asdf">z</a>';
// $samples[] = '</a>';
// $samples[] = '<a/><b/>';
// $samples[] = '<a></a><b></b><c/>';
// $samples[] = '<a><b><c/></b></a>';
// $samples[] = '<!--asdfasdf--';
// $samples[] = '<!-- &amp; askjdfgafgajksdf -->dfgsdfgsdfgsdfg';
// $samples[] = '<!--';
// $samples[] = '<?xml version="1.0" encoding="UTF-8" standalone="no" ?'.'>';
// $samples[] = '<a:b:c/><d_e f.g-h="&amp;"/>';
// $samples[] = "<a              a b           c='d'>d<b />b<c type=\"l\">d</c></a>zzz<e>f</e>";
$samples[] = "<z               >aSDasdAS<b>Z\\ZZZZZ&amp;</b>asdfasdfs\naf</z>
<a href=\"asdfas\ndfasdf\" asdfa=\"1\" />
<plau z='\"minha irmã '>sdfasdfasdfa </plau>
<!--asdf-->ffffffffffffffffff&tesgj;wertyu\"''   <!--oirytiu <zxcvbnm> o <> eryoti-->";
$samples[] = '<p><b>Área:</b> Serviços</p><p><b>Sexo:</b> Masculino</p><p><b>Descrição:</b>
	Atendente / Estoquista\\r\\n\\r\\nvaga para o Sexo Masculino\\r\\n\\r\\nA partir dos 18 anos at&eacute;
	30 anos\\r\\n\\r\\nCom Experiencia na fun&ccedil;&atilde;o\\r\\n\\r\\nInteressados pela
	vaga devem ir pessoalmente&nbsp;das 10h. &aacute;s 12h. ou das 16h. &aacute;s 17h. Na
	Pra&ccedil;a Coronel Raphael de Moura Campos, 11 (Questa Tattoo)&nbsp;Falar com
	Pex&atilde;o\\r\\n\\r\\nPor favor fale que viu a vaga pela SoluTudo\\r\\n
</p><p><b>Última atualização:</b> 08/09/2014 - 15:24:45</p><p><b>Validade:</b> 24/12/2015</p>';

// mt_srand(918236);
// for ($i = 0; $i < 9; ++$i) {
// 	$tmp = '';
// 	$count = 200;
// 	for ($z = 0; $z < $count; ++$z) {
// 		$tmp .= chr(mt_rand(1, 255));
// 	}
// 	$samples[] = $tmp;
// }
// $samples = array_merge($samples, array($samples[8]));

// var_dump(memory_get_usage(true));

foreach ($samples as $val) {
	echo '<hr/>';
	$tokens = Lexer::Parse($val);
	$elements = Parser::Parse($tokens);
	var_dump($val);
	var_dump(Element::Html($elements));
}
// var_dump(memory_get_usage(true));
