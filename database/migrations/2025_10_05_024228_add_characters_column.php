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
        
        Schema::table('scripts', function (Blueprint $table) {
            // Adds the column to store the mind map data
            $table->json('characters')->nullable()->after('characterMindMap'); 
                        $table->longtext('pacingMindmap')->nullable()->after('characters'); 

            // NOTE: Change 'some_existing_column' to the name of the column you want 
            // this new column to appear after in your database.
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scripts', function (Blueprint $table) {
            // Drops the column if you rollback the migration
            $table->dropColumn('characters');
        });
    }
};
