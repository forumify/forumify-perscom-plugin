<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin;

use Forumify\Plugin\AbstractForumifyPlugin;
use Forumify\Plugin\PluginMetadata;

class ForumifyPerscomPlugin extends AbstractForumifyPlugin
{
    public function getPluginMetadata(): PluginMetadata
    {
        return new PluginMetadata(
            'PERSCOM.io',
            'forumify',
            'Seamlessly integrate PERSCOM.io into your forumify instance.',
            'https://forumify.net',
            'perscom_admin_settings',
        );
    }
}
