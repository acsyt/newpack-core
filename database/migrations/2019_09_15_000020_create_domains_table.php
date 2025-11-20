<?php

declare(strict_types=1);

use App\Models\Tenant;
use App\StateMachines\DomainStateMachine;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDomainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('domains', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uuid')->unique();
            $table->string('domain', 255)->unique();
            $table->boolean('domain_configured')->default(false);
            $table->boolean('domain_created')->default(false);
            $table->unsignedBigInteger('tenant_id');
            $table->timestamps();
            $table->foreign('tenant_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');

            $table->index('uuid');
            $table->index('domain');
            $table->index('tenant_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('domains');
    }
}
