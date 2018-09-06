<?php

namespace App\Http\Controllers\Api;

use App\Models\Stype;
use Illuminate\Http\Request;
use App\Transformers\StypeTransformer;

class StypesController extends Controller
{
    protected $table = 'stypes';

    public function index()
    {
        $stypes = Stype::with('cmodels')->get();

        return $this->response()->collection($stypes, new StypeTransformer());
    }
}
