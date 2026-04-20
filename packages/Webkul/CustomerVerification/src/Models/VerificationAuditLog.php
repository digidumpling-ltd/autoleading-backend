<?php

namespace Webkul\CustomerVerification\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Admin\Models\Admin;
use Webkul\Customer\Models\Customer;

class VerificationAuditLog extends Model
{
    protected $table = 'verification_audit_logs';

    protected $fillable = [
        'admin_id',
        'customer_id',
        'action',
        'reason',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
