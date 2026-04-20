<?php

namespace Webkul\CustomerVerification\Repositories;

use Webkul\Core\Eloquent\Repository;

class CustomerVerificationDocumentRepository extends Repository
{
    public function model(): string
    {
        return 'Webkul\\CustomerVerification\\Models\\CustomerVerificationDocument';
    }
}
