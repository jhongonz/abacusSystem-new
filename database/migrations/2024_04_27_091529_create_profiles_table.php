<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * @codeCoverageIgnore
 */
return new class extends Migration {
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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id('pro_id');
            $table->string('pro_name', 100);
            $table->string('pro_description', 100)->nullable();
            $table->string('pro_search', 255)->nullable();
            $table->tinyInteger('pro_state')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
