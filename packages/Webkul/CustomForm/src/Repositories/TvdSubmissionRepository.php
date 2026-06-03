<?php

namespace Webkul\CustomForm\Repositories;

use Webkul\Core\Eloquent\Repository;
use Webkul\CustomForm\Models\TvdSubmission;

class TvdSubmissionRepository extends Repository
{
    public function model(): string
    {
        return TvdSubmission::class;
    }
}
