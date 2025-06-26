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

    public function getPermissions(): array
    {
        return [
            'frontend' => [
                'attendance_sheet' => [
                    'view',
                ]
            ],
            'admin' => [
                'view',
                'configuration' => [
                    'manage',
                ],
                'users' => [
                    'view',
                    'manage',
                    'delete',
                    'assign_records',
                ],
                'submissions' => [
                    'view',
                    'assign_statuses',
                ],
                'organization' => [
                    'view', [
                        'ranks' => [
                            'view',
                            'manage',
                            'delete',
                            'create'
                        ],
<<<<<<< 2.0-crud-statues -- Incoming Change
                        'statuses' => [
=======
                        'awards' => [
>>>>>>> 2.0 -- Current Change
                            'view',
                            'manage',
                            'delete',
                            'create'
                        ]
                    ]
                ],
                'operations' => [
                    'view',
                    'manage',
                    'delete',
                ],
                'courses' => [
                    'view',
                    'manage',
                    'delete',
                ],
            ],
        ];
    }
}
