<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTranslationRequest;
use App\Http\Requests\UpdateTranslationRequest;
use App\Services\TranslationService;
use Illuminate\Http\Request;

class TranslationController extends Controller
{

    public function __construct(private TranslationService $service)
    {

    }
   
    public function get($context, $locale)
    {
        $response = $this->service->show($context, $locale);
         return response()->sendResponse($response->getResponseType(), $response->code(), $response->message(), $response->getData());
    }

    public function create(CreateTranslationRequest $request)
    {
        $response = $this->service->createTranslation($request->validated());
        return response()->sendResponse($response->getResponseType(), $response->code(), $response->message(), $response->getData());
    }

    public function update(UpdateTranslationRequest $request)
    {
        $response = $this->service->updateTranslation($request->validated());
        return response()->sendResponse($response->getResponseType(), $response->code(), $response->message(), $response->getData());
    }

    public function search($keyword)
    {
        $response = $this->service->search($keyword);
        return response()->sendResponse($response->getResponseType(), $response->code(), $response->message(), $response->getData());

    }
}
