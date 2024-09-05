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
        Schema::create('_company_service', function (Blueprint $table) {
            $table->id('companyServiceId');
            $table->string('companyServiceName'); 
            $table->text('companyServiceImageUrl'); 
            $table->text('companyServiceDescription');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('_company_service');
    }
};
