<?php

namespace App\Models;

use Mikrocloud\Mikrocloud\Models\Customer as CustomerModel;

abstract class Customer extends CustomerModel
{
    /**
     * Pass the customer_id to the parent constructor
     */
    public function __construct(string $customer_id)
    {
        parent::__construct($customer_id);
    }

    // You can add your own relationships here
    // Example:
    // public function sites(): hasMany
    // {
    //     return $this->hasMany(Site::class);
    // }
    //
    // It will match to the customer_id column in the sites table

    // You can then access the relationship like this:
    // auth()->user()->sites or
    // auth()->user()->sites()->where('name', 'like', '%foo%')->get()
    // and create new sites like this:
    // auth()->user()->sites()->create(['name' => 'foo'])

}
