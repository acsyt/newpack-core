<?php

namespace App\Http\Actions\Warehouse;

use App\Exceptions\CustomException;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class CreateWarehouseAction
{
    public function handle(array $data): Warehouse
    {
        DB::beginTransaction();

        try {
            $data['created_by'] = Auth::id();

            $warehouse = Warehouse::create($data);

            DB::commit();

            return $warehouse;

        } catch (CustomException $e) {
            DB::rollBack();

            throw $e;
        }  catch (Exception $e) {
            DB::rollBack();

            Log::error(CreateWarehouseAction::class, [
                'error'         => $e->getMessage(),
                'trace'         => $e->getTraceAsString(),
                'data'          => $data,
                'user_id'       => Auth::id(),
            ]);

            throw new CustomException(
                'The system was unable to create the warehouse. Please try again.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
