<?php

namespace App\Http\Controllers\Central;

use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Central\Tenant\StoreTenantRequest;
use App\Http\Resources\Central\TenantResource as CentralTenantResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Tenant;
use App\Queries\Central\TenantQuery as CentralTenantQuery;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Stancl\Tenancy\Jobs\DeleteDatabase;


class TenantController extends Controller
{

    public function __construct(
        private readonly CentralTenantQuery $tenantQuery,
    ) {}

    // {
    //     "name": "Monterrey",
    //     "code": "mty",
    //     "domain": "mty.localhost",
    // }
    public function findAll( Request $request )
    {
        $query = $this->tenantQuery->paginated($request);
        return CentralTenantResource::collection( $query);
    }

    public function show(Tenant $tenant)
    {
        return response()->json(new CentralTenantResource($tenant));
    }

    public function store(StoreTenantRequest $request)
    {

        $data = $request->validated();

        $data['code'] = "test_" . Str::random(3);
        $data['domain'] = "{$data['code']}.localhost";

        $newTenant = null;
        try {
            DB::transaction(function () use ($data, &$newTenant) {
                $domain = $data['domain'];

                $data['tenancy_db_name'] = "tenant_" . $data['code'];

                $newTenant = Tenant::create( Arr::except($data, ['domain']) );

                $newTenant->domains()->create([
                    'domain' => $domain,
                    'uuid'   => (string) Str::uuid()
                ]);
            });

            return response()->json([
                'message'   => 'El tenant se ha creado correctamente',
                'data'      => true
            ]);
        } catch (\Exception $e) {
            Log::error('Error desconocido: ' . $e->getMessage());
            throw new CustomException('Se produjo un error inesperado. Por favor, inténtalo de nuevo.');
        }
    }

}
