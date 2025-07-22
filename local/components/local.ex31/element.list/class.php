<?php


use Local\Ex31\Filter;
use Local\Ex31\History\InfoService;
use Local\Ex31\Integration\Intranet\Employee\Collection as EmployeeCollection;
use Local\Ex31\Integration\Intranet\Employee\Service as EmployeeService;
use Local\Ex31\Integration\UI\FieldNameProvider;
use Local\Ex31\Integration\UI\Filter\ProjectDataProvider;
use Local\Ex31\Integration\UI\Filter\ProjectSettings;
use Local\Ex31\Integration\UI\PageNavigationFactory;
use Local\Ex31\Integration\UI\ValueFormatter;
use Local\Ex31\Element;
use Local\Ex31\Service as ProjectService;
use Local\Ex31\ServiceException;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Context;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Engine\Response\AjaxJson;
use Bitrix\Main\Error as BitrixError;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Filter\Field;
use Bitrix\Main\Filter\Filter as FilterService;
use Bitrix\Main\Grid\Options as GridService;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\UI\Buttons\CreateButton;

defined('B_PROLOG_INCLUDED') || die;

final class ElementListComponent extends CBitrixComponent implements Controllerable
{
    private const GRID_ID = 'investment-project-grid';

    private readonly ProjectService $projectService;

    private readonly InfoService $infoService;
    private readonly ValueFormatter $valueFormatter;
    private readonly PageNavigationFactory $pageNavigationFactory;

    public function __construct(
        ?CBitrixComponent $component = null,
        ?ProjectService $projectService = null,
        ?InfoService $infoService = null,
        ?ValueFormatter $valueFormatter = null,
        ?PageNavigationFactory $pageNavigationFactory = null
    ) {
        parent::__construct($component);

        Loader::requireModule('local.ex31');

        $this->projectService = $projectService ?? new ProjectService();
        $this->infoService = $infoService ?? new \Local\Ex31\History\InfoService();
        $this->valueFormatter = $valueFormatter ?? new ValueFormatter();
        $this->pageNavigationFactory = $pageNavigationFactory ?? new PageNavigationFactory();
    }

    public function executeComponent(): void
    {
        $projectDataProvider = $this->getDataProvider();

        $gridService = new GridService($projectDataProvider->getID());
        $filterService = new FilterService($projectDataProvider->getID(), $projectDataProvider);

        $fields = $filterService->getValue();

        $filter = new Filter(
            $fields['TITLE'] ?? null,
            $fields['MODIFY_DATE'] ?? null,
            $fields['ACTIVE'] ?? null
        );
        $sort = $gridService->getSorting(['sort' => ['ID' => 'DESC']]);

        $navigationParameters = $gridService->GetNavParams();
        $navigation = $this->pageNavigationFactory->create(
            $navigationParameters['nPageSize'],
            $this->projectService->count($filter)
        );

        $fragment = $this->projectService->getFragment(
            $filter,
            $sort['sort'],
            $navigation->getPageSize(),
            $navigation->getCurrentPage()
        );

        $visibleColumns = $gridService->GetVisibleColumns();
        if (empty($visibleColumns)) {//echo "fff";
            $visibleColumns = $filterService->getDefaultFieldIDs();
            $gridService->SetVisibleColumns($visibleColumns);
        }


/*
        $infoIds = $userIds = [];
        foreach ($fragment as $item) {//print_r( $item->info);
            $infoIds[] = $item->info;
        }
print_r($infoIds);
        $employees = $this->infoService->getByIds(...array_unique($infoIds));
*/

        $this->arResult = [
            'grid' => [
                'GRID_ID' => $gridService->getId(),
                'COLUMNS' => array_map(
                    static fn(Field $field): array => [
                        'id' => $field->getId(),
                        'type' => $field->getType(),
                        'name' => $field->getName(),
                        'default' => $field->isDefault(),
                        'sort' => $projectDataProvider->getFieldSortingName($field),
                    ],
                    $filterService->getFields()
                ),
                'ROWS' => $fragment->map(fn(Element $item): array => [
                    'id' => $item->id,
                    'actions' => $this->prepareRowActions($item),
                    'data' => $this->prepareRowData($item, $visibleColumns)
                ]),
                'NAV_OBJECT' => $navigation,
                'AJAX_MODE' => 'Y',
                'AJAX_OPTION_HISTORY' => 'N',
                'TOTAL_ROWS_COUNT' => $navigation->getRecordCount(),
                'SHOW_PAGESIZE' => true,
                'PAGE_SIZES' => $navigation->getPageSizes()
            ],
            'gridManager' => [
                'gridId' => $gridService->getId(),
                'componentName' => $this->getName(),
                'deleteProjectAction' => 'deleteProject'
            ],
            'filter' => [
                'FILTER' => $filterService->getFieldArrays([
                    'TITLE',
                    'MODIFY_DATE',
                    'ACTIVE',
                ]),
                'FILTER_ID' => $filterService->getID(),
                'GRID_ID' => $filterService->getID(),
                'ENABLE_LABEL' => true,
                'DISABLE_SEARCH' => true
            ],
            'toolbar' => [
                'buttons' => [
                    new CreateButton([
                        'link' => CComponentEngine::makePathFromTemplate(
                            $this->arParams['DETAIL_PAGE_URL'],
                            ['INVESTMENT_PROJECT_ID' => 0]
                        )
                    ])
                ]
            ],
        ];
        $this->includeComponentTemplate();
    }

