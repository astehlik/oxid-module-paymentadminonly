<?php

namespace De\Swebhosting\PaymentAdminOnly\Application\Model;

use OxidEsales\Eshop\Application\Model\Payment;
use OxidEsales\Eshop\Application\Model\User;

/**
 * Enhances oxPaymentList and filters out payment methods that are only allowed in the administration.
 */
class PaymentList extends PaymentList_parent
{
    /**
     * Loads and returns list of user payments and filters out the payments that are
     * allowed in the adminstration only.
     *
     * @param string $sShipSetId user chosen delivery set
     * @param double $dPrice basket product price excl. discount
     * @param User $oUser session user object
     *
     * @return array
     */
    public function getPaymentList($sShipSetId, $dPrice, $oUser = null)
    {
        $paymentMethods = parent::getPaymentList($sShipSetId, $dPrice, $oUser);

        if (!$paymentMethods) {
            return $paymentMethods;
        }

        foreach ($paymentMethods as $paymentID => $payment) {
            if (!$payment instanceof Payment) {
                continue;
            }

            if (intval($payment->oxpayments__swh_adminonly->getRawValue()) && (!isAdmin())) {
                unset($paymentMethods[$paymentID]);
            }
        }

        return $paymentMethods;
    }
}
