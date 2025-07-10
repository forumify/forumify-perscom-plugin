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
            'admin' => [
                'view',
                'run_sync',
                'configuration' => [
                    'manage',
                ],
                'users' => [
                    'view',
                    'create',
                    'manage',
                    'delete',
                ],
                'submissions' => [
                    'view',
                    'assign_statuses',
                    'delete',
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
                    ],
                    'positions' => [
                        'view',
                        'manage',
                        'delete',
                        'create',
                    ],
                    'units' => [
                        'view',
                        'manage',
                        'delete',
                        'create',
                    ],
                    'documents' => [
                        'view',
                        'manage',
                        'delete',
                        'create',
                    ],
                ],
                'records' => [
                    'view',
                    'assignment_records' => [
                        'view',
                        'create',
                        'delete',
                    ],
                    'award_records' => [
                        'view',
                        'create',
                        'delete',
                    ],
                    'combat_records' => [
                        'view',
                        'create',
                        'delete',
                    ],
                    'qualification_records' => [
                        'view',
                        'create',
                        'delete',
                    ],
                    'rank_records' => [
                        'view',
                        'create',
                        'delete',
                    ],
                    'service_records' => [
                        'view',
                        'create',
                        'delete',
                    ],
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
            'frontend' => [
                'attendance_sheet' => [
                    'view',
                ],
            ],
        ];
    }
}
