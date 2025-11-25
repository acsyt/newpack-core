<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Proveedor de Materia Prima (Polietileno)
        Supplier::factory()->create([
            'company_name' => 'Polímeros Nacionales S.A. de C.V.',
            'contact_name' => 'Ing. Roberto Méndez',
            'email' => 'ventas@polimerosnacionales.com',
            'supplier_type' => 'product',
            'rfc' => 'PNA900101XYZ',
            'payment_terms' => 'Crédito 30 días',
        ]);

        // Proveedor de Insumos (Tintas)
        Supplier::factory()->create([
            'company_name' => 'Tintas y Solventes de México',
            'contact_name' => 'Lic. Ana Torres',
            'email' => 'contacto@tintasmex.com',
            'supplier_type' => 'product',
            'rfc' => 'TSM850505ABC',
            'payment_terms' => 'Contado',
        ]);

        // Proveedor de Servicios (Mantenimiento)
        Supplier::factory()->create([
            'company_name' => 'Mantenimiento Industrial Express',
            'contact_name' => 'Carlos Ruiz',
            'email' => 'servicios@mie.com.mx',
            'supplier_type' => 'service',
            'rfc' => 'MIE101010LMN',
            'payment_terms' => 'Crédito 15 días',
        ]);

        // Generar 10 proveedores aleatorios adicionales
        Supplier::factory()->count(10)->create();
    }
}
