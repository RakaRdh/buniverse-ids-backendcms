<?php

namespace App\Models;

use CodeIgniter\Model;

class GalleryImageModel extends Model
{
    protected $table            = 'gallery_images';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'event_id',
        'image_url',
        'caption',
        'is_featured',
        'sort_order',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getByEvent(int $eventId)
    {
        return $this->where('event_id', $eventId)
            ->orderBy('sort_order', 'ASC')
            ->findAll();
    }
}
