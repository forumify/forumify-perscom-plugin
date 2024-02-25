<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\MenuBuilder;

use Forumify\Admin\MenuBuilder\AdminMenuBuilderInterface;
use Forumify\Core\MenuBuilder\Menu;
use Forumify\Core\MenuBuilder\MenuItem;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PerscomAdminMenuBuilder implements AdminMenuBuilderInterface
{
    public function __construct(private readonly UrlGeneratorInterface $urlGenerator)
    {
    }

    public function build(Menu $menu): void
    {
        $u = $this->urlGenerator->generate(...);

        $menu->addItem(new Menu('PERSCOM', ['icon' => 'ph ph-shield-chevron'], [
            new MenuItem('Users', $u('perscom_admin_user_list'), ['icon' => 'ph ph-users']),
            new MenuItem('Submissions', $u('perscom_admin_submission_list'), ['icon' => 'ph ph-table']),
            new Menu('Organization', ['icon' => 'ph ph-buildings'], [
                new MenuItem('Units', $u('perscom_admin_unit_list')),
                new MenuItem('Positions', $u('perscom_admin_position_list')),
                new MenuItem('Specialties', $u('perscom_admin_specialty_list')),
                new MenuItem('Statuses', $u('perscom_admin_status_list')),
                new MenuItem('Awards', $u('perscom_admin_award_list')),
                new MenuItem('Qualifications', $u('perscom_admin_qualification_list')),
            ])
        ]));
    }
}
