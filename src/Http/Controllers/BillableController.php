<?php

namespace Mikrocloud\Mikrocloud\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Mikrocloud\Mikrocloud\Http\Resources\BillableCountResource;

class BillableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function count(Request $request, $model)
    {

        $count_only = $request->input('count', false);
        $model = ucfirst($model);
        $class = 'App\Models\\' . $model;

        if (!class_exists($class)) {
            abort(410, "The model '" . $model . "' does not exist");
        }

        if (!is_subclass_of($class, 'Mikrocloud\Mikrocloud\Models\BillableModel')) {
            abort(422, "The model '" . $model . "' does not extend the 'MikroCloud BillableModel' class");
        }

        $has_soft_deletes = \Schema::hasColumn((new $class)->getTable(), 'deleted_at');

        $model = new $class;

        $query = $model::where('customer_id', auth()->user()->id)
            ->when($has_soft_deletes, function ($query) {
                return $query->whereNull('deleted_at');
            });

        if ($count_only) {
            return response()->json(['count' => $query->count()]);
        }

        return response(BillableCountResource::collection($query->get()));
    }
}
