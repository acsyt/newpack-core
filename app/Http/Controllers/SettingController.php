<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{

    public function getSettings() {
        $settings = Setting::pluck('value', 'slug');
        return response()->json($settings);
    }

    public function updateSettings(Request $request) {

        $data = $request->validate([
            'iva'                       => ['required', 'numeric', 'min:0', 'max:1'],
        ]);

        foreach ($data as $key => $value) {
            Setting::where('slug', $key)->update(['value' => $value]);
        }

        return response()->json([
            'message'   => 'Configuración actualizada correctamente',
            'data'      => true,
        ]);
    }

}
