<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Serializer;

use DateTime;
use Forumify\Core\Repository\UserRepository;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use Forumify\PerscomPlugin\Perscom\Perscom;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

use function Symfony\Component\String\u;

class UserSerializer implements DenormalizerInterface, NormalizerInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly FilesystemOperator $perscomAssetStorage,
    ) {
    }

    /**
     * @param PerscomUser $object
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        $data = [];

        $data['name'] = $object->getName();
        $data['created_at'] = $object->getCreatedAt()->format(Perscom::DATE_FORMAT);
        $data['email_verified_at'] = $object->getCreatedAt()->format(Perscom::DATE_FORMAT);
        $data['position'] = $object->getPosition()?->getPerscomId();
        $data['rank'] = $object->getRank()?->getPerscomId();
        $data['specialty'] = $object->getSpecialty()?->getPerscomId();
        $data['status'] = $object->getStatus()?->getPerscomId();
        $data['unit'] = $object->getUnit()?->getPerscomId();

        return $data;
    }

    public function supportsNormalization(mixed $data, ?string $format = null): bool
    {
        return $data instanceof PerscomUser && $format === 'perscom_array';
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

        if ($user->isSignatureDirty()) {
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
        $user->setSignatureDirty(false);
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

        if ($user->isUniformDirty()) {
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
        $user->setUniformDirty(false);
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null): bool
    {
        return is_array($data) && $type === PerscomUser::class;
    }
}
