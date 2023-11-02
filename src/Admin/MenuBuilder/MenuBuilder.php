<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\MenuBuilder;

use Forumify\Admin\MenuBuilder\AdminMenuBuilderInterface;
use Forumify\Core\MenuBuilder\Menu;
use Forumify\Core\MenuBuilder\MenuItem;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MenuBuilder implements AdminMenuBuilderInterface
{
    public function __construct(private readonly UrlGeneratorInterface $urlGenerator) { }

    public function build(Menu $menu): void
    {
        $menu->addItem(new Menu('Perscom', [
            'icon' => 'ph ph-caret-double-up',
        ], [
            new MenuItem('Settings', $this->urlGenerator->generate('perscom_admin_settings')),
        ]));
    }
}
