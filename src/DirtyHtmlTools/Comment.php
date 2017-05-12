<?php

namespace DirtyHtmlTools;

class Comment extends Element
{
	public $value;

	public function __construct()
	{
		$this->type = ElementType::Comment;
	}

	public function ToHtml()
	{
		return '<!--' . $this->value . '-->';
	}

	public function __toString()
	{
		return "//: {$this->value}";
	}
}
