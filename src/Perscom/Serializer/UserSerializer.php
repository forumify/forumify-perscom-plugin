<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Serializer;

use DateTime;
use Forumify\Core\Repository\UserRepository;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

use function Symfony\Component\String\u;

class UserSerializer implements DenormalizerInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly FilesystemOperator $perscomAssetStorage,
    ) {
    }

    public function getSupportedTypes(): array
    {
        return [
            PerscomUser::class => true,
            'perscom_array' => true,
        ];
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): PerscomUser
    {
        /** @var PerscomUser $user */
        $user = $context[AbstractNormalizer::OBJECT_TO_POPULATE] ?? new PerscomUser();

        $user->setPerscomId($data['id']);
        $user->setName($data['name'] ?? '');
        $user->setCreatedAt(new DateTime($data['created_at'] ?? 'now'));

        if (!empty($data['email'])) {
            $fUser = $this->userRepository->findOneBy(['email' => $data['email']]);
            $user->setUser($fUser);
        }

        $user->setPosition($context['positions'][$data['position_id'] ?? 0] ?? null);
        $user->setRank($context['ranks'][$data['rank_id'] ?? 0] ?? null);
        $user->setSpecialty($context['specialties'][$data['specialty_id'] ?? 0] ?? null);
        $user->setStatus($context['statuses'][$data['status_id'] ?? 0] ?? null);
        $user->setUnit($context['units'][$data['unit_id'] ?? 0] ?? null);

        $this->handleSignature($data, $user);
        $this->handleUniform($data, $user);

        return $user;
    }

    private function handleSignature(array $data, PerscomUser $user): void
    {
        if (empty($data['profile_photo']) || empty($data['profile_photo_url'])) {
            if (!empty($user->getSignature())) {
                $this->perscomAssetStorage->delete($user->getSignature());
                $user->setSignature(null);
                $user->setPerscomSignature(null);
            }
            return;
        }

        $imageId = $data['profile_photo'];
        if ($imageId === $user->getPerscomSignature()) {
            return;
        }

        $source = $data['profile_photo_url'];
        $imageData = file_get_contents($source);
        if ($imageData === false) {
            return;
        }

        $directory = 'user/signature';
        $path = u($source)->afterLast('/')->toString();
        $imagePath = $directory . '/' . $path;
        $this->perscomAssetStorage->write($imagePath, $imageData);

        $user->setPerscomSignature($imageId);
        $user->setSignature($imagePath);
    }

    private function handleUniform(array $data, PerscomUser $user): void
    {
        if (empty($data['cover_photo']) || empty($data['cover_photo_url'])) {
            if (!empty($user->getUniform())) {
                $this->perscomAssetStorage->delete($user->getUniform());
                $user->setUniform(null);
                $user->setPerscomUniform(null);
            }
            return;
        }

        $imageId = $data['cover_photo'];
        if ($imageId === $user->getPerscomUniform()) {
            return;
        }

        $source = $data['cover_photo_url'];
        $imageData = file_get_contents($source);
        if ($imageData === false) {
            return;
        }

        $directory = 'user/uniform';
        $path = u($source)->afterLast('/')->toString();
        $imagePath = $directory . '/' . $path;
        $this->perscomAssetStorage->write($imagePath, $imageData);

        $user->setPerscomUniform($imageId);
        $user->setUniform($imagePath);
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null): bool
    {
        return is_array($data) && $type === PerscomUser::class;
    }
}
