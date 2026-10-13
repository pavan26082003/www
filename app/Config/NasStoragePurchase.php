<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * NAS storage purchase framework configuration.
 *
 * V1 intentionally has no external payment gateway.
 *
 * temporaryImmediateActivation:
 *   true  = create purchase record and immediately activate the quota.
 *   false = create pending purchase only. This is the mode to use later
 *           when a payment gateway is connected.
 */
class NasStoragePurchase extends BaseConfig
{
    public bool $temporaryImmediateActivation = true;

    /**
     * Payment status written while no gateway exists.
     */
    public string $temporaryPaymentStatus = 'not_integrated';

    /**
     * Purchase history shown on the plans page.
     */
    public int $historyLimit = 8;
}
