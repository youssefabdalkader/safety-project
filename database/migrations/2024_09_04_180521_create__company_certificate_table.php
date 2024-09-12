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
        Schema::create('_company_certificate', function (Blueprint $table) {
            $table->uuid('certificateCode')->primary();
            $table->string('certificatePhotoUrl');
            $table->timestamp('startat')->nullable();
            $table->timestamp('endat')->nullable();
            $table->boolean('invalid')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('_company_certificate');
    }
};
