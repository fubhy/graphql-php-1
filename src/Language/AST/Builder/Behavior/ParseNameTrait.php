<?php

namespace Digia\GraphQL\Language\AST\Builder\Behavior;

use Digia\GraphQL\Language\AST\Builder\Contract\DirectorInterface;
use Digia\GraphQL\Language\AST\Node\Contract\NodeInterface;

trait ParseNameTrait
{

    /**
     * @return DirectorInterface
     */
    abstract public function getDirector(): DirectorInterface;

    /**
     * @param array  $ast
     * @param string $fieldName
     * @return NodeInterface|null
     */
    protected function parseName(array $ast, string $fieldName = 'name'): ?NodeInterface
    {
        return isset($ast[$fieldName]) ? $this->getDirector()->build($ast[$fieldName]) : null;
    }
}