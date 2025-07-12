<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Service;

use Forumify\Core\Entity\User;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use Forumify\PerscomPlugin\Perscom\Repository\PerscomUserRepository;
use Symfony\Bundle\SecurityBundle\Security;

class PerscomUserService
{
    public function __construct(
        private readonly PerscomUserRepository $perscomUserRepository,
        private readonly Security $security,
    ) {
    }

    public function getLoggedInPerscomUser(): ?PerscomUser
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return null;
        }

        return $this->getPerscomUser($user);
    }

    public function getPerscomUser(User $user): ?PerscomUser
    {
        return $this->perscomUserRepository->findOneBy(['user' => $user]);
    }

    public function createUser(string $firstName, string $lastName): PerscomUser
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $name = ucfirst($firstName) . ' ' . ucfirst($lastName);

        $perscomUser = new PerscomUser();
        $perscomUser->setUser($user);
        $perscomUser->setName($name);
        $this->perscomUserRepository->save($perscomUser);

        return $perscomUser;
    }

    public function sortUsers(&$users): void
    {
        usort($users, static function (array $a, array $b): int {
            $aRank = $a['rank']['order'] ?? 100;
            $bRank = $b['rank']['order'] ?? 100;
            if ($aRank !== $bRank) {
                return $aRank - $bRank;
            }

            $aPos = $a['position']['order'] ?? 100;
            $bPos = $b['position']['order'] ?? 100;
            if ($aPos !== $bPos) {
                return $aPos - $bPos;
            }

            $aSpec = $a['specialty']['order'] ?? 100;
            $bSpec = $b['specialty']['order'] ?? 100;
            if ($aSpec !== $bSpec) {
                return $aSpec - $bSpec;
            }

            return strcmp($a['name'], $b['name']);
        });
    }

    public function sortPerscomUsers(&$users): void
    {
        usort($users, static function (PerscomUser $a, PerscomUser $b): int {
            $aRank = $a->getRank()?->getPosition() ?? 1000;
            $bRank = $b->getRank()?->getPosition() ?? 1000;
            if ($aRank !== $bRank) {
                return $aRank - $bRank;
            }

            $aPos = $a->getPosition()?->getPosition() ?? 1000;
            $bPos = $b->getPosition()?->getPosition() ?? 1000;
            if ($aPos !== $bPos) {
                return $aPos - $bPos;
            }

            $aSpec = $a->getSpecialty()?->getPosition() ?? 1000;
            $bSpec = $b->getSpecialty()?->getPosition() ?? 1000;
            if ($aSpec !== $bSpec) {
                return $aSpec - $bSpec;
            }

            return strcmp($a->getName(), $b->getName());
        });
    }
}
