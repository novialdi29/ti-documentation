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
        Schema::table('dokumentasis', function (Blueprint $table): void {
            if (! Schema::hasColumn('dokumentasis', 'nomor_ticket')) {
                $table->string('nomor_ticket')->nullable()->after('id');
            }
            if (! Schema::hasColumn('dokumentasis', 'tanggal_ticket')) {
                $table->date('tanggal_ticket')->nullable()->after('nomor_ticket');
            }
            if (! Schema::hasColumn('dokumentasis', 'lokasi_unit')) {
                $table->string('lokasi_unit')->nullable()->after('tanggal_ticket');
            }
            if (! Schema::hasColumn('dokumentasis', 'nama_pic')) {
                $table->string('nama_pic')->nullable()->after('lokasi_unit');
            }
            if (! Schema::hasColumn('dokumentasis', 'kontak_pic')) {
                $table->string('kontak_pic')->nullable()->after('nama_pic');
            }
        });

        $records = DB::table('dokumentasis')
            ->select('id', 'created_at')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        $yearCounters = [];

        foreach ($records as $record) {
            $year = (int) date('Y', strtotime((string) $record->created_at));
            $yearCounters[$year] = ($yearCounters[$year] ?? 0) + 1;
            $nomorTicket = sprintf('TIK-%d-%04d', $year, $yearCounters[$year]);

            DB::table('dokumentasis')
                ->where('id', $record->id)
                ->update([
                    'nomor_ticket' => $nomorTicket,
                    'tanggal_ticket' => date('Y-m-d', strtotime((string) $record->created_at)),
                    'nama_pic' => 'Belum diisi',
                ]);
        }

        $this->createNomorTicketUniqueIndexIfMissing();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dokumentasis', function (Blueprint $table): void {
            if ($this->hasIndex('dokumentasis', 'dokumentasis_nomor_ticket_unique')) {
                $table->dropUnique('dokumentasis_nomor_ticket_unique');
            }
            if (Schema::hasColumn('dokumentasis', 'nomor_ticket')) {
                $table->dropColumn('nomor_ticket');
            }
            if (Schema::hasColumn('dokumentasis', 'tanggal_ticket')) {
                $table->dropColumn('tanggal_ticket');
            }
            if (Schema::hasColumn('dokumentasis', 'lokasi_unit')) {
                $table->dropColumn('lokasi_unit');
            }
            if (Schema::hasColumn('dokumentasis', 'nama_pic')) {
                $table->dropColumn('nama_pic');
            }
            if (Schema::hasColumn('dokumentasis', 'kontak_pic')) {
                $table->dropColumn('kontak_pic');
            }
        });
    }

    private function createNomorTicketUniqueIndexIfMissing(): void
    {
        if ($this->hasIndex('dokumentasis', 'dokumentasis_nomor_ticket_unique')) {
            return;
        }

        Schema::table('dokumentasis', function (Blueprint $table): void {
            $table->unique('nomor_ticket');
        });
    }

    private function hasIndex(string $table, string $index): bool
    {
        $result = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$index]);

        return count($result) > 0;
    }
};
