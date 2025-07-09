<?php B_PROLOG_INCLUDED === true || die();

use Bitrix\Main\Localization\Loc;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 */

$this->addExternalCss('/bitrix/js/local.ex31/css/notifications.css');

?>

<form method="get">
	<div class="row notification-prefilters mb-3">
		<div class="col-3">
			<select name="prefilter" class="form-select form-select-sm">
				<?php foreach ($arResult['PREFILTERS'] as $value):?>
					<option value="<?=htmlspecialcharsbx($value)?>">
						<?=Loc::getMessage('NOTIFICATION_LIST_FILTER_' . strtoupper($value))?>
					</option>
				<?php endforeach;?>
			</select>
		</div>
		<div class="col">
			<button class="mark-important btn btn-sm btn-primary rounded-pill">
				<?=Loc::getMessage('NOTIFICATION_LIST_FILTER_APPLY')?>
			</button>
		</div>
	</div>
</form>

<table class="table table-hover table-bordered notifications-list">
	<thead class="table-primary">
		<tr>
			<th class="col-8" scope="col"><?=Loc::getMessage('NOTIFICATION_LIST_SUBJECT')?></th>
			<th class="col" scope="col"><?=Loc::getMessage('NOTIFICATION_LIST_DATE')?></th>
		</tr>
	</thead>
	<tbody>
		<?php if (count($arResult['DISPLAY_VALUES']) > 0):?>
			<?php foreach ($arResult['DISPLAY_VALUES'] as $item):?>
				<tr class="<?=$item['IS_READ'] ? '' : 'table-secondary'?>">
					<td><a href="<?=$item['DETAIL_PAGE_URL']?>"><?=$item['TITLE']?></a></td>
					<td><?=$item['DATE_CREATE']?></td>
				</tr>
			<?php endforeach;?>
		<?php else: ?>
			<tr>
				<td colspan="2" class="text-center"><?=Loc::getMessage('NOTIFICATION_LIST_NOT_FOUND')?></td>
			</tr>
		<?php endif; ?>
	</tbody>
</table>

<?php if (count($arResult['DISPLAY_VALUES']) > 0): ?>
	<div class="notifications-nav">
		<?php $APPLICATION->IncludeComponent(
			"bitrix:main.pagenavigation",
			"",
			[
				"NAV_OBJECT" => $arResult['NAV'],
				"SEF_MODE" => "Y",
			],
			false
		); ?>
	</div>
<?php endif;?>
