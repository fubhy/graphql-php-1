<?php

namespace Digia\GraphQL\Language\AST\Node;

use Digia\GraphQL\Behavior\ConfigTrait;
use Digia\GraphQL\Behavior\ValueTrait;
use Digia\GraphQL\Language\AST\KindEnum;

class IntDefinitionNode implements NodeInterface
{

    use KindTrait;
    use ValueTrait;
    use ConfigTrait;

    /**
     * @inheritdoc
     */
    protected function configure(): array
    {
        return [
            'kind' => KindEnum::INT,
        ];
    }
}
