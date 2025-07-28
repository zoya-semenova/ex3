<?php

namespace Local\Ex31;

use Bitrix\Main\Context;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\UI\Extension;

class LeftMenuExtender
{
    public static function handleOnEpilog(): void
    {
        if (Context::getCurrent()->getRequest()->isAdminSection()) {
            return;
        }
        Extension::load('ex31.menu');

        Asset::getInstance()->addString(
            <<<HTML
            <script>
            BX.ready(function () {                
                const extraBtnBox = document.querySelector('.menu-extra-btn-box');
                if (extraBtnBox === null) {
                    console.warn('Extra btn box is missing.');
                    return;
                }
                
                const menuItem = document.createElement('div');
                menuItem.id = 'companyFactMenuItem';
                menuItem.innerText = 'menu item';
                menuItem.onclick = BX.ex31.menu.showFacts;
                menuItem.style.color = '#eaeff8';
                menuItem.style.cursor = 'pointer';
                extraBtnBox.parentNode.insertBefore(menuItem, extraBtnBox);
            });
            </script>
            HTML
        );
    }
}
