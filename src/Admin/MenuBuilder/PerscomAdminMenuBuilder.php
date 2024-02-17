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
            new MenuItem('Form Submissions', $u('perscom_admin_submission_list'), ['icon' => 'ph ph-table'])
        ]));
    }
}
