<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CheckSeedStatus extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group       = 'Database';
    protected $name        = 'check:seed';
    protected $description = 'Checks the current seeded data and admin password';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'command:name [arguments] [options]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        $db = \Config\Database::connect();

        CLI::write("=== ADMIN USERS ===");
        foreach ($db->table('admin_users')->get()->getResultArray() as $u) {
            $ok = password_verify('admin123', $u['password_hash']);
            CLI::write("User: {$u['username']} ({$u['email']}) | Role: {$u['role']} | admin123: " . ($ok ? 'VALID (PASSED)' : 'INVALID'));
        }

        CLI::write("\n=== COUNTS ===");
        CLI::write("Speakers: " . $db->table('speakers')->countAllResults());
        CLI::write("Moderators: " . $db->table('moderators')->countAllResults());
        CLI::write("Rooms: " . $db->table('schedule_rooms')->countAllResults());
        CLI::write("Sessions: " . $db->table('schedule_sessions')->countAllResults());

        CLI::write("\n=== ROOMS ===");
        foreach ($db->table('schedule_rooms')->get()->getResultArray() as $r) {
            CLI::write("Room #{$r['id']}: {$r['name']} (Cap: {$r['capacity']})");
        }

        CLI::write("\n=== MODERATORS ===");
        foreach ($db->table('moderators')->get()->getResultArray() as $m) {
            CLI::write("Mod #{$m['id']}: {$m['name']} ({$m['role_text']})");
        }

        CLI::write("\n=== DAY 1 SPECIAL SESSION ===");
        $special = $db->table('schedule_sessions')->where('title', 'Special Session A')->get()->getRowArray();
        if ($special) {
            CLI::write("Parent #{$special['id']}: {$special['title']} | Time: {$special['start_time']} - {$special['end_time']} | Type: {$special['session_type']}");
            $children = $db->table('schedule_sessions')->where('parent_session_id', $special['id'])->get()->getResultArray();
            foreach ($children as $c) {
                $rm = $db->table('schedule_rooms')->where('id', $c['room_id'])->get()->getRowArray();
                CLI::write(" -> Sub Session #{$c['id']}: " . str_replace("\n", " ", $c['title']));
                CLI::write("    Room: " . ($rm['name'] ?? 'N/A'));

                $spks = $db->table('session_speakers')
                    ->join('speakers', 'speakers.id = session_speakers.speaker_id')
                    ->where('session_speakers.session_id', $c['id'])
                    ->get()->getResultArray();
                CLI::write("    Speakers (" . count($spks) . "): " . implode(', ', array_column($spks, 'name')));

                $mods = $db->table('session_moderators')
                    ->join('moderators', 'moderators.id = session_moderators.moderator_id')
                    ->where('session_moderators.session_id', $c['id'])
                    ->get()->getResultArray();
                if ($mods) {
                    CLI::write("    Moderators: " . implode(', ', array_column($mods, 'name')));
                }
            }
        }
    }
}
