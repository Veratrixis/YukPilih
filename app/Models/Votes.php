<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Votes extends Model
{
    use HasFactory;

    protected $table = 'votes';
    protected $primary_key = 'id';
    protected $fillable = [
        'id',
        'choice_id',
        'user_id',
        'poll_id',
        'division_id',
    ];

    public function get_votes() {
        return self::all();
    }
    public function read_votes($id) {
        return self::find($id);
    }
    public function create_votes($data) {
        return self::create($data);
    }
    public function update_votes($id, $data) {
        $votes = self::find($id);
        $votes->fill($data);
        $votes->update();
        return $votes;
    }
    public function delete_votes($id) {
        $votes = self::find($id);
        self::destroy($id);
        return $votes;
    }
    public function getVoteByUserAndPoll($userId, $pollId)
    {
        return $this->where('user_id', $userId)
                    ->where('poll_id', $pollId)
                    ->first();
    }
    public function getVotesResults($pollId) {
        $choices = Choices::where('poll_id', $pollId)->get();
        $votes = Votes::whereIn('choice_id', $choices->pluck('id'))->where('poll_id', $pollId)->get();
        $totalValue = 0;
        $results = [];

        if (count($choices) > 0) {
            $totalValue = 1 / count($choices);
        }
        
        foreach ($choices as $choice) {
            $point = 0;

            foreach ($votes as $vote) {
                if ($vote->choice_id == $choice->id) {
                    if ($vote->division_id != 0) {
                        $point += 1 / ($vote->division_id);
                    }
                }
            }

            $results[] = [
                'total' => $totalValue,
                'point' => $point,
            ];
        }

        return $results;
    }
}