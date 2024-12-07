<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use Forumify\Plugin\Attribute\PluginVersion;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('Perscom\\CourseList\\Expanded', '@ForumifyPerscomPlugin/frontend/components/course_list_expanded.html.twig')]
#[PluginVersion('forumify/forumify-perscom-plugin', 'premium')]
class CourseListExpanded extends CourseList
{
}
