<?php


namespace App\Http\Model;


class Role extends \Spatie\Permission\Models\Role
{
    public $fillable = [
        "name",
        "guard_name",
        "alias"
    ];

    protected $casts = [
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
