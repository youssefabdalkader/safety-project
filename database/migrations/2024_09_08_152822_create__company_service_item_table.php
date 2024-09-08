<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('_company_service_service_item', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('companyServiceId');
            $table->unsignedBigInteger('serviceItemId');    
            $table->foreign('companyServiceId')->references('companyServiceId')->on('_company_service')->onDelete('cascade');
            $table->foreign('serviceItemId')->references('serviceItemId')->on('_service_item')->onDelete('cascade');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('_company_service_item');
    }
};
