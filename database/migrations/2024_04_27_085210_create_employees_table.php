<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @codeCoverageIgnore
 */
return new class extends Migration
{
    /**
     * The database connection that should be used by the migration.
     *
     * @var string
     */
    protected $connection = 'sqlite';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id('emp_id');
            $table->string('emp_identification', 50);
            $table->string('emp_identification_type', 10);
            $table->string('emp_name', 100);
            $table->string('emp_lastname', 100)->nullable();
            $table->string('emp_phone_number', 15)->nullable();
            $table->date('emp_birthdate')->nullable();
            $table->string('emp_email', 150)->nullable();
            $table->string('emp_address', 150)->nullable();
            $table->string('emp_observations', 255)->nullable();
            $table->string('emp_image', 100)->nullable();
            $table->text('emp_search')->nullable();
            $table->tinyInteger('emp_state')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
