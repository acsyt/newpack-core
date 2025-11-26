<?php

namespace App\Http\Actions\Warehouse;

use App\Exceptions\CustomException;
use App\Models\WarehouseLocation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class UpdateWarehouseLocationAction
{
    public function handle(WarehouseLocation $location, array $data): WarehouseLocation
    {
        DB::beginTransaction();

        try {
            $location->update($data);

            $location->refresh();

            DB::commit();

            return $location;

        } catch (CustomException $e) {
            DB::rollBack();

            throw $e;
        } catch (Exception $e) {
            DB::rollBack();

            Log::error(UpdateWarehouseLocationAction::class, [
                'location_id' => $location->id,
                'error'       => $e->getMessage(),
                'trace'       => $e->getTraceAsString(),
                'data'        => $data,
                'user_id'     => Auth::id(),
            ]);

            throw new CustomException(
                'The system was unable to update the warehouse location. Please try again.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
