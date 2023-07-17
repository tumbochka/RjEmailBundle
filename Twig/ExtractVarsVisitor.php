<?php

namespace Rj\EmailBundle\Twig;

use Twig\Environment;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\GetAttrExpression;
use Twig\Node\Expression\NameExpression;
use Twig\Node\Node;
use Twig\NodeVisitor\NodeVisitorInterface;

/**
 * @author Arnaud Le Blanc <arnaud.lb@gmail.com>
 */
class ExtractVarsVisitor implements NodeVisitorInterface
{
    private $stack;
    private $vars;
    private $currentVar;

    public function __construct()
    {
        $this->stack = [];
        $this->vars = [];
        $this->currentVar = &$this->vars;
    }

    public function enterNode(Node $node, Environment $env)
    {
        $this->stack[] = $node;
        return $node;
    }

    public function leaveNode(Node $node, Environment $env)
    {
        array_pop($this->stack);

        if ($node instanceof GetAttrExpression) {

            $nameNode = $node->getNode('node');
            if ($nameNode instanceof NameExpression) {
                $this->addVar($nameNode->getAttribute('name'));
            }

            $attributeNode = $node->getNode('attribute');
            if ($attributeNode instanceof ConstantExpression) {
                $this->addVar($attributeNode->getAttribute('value'));
            } else {
                $this->addVar('...');
            }

            if (!(end($this->stack) instanceof GetAttrExpression)) {
                $this->resetVars();
            }
        }

        return $node;
    }

    public function getPriority()
    {
        return 0;
    }

    public function getExtractedVars()
    {
        return $this->vars;
    }

    private function addVar($name)
    {
        if (!isset($this->currentVar[$name])) {
            $this->currentVar[$name] = [];
        }
        $this->currentVar = &$this->currentVar[$name];
    }

    private function resetVars()
    {
        $this->currentVar = &$this->vars;
    }
}

