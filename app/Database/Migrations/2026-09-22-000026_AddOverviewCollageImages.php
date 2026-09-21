<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOverviewCollageImages extends Migration
{
    public function up()
    {
        $fields = [
            'collage_img_1' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
                'after'      => 'body_text',
            ],
            'collage_img_2' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
                'after'      => 'collage_img_1',
            ],
            'collage_img_3' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
                'after'      => 'collage_img_2',
            ],
            'collage_img_4' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
                'after'      => 'collage_img_3',
            ],
        ];

        if ($this->db->tableExists('event_overview')) {
            $this->forge->addColumn('event_overview', $fields);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('event_overview')) {
            $this->forge->dropColumn('event_overview', [
                'collage_img_1',
                'collage_img_2',
                'collage_img_3',
                'collage_img_4',
            ]);
        }
    }
}
