<?php

namespace Local\Ex31\Integration\UI;

use Local\Ex31\History\ElementInfo;
use Local\Ex31\Integration\Intranet\Employee\Employee;
use Local\Ex31\Element;
use CComponentEngine;

final class ValueFormatter
{
    public function formatInfo(
        Element $project,
        string  $urlTemplate,
                $cnt,
        string  $projectIdPlaceholder = 'INVESTMENT_PROJECT_ID'): string
    {
        return sprintf(
            '<a href="%s">%s</a>',
            CComponentEngine::makePathFromTemplate($urlTemplate, [$projectIdPlaceholder => $project->id]),
            'Инфо: '.$cnt
        );
    }

    public function formatProject(
        Element $project,
        string  $urlTemplate,
        string  $projectIdPlaceholder = 'INVESTMENT_PROJECT_ID'
    ): string {
        return sprintf(
            '<a href="%s">%s</a>',
            CComponentEngine::makePathFromTemplate($urlTemplate, [$projectIdPlaceholder => $project->id]),
            $project->title
        );
    }
}