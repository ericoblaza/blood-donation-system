<?php
// starting point for table mapping, fillable fields, relationships

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;

abstract class Model extends EloquentModel
{
    protected $guarded = [];
}
