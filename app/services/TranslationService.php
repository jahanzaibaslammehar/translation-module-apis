<?php
namespace App\Services;

use App\Core\Contracts\Services\TranslationServiceInterface;
use App\Core\Services\AbstractService;
use App\Helpers\ResponseCode;
use App\Http\Resources\TranslationResource;
use App\Repositories\TranslationRepository;
use App\Responses\TranslationResponse;
use Illuminate\Http\Request;

class TranslationService extends AbstractService implements TranslationServiceInterface
{
    public function __construct(TranslationRepository $repository, TranslationResponse $response,   Request $request)
    {
        $this->repository = $repository;
        $this->response = $response;
        $this->request = $request;
    }

    public function show($context, $locale)
    {
        $translation = $this->repository->findOne(['context' => $context, 'locale' => $locale]);

        $translationResource = new TranslationResource($translation);

        $this->response->setResponse(ResponseCode::SUCCESS, 200, $this->response->getListResponseMessage(), $translationResource->toArray($this->request));

        return $this->response;
    }

    public function createTranslation(array $data)
    {
        $translation = $this->create($data);

        $translationResource = new TranslationResource($translation);

          $this->response->setResponse(ResponseCode::SUCCESS, 200, $this->response->getCreateResponseMessage(), $translationResource->toArray($this->request));


        return $this->response;

    }

    public function updateTranslation(array $data)
    {
        $translation = $this->update($data);

        $translationResource = new TranslationResource($translation);

          $this->response->setResponse(ResponseCode::SUCCESS, 200, $this->response->getUpdateResponseMessage(), $translationResource->toArray($this->request));

        return $this->response;
    }

    public function search($keyword)
    {
        $translations = $this->repository->search($keyword);

        $translationResources = TranslationResource::collection($translations);

        // For paginated results, we need to preserve the pagination structure
        if ($translations instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator) {
            // Convert paginated results to array format with pagination metadata
            $data = [
                'data' => $translationResources->toArray($this->request),
                'current_page' => $translations->currentPage(),
                'per_page' => $translations->perPage(),
                'total' => $translations->total(),
                'last_page' => $translations->lastPage(),
                'from' => $translations->firstItem(),
                'to' => $translations->lastItem(),
            ];
            $this->response->setResponse(ResponseCode::SUCCESS, 200, $this->response->getListResponseMessage(), $data);
        } else {
            $this->response->setResponse(ResponseCode::SUCCESS, 200, $this->response->getListResponseMessage(), $translationResources->toArray($this->request));
        }

        return $this->response;
    }
}