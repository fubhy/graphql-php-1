<?php

namespace Digia\GraphQL\Test\Unit\Type\Definition;

use Digia\GraphQL\Language\AST\Node\FloatDefinitionNode;
use Digia\GraphQL\Language\AST\Node\IntDefinitionNode;
use Digia\GraphQL\Language\AST\Node\ObjectTypeDefinitionNode;
use Digia\GraphQL\Test\Unit\TestCase;
use Digia\GraphQL\Type\Definition\FloatType;
use Digia\GraphQL\Type\Definition\TypeEnum;

/**
 * Class FloatTypeTest
 *
 * @package Digia\GraphQL\Test\Unit\Type\Definition
 * @property FloatType $type
 */
class FloatTypeTest extends AbstractTypeTestCase
{

    public function setUp()
    {
        $this->type = new FloatType();
    }

    /**
     * @throws \Exception
     */
    public function testGetName()
    {
        $this->assertEquals(TypeEnum::FLOAT, $this->type->getName());
    }

    /**
     * @throws \Exception
     * @dataProvider validValuesProvider
     */
    public function testSerializeValidValues($value, $expected)
    {
        $this->assertEquals($expected, $this->type->serialize($value));
    }

    /**
     * @throws \Exception
     * @dataProvider invalidValuesProvider
     * @expectedException \TypeError
     */
    public function testSerializeInvalidValues($value)
    {
        $this->type->serialize($value);
    }

    /**
     * @throws \Exception
     * @dataProvider validValuesProvider
     */
    public function testParseValidValues($value, $expected)
    {
        $this->assertEquals($expected, $this->type->parseValue($value));
    }

    /**
     * @throws \Exception
     * @dataProvider invalidValuesProvider
     * @expectedException \TypeError
     */
    public function testParseInvalidValues($value)
    {
        $this->type->parseValue($value);
    }

    /**
     * @throws \Exception
     */
    public function testParseLiteralWithValidValues()
    {
        $this->assertEquals(4.2, $this->type->parseLiteral(new FloatDefinitionNode([
            'value' => 4.2,
        ])));
        $this->assertEquals(42, $this->type->parseLiteral(new IntDefinitionNode([
            'value' => 42,
        ])));
    }

    /**
     * @throws \Exception
     */
    public function testParseLiteralWithInvalidValues()
    {
        $this->assertNull($this->type->parseLiteral(new ObjectTypeDefinitionNode()));
    }

    /**
     * @return array
     */
    public function validValuesProvider(): array
    {
        return [
            'integer value' => [42, 42.0],
            'string value'  => ['42.3', 42.3],
        ];
    }

    /**
     * @return array
     */
    public function invalidValuesProvider(): array
    {
        return [
            'non-numeric string value' => ['foo'],
            'null value'               => [null],
            'empty string value'       => [''],
        ];
    }
}
