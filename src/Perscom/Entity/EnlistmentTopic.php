<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity;

use Doctrine\ORM\Mapping as ORM;
use Forumify\Core\Entity\User;
use Forumify\Forum\Entity\Topic;

#[ORM\Entity]
#[ORM\Table('perscom_enlistment_topic')]
class EnlistmentTopic
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    private int $submissionId;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private User $user;

    #[ORM\OneToOne(targetEntity: Topic::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private Topic $topic;

    public function __construct(int $submissionId, Topic $topic)
    {
        $this->submissionId = $submissionId;
        $this->user = $topic->getCreatedBy();
        $this->topic = $topic;
    }

    public function getSubmissionId(): int
    {
        return $this->submissionId;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getTopic(): Topic
    {
        return $this->topic;
    }
}
