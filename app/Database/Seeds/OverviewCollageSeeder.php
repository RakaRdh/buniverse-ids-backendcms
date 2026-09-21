<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class OverviewCollageSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'collage_img_1' => 'uploads/events/1/overview/gallery1.webp',
            'collage_img_2' => 'uploads/events/1/overview/gallery2.webp',
            'collage_img_3' => 'uploads/events/1/overview/gallery3.webp',
            'collage_img_4' => 'uploads/events/1/overview/gallery4.webp',
        ];

        $this->db->table('event_overview')->where('event_id', 1)->update($data);
        echo "Overview collage images seeded successfully.\n";
    }
}
