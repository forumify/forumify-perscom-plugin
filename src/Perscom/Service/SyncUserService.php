<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Service;

use Exception;
use Forumify\Core\Entity\User;
use Forumify\Core\Repository\SettingRepository;
use Forumify\Core\Repository\UserRepository;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use Forumify\PerscomPlugin\Perscom\Message\SyncUserMessage;
use Forumify\PerscomPlugin\Perscom\Repository\PerscomUserRepository;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Twig\Environment;

class SyncUserService
{
    public function __construct(
        private readonly PerscomUserService $perscomUserService,
        private readonly PerscomUserRepository $perscomUserRepository,
        private readonly UserRepository $userRepository,
        private readonly Environment $twig,
        private readonly SettingRepository $settingRepository,
        private readonly MessageBusInterface $messageBus,
        private readonly FilesystemOperator $avatarStorage,
        private readonly FilesystemOperator $perscomAssetStorage,
        private readonly SluggerInterface $slugger,
        private readonly Packages $packages,
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
            return;
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

    /**
     * @return array{0: User|null, 1: PerscomUser|null}
     */
    private function getUsersFromPerscom(int $userId): array
    {
        /** @var PerscomUser|null $perscomUser */
        $perscomUser = $this->perscomUserRepository->findOneBy(['perscomId' => $userId]);
        return [$perscomUser?->getUser(), $perscomUser];
    }

    /**
     * @return array{0: User|null, 1: PerscomUser|null}
     */
    private function getUsersFromForumify(int $userId): array
    {
        $forumifyUser = $this->userRepository->find($userId);
        if ($forumifyUser === null) {
            return [null, null];
        }

        $perscomUser = $this->perscomUserService->getPerscomUser($forumifyUser);
        return [$forumifyUser, $perscomUser];
    }

    private function syncDisplayName(User $user, PerscomUser $perscomUser): void
    {
        $template = $this->settingRepository->get('perscom.profile.display_name_format');
        if ($template === null) {
            $template = '{{user.rank.abbreviation}} {{user.name}}';
        }

        try {
            $twigTemplate = $this->twig->createTemplate($template);
            $displayName = $twigTemplate->render(['user' => $perscomUser]);
        } catch (\Exception) {
            return;
        }

        $user->setDisplayName($displayName);
    }

    private function syncSignature(User $user, PerscomUser $perscomUser): void
    {
        $signature = $perscomUser->getSignature();
        if (!$signature) {
            return;
        }

        $imgUrl = $this->packages->getUrl($signature, 'perscom.asset');
        $user->setSignature(
            '<p class="ql-align-center"><img src="' . $imgUrl . '" style="max-width: 1000px; width: 100%; max-height: 200px; height: auto"/></p>',
        );
    }

    private function syncAvatar(User $user, PerscomUser $perscomUser): void
    {
        $rankImgUrl = $perscomUser->getRank()?->getImage();
        if ($rankImgUrl === null) {
            return;
        }

        $ext = pathinfo($rankImgUrl, PATHINFO_EXTENSION);
        $filename = $this->slugger
            ->slug($perscomUser->getRank()->getPerscomId() . '-' . $perscomUser->getRank()->getName())
            ->lower()
            ->append('.', $ext)
            ->toString();

        if ($filename === $user->getAvatar()) {
            return;
        }

        try {
            $rankImg = $this->perscomAssetStorage->read($rankImgUrl);
        } catch (Exception) {
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
