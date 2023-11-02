<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\MenuBuilder;

use Forumify\Core\MenuBuilder\Menu;
use Forumify\Core\MenuBuilder\MenuItem;
use Forumify\Forum\MenuBuilder\ForumMenuBuilderInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MenuBuilder implements ForumMenuBuilderInterface
{
    public function __construct(private readonly UrlGeneratorInterface $urlGenerator)
    {
    }

    public function build(Menu $menu): void
    {
        $menu->addItem(new Menu('PERSCOM', [], [
            new MenuItem('Personnel Files', $this->urlGenerator->generate('perscom_roster')),
        ]));
    }
}
