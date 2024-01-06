<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Choices extends Model
{
    use HasFactory;

    protected $table = 'choices';
    protected $primary_key = 'id';
    protected $fillable = [
        'id',
        'choice',
        'poll_id',
    ];

    public function get_choices() {
        return self::all();
    }
    public function read_choices($id) {
        return self::find($id);
    }
    public function create_choices($data) {
        return self::create($data);
    }
    public function update_choices($id, $data) {
        $choices = self::find($id);
        $choices->fill($data);
        $choices->update();
        return $choices;
    }
    public function delete_choices($id) {
        $choices = self::find($id);
        self::destroy($id);
        return $choices;
    }
    public function getChoicesByPollId($pollId)
    {
        return $this->where('poll_id', $pollId)->get();
    }
}