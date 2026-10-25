<?php

namespace App\Services;

class FactRepository
{
    private string $file;

    public function __construct()
    {
        $this->file = storage_path('app/facts.json');
    }

    public function all(): array
    {
        if (!file_exists($this->file)) {
            return [];
        }
        return json_decode(file_get_contents($this->file), true) ?? [];
    }

    public function find(int $id): ?array
    {
        foreach ($this->all() as $item) {
            if ($item['id'] === $id) {
                return $item;
            }
        }
        return null;
    }

    public function existsByText(string $text): bool
    {
        foreach ($this->all() as $item) {
            if (mb_strtolower($item['text']) === mb_strtolower($text)) {
                return true;
            }
        }
        return false;
    }

    public function create(array $data): array
    {
        $items = $this->all();
        $ids = array_column($items, 'id');

        $item = $data;
        $item['id'] = $ids ? max($ids) + 1 : 1;
        $item['likes_count'] = 0;
        $item['dislikes_count'] = 0;
        $item['created_at'] = date('c');

        $items[] = $item;
        $json = json_encode($items, JSON_UNESCAPED_UNICODE);
        file_put_contents($this->file, $json, LOCK_EX);

        return $item;
    }
}