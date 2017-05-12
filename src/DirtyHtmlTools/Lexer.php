<?php

namespace DirtyHtmlTools;
class Lexer
{
	public static function Parse($test)
	{
		$chars = $test;
		$tokens = array();

		$i = 0;
		$state = LexerState::Identify;

		while ($i < mb_strlen($chars)) {
			$lastValid = $i;
			try {
				switch ($state) {
					case LexerState::Identify:
						if (self::peek($chars, $i) == '<') {
							if (mb_strlen($chars) === $i + 1) { 
								$state = LexerState::Unknown;
								break;
							}
							
							if (mb_substr($chars, $i, 4) === '<!--') {
								$state = LexerState::Comment;
								break;
							}

							if (mb_substr($chars, $i, 2) === '</') {
								$state = LexerState::TagEnd;
								break;
							}

							if (preg_match('/[[:alpha:]]/ui', self::peek($chars, $i + 1))) {
								$state = LexerState::TagStart;
								break;
							}

							$state = LexerState::Unknown;
							break;
						}

						$state = LexerState::Content;
						/*
					}
					*/
					break;

					case LexerState::IdentifyInsideTag:
						self::SkipWhitespace($chars, $i);
						$start = $i;

						if (preg_match('/[[:alnum:]]/ui', self::peek($chars, $i))) {
							$tokens[] = new Token(TokenType::AttributeName, self::ReadName($chars, $i), $start);
							break;
						}

						if (self::peek($chars, $i) == '=') {
							++$i; // =
							$tokens[] = new Token(TokenType::AttributeEqual, '=', $start);
							break;
						}

						if (self::peek($chars, $i) == "'") {
							++$i; // '
							$tmp = html_entity_decode(self::ReadUntil($chars, $i, "'"));
							$tokens[] = new Token(TokenType::AttributeValue, $tmp, $start);
							++$i; // '
							break;
						}

						if (self::peek($chars, $i) == '"') {
							++$i; // "
							$tmp = html_entity_decode(self::ReadUntil($chars, $i, '"'));
							$tokens[] = new Token(TokenType::AttributeValue, $tmp, $start);
							++$i; // "
							break;
						}

						if (self::peek($chars, $i) == '/' && self::peek($chars, $i + 1) == '>') {
							$i += 2; // />
							$tokens[] = new Token(TokenType::ShortTagClose, '', $start);
							$state = LexerState::Identify;
							break;
						}

						if (self::peek($chars, $i) == '>') {
							++$i; // >
							$tokens[] = new Token(TokenType::TagClose, '', $start);
							$state = LexerState::Identify;
							break;
						}

						$tokens[] = new Token(TokenType::InvalidInsideTag, (string) self::peek($chars, $i), $start);
						++$i; // <unknown>
						break;
						/*
					}
					*/

					case LexerState::TagEnd:
					$start = $i;
					$i += 2; // </
					
					$name = self::ReadName($chars, $i);
					
					if (empty($name)) {
						throw new LexerException("Falta o nome da tag de fechamento");
					}
					
					self::SkipWhitespace($chars, $i);
						
					if (self::peek($chars, $i) === null) {
						throw new LexerException("Falta o fechamento da tag");
					}
					
					++$i; // >
					
					$tokens[] = new Token(TokenType::TagEnd, $name, $start);

					$state = LexerState::Identify;
					break;

					case LexerState::Comment:
					$start = $i;
					$i += 4; // <!--
					$end = mb_strpos($chars, '-->', $i);
					
					if ($end === false) {
						throw new LexerException("Falta o fechamento do comentário");
					}
					
					$tokens[] = new Token(TokenType::Comment, mb_substr($chars, $i, $end - $i), $start);
					
					$i = $end + 3; // -->

					$state = LexerState::Identify;
					break;

					case LexerState::TagStart:
					$start = $i;
					++$i; // <
					$tagName = self::ReadName($chars, $i);
					$tokens[] = new Token(TokenType::TagStart, $tagName, $start);

					$state = LexerState::IdentifyInsideTag;
					break;

					case LexerState::Content:
					$start = $i;
					$tokens[] = new Token(TokenType::Content, self::ReadContent($chars, $i), $start);
					$state = LexerState::Identify;
					break;

					case LexerState::Unknown:
					$start = $i;
					$tokens[] = new Token(TokenType::Incomplete, self::ReadToEnd($chars, $i), $start);
					$state = LexerState::Identify;
					break;
				}
			} catch (\Exception $ex) {
				$i = $lastValid;
				$tokens[] = new Token(TokenType::Incomplete, self::ReadToEnd($chars, $i), $i);
				break;
			}
		}
		
		return $tokens;
	}

	private static function peek($chars, $i)
	{
		$chr = mb_substr($chars, $i, 1);
		
		if ($chr !== '') {
			return $chr;
		}

		return;
		//throw new \Exception();
	}

	private static function ReadToEnd($chars, &$i)
	{
		$buffer = '';

		while ($i < mb_strlen($chars)) {
			$buffer .= mb_substr($chars, $i, 1);
			++$i;
		}

		return $buffer;
	}

	private static function ReadUntil($chars, &$i, $stop) #TODO trocar por strpos
	{
		$buffer = '';

		while (($chr = self::peek($chars, $i)) !== $stop) {
			if ($chr === null) {
				break;
			}

			$buffer .= $chr;
			++$i;
		}

		return $buffer;
	}

	private static function ReadContent($chars, &$i)
	{
		$buffer = '';

		while (true) {
			$chr = mb_substr($chars, $i, 1);
			if ($chr === '') {
				break;
			}

			if ($chr == '<') {
				break;
			} elseif ($chr == '&') {
				$entity;
				if (self::TryReadEntity($chars, $i, $entity)) {
					$buffer .= $entity;
				}
			} else {
				$buffer .= $chr;
			}
			++$i;
		}

		return $buffer;
	}

	private static function SkipWhitespace($chars, &$i)
	{
		$sub = mb_substr($chars, $i);
		
		if(!preg_match('/^[[:space:]]*/ui', $sub, $matches)) {
			return;
		}
		
		$i += mb_strlen($matches[0]);
	}

	private static function ReadName($chars, &$i)
	{
		$buffer = '';
		
		$sub = mb_substr($chars, $i);
		
		if (!preg_match('/^[[:alnum:]\:\_\.\-]*/uim', $sub, $matches)) {
			$i++;
			return $buffer;
		}
		
		$buffer = $matches[0];
		$i += mb_strlen($buffer);

		return $buffer;
	}

	private static function TryReadEntity($chars, &$i, &$ret)
	{
		$buffer = '';

		while (true) {
			$chr = mb_substr($chars, $i, 1);
			if ($chr === '') {
				break;
			}

			$buffer .= $chr;

			if ($chr == ';') {
				break;
			}
			++$i;
		}

		$ret = html_entity_decode($buffer);

		return true;
	}
}
