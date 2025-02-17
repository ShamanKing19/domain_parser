<?php

namespace App\Services;

use App\Models\WebsiteType;

class WebsiteTypeService
{
    /**
     * Присоединение ключевых слов к типу
     *
     * @param WebsiteType $type
     * @param array $keywords
     *
     * @return void
     */
    public function attachKeywords(WebsiteType $type, array $keywords): void
    {
        $type->keywords()->delete();
        foreach ($keywords as $keyword) {
            $type->keywords()->create([
                'type_id' => $type->id,
                'word' => $keyword
            ]);
        }
    }

    public function delete(int $id): bool
    {
        return WebsiteType::destroy($id) > 0;
    }

    public function create(array $fields): WebsiteType|null
    {
        return WebsiteType::create($fields);
    }

    public function createOrUpdate(array $fields): WebsiteType|null
    {
        if (!empty($fields['id'])) {
            return $this->update($fields);
        }

        return $this->create($fields);
    }

    public function update(array $fields): WebsiteType|null
    {
        $type = WebsiteType::find($fields['id']);
        if (is_null($type)) {
            return null;
        }

        $type->update($fields);

        return $type;
    }
}
