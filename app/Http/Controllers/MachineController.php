<?php

namespace App\Http\Controllers;

use App\Http\Actions\Machine\CreateMachineAction;
use App\Http\Actions\Machine\UpdateMachineAction;
use App\Http\Resources\MachineResource;
use App\Http\Requests\Machine\StoreMachineRequest;
use App\Http\Requests\Machine\UpdateMachineRequest;
use App\Queries\MachineQuery;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;

/**
 * @OA\Tag(name="Machines")
 */
class MachineController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/machines",
     *     summary="Get all machines",
     *     tags={"Machines"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filter[code]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filter[name]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filter[process_id]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filter[status]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filter[search]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="include", in="query", required=false, @OA\Schema(type="string"), description="Include process relationship"),
     *     @OA\Response(response=200, description="Machines list", @OA\JsonContent(ref="#/components/schemas/MachineResource")),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function findAllMachines()
    {
        $machines = MachineQuery::make()->paginated();
        return MachineResource::collection($machines);
    }

    /**
     * @OA\Post(
     *     path="/api/machines",
     *     summary="Create a new machine",
     *     tags={"Machines"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/StoreMachineRequest")),
     *     @OA\Response(response=201, description="Machine created", @OA\JsonContent(ref="#/components/schemas/MachineResource")),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function createMachine(StoreMachineRequest $request)
    {
        $data = $request->validated();
        $machine = app(CreateMachineAction::class)->handle($data);

        return response()->json([
            'data'      => new MachineResource($machine),
            'message'   => 'Máquina creada exitosamente',
        ], Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/machines/{machine}",
     *     summary="Get a machine by ID",
     *     tags={"Machines"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="machine", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Machine details", @OA\JsonContent(ref="#/components/schemas/MachineResource")),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function findOneMachine($id)
    {
        $machine = MachineQuery::make()->findByIdOrFail((int) $id);
        return new MachineResource($machine);
    }

    /**
     * @OA\Patch(
     *     path="/api/machines/{machine}",
     *     summary="Update a machine",
     *     tags={"Machines"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="machine", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/UpdateMachineRequest")),
     *     @OA\Response(response=200, description="Machine updated", @OA\JsonContent(ref="#/components/schemas/MachineResource")),
     *     @OA\Response(response=404, description="Not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/machines/{machine}",
     *     summary="Delete a machine",
     *     tags={"Machines"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="machine", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Deleted"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function deleteMachine($id)
    {
        $machine = MachineQuery::make()->findByIdOrFail((int) $id);
        $machine->delete();
        return response()->noContent();
    }
}
