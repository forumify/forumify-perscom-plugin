<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Service;

use Forumify\Core\Entity\User;
use Forumify\Core\Repository\SettingRepository;
use Forumify\Core\Repository\UserRepository;
use Forumify\PerscomPlugin\Perscom\Message\SyncUserMessage;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Perscom\Data\FilterObject;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Twig\Environment;

class SyncUserService
{
    public function __construct(
        private readonly PerscomFactory $perscomFactory,
        private readonly UserRepository $userRepository,
        private readonly Environment $twig,
        private readonly SettingRepository $settingRepository,
        private readonly MessageBusInterface $messageBus,
        private readonly FilesystemOperator $avatarStorage,
        private readonly SluggerInterface $slugger,
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
        $avatarEnabled = $this->settingRepository->get('perscom.profile.overwrite_avatars');

        if (!$displayNameEnabled && !$signatureEnabled && !$avatarEnabled) {
            return;
        }

        if ($message->perscomUserId) {
            [$forumifyUser, $perscomData] = $this->getUsersFromPerscom($message->perscomUserId);
        } elseif ($message->forumifyUserId) {
            [$forumifyUser, $perscomData] = $this->getUsersFromForumify($message->forumifyUserId);
        } else {
            return;
        }

        if ($forumifyUser === null || $perscomData === null) {
            throw new \RuntimeException('Unable to get forumify or perscom user.');
        }

        if ($displayNameEnabled) {
            $this->syncDisplayName($forumifyUser, $perscomData);
        }

        if ($signatureEnabled) {
            $this->syncSignature($forumifyUser, $perscomData);
        }

        if ($avatarEnabled) {
            $this->syncAvatar($forumifyUser, $perscomData);
        }

        $this->userRepository->save($forumifyUser);
    }

    private function getUsersFromPerscom(int $userId): array
    {
        try {
            $perscomData = $this->perscomFactory
                ->getPerscom(true)
                ->users()
                ->get($userId, ['rank', 'rank.image', 'position', 'specialty', 'status'])
                ->json('data')
            ;
        } catch (\Exception) {
            return [null, null];
        }

        $forumifyUser = $this->userRepository->findOneBy(['email' => $perscomData['email']]);
        if ($forumifyUser === null) {
            return [null, null];
        }

        return [$forumifyUser, $perscomData];
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
                    include: ['rank', 'rank.image', 'position', 'specialty', 'status'],
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
        if ($template === null) {
            $template = '{{user.rank.abbreviation}} {{user.name}}';
        }

        try {
            $twigTemplate = $this->twig->createTemplate($template);
            $displayName = $twigTemplate->render(['user' => $userData]);
        } catch (\Exception) {
            return;
        }

        $user->setDisplayName($displayName);
    }

    private function syncSignature(User $user, array $userData): void
    {
        $signature = $userData['profile_photo_url'];
        if (!str_contains($signature, 'ui-avatars.com')) {
            $user->setSignature(sprintf(
                '<p class="ql-align-center"><img src="%s" style="max-width: 1000px; width: 100%%; max-height: 200px; height: auto"/></p>',
                $signature,
            ));
        }
    }

    private function syncAvatar(User $user, array $userData): void
    {
        $rankImgUrl = $userData['rank']['image']['image_url'] ?? null;
        if ($rankImgUrl === null) {
            return;
        }

        $ext = pathinfo($rankImgUrl, PATHINFO_EXTENSION);
        $filename = $this->slugger
            ->slug($userData['rank']['id'] . '-' . $userData['rank']['name'])
            ->lower()
            ->append('.', $ext)
            ->toString();

        if ($filename === $user->getAvatar()) {
            return;
        }

        $rankImg = file_get_contents($rankImgUrl);
        if ($rankImg === false) {
            return;
        }

        try {
            $this->avatarStorage->write($filename, $rankImg);
        } catch (FilesystemException) {
            return;
        }

        $user->setAvatar($filename);
    }
}
