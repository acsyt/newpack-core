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
        $processData = [
            [
                "id"                    => 1,
                "code"                  => "COEX",
                "name"                  => "Extrusion",
                "applies_to_pt"         => true, // Aplica en PT
                "applies_to_mp"         => false, // No aplica en MP
                "applies_to_compounds"  => true // Aplica en compuestos
            ],
            [
                "id"                    => 2,
                "code"                  => "IMP",
                "name"                  => "Impresión",
                "applies_to_pt"         => true,
                "applies_to_mp"         => false,
                "applies_to_compounds"  => true
            ],
            [
                "id"                    => 3,
                "code"                  => "BS",
                "name"                  => "BOLSA SUELTA",
                "applies_to_pt"         => true,
                "applies_to_mp"         => false,
                "applies_to_compounds"  => false
            ],
            [
                "id"                    => 4,
                "code"                  => "BR",
                "name"                  => "BOLSA EN ROLLO",
                "applies_to_pt"         => true,
                "applies_to_mp"         => false,
                "applies_to_compounds"  => false
            ],
            [
                "id"                    => 5,
                "code"                  => "PELET",
                "name"                  => "PELETIZADO",
                "applies_to_pt"         => true,
                "applies_to_mp"         => true,
                "applies_to_compounds"  => false
            ]
        ];

        $machinesData = [
            [
                "code"                  => "EXTMONOC",
                "name"                  => "EXTRUDER MONOCAPA 1",
                "process_code"          => "COEX",
                "speed_mh"              => "",
                "speed_kgh"             => "",
                "circumference_total"   => "",
                "max_width"             => "",
                "max_center"            => ""
            ],
            [
                "code"                  => "COEXABA0",
                "name"                  => "COEXTRUSORA ABA 1",
                "process_code"          => "COEX",
                "speed_mh"              => "",
                "speed_kgh"             => "",
                "circumference_total"   => "",
                "max_width"             => "",
                "max_center"            => ""
            ],
            [
                "code"                  => "COEXABC0",
                "name"                  => "COEXTRUSORA ABC 1",
                "process_code"          => "COEX",
                "speed_mh"              => "",
                "speed_kgh"             => "",
                "circumference_total"   => "",
                "max_width"             => "",
                "max_center"            => ""
            ],
            [
                "code"                  => "BS01",
                "name"                  => "BOLSEADORA SELLO FONDO 2000 MM",
                "process_code"          => "BS",
                "speed_mh"              => "",
                "speed_kgh"             => "",
                "circumference_total"   => "",
                "max_width"             => "",
                "max_center"            => ""
            ],
            [
                "code"                  => "BS02",
                "name"                  => "BOLSEADORA SELLO FONDO Y SELLO LATERAL 1400 MM",
                "process_code"          => "BS",
                "speed_mh"              => "",
                "speed_kgh"             => "",
                "circumference_total"   => "",
                "max_width"             => "",
                "max_center"            => ""
            ],
            [
                "code"                  => "BS03",
                "name"                  => "BOLSEADORA SELLO FONDO Y SELLO LATERAL 600 MM",
                "process_code"          => "BS",
                "speed_mh"              => "",
                "speed_kgh"             => "",
                "circumference_total"   => "",
                "max_width"             => "",
                "max_center"            => ""
            ],
            [
                "code"                  => "BS04",
                "name"                  => "BOLSEADORA SELLO FONDO 1400 MM",
                "process_code"          => "BS",
                "speed_mh"              => "",
                "speed_kgh"             => "",
                "circumference_total"   => "",
                "max_width"             => "",
                "max_center"            => ""
            ],
            [
                "code"                  => "BR01",
                "name"                  => "BOLSA EN ROLLO BOBINA 1.5\" A 900 MM",
                "process_code"          => "BR",
                "speed_mh"              => "",
                "speed_kgh"             => "",
                "circumference_total"   => "",
                "max_width"             => "",
                "max_center"            => ""
            ],
            [
                "code"                  => "BR02",
                "name"                  => "BOLSA EN ROLLO GLOUCESTER BOBINA 3\" A 1400 MM",
                "process_code"          => "BR",
                "speed_mh"              => "",
                "speed_kgh"             => "",
                "circumference_total"   => "",
                "max_width"             => "",
                "max_center"            => ""
            ],
            [
                "code"                  => "BR03",
                "name"                  => "BOLSA EN ROLLO BOBINA 3\" A 1500 MM",
                "process_code"          => "BR",
                "speed_mh"              => "",
                "speed_kgh"             => "",
                "circumference_total"   => "",
                "max_width"             => "",
                "max_center"            => ""
            ],
            [
                "code"                  => "PCR01",
                "name"                  => "PELETIZADORA 01 200 KG/HR",
                "process_code"          => "PELET",
                "speed_mh"              => "",
                "speed_kgh"             => "",
                "circumference_total"   => "",
                "max_width"             => "",
                "max_center"            => ""
            ]
        ];

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
