<?php

namespace Webkul\Wallet\Models;

use Bavix\Wallet\Interfaces\Wallet;
use Bavix\Wallet\Interfaces\WalletFloat;
use Bavix\Wallet\Traits\HasWalletFloat;
use Webkul\Core\Models\Channel as BaseChannel;

class Channel extends BaseChannel implements Wallet, WalletFloat
{
    use HasWalletFloat;
}
