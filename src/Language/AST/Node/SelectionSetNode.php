<?php

namespace Digia\GraphQL\Language\AST\Node;

use Digia\GraphQL\Language\AST\NodeKindEnum;
use Digia\GraphQL\Language\AST\Node\Contract\NodeInterface;
use Digia\GraphQL\Language\AST\Node\Contract\SelectionNodeInterface;

class SelectionSetNode extends AbstractNode implements NodeInterface
{

    /**
     * @var string
     */
    protected $kind = NodeKindEnum::SELECTION_SET;

    /**
     * @var SelectionNodeInterface[]
     */
    protected $selections;

    /**
     * @return SelectionNodeInterface[]
     */
    public function getSelections(): array
    {
        return $this->selections;
    }
}