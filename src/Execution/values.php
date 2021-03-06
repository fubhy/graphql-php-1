<?php

namespace Digia\GraphQL\Execution;

use Digia\GraphQL\GraphQL;
use Digia\GraphQL\Language\Node\ArgumentsAwareInterface;
use Digia\GraphQL\Language\Node\VariableDefinitionNode;
use Digia\GraphQL\Type\Definition\Directive;
use Digia\GraphQL\Type\Definition\DirectiveInterface;
use Digia\GraphQL\Type\Definition\Field;
use Digia\GraphQL\Schema\SchemaInterface;

// TODO: Implement coerceVariableValues.

/**
 * @param SchemaInterface          $schema
 * @param VariableDefinitionNode[] $nodes
 * @param array                    $inputs
 * @return array
 */
//function coerceVariableValues(SchemaInterface $schema, array $nodes, array $inputs): array
//{
//    return GraphQL::make(ValuesHelper::class)->coerceVariableValues($schema, $nodes, $inputs);
//}

/**
 * @param Field|Directive         $definition
 * @param ArgumentsAwareInterface $node
 * @param array                   $variableValues
 * @return array
 */
function coerceArgumentValues($definition, ArgumentsAwareInterface $node, array $variableValues = []): array
{
    return GraphQL::make(ValuesHelper::class)->coerceArgumentValues($definition, $node, $variableValues);
}

/**
 * @param DirectiveInterface $directive
 * @param mixed              $node
 * @param array              $variableValues
 * @return array|null
 */
function coerceDirectiveValues(Directive $directive, $node, array $variableValues = []): ?array
{
    return GraphQL::make(ValuesHelper::class)->coerceDirectiveValues($directive, $node, $variableValues);
}
