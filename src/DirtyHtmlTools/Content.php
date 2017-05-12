<?php

namespace DirtyHtmlTools;

class Content extends Element
{
	public $value;

	public function __construct()
	{
		$this->type = ElementType::Content;
	}

	public function ToHtml()
	{
		return html_entity_decode($this->value);
	}

	public function __toString()
	{
		return "C: {$this->value}";
	}
}
