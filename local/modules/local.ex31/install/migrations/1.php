<?php

$db->queryExecute('
CREATE TABLE `ex_element`
(
    `ID`                        int          NOT NULL AUTO_INCREMENT,
    `TITLE`                     varchar(255) NOT NULL,
    `DATE_MODIFY`                datetime     NOT NULL,
    `UPDATED_BY`                int          NOT NULL,
    `TEXT`                   text,
    PRIMARY KEY (`ID`)
);');

\Bitrix\Main\Config\Option::set('local.ex31', 'VERSION', '1');

