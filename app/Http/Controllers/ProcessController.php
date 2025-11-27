<?php

namespace App\Http\Controllers;

use App\Http\Actions\Process\CreateProcessAction;
use App\Http\Actions\Process\UpdateProcessAction;
use App\Http\Resources\ProcessResource;
use App\Http\Requests\Process\StoreProcessRequest;
use App\Http\Requests\Process\UpdateProcessRequest;
use App\Queries\ProcessQuery;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;

/**
 * @OA\Tag(name="Processes")
 */
class ProcessController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/processes",
     *     summary="Get all processes",
     *     tags={"Processes"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filter[code]", in="query", required=false, @OA\Schema(type="string"), description="Filter by process code (partial match)"),
     *     @OA\Parameter(name="filter[name]", in="query", required=false, @OA\Schema(type="string"), description="Filter by process name (partial match)"),
     *     @OA\Parameter(name="filter[applies_to_pt]", in="query", required=false, @OA\Schema(type="boolean"), description="Filter by applies to PT"),
     *     @OA\Parameter(name="filter[applies_to_mp]", in="query", required=false, @OA\Schema(type="boolean"), description="Filter by applies to MP"),
     *     @OA\Parameter(name="filter[applies_to_compounds]", in="query", required=false, @OA\Schema(type="boolean"), description="Filter by applies to compounds"),
     *     @OA\Parameter(name="filter[search]", in="query", required=false, @OA\Schema(type="string"), description="Search across code and name fields"),
     *     @OA\Parameter(name="sort", in="query", required=false, @OA\Schema(type="string"), description="Sort by field (prefix with - for descending)"),
     *     @OA\Response(response=200, description="Processes list", @OA\JsonContent(ref="#/components/schemas/ProcessResource")),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function findAllProcesses()
    {
        $processes = ProcessQuery::make()->paginated();
        return ProcessResource::collection($processes);
    }

    /**
     * @OA\Post(
     *     path="/api/processes",
     *     summary="Create a new process",
     *     tags={"Processes"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/StoreProcessRequest")),
     *     @OA\Response(response=201, description="Process created", @OA\JsonContent(ref="#/components/schemas/ProcessResource")),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function createProcess(StoreProcessRequest $request)
    {
        $data = $request->validated();
        $process = app(CreateProcessAction::class)->handle($data);

        return response()->json([
            'data'      => new ProcessResource($process),
            'message'   => 'Proceso creado exitosamente',
        ], Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/processes/{process}",
     *     summary="Get a process by ID",
     *     tags={"Processes"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="process", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Process details", @OA\JsonContent(ref="#/components/schemas/ProcessResource")),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function findOneProcess($id)
    {
        $process = ProcessQuery::make()->findByIdOrFail((int) $id);
        return new ProcessResource($process);
    }

    /**
     * @OA\Patch(
     *     path="/api/processes/{process}",
     *     summary="Update a process",
     *     tags={"Processes"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="process", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/UpdateProcessRequest")),
     *     @OA\Response(response=200, description="Process updated", @OA\JsonContent(ref="#/components/schemas/ProcessResource")),
     *     @OA\Response(response=404, description="Not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function updateProcess(UpdateProcessRequest $request, $id)
    {
        $process = ProcessQuery::make()->findByIdOrFail((int) $id);
        $data = $request->validated();
        $process = app(UpdateProcessAction::class)->handle($process, $data);

        return response()->json([
            'data'      => new ProcessResource($process),
            'message'   => 'Proceso actualizado exitosamente',
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *     path="/api/processes/{process}",
     *     summary="Delete a process",
     *     tags={"Processes"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="process", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Deleted"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function deleteProcess($id)
    {
        $process = ProcessQuery::make()->findByIdOrFail((int) $id);
        $process->delete();
        return response()->noContent();
    }
}
