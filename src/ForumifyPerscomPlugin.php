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
                    'view',
                    'ranks' => [
                        'view',
                        'manage',
                        'delete',
                        'create',
                    ],
                    'qualifications' => [
                        'view',
                        'manage',
                        'delete',
                        'create',
                    ],
                    'statuses' => [
                        'view',
                        'manage',
                        'delete',
                        'create',
                    ],
                    'awards' => [
                        'view',
                        'manage',
                        'delete',
                        'create',
                    ],
                    'specialties' => [
                        'view',
                        'manage',
                        'delete',
                        'create',
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
