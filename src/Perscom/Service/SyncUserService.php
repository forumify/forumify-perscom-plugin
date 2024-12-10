<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Service;

use Forumify\Core\Entity\User;
use Forumify\Core\Repository\SettingRepository;
use Forumify\Core\Repository\UserRepository;
use Forumify\PerscomPlugin\Perscom\Message\SyncUserMessage;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Perscom\Data\FilterObject;
use Symfony\Component\Messenger\MessageBusInterface;
use Twig\Environment;

class SyncUserService
{
    public function __construct(
        private readonly PerscomFactory $perscomFactory,
        private readonly UserRepository $userRepository,
        private readonly Environment $twig,
        private readonly SettingRepository $settingRepository,
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    public function syncFromPerscom(int $persomUserId, bool $async = true): void
    {
        $message = new SyncUserMessage($persomUserId);
        if ($async) {
            $this->messageBus->dispatch($message);
            return;
        }

        try {
            $this->doSync($message);
        } catch (\Exception) {
        }
    }

    public function syncFromForumify(int $userId, bool $async = true): void
    {
        $message = new SyncUserMessage(forumifyUserId: $userId);
        if ($async) {
            $this->messageBus->dispatch($message);
            return;
        }

        try {
            $this->doSync($message);
        } catch (\Exception) {
        }
    }

    public function doSync(SyncUserMessage $message): void
    {
        $displayNameEnabled = $this->settingRepository->get('perscom.profile.overwrite_display_names');
        $signatureEnabled = $this->settingRepository->get('perscom.profile.overwrite_signatures');
        if (!$displayNameEnabled && !$signatureEnabled) {
            return;
        }

        if ($message->forumifyUserId === null && $message->perscomUserId === null) {
            return;
        }

        if ($message->perscomUserId) {
            [$forumifyUser, $perscomData] = $this->getUsersFromPerscom($message->perscomUserId);
        } elseif ($message->forumifyUserId) {
            [$forumifyUser, $perscomData] = $this->getUsersFromForumify($message->forumifyUserId);
        }

        if ($forumifyUser === null || $perscomData === null) {
            throw new \RuntimeException('Unable to get forumify or perscom user.');
        }

        if ($displayNameEnabled) {
            $this->syncDisplayName($forumifyUser, $perscomData);
        }

        if ($signatureEnabled) {
            $signature = $perscomData['profile_photo_url'];
            if (!str_contains($signature, 'ui-avatars.com')) {
                $forumifyUser->setSignature(sprintf(
                    '<p class="ql-align-center"><img src="%s" style="max-width: 1000px; width: 100%%; max-height: 200px; height: auto"/></p>',
                    $signature,
                ));
            }
        }

        $this->userRepository->save($forumifyUser);
    }

    private function getUsersFromPerscom(int $userId): array
    {
        try {
            $perscomData = $this->perscomFactory
                ->getPerscom(true)
                ->users()
                ->get($userId, ['rank', 'position', 'specialty', 'status'])
                ->json('data')
            ;
        } catch (\Exception) {
            return [null, null];
        }

        $forumifyUser = $this->userRepository->findOneBy(['email' => $perscomData['email']]);
        if ($forumifyUser === null) {
            return [null, null];
        }

        return [$perscomData, $forumifyUser];
    }

    private function getUsersFromForumify(int $userId): array
    {
        $forumifyUser = $this->userRepository->find($userId);
        if ($forumifyUser === null) {
            return [null, null];
        }

        try {
            $perscomData = $this->perscomFactory
                ->getPerscom()
                ->users()
                ->search(
                    filter: [new FilterObject('email', 'like', $forumifyUser->getEmail())],
                    include: ['rank', 'position', 'specialty', 'status'],
                )
                ->json('data')[0] ?? null;
        } catch (\Exception) {
            return [null, null];
        }

        return [$forumifyUser, $perscomData];
    }

    private function syncDisplayName(User $user, array $userData): void
    {
        $template = $this->settingRepository->get('perscom.profile.display_name_format');
        try {
            $twigTemplate = $this->twig->createTemplate($template);
            $displayName = $twigTemplate->render(['user' => $userData]);
        } catch (\Exception) {
            return;
        }

        $user->setDisplayName($displayName);
    }
}
