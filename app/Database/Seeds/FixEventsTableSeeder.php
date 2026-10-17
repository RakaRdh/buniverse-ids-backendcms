<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FixEventsTableSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        try {
            $db->query("ALTER TABLE events DROP INDEX year");
            echo "Index 'year' dropped from events table.\n";
        } catch (\Throwable $e) {
            echo "Notice: " . $e->getMessage() . "\n";
        }
    }
}
