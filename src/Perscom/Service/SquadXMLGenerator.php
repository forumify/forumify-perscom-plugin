<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Service;

use Forumify\Core\Repository\SettingRepository;
use Forumify\PerscomPlugin\Perscom\Repository\PerscomUserRepository;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use SimpleXMLElement;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SquadXMLGenerator
{
    public function __construct(
        private readonly SettingRepository $settingRepository,
        private readonly PerscomUserRepository $perscomUserRepository,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function generateXml(): string
    {
        $nick = $this->settingRepository->get('perscom.squadxml.nick') ?? '';
        $name = $this->settingRepository->get('perscom.squadxml.name') ?? $this->settingRepository->get('forumify.title') ?? '';
        $title = $this->settingRepository->get('perscom.squadxml.title') ?? $name;
        $web = $this->settingRepository->get('perscom.squadxml.web') ?? $this->urlGenerator->generate('forumify_core_index', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $email = $this->settingRepository->get('perscom.squadxml.email');
        $picture = $this->settingRepository->get('perscom.squadxml.picture');

        $root = new SimpleXMLElement('<squad></squad>');
        $root->addAttribute('nick', $nick);
        $root->addChild('name', $name);
        $root->addChild('title', $title);
        $root->addChild('web', $web);
        if ($email) {
            $root->addChild('email', $email);
        }
        if ($picture) {
            $root->addChild('picture', 'logo.paa');
        }
        $this->addMembers($root);

        $header = <<<XML
<?xml version="1.0"?>
<!DOCTYPE squad SYSTEM "squad.dtd">
XML;

        $xml = $root->asXML();
        $xml = substr($xml, strpos($xml, "\n") + 1);

        return $header . "\n" . $xml;
    }

    private function addMembers(SimpleXMLElement $root): void
    {
        $users = $this->perscomUserRepository
            ->createQueryBuilder('pu')
            ->where('pu.steamId IS NOT NULL')
            ->andWhere('pu.user IS NOT NULL')
            ->getQuery()
            ->getResult()
        ;

        /** @var PerscomUser $user */
        foreach ($users as $user) {
            $member = $root->addChild('member');
            $member->addAttribute('id', (string)$user->getSteamId());
            $member->addAttribute('nick', $user->getUser()?->getDisplayName());
            $member->addChild('name', $user->getName());
            $member->addChild('email', 'N/A');
            $member->addChild('icq', 'N/A');
            if ($unit = $user->getUnit()) {
                $member->addChild('remark', $unit->getName());
            }
        }
    }
}
