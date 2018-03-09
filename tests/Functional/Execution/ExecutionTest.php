<?php

namespace Digia\GraphQL\Test\Functional\Execution;

use Digia\GraphQL\Execution\ExecutionResult;
use Digia\GraphQL\Language\AST\Node\ArgumentNode;
use Digia\GraphQL\Language\AST\Node\DocumentNode;
use Digia\GraphQL\Language\AST\Node\FieldNode;
use Digia\GraphQL\Language\AST\Node\NamedTypeNode;
use Digia\GraphQL\Language\AST\Node\NameNode;
use Digia\GraphQL\Language\AST\Node\OperationDefinitionNode;
use Digia\GraphQL\Language\AST\Node\SelectionSetNode;
use Digia\GraphQL\Language\AST\Node\StringValueNode;
use Digia\GraphQL\Language\AST\NodeKindEnum;
use Digia\GraphQL\Language\Location;
use Digia\GraphQL\Language\Source;
use Digia\GraphQL\Language\SourceLocation;
use Digia\GraphQL\Test\TestCase;
use Digia\GraphQL\Type\Definition\ObjectType;
use Digia\GraphQL\Type\Schema;
use function Digia\GraphQL\graphql;
use function Digia\GraphQL\execute;
use function Digia\GraphQL\Type\GraphQLObjectType;
use function Digia\GraphQL\Type\GraphQLSchema;
use function Digia\GraphQL\Type\GraphQLInt;
use function Digia\GraphQL\Type\GraphQLList;
use function Digia\GraphQL\Type\GraphQLString;

class ExecutionTest extends TestCase
{
    /**
     * throws if no document is provided
     * @throws \Digia\GraphQL\Error\InvariantException
     */
    public function testNoDocumentIsProvided()
    {
        $this->expectException(\TypeError::class);

        $schema = GraphQLSchema([
            'query' =>
                new ObjectType([
                    'name'   => 'Type',
                    'fields' => [
                        'a' => [
                            'type' => GraphQLString()
                        ]
                    ]
                ])
        ]);

        graphql($schema, null);
    }

    /**
     * throws if no schema is provided
     *
     * @throws \Exception
     */
    public function testNoSchemaIsProvided()
    {
        $this->expectException(\TypeError::class);

        graphql(null, '{field}');
    }

    /**
     * Test accepts an object with named properties as arguments
     * @throws \Digia\GraphQL\Error\InvariantException
     */
    public function testAcceptAnObjectWithNamedPropertiesAsArguments()
    {
        $schema = new Schema([
            'query' =>
                new ObjectType([
                    'name'   => 'Greeting',
                    'fields' => [
                        'a' => [
                            'type'    => GraphQLString(),
                            'resolve' => function ($source, $args, $context, $info) {
                                return $source;
                            }
                        ]
                    ]
                ])
        ]);

        $rootValue = 'rootValue';
        $source  = 'query Example { a }';

        /** @var ExecutionResult $executionResult */
        $result = graphql($schema, $source, $rootValue);

        $expected = new ExecutionResult(['a' => $rootValue], []);

        $this->assertEquals($expected, $result);
    }

    /**
     * @throws \Digia\GraphQL\Error\InvariantException
     */
    public function testExecuteHelloQuery()
    {
        $schema = new Schema([
            'query' =>
                new ObjectType([
                    'name'   => 'Greeting',
                    'fields' => [
                        'hello' => [
                            'type'    => GraphQLString(),
                            'resolve' => function () {
                                return 'world';
                            }
                        ]
                    ]
                ])
        ]);

        $documentNode = new DocumentNode([
            'definitions' => [
                new OperationDefinitionNode([
                    'kind'                => NodeKindEnum::OPERATION_DEFINITION,
                    'name'                => new NameNode([
                        'value' => 'query',
                    ]),
                    'selectionSet'        => new SelectionSetNode([
                        'selections' => [
                            new FieldNode([
                                'name'      => new NameNode([
                                    'value'    => 'hello',
                                    'location' => new Location(
                                        15,
                                        20,
                                        new Source('query Example {hello}', 'GraphQL', new SourceLocation())
                                    )
                                ]),
                                'arguments' => []
                            ])
                        ]
                    ]),
                    'operation'           => 'query',
                    'directives'          => [],
                    'variableDefinitions' => []
                ])
            ],
        ]);

        /** @var ExecutionResult $executionResult */
        $executionResult = execute($schema, $documentNode);

        $expected = new ExecutionResult(['hello' => 'world'], []);

        $this->assertEquals($expected, $executionResult);
    }

