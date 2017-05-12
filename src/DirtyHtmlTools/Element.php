<?php 
namespace DirtyHtmlTools;

abstract class Element {
	public abstract function ToHtml();
	public $type;
	
	public static function Html($elements)
	{
		$sb = '';
		
		foreach ($elements as $item)
		{
			$sb .= $item->ToHtml();
		}
		return $sb;
	}
}