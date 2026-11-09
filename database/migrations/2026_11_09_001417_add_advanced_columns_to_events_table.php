
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {

            // description already exists in Week 10.
            // Only add details if it does not exist.
            if (!Schema::hasColumn('events', 'details')) {
                $table->jsonb('details')->nullable();
            }

        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {

            if (Schema::hasColumn('events', 'details')) {
                $table->dropColumn('details');
            }

        });
    }
};
