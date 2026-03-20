<?php

namespace Exam31\Ticket1;

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
                menuItem.id = 'companyFactMenuItem2';
                //menuItem.innerText = BX.message('COMPANY_FACT');
               // menuItem.innerText =  'Админ';
                menuItem.onclick = function () {
                   // document.location.href = '/bitrix/admin';
                };
                menuItem.style.color = '#eaeff8';
                menuItem.style.cursor = 'pointer';
                menuItem.classList.add('menu-item-block');
                const link = document.createElement('a');
                link.href = '/bitrix/admin';
                //link.innerText = 'Админ';
                link.classList.add('menu-item-link');
                const span = document.createElement('span');
                span.innerText = 'Админ';
                span.classList.add('menu-item-link-text');
                link.appendChild(span);
                menuItem.appendChild(link);
                //menuItem.appendChild(link);
                extraBtnBox.parentNode.insertBefore(menuItem, extraBtnBox);
            });
            </script>
            HTML
        );
    }
}
