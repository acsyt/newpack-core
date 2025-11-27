<?php

namespace App\Http\Actions\Machine;

use App\Exceptions\CustomException;
use App\Models\Machine;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class CreateMachineAction
{
    public function handle(array $data): Machine
    {
        DB::beginTransaction();

        try {
            $machine = Machine::create($data);

            DB::commit();

            return $machine->load('process');

        } catch (CustomException $e) {
            DB::rollBack();
            throw $e;

        } catch (Exception $e) {
            DB::rollBack();

            Log::error(CreateMachineAction::class, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data,
                'user_id' => Auth::id(),
            ]);

            throw new CustomException(
                'El sistema no pudo crear la máquina. Por favor, intente nuevamente.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
