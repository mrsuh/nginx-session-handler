<?php

namespace Mrsuh\NginxSessionHandlerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('mrsuh_nginx_session_handler')
            ->children()
            ->variableNode('session_lifetime')
            ->defaultValue('3600')->end()
            ->variableNode('session_prefix')
            ->defaultvalue('phpsession')->end();

        return $treeBuilder;
    }
}
