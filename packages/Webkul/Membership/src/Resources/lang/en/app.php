<?php

return [
    'shop' => [
        'customers' => [
            'profile' => [
                'membership-group' => 'Membership',
            ],
        ],
    ],

    'admin' => [
        'tiers' => [
            'acl-title'    => 'Memberships',
            'menu-title'   => 'Memberships',
            'title'        => 'Memberships',
            'subtitle'     => 'Tier Rules',
            'description'  => 'Assign customers to groups automatically based on their wallet balance range.',
            'drawer-title' => 'Manage Tier Rules',
            'add-tier'     => 'Add Tier',
            'save'         => 'Save',
            'saved'        => 'Membership tier rules saved successfully.',
            'no-rules'     => 'No tier rules configured yet.',
            'select-group' => '— Select Customer Group —',

            'col-sort-order'    => 'Priority',
            'col-min-balance'   => 'Min Balance',
            'col-max-balance'   => 'Max Balance',
            'col-customer-group'=> 'Customer Group',

            'validation' => [
                'min-balance-required' => 'Tier :n: minimum balance is required and must be ≥ 0.',
                'max-balance-required' => 'Tier :n: maximum balance is required and must be ≥ 0.',
                'min-exceeds-max'      => 'Tier :n: minimum balance cannot be greater than maximum balance.',
                'group-required'       => 'Tier :n: customer group is required.',
                'overlap'              => 'Tiers :a and :b have overlapping balance ranges.',
            ],
        ],
    ],

];
