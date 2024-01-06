<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Users;
use App\Models\Choices;
use App\Models\Polls;
use App\Models\Votes;

class PollsController extends Controller
{
    protected $usersModel;
    protected $choicesModel;
    protected $pollsModel;
    protected $votesModel;

    public function __construct()
    {
        $this->usersModel = new Users();
        $this->choicesModel = new Choices();
        $this->pollsModel = new Polls();
        $this->votesModel = new Votes();
    }

    public function index()
    {
        $polls = Cache::remember('polls', now()->addMinutes(5), function () {
            return $this->pollsModel->get_polls();
        });

        if (count($polls) === 0) {
            return response()->json([], 204);
        }

        $data = $this->mapPollsData($polls);

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mendapat data',
            'data' => $data
        ], 200)->header('Cache-Control', 'public, max-age=300');
    }

    public function show($id)
    {
        $poll = $this->pollsModel->read_polls($id);

        if (!$poll) {
            return response()->json([
                'status' => 422,
                'message' => 'Gagal mendapat data'
            ], 422);
        }

        $choices = $this->choicesModel->getChoicesByPollId($id);
        $results = $this->votesModel->getVotesResults($id);
        $creators = $this->usersModel->read_user($poll->created_by);

        $data = $this->mapPollData($poll, $choices, $results, $creators);

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mendapat data',
            'data' => $data
        ], 200);
    }
    public function store(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
    
            if ($user->role !== 'Admin') {
                return response()->json([
                    'status' => 401,
                    'message' => 'Unauthorized. Only users with the "Admin" role can create polls.'
                ], 401);
            }
    
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:191',
                'description' => 'required|string',
                'deadline' => 'required|date',
                'choices' => 'required|array|min:2',
                'choices.*' => 'required|string',
            ]);
    
            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }
    
            return DB::transaction(function () use ($validator, $user) {
                $validatedData = $validator->validated();
                $validatedData['created_by'] = $user->id;
    
                $poll = $this->pollsModel->create_polls($validatedData);
    
                foreach ($validatedData['choices'] as $choice) {
                    $choicesData = ['choice' => $choice, 'poll_id' => $poll->id];
                    $this->choicesModel->create_choices($choicesData);
                }
    
                Cache::forget('polls');
    
                return $this->successResponse('Berhasil menambah data', $poll);
            });
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return $this->tokenErrorResponse('Access Token Expired', 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return $this->tokenErrorResponse('Access Token Invalid', 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return $this->tokenErrorResponse('Access Token Absent', 401);
        }
    }    

    public function update($id, Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $poll = $this->pollsModel->read_polls($id);

        if (!$poll || $poll->created_by !== $user->id) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthorized. Only users with the "Admin" role can update polls.'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:191',
            'description' => 'required|string',
            'deadline' => 'required|date',
            'created_by' => 'required',
            'choices' => 'required|array|min:2',
            'choices.*.id' => 'required|exists:choices,id',
            'choices.*.choice' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        return DB::transaction(function () use ($id, $validator) {
            $poll = $this->pollsModel->update_polls($id, $validator->validated());

            foreach ($validator->validated()['choices'] as $choiceData) {
                $this->choicesModel->update_choices($choiceData['id'], ['choice' => $choiceData['choice']]);
            }

            Cache::forget('polls');

            return $this->successResponse('Berhasil mengubah data', $poll);
        });
    }

    public function destroy($id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $poll = $this->pollsModel->read_polls($id);

        if (!$poll || $poll->created_by !== $user->id) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthorized. Only users with the "Admin" role can delete polls.'
            ], 401);
        }

        $polls = $this->pollsModel->delete_polls($id);

        Cache::forget('polls');

        return $this->successResponse('Berhasil menghapus data', $poll);
    }

    public function vote(Request $request, $poll_id, $choice_id)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return $this->tokenErrorResponse('Token has expired', 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return $this->tokenErrorResponse('Token is invalid', 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return $this->tokenErrorResponse('Token is absent', 401);
        }

        if ($user->role !== 'User') {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthorized. Only users with the "User" role can vote.'
            ], 401);
        }

        $existingVote = $this->votesModel->getVoteByUserAndPoll($user->id, $poll_id);

        if ($existingVote) {
            return response()->json([
                'status' => 422,
                'message' => 'Already voted.'
            ], 401);
        }

        $poll = $this->pollsModel->read_polls($poll_id);

        if (!$poll) {
            if ($existingVote) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Poll not found.'
                ], 401);
            }
        }

        $now = now();
        if ($poll->deadline <= $now) {
            if ($existingVote) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Poll Deadline.'
                ], 401);
            }
        }

        $choice = $this->choicesModel->read_choices($choice_id);

        if (!$choice || $choice->poll_id !== $poll_id) {
            if ($existingVote) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Invalid Choice.'
                ], 401);
            }
        }

        $data = [
            'choice_id' => $choice_id,
            'user_id' => $user->id,
            'poll_id' => $poll_id,
            'division_id' => $user->division_id
        ];
        
        $this->votesModel->create_votes($data);

        return $this->successResponse('Vote recorded successfully');
    }
    private function mapPollsData($polls)
    {
        $data = [];

        foreach ($polls as $poll) {
            $choices = $this->choicesModel->getChoicesByPollId($poll->id);
            $results = $this->votesModel->getVotesResults($poll->id);
            $creators = $this->usersModel->read_user($poll->created_by);

            $data[] = $this->mapPollData($poll, $choices, $results,  $creators);
        }

        return $data;
    }

    private function mapPollData($poll, $choices = [], $results = [], $creators)
    {
        return [
            'id' => $poll->id,
            'title' => $poll->title,
            'description' => $poll->description,
            'deadline' => $poll->deadline,
            'created_by' => $poll->created_by,
            'creator' => $creators->username,
            'created_at' => $poll->created_at,
            'updated_at' => $poll->updated_at,
            'deleted_at' => $poll->deleted_at,
            'choices' => $choices,
            'results' => $results,
        ];
    }
    private function tokenErrorResponse($message, $statusCode)
    {
        return response()->json([
            'status' => $statusCode,
            'message' => $message,
        ], $statusCode);
    }
    private function successResponse($message, $data = null, $statusCode = 200)
    {
        $response = [
            'status' => $statusCode,
            'message' => $message,
        ];
        if (!is_null($data)) {
            $response['data'] = $data;
        }
        return response()->json($response, $statusCode);
    }
    private function validationErrorResponse($errors)
    {
        return response()->json([
            'status' => 422,
            'message' => 'Error Params',
            'errors' => $errors,
        ], 422);
    }
}