    private function getDataProvider(): ProjectDataProvider
    {
        try {
            return new ProjectDataProvider(
                new ProjectSettings(ElementListComponent::GRID_ID, new FieldNameProvider())
            );
        } catch (ArgumentException $e) {
            // Never happens.
            throw new RuntimeException($e->getMessage(), previous: $e);
        }
    }

    private function prepareRowActions(Element $project): array
    {
        return [
            [
                'text' => Loc::getMessage('INVESTMENT_PROJECT_LIST_VIEW_BUTTON_LABEL'),
                'default' => false,
                'href' => CComponentEngine::makePathFromTemplate(
                    $this->arParams['DETAIL_PAGE_URL'],
                    ['INVESTMENT_PROJECT_ID' => $project->id]
                )
            ],
            [
                'text' => Loc::getMessage('INVESTMENT_PROJECT_LIST_HISTORY_BUTTON_LABEL'),
                'default' => false,
                'href' => CComponentEngine::makePathFromTemplate(
                    $this->arParams['HISTORY_PAGE_URL'],
                    ['INVESTMENT_PROJECT_ID' => $project->id]
                )
            ],
            [
                'text' => Loc::getMessage('INVESTMENT_PROJECT_LIST_DELETE_BUTTON_LABEL'),
                'default' => false,
                'onclick' => "BX.Academy.Element.Grid.Manager.getInstance().deleteProject({$project->id})"
            ]
        ];
    }

    private function prepareRowData(Element $project, array $visibleColumns//, \Local\Ex31\History\InfoCollection $info
    ): array
    {
        $row = [];//echo "<pre>";print_r($visibleColumns);echo "</pre>";exit;
        foreach ($visibleColumns as $column) {
            $row[$column] = match ($column) {
                'ID' => $project->id,
                'TITLE' => $this->valueFormatter->formatProject($project, $this->arParams['DETAIL_PAGE_URL']),
                'TEXT' => $project->text,
                'ACTIVE' => $project->active,
                'MODIFY_DATE' => $project->modifyDate,
                'INFO' => $this->valueFormatter->formatInfo($project, $this->arParams['HISTORY_PAGE_URL'],$project->infoCnt),
            };
        }

        return $row;
    }

    public function deleteProjectAction(int $projectId): AjaxJson
    {
        $errorCollection = new ErrorCollection();
        $result = [];

        try {
            $project = $this->projectService->getById($projectId);
            $this->projectService->delete($project);

            $result['title'] = $project->title;
        } catch (ServiceException $e) {
            $errorCollection->add([
                new BitrixError('Не удалось удалить проект.'),
                BitrixError::createFromThrowable($e)
            ]);
        }

        return new AjaxJson(
            $result,
            $errorCollection->isEmpty() ? AjaxJson::STATUS_SUCCESS : AjaxJson::STATUS_ERROR,
            $errorCollection
        );
    }

    public function configureActions(): array
    {
        return [];
    }
}