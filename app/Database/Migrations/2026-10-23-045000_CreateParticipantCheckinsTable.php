<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateParticipantCheckinsTable extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('participant_checkins')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'BIGINT',
                    'constraint'     => 20,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'participant_id' => [
                    'type'       => 'BIGINT',
                    'constraint' => 20,
                    'unsigned'   => true,
                ],
                'event_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'checkin_date' => [
                    'type' => 'DATE',
                ],
                'checkin_time' => [
                    'type' => 'DATETIME',
                ],
                'gate_officer_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'note' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);

            $this->forge->addKey('id', true);
            // Kunci Utama: 1 peserta hanya bisa 1x checkin pada tanggal yang sama
            $this->forge->addUniqueKey(['participant_id', 'checkin_date']);
            $this->forge->addKey(['event_id', 'checkin_date']);
            $this->forge->addForeignKey('participant_id', 'participants', 'id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('event_id', 'events', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('participant_checkins', true);
        }
    }

    public function down()
    {
        $this->forge->dropTable('participant_checkins', true);
    }
}
