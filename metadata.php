<?php

/**
 * Metadata version
 */

use De\Swebhosting\PaymentAdminOnly\Core\Installer;
use OxidEsales\Eshop\Application\Model\PaymentList;

$sMetadataVersion = '2.0';

/**
 * Module information
 */
$aModule = [
    'id' => 'swh-paymentadminonly',
    'title' => 'Payments for admin only usage',
    'version' => '2.0.0.',
    'author' => 'Alexander Stehlik',
    'extend' => [
        PaymentList::class => \De\Swebhosting\PaymentAdminOnly\Application\Model\PaymentList::class,
    ],
    'controllers' => [],
    'templates' => [],
    'blocks' => [],
    'settings' => [],
    'events' => [
        'onActivate' => Installer::class . '::onActivate',
    ],
];
