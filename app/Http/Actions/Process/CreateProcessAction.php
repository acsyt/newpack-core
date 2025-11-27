<?php

namespace App\Http\Actions\Process;

use App\Exceptions\CustomException;
use App\Models\Process;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class CreateProcessAction
{
    public function handle(array $data): Process
    {
        DB::beginTransaction();

        try {
            $process = Process::create([
                'code' => $data['code'],
                'name' => $data['name'],
                'applies_to_pt' => $data['applies_to_pt'] ?? false,
                'applies_to_mp' => $data['applies_to_mp'] ?? false,
                'applies_to_compounds' => $data['applies_to_compounds'] ?? false,
            ]);

            DB::commit();

            return $process;

        } catch (CustomException $e) {
            DB::rollBack();
            throw $e;

        } catch (Exception $e) {
            DB::rollBack();

            Log::error(CreateProcessAction::class, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data,
                'user_id' => Auth::id(),
            ]);

            throw new CustomException(
                'El sistema no pudo crear el proceso. Por favor, intente nuevamente.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
