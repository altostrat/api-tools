<?php

namespace Altostrat\Tools\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Altostrat\Tools\Traits\CustomerTrait;

class Customer extends Model implements Authenticatable
{
    use CustomerTrait;

    public $exists = true;

    public $incrementing = false;

    public $timestamps = false;

    protected $keyType = 'string';

    protected $attributes = [
        'timezone' => 'UTC',
        'date_format' => 'd M Y',
        'time_format' => 'H:i:s',
    ];

    public function __construct(string $customer_id)
    {
        parent::__construct();

        $this->attributes['id'] = $customer_id;
    }

    public function can($ability): bool
    {
        $scopes = Arr::get($this->attributes, 'scopes', []);
        return in_array($ability, $scopes);
    }

    public function getAuthPasswordName(): string
    {
        return 'password';
    }
}
