<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sub_workstations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_workstation');
            $table->string('subsect_name');
            $table->timestamps();

            $table->foreign('id_workstation')->references('id')->on('workstations')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_workstations');
    }
};
