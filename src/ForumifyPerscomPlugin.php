<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Forumify\Plugin\AbstractForumifyPlugin;
use Forumify\Plugin\PluginMetadata;
use Forumify\Calendar\Entity\Calendar;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/** @codeCoverageIgnore */
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

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        parent::loadExtension($config, $container, $builder);

        $this->loadDoctrineCalendarPluginIntegration($builder);
    }

    private function loadDoctrineCalendarPluginIntegration(ContainerBuilder $builder): void
    {
        if (!class_exists(Calendar::class)) {
            return;
        }

        $mappingDir = $this->getPath() . '/config/doctrine/calendar';
        $pass = DoctrineOrmMappingsPass::createXmlMappingDriver([
            $mappingDir => 'Forumify\PerscomPlugin\Perscom\Entity',
        ]);
        $pass->process($builder);
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
                    'discharge',
                ],
                'submissions' => [
                    'view',
                    'assign_statuses',
                    'delete',
                ],
                'organization' => [
                    'view',
                    'awards' => [
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
                    'forms' => [
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
                    'qualifications' => [
                        'view',
                        'manage',
                        'delete',
                        'create',
                    ],
                    'ranks' => [
                        'view',
                        'manage',
                        'delete',
                        'create',
                    ],
                    'rosters' => [
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
                    'statuses' => [
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
