<?php

namespace App\Http\Controllers;

use App\Http\Resources\MeasureUnitResource;
use App\Models\MeasureUnit;
use Symfony\Component\HttpFoundation\Response;

class MeasureUnitController extends Controller
{
    public function findAllMeasureUnits()
    {
        $measureUnits = MeasureUnit::all();
        return response()->json([
         'data' => MeasureUnitResource::collection($measureUnits),
         'message' => 'Unidades de medida obtenidas exitosamente',
        ], Response::HTTP_OK);
    }
}