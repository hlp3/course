<?php

namespace App\Http\Controllers;

use App\Services\FactRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class FactController extends Controller
{
    public function __construct(private FactRepository $facts)
    {
    }

    // GET /api/facts
    public function index(Request $request)
    {
        $categoryId = $request->query('category_id');

        $items = $this->facts->all();

        if ($categoryId !== null) {
            $items = array_values(array_filter(
                $items,
                fn ($item) => (int) $item['category_id'] === (int) $categoryId
            ));
        }

        return ['data' => $items];
    }

    // GET /api/facts/{id}
    public function show(string $id)
    {
        $fact = $this->facts->find((int) $id)
            ?? abort(404, 'Факт не найден.');

        return ['data' => $fact];
    }

    // POST /api/facts
    public function store(Request $request)
    {
        $data = $request->validate([
            'text' => ['required', 'string', 'min:10', 'max:500'],
            'category_id' => ['required', 'integer', 'between:1,20'],
            'source' => ['nullable', 'string', 'max:255', 'url'],
        ], [
            'text.required' => 'Укажите текст факта.',
            'text.min' => 'Текст факта — не короче 10 символов.',
            'text.max' => 'Текст факта — не длиннее 500 символов.',
            'category_id.required' => 'Укажите категорию.',
            'category_id.between' => 'Такой категории не существует.',
            'source.url' => 'Ссылка на источник должна быть корректным URL.',
        ]);

        if ($this->facts->existsByText($data['text'])) {
            throw ValidationException::withMessages([
                'text' => 'Такой факт уже есть в базе.',
            ]);
        }

        $fact = $this->facts->create($data);

        return response()->json(['data' => $fact], 201);
    }
}