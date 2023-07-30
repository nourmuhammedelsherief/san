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
        Schema::create('seller_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->enum('type' , ['teacher' , 'school']);
            $table->enum('status' , ['active' , 'finished'])->default('active');
            $table->integer('discount')->default(0);
            $table->date('start_at')->nullable();
            $table->date('end_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_codes');
    }
};
