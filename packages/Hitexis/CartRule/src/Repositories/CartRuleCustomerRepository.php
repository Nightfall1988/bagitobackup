<?php

namespace Hitexis\CartRule\Repositories;

use Webkul\Core\Eloquent\Repository;

class CartRuleCustomerRepository extends Repository
{
    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return 'Hitexis\CartRule\Contracts\CartRuleCustomer';
    }
}
