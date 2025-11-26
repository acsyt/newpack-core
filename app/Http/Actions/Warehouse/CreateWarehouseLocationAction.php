<?php

namespace App\Http\Actions\Warehouse;

use App\Exceptions\CustomException;
use App\Models\WarehouseLocation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class CreateWarehouseLocationAction
{
    public function handle(array $data): WarehouseLocation
    {
        DB::beginTransaction();

        try {
            $data['unique_id'] = Str::uuid();

            $location = WarehouseLocation::create($data);

            DB::commit();

            return $location;

        } catch (CustomException $e) {
            DB::rollBack();

            throw $e;
        }  catch (Exception $e) {
            DB::rollBack();

            Log::error(CreateWarehouseLocationAction::class, [
                'error'         => $e->getMessage(),
                'trace'         => $e->getTraceAsString(),
                'data'          => $data,
                'user_id'       => Auth::id(),
            ]);

            throw new CustomException(
                'The system was unable to create the warehouse location. Please try again.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
