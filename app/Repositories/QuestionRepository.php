<?php

namespace App\Repositories;

use App\Helpers\Helper;
use App\Models\Question;
use App\Repositories\Interfaces\QuestionRepositoryInterface;

class QuestionRepository implements QuestionRepositoryInterface
{
    public function all()
    {
        return Question::with('category')->get();
    }


    public function find($id)
    {
        return Question::find($id);
    }

    public function create(array $data)
    {

        $data['slug'] = Helper::makeSlug(Question::class, $data['question']);

        Question::create($data);
    }

    public function update($id, array $data)
    {
        $question = Question::findOrFail($id);

        $question->update($data);
    }

    public function delete($id)
    {
        $data = Question::findOrFail($id);
        $data->delete();
    }
    public function status($id)
    {
        $data = Question::findOrFail($id);
        if (!$data) {
            return response()->json([
                'status' => 't-error',
                'message' => 'Item not found.',
            ]);
        }
        $data->status = $data->status === 'active' ? 'inactive' : 'active';
        $data->save();
    }
}
