<?php

namespace Webkul\Wallet\Models;

use Bavix\Wallet\Interfaces\Wallet;
use Bavix\Wallet\Interfaces\WalletFloat;
use Bavix\Wallet\Traits\HasWalletFloat;
use Webkul\Customer\Models\Customer as BaseCustomer;

class Customer extends BaseCustomer implements Wallet, WalletFloat
{
    use HasWalletFloat;
}
