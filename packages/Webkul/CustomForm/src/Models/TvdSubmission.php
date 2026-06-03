<?php

namespace Webkul\CustomForm\Models;

use Illuminate\Database\Eloquent\Model;

class TvdSubmission extends Model
{
    protected $fillable = [
        'chinese_name',
        'english_name',
        'rental_model',
        'return_date',
        'contact_number',
        'email',
        'refund_type',
        'local_bank_info',
        'overseas_bank_info',
    ];
}
