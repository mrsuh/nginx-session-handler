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
        $treeBuilder = new TreeBuilder('mrsuh_nginx_session_handler');

        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            $rootNode = $treeBuilder->root('mrsuh_nginx_session_handler');
        }

        $rootNode
            ->children()
                ->variableNode('session_lifetime')->defaultValue('3600')->end()
                ->variableNode('session_prefix')->defaultvalue('phpsession')->end();

        return $treeBuilder;
    }
}
