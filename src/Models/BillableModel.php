<?php

namespace Mikrocloud\Mikrocloud\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class BillableModel extends Model
{

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

            // TODO:: Add atomic lock to avoid race conditions during concurrent requests

            if (!auth()->check()) {
                abort(500, 'Only a JWT authenticated user can create a billable model');
            }

            if (\Schema::hasColumn((new $class)->getTable(), 'customer_id') === false) {
                abort(500, 'The billable model must have a customer_id column');
            }

            if (!in_array('customer_id', $model->getFillable())) {
                abort(500, 'The billable model must be fillable');
            }

            if (str($model->id)->isUuid() === false) {
                abort(500, 'The id field of the billable model must be a UUID');
            }

            $bearer = request()->bearerToken();

            $model->customer_id = auth()->user()->id;

            $data = [
                'class' => get_class($model),
                'basename' => class_basename($model),
                'api_prefix' => config('mikrocloud.api_prefix'),
            ];

            $billable = Http::withToken($bearer)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->post(config('mikrocloud.url') . '/v1/billable', $data);

            if ($billable->failed()) {
                if (!empty($billable->json()['message'])) {
                    abort($billable->status(), $billable->json()['message']);
                }
                abort($billable->status(), 'An unspecified error occurred while creating a billable model. Timestamp: ' . now()->toDateTimeString());
            }

            if ($billable->successful()) {
                return $model;
            }
        });
        
    }
}
