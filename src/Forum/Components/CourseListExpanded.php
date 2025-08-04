<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use Forumify\Plugin\Attribute\PluginVersion;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[PluginVersion('forumify/forumify-perscom-plugin', 'premium')]
#[AsLiveComponent('Perscom\\CourseList\\Expanded', '@ForumifyPerscomPlugin/frontend/components/course_list_expanded.html.twig')]
class CourseListExpanded extends CourseList
{
}
