<?php

namespace Rj\EmailBundle\Twig;

use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\NodeTraverser;

/**
 * @author Arnaud Le Blanc <arnaud.lb@gmail.com>
 */
class ExtractVarsVisitorTest extends TestCase
{
    public function testExtractVars()
    {
        $loader = new ArrayLoader(array());
        $env = new Environment($loader);

        $code = 'foo bar {{ test.bar.baz }} {{ foo.bar }} {{ foo2[bar] }} {{ test.bar.qux }}';
        $node = $env->parse($env->tokenize($code));

        $traverser = new NodeTraverser($env);
        $visitor = new ExtractVarsVisitor;
        $traverser->addVisitor($visitor);

        $traverser->traverse($node);

        $this->assertSame(array(
            'test' => array(
                'bar' => array(
                    'baz' => array(),
                    'qux' => array(),
                ),
            ),
            'foo' => array(
                'bar' => array(),
            ),
            'foo2' => array(
                '...' => array(),
            ),
        ), $visitor->getExtractedVars());
    }
}
