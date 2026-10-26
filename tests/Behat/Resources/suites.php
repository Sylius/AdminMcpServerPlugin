<?php

declare(strict_types=1);

use Behat\Config\Config;
use Behat\Config\Filter\TagFilter;
use Behat\Config\Profile;
use Behat\Config\Suite;

return (new Config())
    ->withProfile(
        (new Profile('default'))
            ->withSuite(
                (new Suite('greeting_customer'))
                    ->withContexts(
                        'sylius_admin_mcp_server.context.ui.shop.welcome',
                    )
                    ->withFilter(new TagFilter('greeting_customer'))
            )
    );
