<?php

namespace App\Http\Actions\Process;

use App\Exceptions\CustomException;
use App\Models\Process;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class UpdateProcessAction
{
    public function handle(Process $process, array $data): Process
    {
        DB::beginTransaction();

        try {
            $process->update($data);

            DB::commit();

            return $process->refresh();

        } catch (CustomException $e) {
            DB::rollBack();
            throw $e;

        } catch (Exception $e) {
            DB::rollBack();

            Log::error(UpdateProcessAction::class, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data,
                'process_id' => $process->id,
                'user_id' => Auth::id(),
            ]);

            throw new CustomException(
                'El sistema no pudo actualizar el proceso. Por favor, intente nuevamente.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
