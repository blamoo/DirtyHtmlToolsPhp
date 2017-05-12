<?php

namespace DirtyHtmlTools;

class Token
{
	public $type;
	public $value;
	public $position;

	public function __construct($type, $value, $position)
	{
		$this->type = $type;
		$this->value = $value;
		$this->position = $position;
	}

	public function __toString()
	{
		return "{$this->type}: {$this->value}";
	}
}
