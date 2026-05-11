<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kategoris', function (Blueprint $table): void {
            if (! Schema::hasColumn('kategoris', 'slug')) {
                $table->string('slug')->nullable()->after('nama');
            }
            if (! Schema::hasColumn('kategoris', 'tipe')) {
                $table->string('tipe')->nullable()->after('deskripsi');
            }
            if (! Schema::hasColumn('kategoris', 'warna_badge')) {
                $table->string('warna_badge')->nullable()->after('tipe');
            }
        });

        if (! $this->hasIndex('kategoris', 'kategoris_slug_unique')) {
            Schema::table('kategoris', function (Blueprint $table): void {
                $table->unique('slug');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kategoris', function (Blueprint $table): void {
            if ($this->hasIndex('kategoris', 'kategoris_slug_unique')) {
                $table->dropUnique('kategoris_slug_unique');
            }
            if (Schema::hasColumn('kategoris', 'slug')) {
                $table->dropColumn('slug');
            }
            if (Schema::hasColumn('kategoris', 'tipe')) {
                $table->dropColumn('tipe');
            }
            if (Schema::hasColumn('kategoris', 'warna_badge')) {
                $table->dropColumn('warna_badge');
            }
        });
    }

    private function hasIndex(string $table, string $index): bool
    {
        $result = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$index]);

        return count($result) > 0;
    }
};
