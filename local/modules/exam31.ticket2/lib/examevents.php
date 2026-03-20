<?php
namespace Exam31\Ticket2;

use Bitrix\Main\Page\Asset;
use Bitrix\Main\UI\Extension;
use Bitrix\Main\UserField\Types\BaseType;
use Bitrix\Main\Localization\Loc;

class ExamEvents
{

	public static function injectAnchorRules()
	{
		Extension::load(['sidepanel']);
        $asset = Asset::getInstance();
        $asset->addString(
            $asset->insertJs(
<<<JS
            BX.ready(function () {
                BX.SidePanel.Instance.bindAnchors({
                rules: [
            {
	      condition: [RegExp('/exam312/detail/[0-9]+/')],
	      options: {
	        width: 580,
	        cacheable: false,
	        allowChangeHistory: false
	      }
	    },
	    {
	      condition: [RegExp('/exam312/info/[0-9]+/')],
	      options: {
	        width: 580,
	        cacheable: false,
	        allowChangeHistory: false
	      }
	    }
        ]});
            });

JS, inline: true
            )
        );

        return true;
	}

}