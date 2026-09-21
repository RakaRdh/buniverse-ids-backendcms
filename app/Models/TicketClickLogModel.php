<?php

namespace App\Models;

use CodeIgniter\Model;

class TicketClickLogModel extends Model
{
    protected $table            = 'ticket_click_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'event_id',
        'clicked_at',
        'user_agent',
        'referrer',
    ];

    protected $useTimestamps = false;
}
