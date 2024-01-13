<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\MenuBuilder;

use Forumify\Core\MenuBuilder\Menu;
use Forumify\Core\MenuBuilder\MenuItem;
use Forumify\Forum\MenuBuilder\ForumMenuBuilderInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class MenuBuilder implements ForumMenuBuilderInterface
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly TranslatorInterface $translator,
    ) {

    }

    public function build(Menu $menu): void
    {
        $u = $this->urlGenerator;
        $t = $this->translator;

        $menu->addItem(new Menu('PERSCOM', [], [
            new MenuItem($t->trans('perscom.roster.title'), $u->generate('perscom_roster')),
            new MenuItem($t->trans('perscom.rank.title'), $u->generate('perscom_ranks')),
        ]));
    }
}
