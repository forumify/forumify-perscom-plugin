<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Service;

use Forumify\Core\Entity\User;
use Forumify\PerscomPlugin\Perscom\Perscom;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Perscom\Data\FilterObject;
use Symfony\Bundle\SecurityBundle\Security;

class PerscomUserService
{
    private ?array $perscomUser = null;

    public function __construct(
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

    public function getPerscomUser(User $user): ?array
    {
        if ($this->perscomUser !== null) {
            return $this->perscomUser;
        }

        try {
            $this->perscomUser = $this->perscomFactory
                ->getPerscom()
                ->users()
                ->search(filter: [new FilterObject('email', 'like', $user->getEmail())])
                ->json('data')[0] ?? null;

            return $this->perscomUser;
        } catch (\Exception) {
            return null;
        }
    }

    public function createUser(string $firstName, string $lastName)
    {
        /** @var User $user */
        $user = $this->security->getUser();
        return $this->perscomFactory
            ->getPerscom()
            ->users()
            ->create([
                'name' => ucfirst($firstName) . ' ' . ucfirst($lastName),
                'email' => $user->getEmail(),
                'email_verified_at' => (new \DateTime())->format(Perscom::DATE_FORMAT),
            ])
            ->json('data');
    }
}
