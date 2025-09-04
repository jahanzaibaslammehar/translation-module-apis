<?php
namespace App\Repositories;

use App\Core\Repositories\AbstractRepository;
use App\Models\Translation;

class TranslationRepository extends AbstractRepository {
    
    public function __construct(Translation $model) {

        $this->model = $model;

    }

    public function search($keyword)
    {
        $query = $this->model->where(function($q) use ($keyword) {
            $q->where('context', 'like', "%$keyword%")
              ->orWhere('locale', 'like', "%$keyword%");
        });

        // For testing purposes, we'll also search within the JSON content
        // This works with SQLite and other databases
        $translations = $this->model->all();
        $matchingIds = [];
        
        foreach ($translations as $translation) {
            $translationsArray = $translation->translations;
            if (is_string($translationsArray)) {
                $translationsArray = json_decode($translationsArray, true);
            }
            
            if (is_array($translationsArray)) {
                foreach ($translationsArray as $key => $value) {
                    // Ensure both key and value are strings before using stripos
                    $keyStr = is_string($key) ? $key : (string)$key;
                    $valueStr = is_string($value) ? $value : (string)$value;
                    
                    if (stripos($keyStr, $keyword) !== false || stripos($valueStr, $keyword) !== false) {
                        $matchingIds[] = $translation->id;
                        break;
                    }
                }
            }
        }
        
        if (!empty($matchingIds)) {
            $query->orWhereIn('id', $matchingIds);
        }

        return $query->paginate(100);
    }
}