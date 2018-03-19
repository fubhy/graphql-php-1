<?php

namespace Digia\GraphQL\Type\Definition;

use Digia\GraphQL\Config\ConfigObject;
use Digia\GraphQL\Language\Node\NodeAwareInterface;
use Digia\GraphQL\Language\Node\NodeTrait;

class Argument extends ConfigObject implements NodeAwareInterface
{
    use NameTrait;
    use DescriptionTrait;
    use TypeTrait;
    use DefaultValueTrait;
    use NodeTrait;
}
