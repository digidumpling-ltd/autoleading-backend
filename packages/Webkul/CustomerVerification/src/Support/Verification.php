<?php

namespace Webkul\CustomerVerification\Support;

class Verification
{
    public const STATUS_INCOMPLETE = 'incomplete';

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const DOCUMENT_TYPE_ID_DOCUMENT = 'id_document';

    public const DOCUMENT_TYPE_DRIVER_LICENSE = 'driver_license';

    public const DOCUMENT_TYPE_ADDRESS_PROOF = 'address_proof';

    public const REQUIRED_DOCUMENT_TYPES = [
        self::DOCUMENT_TYPE_ID_DOCUMENT,
        self::DOCUMENT_TYPE_DRIVER_LICENSE,
        self::DOCUMENT_TYPE_ADDRESS_PROOF,
    ];

    public const TRANSITIONABLE_UPLOAD_STATUSES = [
        self::STATUS_INCOMPLETE,
        self::STATUS_REJECTED,
    ];

    public const STATUSES = [
        self::STATUS_INCOMPLETE,
        self::STATUS_PENDING,
        self::STATUS_APPROVED,
        self::STATUS_REJECTED,
    ];
}
