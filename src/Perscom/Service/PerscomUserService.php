<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Service;

use DateInterval;
use Forumify\Core\Entity\User;
use Forumify\PerscomPlugin\Perscom\Perscom;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class PerscomUserService
{

    public function __construct(
        private readonly PerscomFactory $perscomFactory,
        private readonly Security $security,
        private readonly CacheInterface $cache,
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

    public function getPerscomUser(User $user): ?array
    {
        return $this->cache->get(
            'perscom.user.' . $user->getUserIdentifier(),
            $this->refreshUserCache($user)
        );
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

        $this->cache->delete('perscom.user.' . $user->getUserIdentifier());
        return $data;
    }

    private function refreshUserCache(User $user): callable
    {
        return function (ItemInterface $item) use ($user) {
            $perscomUser = $this->perscomFactory->getPerscom()->users()->search([
                'filters' => [
                    [
                        'field' => 'email',
                        'operator' => 'like',
                        'value' => $user->getEmail(),
                    ],
                ],
            ])->json('data')[0] ?? null;

            $expiresAfter = $perscomUser !== null ? 'PT1H' : 'PT15M';
            $item->expiresAfter(new DateInterval($expiresAfter));

            return $perscomUser;
        };
    }
}