    /**
     * @throws \Digia\GraphQL\Error\InvariantException
     */
    public function testExecuteQueryHelloWithArgs()
    {
        $schema = GraphQLSchema([
            'query' =>
                GraphQLObjectType([
                    'name'   => 'Greeting',
                    'fields' => [
                        'greeting' => [
                            'type'    => GraphQLString(),
                            'resolve' => function ($source, $args, $context, $info) {
                                return sprintf('Hello %s', $args['name']);
                            },
                            'args'    => [
                                'name' => [
                                    'type' => GraphQLString(),
                                ]
                            ]
                        ]
                    ]
                ])
        ]);

        $documentNode = new DocumentNode([
            'definitions' => [
                new OperationDefinitionNode([
                    'kind'                => NodeKindEnum::OPERATION_DEFINITION,
                    'name'                => new NameNode([
                        'value' => 'query'
                    ]),
                    'selectionSet'        => new SelectionSetNode([
                        'selections' => [
                            new FieldNode([
                                'name'      => new NameNode([
                                    'value'    => 'greeting',
                                    'location' => new Location(
                                        15,
                                        20,
                                        new Source('query Hello($name: String) {greeting(name: $name)}', 'GraphQL',
                                            new SourceLocation())
                                    )
                                ]),
                                'arguments' => [
                                    new ArgumentNode([
                                        'name'  => new NameNode([
                                            'value' => 'name',
                                        ]),
                                        'type'  => new NamedTypeNode([
                                            'name' => new NameNode([
                                                'value' => 'String',
                                            ]),
                                        ]),
                                        'value' => new StringValueNode([
                                            'value' => 'Han Solo'
                                        ])
                                    ])
                                ]
                            ])
                        ]
                    ]),
                    'operation'           => 'query',
                    'directives'          => [],
                    'variableDefinitions' => []
                ])
            ],
        ]);

        /** @var ExecutionResult $executionResult */
        $executionResult = execute($schema, $documentNode);

        $expected = new ExecutionResult(['greeting' => 'Hello Han Solo'], []);

        $this->assertEquals($expected, $executionResult);
    }

    /**
     * @throws \Digia\GraphQL\Error\InvariantException
     */
    public function testExecuteQueryWithMultipleFields()
    {
        $schema = new Schema([
            'query' =>
                new ObjectType([
                    'name'   => 'Human',
                    'fields' => [
                        'id'         => [
                            'type'    => GraphQLInt(),
                            'resolve' => function () {
                                return 1000;
                            }
                        ],
                        'type'       => [
                            'type'    => GraphQLString(),
                            'resolve' => function () {
                                return 'Human';
                            }
                        ],
                        'friends'    => [
                            'type'    => GraphQLList(GraphQLString()),
                            'resolve' => function () {
                                return ['1002', '1003', '2000', '2001'];
                            }
                        ],
                        'appearsIn'  => [
                            'type'    => GraphQLList(GraphQLInt()),
                            'resolve' => function () {
                                return [4, 5, 6];
                            }
                        ],
                        'homePlanet' => [
                            'type'    => GraphQLString(),
                            'resolve' => function () {
                                return 'Tatooine';
                            }
                        ],
                    ]
                ])
        ]);

        /** @var ExecutionResult $executionResult */
        $executionResult = graphql($schema, 'query Human {id, type, friends, appearsIn, homePlanet}');

        $expected = new ExecutionResult([
            'id'         => 1000,
            'type'       => 'Human',
            'friends'    => ['1002', '1003', '2000', '2001'],
            'appearsIn'  => [4, 5, 6],
            'homePlanet' => 'Tatooine'
        ], []);

        $this->assertEquals($expected, $executionResult);
    }

    /**
     * @throws \Digia\GraphQL\Error\InvariantException
     */
    public function testHandleFragments()
    {
        $source = <<<SRC
{ a, ...FragOne, ...FragTwo }
fragment FragOne on Type {
    b
    deep { b, deeper: deep { b } }
}
fragment FragTwo on Type {
    c
    deep { c, deeper: deep { c } }
}
SRC;

        $Type = new ObjectType([
            'name'   => 'Type',
            'fields' => function () use (&$Type) {
                return [
                    'a'    => [
                        'type'    => GraphQLString(),
                        'resolve' => function () {
                            return 'Apple';
                        }
                    ],
                    'b'    => [
                        'type'    => GraphQLString(),
                        'resolve' => function () {
                            return 'Banana';
                        }
                    ],
                    'c'    => [
                        'type'    => GraphQLString(),
                        'resolve' => function () {
                            return 'Cherry';
                        }
                    ],
                    'deep' => [
                        'type'    => $Type,
                        'resolve' => function () {
                            return [];
                        }
                    ]
                ];
            }
        ]);

        $schema = new Schema([
            'query' => $Type
        ]);

        /** @var ExecutionResult $executionResult */
        $executionResult = graphql($schema, $source);

        $expected = new ExecutionResult([
            'a'    => 'Apple',
            'b'    => 'Banana',
            'c'    => 'Cherry',
            'deep' => [
                'b'      => 'Banana',
                'c'      => 'Cherry',
                'deeper' => [
                    'b' => 'Banana',
                    'c' => 'Cherry'
                ]
            ]
        ], []);

        $this->assertEquals($expected, $executionResult);
    }
}
