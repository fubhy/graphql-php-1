<?php

namespace Digia\GraphQL\Execution;

use League\Container\ServiceProvider\AbstractServiceProvider;

/**
 * Class ExecutionProvider
 * @package Digia\GraphQL\Provider
 */
class ExecutionProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [
        ExecutionContextBuilder::class,
        ExecutionInterface::class,
        ValuesHelper::class,
    ];

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->container->add(ExecutionContextBuilder::class, ExecutionContextBuilder::class, true/* $shared */);
        $this->container->add(ExecutionInterface::class, Execution::class, true/* $shared */)
            ->withArgument(ExecutionContextBuilder::class);
        $this->container->add(ValuesHelper::class, ValuesHelper::class, true/* $shared */);
    }
}
