<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\MenuBuilder;

use Forumify\Admin\MenuBuilder\AdminMenuBuilderInterface;
use Forumify\Core\MenuBuilder\Menu;
use Forumify\Core\MenuBuilder\MenuItem;
use Forumify\PerscomPlugin\Perscom\Repository\FormRepository;
use Forumify\Plugin\Service\PluginVersionChecker;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PerscomAdminMenuBuilder implements AdminMenuBuilderInterface
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly PluginVersionChecker $pluginVersionChecker,
        private readonly FormRepository $formRepository,
    ) {
    }

    public function build(Menu $menu): void
    {
        $u = $this->urlGenerator->generate(...);

        $perscomMenu = new Menu('PERSCOM', ['icon' => 'ph ph-shield-chevron', 'permission' => 'perscom-io.admin.view'], [
            new MenuItem('Configuration', $u('perscom_admin_configuration'), ['icon' => 'ph ph-wrench', 'permission' => 'perscom-io.admin.configuration.manage']),
            new MenuItem('Users', $u('perscom_admin_user_list'), ['icon' => 'ph ph-users', 'permission' => 'perscom-io.admin.users.view']),
        ]);

        if ($this->pluginVersionChecker->isVersionInstalled('forumify/forumify-perscom-plugin', 'premium')) {
            $perscomMenu
                ->addItem(new MenuItem('Operations', $u('perscom_admin_operations_list'), [
                    'icon' => 'ph ph-airplane-takeoff',
                    'permission' => 'perscom-io.admin.operations.view',
                ]))
                ->addItem(new MenuItem('Courses', $u('perscom_admin_courses_list'), [
                    'icon' => 'ph ph-graduation-cap',
                    'permission' => 'perscom-io.admin.courses.view',
                ]));
        }

        $submissionMenu = new Menu('Submissions', ['icon' => 'ph ph-table', 'permission' => 'perscom-io.admin.submissions.view']);
        $submissionMenu->addItem(new MenuItem('View All', $u('perscom_admin_submission_list')));
        foreach ($this->formRepository->findAll() as $form) {
            $submissionMenu->addItem(new MenuItem($form->getName(), $u('perscom_admin_submission_list', ['form' => $form->getId()])));
        }
        $perscomMenu->addItem($submissionMenu);

        $perscomMenu->addItem(new Menu('Records', ['icon' => 'ph ph-files', 'permission' => 'perscom-io.admin.records.view'], [
            new MenuItem('Service Records', $u('perscom_admin_service_records_list')),
            new MenuItem('Award Records', $u('perscom_admin_award_records_list')),
            new MenuItem('Combat Records', $u('perscom_admin_combat_records_list')),
            new MenuItem('Rank Records', $u('perscom_admin_rank_records_list')),
            new MenuItem('Assignment Records', $u('perscom_admin_assignment_records_list')),
            new MenuItem('Qualification Records', $u('perscom_admin_qualification_records_list')),
        ]));

        $perscomMenu->addItem(new Menu('Organization', ['icon' => 'ph ph-buildings', 'permission' => 'perscom-io.admin.organization.view'], [
            new MenuItem('Awards', $u('perscom_admin_award_list')),
            new MenuItem('Documents', $u('perscom_admin_document_list')),
            new MenuItem('Positions', $u('perscom_admin_position_list')),
            new MenuItem('Qualifications', $u('perscom_admin_qualification_list')),
            new MenuItem('Ranks', $u('perscom_admin_rank_list')),
            new MenuItem('Specialties', $u('perscom_admin_specialty_list')),
            new MenuItem('Statuses', $u('perscom_admin_status_list')),
            new MenuItem('Units', $u('perscom_admin_unit_list')),
        ]));

        $perscomMenu->addItem(new MenuItem('Sync', $u('perscom_admin_sync'), [
            'icon' => 'ph ph-link',
            'permission' => 'perscom-io.admin.run_sync',
        ]));

        $menu->addItem($perscomMenu);
    }
}
