<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Service;

use Forumify\Core\Entity\User;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use Forumify\PerscomPlugin\Perscom\Perscom;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Forumify\PerscomPlugin\Perscom\Repository\PerscomUserRepository;
use Perscom\Data\FilterObject;
use Symfony\Bundle\SecurityBundle\Security;

class PerscomUserService
{
    public function __construct(
        private readonly PerscomUserRepository $perscomUserRepository,
        private readonly PerscomFactory $perscomFactory,
        private readonly Security $security,
    ) {
    }

    public function getLoggedInPerscomUser(): ?array
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return null;
        }

        return $this->getPerscomUser($user);
    }

    public function getPerscomUser(User $user, array $includes = []): ?array
    {
        /** @var PerscomUser|null $perscomUser */
        $perscomUser = $this->perscomUserRepository->findOneBy(['user' => $user]);
        if ($perscomUser !== null) {
            $perscomUserId = $perscomUser->getId();
            try {
                return $this->perscomFactory
                    ->getPerscom()
                    ->users()
                    ->get($perscomUserId, $includes)
                    ->json('data') ?? null;
            } catch (\Exception) {
                return null;
            }
        }

        try {
            $perscomUserData = $this->perscomFactory
                ->getPerscom()
                ->users()
                ->search(
                    filter: [new FilterObject('email', 'like', $user->getEmail())],
                    include: $includes,
                )
                ->json('data')[0] ?? null;
        } catch (\Exception) {
            return null;
        }

        if ($perscomUserData === null) {
            return null;
        }

        $perscomUser = new PerscomUser();
        $perscomUser->setPerscomId($perscomUserData['id']);
        $perscomUser->setUser($user);

        return $perscomUserData;
    }

    public function createUser(string $firstName, string $lastName)
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $data = $this->perscomFactory
            ->getPerscom()
            ->users()
            ->create([
                'name' => ucfirst($firstName) . ' ' . ucfirst($lastName),
                'email' => $user->getEmail(),
                'email_verified_at' => (new \DateTime())->format(Perscom::DATE_FORMAT),
            ])
            ->json('data');

        return $data;
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
