<?php

namespace DynamicJobTabBundle;

use DynamicJobTabBundle\DependencyInjection\Compiler\RegisterTabPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * DynamicJobTab Bundle to view the tab in the job based on the parameter.
 */
class DynamicJobTabBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container
            ->addCompilerPass(new RegisterTabPass());
    }
}
