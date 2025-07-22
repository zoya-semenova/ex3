<?php

use Local\Ex31\Collection as ProjectCollection;
use Local\Ex31\History\Filter;
use Local\Ex31\History\ElementInfo as InfoItem;
use Local\Ex31\History\InfoService;
use Local\Ex31\Integration\Intranet\Employee\Collection as EmployeeCollection;
use Local\Ex31\Integration\Intranet\Employee\Service as EmployeeService;
use Local\Ex31\Integration\UI\FieldNameProvider;
use Local\Ex31\Integration\UI\Filter\InfoDataProvider;
use Local\Ex31\Integration\UI\Filter\InfoSettings;
use Local\Ex31\Integration\UI\Filter\ProjectDataProvider;
use Local\Ex31\Integration\UI\Filter\ProjectSettings;
use Local\Ex31\Integration\UI\PageNavigationFactory;
use Local\Ex31\Integration\UI\ValueFormatter;
use Local\Ex31\Service as ProjectService;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Context;
use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Filter\Field;
use Bitrix\Main\Filter\Filter as FilterService;
use Bitrix\Main\Grid\Options as GridService;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Localization\Loc;

defined('B_PROLOG_INCLUDED') || die;

final class ElementHistoryComponent extends CBitrixComponent
{
    private const GRID_ID = 'investment-project-history-grid';

    private readonly InfoService $infoService;
    private readonly ProjectService $projectService;
    private readonly EmployeeService $employeeService;
    private readonly ValueFormatter $valueFormatter;
    private readonly PageNavigationFactory $pageNavigationFactory;
    private readonly FieldNameProvider $fieldNameProvider;

    private ?int $elementId = null;

    /**
     * @throws LoaderException
     */
    public function __construct(
        ?CBitrixComponent $component = null,
        ?ProjectService $projectService = null,
        ?InfoService $infoService = null,
        ?EmployeeService $employeeService = null,
        ?ValueFormatter $valueFormatter = null,
        ?PageNavigationFactory $pageNavigationFactory = null,
        ?FieldNameProvider $fieldNameProvider = null
    ) {
        parent::__construct($component);

        Loader::requireModule('local.ex31');

        $this->infoService = $infoService ?? new InfoService();
        $this->projectService = $projectService ?? new ProjectService();
        $this->employeeService = $employeeService ?? new EmployeeService(Context::getCurrent()->getCulture());
        $this->valueFormatter = $valueFormatter ?? new ValueFormatter();
        $this->pageNavigationFactory = $pageNavigationFactory ?? new PageNavigationFactory();
        $this->fieldNameProvider = $fieldNameProvider ?? new FieldNameProvider();
    }

    public function onPrepareComponentParams($arParams): array
    {
        if (!isset($arParams['INVESTMENT_PROJECT_ID'])) {
            return $arParams;
        }

        $projectId = (int)$arParams['INVESTMENT_PROJECT_ID'];
        if ($projectId < 0) {
            return $arParams;
        }

        $this->elementId = $projectId;
        return $arParams;
    }

    public function executeComponent(): void
    {
        $gridService = new GridService(ElementHistoryComponent::GRID_ID);

        try {
            $infoDataProvider = new InfoDataProvider(
                new ProjectDataProvider(new ProjectSettings($gridService->getId(), $this->fieldNameProvider)),
                new \Local\Ex31\Integration\UI\Filter\InfoSettings($gridService->getId(), $this->fieldNameProvider)
            );
            $filterService = new FilterService(
                $gridService->getId(),
                $infoDataProvider
            );
        } catch (ArgumentException $e) {
            throw new RuntimeException($e->getMessage(), previous: $e);
        }

        $fields = $filterService->getValue();

        $filter = new Filter(
            $this->elementId ?? $fields['ELEMENT_ID'],
            $fields['TITLE'] ?? null,
        );

        $navigationParameters = $gridService->GetNavParams();
        $count = $this->infoService->count($filter);

        $navigation = $this->pageNavigationFactory->create($navigationParameters['nPageSize'], $count);
        $sort = $gridService->getSorting(['sort' => ['ID' => 'DESC']]);

        $fragment = $this->infoService->getFragment(
            $filter,
            $sort['sort'],
            $navigation->getPageSize(),
            $navigation->getCurrentPage()
        );

        $visibleColumns = $gridService->GetVisibleColumns();
        if (empty($visibleColumns)) {
            $visibleColumns = $filterService->getDefaultFieldIDs();
            $gridService->SetVisibleColumns($visibleColumns);
        }

        $filterFieldMask = [
            'TITLE',
        ];

        $this->arResult = [
            'grid' => [
                'GRID_ID' => $gridService->getId(),
                'COLUMNS' => array_map(
                    static fn(Field $field): array => [
                        'id' => $field->getId(),
                        'type' => $field->getType(),
                        'name' => $field->getName(),
                        'default' => $field->isDefault(),
                        'sort' => $infoDataProvider->getFieldSortingName($field),
                    ],
                    $filterService->getFields()
                ),
                'ROWS' => $fragment->map(fn(InfoItem $history): array => [
                    'id' => $history->id,
                    'data' => $this->prepareRowData(
                        $history,
                        $visibleColumns,
                    )
                ]),
                'NAV_OBJECT' => $navigation,
                'TOTAL_ROWS_COUNT' => $count,
                'SHOW_PAGESIZE' => true,
                'PAGE_SIZES' => $navigation->getPageSizes(),
                'SHOW_ROW_CHECKBOXES' => false,
                'SHOW_ROW_ACTIONS_MENU' => false,
                'SHOW_CHECK_ALL_CHECKBOXES' => false,
                'SHOW_SELECTED_COUNTER' => false,
                'AJAX_MODE' => 'Y',
                'AJAX_OPTION_HISTORY' => 'N',
                'AJAX_OPTION_JUMP' => 'N'
            ],
            'filter' => [
                'FILTER_ID' => $filterService->getId(),
                'GRID_ID' => $filterService->getId(),
                'FILTER' => $filterService->getFieldArrays($filterFieldMask),
                'ENABLE_LABEL' => true,
                'DISABLE_SEARCH' => true,
                'CONFIG' => [
                    // Автофокусировка включена по умолчанию.
                    // С активной автофокусировкой прерывается анимация открытия слайдера.
                    'AUTOFOCUS' => false,
                ],
            ]
        ];
        $this->includeComponentTemplate();
    }

    private function prepareRowData(
        InfoItem $item,
        array $visibleColumns,
    ): array {
        $placeholderValue = Loc::getMessage('INVESTMENT_PROJECT_HISTORY_NO_VALUE_PLACEHOLDER');

        $row = [];
        foreach ($visibleColumns as $column) {
            $row[$column] = match ($column) {
                'ID' => $item->id,
                'TITLE' => $item->title,
            };
        }

        return $row;
    }
}