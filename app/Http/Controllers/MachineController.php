<?php

namespace App\Http\Controllers;

use App\Http\Actions\Machine\CreateMachineAction;
use App\Http\Actions\Machine\UpdateMachineAction;
use App\Http\Requests\Machine\StoreMachineRequest;
use App\Http\Requests\Machine\UpdateMachineRequest;
use App\Http\Resources\MachineResource;
use App\Queries\MachineQuery;
use Symfony\Component\HttpFoundation\Response;

class MachineController extends Controller
{
    public function findAllMachines()
    {
        $machines = MachineQuery::make()->paginated();
        return MachineResource::collection($machines);
    }

    public function createMachine(StoreMachineRequest $request)
    {
        $data = $request->validated();
        $machine = app(CreateMachineAction::class)->handle($data);

        return response()->json([
            'data'      => new MachineResource($machine),
            'message'   => 'Máquina creada exitosamente',
        ], Response::HTTP_CREATED);
    }

    public function findOneMachine($id)
    {
        $machine = MachineQuery::make()->findByIdOrFail((int) $id);
        return new MachineResource($machine);
    }

    public function updateMachine(UpdateMachineRequest $request, $id)
    {
        $machine = MachineQuery::make()->findByIdOrFail((int) $id);
        $data = $request->validated();
        $machine = app(UpdateMachineAction::class)->handle($machine, $data);

        return response()->json([
            'data'      => new MachineResource($machine),
            'message'   => 'Máquina actualizada exitosamente',
        ], Response::HTTP_OK);
    }

    public function deleteMachine($id)
    {
        $machine = MachineQuery::make()->findByIdOrFail((int) $id);
        $machine->delete();
        return response()->noContent();
    }
}
