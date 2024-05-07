<?php

namespace Core\Profile\Infrastructure\Persistence\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Privileges extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'privileges';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'pri_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'pri__pro_id',
        'pri__mod_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
