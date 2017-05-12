<?php

namespace DirtyHtmlTools;

class Parser
{
	public static $autoClose = true;
	public static $fixClose = true;
	public static $fixIncomplete = true;
	public static $fixUnfinishedTag = true;
	public static $fixBadClose = true;

	public static function Parse($data)
	{
		$tokens = null;

		if (is_string($data)) {
			$lexer = new Lexer();
			$tokens = $lexer->Parse($data);
		}

		if (is_array($data)) {
			$tokens = $data;
		}

		if ($tokens === null) {
			throw new \InvalidArgumentException('O primeiro argumento deve ser um array de tokens ou uma string');
		}

		$root = new Tag();
		$root->name = 'ROOT';

		$current = $root;

		$stack = new \SplStack();
		$stack->Push($current);
		$state = ParserState::Content;

		$i = 0;

		while ($i < count($tokens)) {
			switch ($state) {
				case ParserState::Content:
				switch ($tokens[$i]->type) {
					case TokenType::Content:
					$tmp = new Content();
					$tmp->value = $tokens[$i]->value;
					$current->children[] = $tmp;
					++$i;
					break;

					case TokenType::TagStart:
					$current = new Tag();
					$current->name = $tokens[$i]->value;
					$stack->push($current);
					++$i;
					$state = ParserState::Tag;
					break;

					case TokenType::TagEnd:
					$top = $stack->top();

					if (!self::$fixBadClose) {
						if ($top === $root) {
							throw new ParseException("Tag de fechamento inesperada: '{$tokens[$i]->value}'");
						}
						else {
							$i++;
							break;
						}
					}

					if (!self::$fixClose) {
						if ($tokens[$i]->value !== $top->name) {
							throw new ParseException("Tag de fechamento incorreta: '{$tokens[$i]->value}' tentando fechar '{$top->name}'");
						}
					}

					$finished = $stack->pop();
					$current = $stack->top();
					$current->children[] = $finished;
					++$i;
					$state = ParserState::Content;
					break;

					case TokenType::Comment:
					$tmp = new Comment();
					$tmp->value = $tokens[$i]->value;
					$current->children[] = $tmp;
					$i++;
					$state = ParserState::Content;
					break;
					
					case TokenType::TagClose:
					case TokenType::ShortTagClose:
					case TokenType::AttributeEqual:
					case TokenType::AttributeName:
					case TokenType::AttributeValue:
					case TokenType::InvalidInsideTag:
					case TokenType::Incomplete:
					if (self::$fixIncomplete) {
						$c = count($current->children);
						$tmp = null;
						if ($c === 0) {
							$tmp = new Content();
							$current->children[] = $tmp;
						} elseif ($current->children[$c - 1]->type === TokenType::Content) {
							$tmp = $current->children[$c - 1];
						} else {
							$tmp = new Content();
							$current->children[] = $tmp;
						}
						$tmp->value .= htmlspecialchars($tokens[$i]->value);
						
						++$i;
					} else {
						throw new ParseException('Conteúdo incompleto');
					}
					break;
					
					default:
					throw new ParseException('Token inesperado');
				}

				break;

				case ParserState::Tag:
				$isShortTag = self::ReadInsideTag($tokens, $i, $current);

				if ($isShortTag) {
					$finished = $stack->pop();
					$current = $stack->top();
					$current->children[] = $finished;
				}
				$state = ParserState::Content;
				break;
			}
		}

		if (count($stack) !== 1) {
			if (!self::$autoClose) {
				throw new ParseException('Fim de arquivo inesperado');
			} else {
				do {
					$finished = $stack->pop();
					$current = $stack->top();
					$current->children[] = $finished;
				} while (count($stack) !== 1);
			}
		}

		return $current->children;
	}

	private static function ReadInsideTag(array $tokens, &$i, &$current)
	{
		while (true) {
			if (isset($tokens[$i]) && $tokens[$i]->type === TokenType::TagClose) {
				++$i;

				return false;
			}
			if (isset($tokens[$i]) && $tokens[$i]->type === TokenType::ShortTagClose) {
				++$i;

				return true;
			}

			if (isset($tokens[$i]) && $tokens[$i]->type === TokenType::AttributeName) {
				if (isset($tokens[$i + 1]) && $tokens[$i + 1]->type === TokenType::AttributeEqual) {
					if (isset($tokens[$i + 2]) && $tokens[$i + 2]->type === TokenType::AttributeValue) {
						$current->attributes[$tokens[$i]->value] = $tokens[$i + 2]->value;
						$i += 3;
					} else {
						if (self::$fixUnfinishedTag) {
							$current->attributes[$tokens[$i]->value] = null;
							$i += 2;

							return true;
						}

						throw new ParseException("Conteúdo inesperado após o '='");
					}
				} else {
					$current->attributes[$tokens[$i]->value] = null;
					++$i;
				}

				continue;
			}

			if (self::$fixUnfinishedTag) {
				++$i;

				return true;
			} else {
				throw new ParseException('Conteúdo inesperado dentro da tag');
			}
		}
	}
}
