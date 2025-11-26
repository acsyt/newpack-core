<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Machine;
use App\Models\Process;
use Illuminate\Support\Facades\DB;

class MachineSeeder extends Seeder
{
    public function run(): void
    {
        $processDataPath = resource_path('json/process.data.json');
        $processData = json_decode(file_get_contents($processDataPath), true);
        $machinesDataPath = resource_path('json/machines.data.json');
        $machinesData = json_decode(file_get_contents($machinesDataPath), true);

        DB::transaction(function () use ($processData, $machinesData) {
            foreach ($processData as $process) {
                Process::updateOrCreate(
                    ['code' => $process['code']],
                    $process
                );
            }

            foreach ($machinesData as $machine) {
                $processId = Process::where('code', $machine['process_code'])->value('id');

                Machine::updateOrCreate(
                    ['code' => $machine['code']],
                    [
                        'name'                => $machine['name'],
                        'process_id'          => $processId,
                        'speed_mh'            => $machine['speed_mh'] ?: null,
                        'speed_kgh'           => $machine['speed_kgh'] ?: null,
                        'circumference_total' => $machine['circumference_total'] ?: null,
                        'max_width'           => $machine['max_width'] ?: null,
                        'max_center'          => $machine['max_center'] ?: null,
                    ]
                );
            }
        });
    }
}
