<?php

namespace DirtyHtmlTools;

class Sanitize
{
	public static function filterTags($data, array $rules) {
		if (is_string($data)) {
			$data = Parser::Parse($data);
		}
		
		$rulesFixed = array();
		
		foreach ($rules as $key => $val) {
			if (is_int($key)) {
				$rulesFixed[$val] = array();
			} else {
				$rulesFixed[$key] = $val;
			}
		}
			
		$root = new Tag();
		$root->children = $data;
		$root->isRoot = true;
		
		self::filterTag($root, $rulesFixed);
			
		return Element::Html($data);
	}
	
	private static function filterTag(Tag $tag, array $rules) {
		if (!$tag->isRoot) {
			if (!isset($rules[$tag->name])) {
				$tag->filtered = true;
			}
		}
		
		$tag->attributes = array_filter($tag->attributes, function($key, $val) use ($rules) {
			return isset($rules[$key]);
		}, ARRAY_FILTER_USE_BOTH);
		
		foreach ($tag->children as $val) {
			if ($val instanceof Tag) {
				self::filterTag($val, $rules);
			}
		}
	}
}
