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
    private array $perscomUsers = [];

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
        if (isset($this->perscomUsers[$user->getId()])) {
            return $this->perscomUsers[$user->getId()];
        }

        // TODO: link perscom user id to forumify user id so we can get the user by id
        //       then it can use PERSCOM's cache, which should be much faster than a search request.
        try {
            $this->perscomUsers[$user->getId()] = $this->perscomFactory
                ->getPerscom()
                ->users()
                ->search(filter: [new FilterObject('email', 'like', $user->getEmail())])
                ->json('data')[0] ?? null;

            return $this->perscomUsers[$user->getId()];
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
