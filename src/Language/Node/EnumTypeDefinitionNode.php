<?php

namespace Digia\GraphQL\Language\Node;

class EnumTypeDefinitionNode extends AbstractNode implements TypeDefinitionNodeInterface, DirectivesAwareInterface
{
    use DescriptionTrait;
    use NameTrait;
    use DirectivesTrait;
    use EnumValuesTrait;

    /**
     * @var string
     */
    protected $kind = NodeKindEnum::ENUM_TYPE_DEFINITION;

    /**
     * @inheritdoc
     */
    public function toArray(): array
    {
        return [
            'kind'        => $this->kind,
            'description' => $this->getDescriptionAsArray(),
            'name'        => $this->getNameAsArray(),
            'directives'  => $this->getDirectivesAsArray(),
            'values'      => $this->getValuesAsArray(),
            'loc'         => $this->getLocationAsArray(),
        ];
    }
}
