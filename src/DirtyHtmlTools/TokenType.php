<?php

namespace DirtyHtmlTools;

class TokenType
{
	const TagStart = 'TagStart';
	const TagEnd = 'TagEnd';
	const TagClose = 'TagClose';
	const ShortTagClose = 'ShortTagClose';
	const AttributeName = 'AttributeName';
	const AttributeEqual = 'AttributeEqual';
	const AttributeValue = 'AttributeValue';
	const Content = 'Content';
	const Comment = 'Comment';
	const Incomplete = 'Incomplete';
	const InvalidInsideTag = 'InvalidInsideTag';
}
