<?php

namespace Webkul\CustomerVerification\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\CustomerVerification\Contracts\CustomerVerificationDocument as CustomerVerificationDocumentContract;

class CustomerVerificationDocument extends Model implements CustomerVerificationDocumentContract
{
    protected $table = 'customer_verification_documents';

    protected $fillable = [
        'customer_id',
        'type',
        'path',
        'mime',
        'size',
        'status',
        'original_name',
        'document_type',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
    ];
}
