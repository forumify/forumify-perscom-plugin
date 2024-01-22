<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\MenuBuilder;

use Forumify\Core\MenuBuilder\Menu;
use Forumify\Core\MenuBuilder\MenuItem;
use Forumify\Forum\MenuBuilder\ForumMenuBuilderInterface;
use Forumify\PerscomPlugin\Perscom\Service\PerscomEnlistService;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class MenuBuilder implements ForumMenuBuilderInterface
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly TranslatorInterface $translator,
        private readonly PerscomEnlistService $perscomEnlistService,
    ) {
    }

    public function build(Menu $menu): void
    {
        $u = $this->urlGenerator;
        $t = $this->translator;

        $perscomMenu = new Menu('PERSCOM', [], [
            new MenuItem($t->trans('perscom.roster.title'), $u->generate('perscom_roster')),
            new MenuItem($t->trans('perscom.award.title'), $u->generate('perscom_awards')),
            new MenuItem($t->trans('perscom.rank.title'), $u->generate('perscom_ranks')),
        ]);

        if ($this->perscomEnlistService->canEnlist()) {
            $perscomMenu->addItem(new MenuItem($t->trans('perscom.enlistment.enlist'), $u->generate('perscom_enlist')));
        }

        $menu->addItem($perscomMenu);
    }
}
