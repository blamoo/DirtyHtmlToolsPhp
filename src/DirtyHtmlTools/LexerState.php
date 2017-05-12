<?php 
namespace DirtyHtmlTools;

class LexerState
{
	const Identify = 'Identify';
	const Comment = 'Comment';
	const TagEnd = 'TagEnd';
	const TagStart = 'TagStart';
	const Content = 'Content';
	const Unknown = 'Unknown';
	const IdentifyInsideTag = 'IdentifyInsideTag';
}