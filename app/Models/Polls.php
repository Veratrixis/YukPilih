<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Polls extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'polls';
    protected $primary_key = 'id';
    protected $fillable = [
        'id',
        'title',
        'description',
        'deadline',
        'created_by',
    ];
    protected $hidden = [
        'created_by',
    ];
    
    public function get_polls() {
        return self::all();
    }
    public function read_polls($id) {
        return self::find($id);
    }
    public function create_polls($data) {
        return self::create($data);
    }
    public function update_polls($id, $data) {
        $polls = self::find($id);
        $polls->fill($data);
        $polls->update();
        return $polls;
    }
    public function delete_polls($id) {
        $polls = self::find($id);
        self::destroy($id);
        return $polls;
    }
}