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
        Schema::table('objects', function (Blueprint $table) {
           // object_type: personal, hr, department, system
            $table->string('object_type')->default('custom')->after('slug');
            // department_name: dept object এর জন্য actual department name
            $table->string('department_name')->nullable()->after('object_type');
            // icon: SVG path string
            $table->text('icon')->nullable()->after('department_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('objects', function (Blueprint $table) {
            //
        });
    }
};
