<?php

declare(strict_types=1);

namespace App\Database\Custom\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

class Round extends FunctionNode
{

    private $expression = null;

    /**
     * @inheritDoc
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        return 'ROUND(' . $this->expression->dispatch($sqlWalker) . ')';
    }

    /**
     * @inheritDoc
     */
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->expression = $parser->SimpleArithmeticExpression();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}