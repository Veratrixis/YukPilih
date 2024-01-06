<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Divisions extends Model
{
    use HasFactory;

    protected $table = 'divisions';
    protected $primary_key = 'id';
    protected $fillable = [
        'id',
        'name',
    ];

    public function get_divisions() {
        return self::all();
    }
    public function read_divisions($id) {
        return self::find($id);
    }
    public function create_divisions($data) {
        return self::create($data);
    }
    public function update_divisions($id, $data) {
        $divisions = self::find($id);
        $divisions->fill($data);
        $divisions->update();
        return $divisions;
    }
    public function delete_divisions($id) {
        $divisions = self::find($id);
        self::destroy($id);
        return $divisions;
    }
}