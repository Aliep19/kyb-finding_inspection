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
Schema::table('defect_input_details', function (Blueprint $table) {
    $table->timestamp('pica_uploaded_at')->nullable()->after('pica');
});
    }
};
