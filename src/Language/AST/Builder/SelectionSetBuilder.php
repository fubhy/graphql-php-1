<?php

namespace Digia\GraphQL\Language\AST\Builder;

use Digia\GraphQL\Language\AST\Builder\Behavior\ParseLocationTrait;
use Digia\GraphQL\Language\AST\Node\Contract\NodeInterface;
use Digia\GraphQL\Language\AST\Node\Contract\SelectionNodeInterface;
use Digia\GraphQL\Language\AST\Node\SelectionSetNode;
use Digia\GraphQL\Language\AST\NodeKindEnum;

class SelectionSetBuilder extends AbstractBuilder
{

    use ParseLocationTrait;

    /**
     * @inheritdoc
     */
    public function build(array $ast): NodeInterface
    {
        return new SelectionSetNode([
            'selections' => $this->parseSelections($ast),
            'loc'        => $this->parseLocation($ast),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function supportsKind(string $kind): bool
    {
        return NodeKindEnum::SELECTION_SET === $kind;
    }

    /**
     * @param array $ast
     * @return array|SelectionNodeInterface[]
     */
    protected function parseSelections(array $ast): array
    {
        $selections = [];

        if (isset($ast['selections'])) {
            foreach ($ast['selections'] as $selectionAst) {
                $selections[] = $this->director->build($selectionAst);
            }
        }

        return $selections;
    }
}
