<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop existing table or view if present
        Schema::dropIfExists('daily_performances');
        DB::statement("DROP VIEW IF EXISTS daily_performances");

        // Database View jo tasks ke created_at aur updated_at se exact minutes aur efficiency percentage calculate karegi
        DB::statement("
            CREATE VIEW daily_performances AS
            SELECT 
                ROW_NUMBER() OVER (ORDER BY assigned_to, DATE(updated_at)) as id,
                assigned_to as user_id,
                DATE(updated_at) as date,
                COUNT(*) as tasks_completed,
                SUM(COALESCE(estimated_minutes, 0)) as total_estimated_minutes,
                SUM(TIMESTAMPDIFF(MINUTE, created_at, updated_at)) as total_time_spent,
                ROUND(
                    CASE 
                        WHEN SUM(TIMESTAMPDIFF(MINUTE, created_at, updated_at)) > 0 AND SUM(COALESCE(estimated_minutes, 0)) > 0
                        THEN (SUM(COALESCE(estimated_minutes, 0)) / SUM(TIMESTAMPDIFF(MINUTE, created_at, updated_at))) * 100
                        ELSE 100
                    END, 2
                ) as efficiency_percentage
            FROM tasks
            WHERE status = 'completed' AND assigned_to IS NOT NULL
            GROUP BY assigned_to, DATE(updated_at)
        ");
    }

    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS daily_performances");
    }
};