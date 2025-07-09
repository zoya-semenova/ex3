<?php B_PROLOG_INCLUDED === true || die();


/**
 * НЕ РАЗМЕЩАЙТЕ ЭТОТ КОД В ДЕЙСТВУЩИХ ПРОЕКТАХ И ПУБЛИЧНЫХ ВЕБ-СЕРВЕРАХ!
 * Здесь специально подготовленные примеры с грубыми ошибками в безопасности,
 * которые будут продемонстрированы и исправлены в уроке.
 * Никогда не размещайте эти материалы в действующих проектах
 * и тем более на публичных веб-серверах,
 * используйте только для обучения в рамках вашего личного
 * учебного проекта на локальном веб-сервере.
 */


use Bitrix\Main\Localization\Loc;

/**
 * @var array $arParams
 * @var array $arResult
 */

$this->addExternalCss('/bitrix/js/local.ex31/css/notifications.css');

?>

<div class="container">
	<?php if ($arResult['NOTIFICATION']): ?>
		<div class="row mb-3">
			<div class="col text-end">
				<form method="post">
					<!-- ! Требуется защита формы от CSRF атаки ! -->
					<button name="important" value="<?=($arResult['NOTIFICATION']['IS_IMPORTANT'] ? 0 : 1)?>" class="mark-important btn btn-sm rounded-pill <?=$arResult['NOTIFICATION']['IS_IMPORTANT'] ? 'btn-primary' : 'btn-outline-primary'?>">
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bookmark-fill" viewBox="0 0 16 16">
							<path d="M2 2v13.5a.5.5 0 0 0 .74.439L8 13.069l5.26 2.87A.5.5 0 0 0 14 15.5V2a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2z"/>
						</svg>
						<?php if ($arResult['NOTIFICATION']['IS_IMPORTANT']): ?>
							<?=Loc::getMessage('NOTIFICATION_DETAIL_IMPORTANT')?>
						<?php else: ?>
							<?=Loc::getMessage('NOTIFICATION_DETAIL_MARK_IMPORTANT')?>
						<?php endif; ?>
					</button>
				</form>
			</div>
		</div>
		<div class="row">
			<div class="col">
				<p class="fs-5"><?=$arResult['DISPLAY_VALUES']['MESSAGE']?></p>
			</div>
		</div>
		<?php if ($arResult['DISPLAY_VALUES']['FILES']):?>
			<div class="row">
				<div class="col">
					<p class="fs-3"><?=Loc::getMessage('NOTIFICATION_DETAIL_FILES')?></p>
				</div>
			</div>
			<div class="row">
				<?php foreach ($arResult['DISPLAY_VALUES']['FILES'] as $file): ?>
					<div class="fs-5 mb-3 notification-files">
						<a href="<?=$file['SRC']?>" class="text-decoration-none" download>
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-arrow-down" viewBox="0 0 16 16">
								<path d="M8.5 6.5a.5.5 0 0 0-1 0v3.793L6.354 9.146a.5.5 0 1 0-.708.708l2 2a.5.5 0 0 0 .708 0l2-2a.5.5 0 0 0-.708-.708L8.5 10.293V6.5z"></path>
								<path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"></path>
							</svg>
							<?=$file['ORIGINAL_NAME']?>
						</a>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	<?php else: ?>
		<?php ShowError(Loc::getMessage('NOTIFICATION_DETAIL_NOT_FOUND')) ?>
	<?php endif; ?>
	<div class="row">
		<div class="col text-end">
			<a class="btn btn-outline-primary rounded-pill btn-sm" href="<?=$arResult['LIST_URL']?>">
				<?=Loc::getMessage('NOTIFICATION_DETAIL_BACK_TO_LIST')?>
			</a>
		</div>
	</div>
</div>
