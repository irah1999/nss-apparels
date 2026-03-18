<?php

namespace App\Models;

use CodeIgniter\Model;

class BulkImportModel extends Model
{
    protected $table            = 'bulk_imports';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['file_name', 'filepath', 'total_count', 'inserted_count', 'failed_count', 'status'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
}
