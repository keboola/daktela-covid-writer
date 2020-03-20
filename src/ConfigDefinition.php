<?php

declare(strict_types=1);

namespace Keboola\DaktelaCovid;

use Keboola\Component\Config\BaseConfigDefinition;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class ConfigDefinition extends BaseConfigDefinition
{
    protected function getParametersDefinition(): ArrayNodeDefinition
    {
        $parametersNode = parent::getParametersDefinition();
        // @formatter:off
        /** @noinspection NullPointerExceptionInspection */
        $parametersNode
            ->children()
                ->scalarNode('contact_table')
                    ->isRequired()
                ->end()
                ->scalarNode('already_sent_table')
                    ->isRequired()
                ->end()
                ->scalarNode('extend_info_table')
                    ->isRequired()
                ->end()
                ->scalarNode('daktela_gateway_url')
                    ->isRequired()
                ->end()
                ->scalarNode('#daktela_token')
                    ->isRequired()
                ->end()
            ->end()
        ;
        // @formatter:on
        return $parametersNode;
    }
}
