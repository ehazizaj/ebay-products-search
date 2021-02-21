<?php

namespace App\Http\Controllers;

use App\Repositories\eBayRepository\eBayRepositoryInterface;
use App\Requests\eBay\SearchEbayProducts;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class eBay extends Controller
{
    /**
     * @var $repository
     */
    private $repository;

    /**
     * eBayRepositoryInterface constructor.
     * @param eBayRepositoryInterface $repository
     */
    public function __construct(eBayRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Store a newly created node.
     *
     * @param SearchEbayProducts $request
     * @return JsonResponse
     */
    public function search(SearchEbayProducts $request): JsonResponse
    {
        $validator = $this->validator($request);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }
        $data = $request->all();
        return ($this->repository->searchEBay($data));
    }

    /**
     * Validate Request before proceeding to business logic.
     *
     * @param SearchEbayProducts $request
     * @return  \Illuminate\Contracts\Validation\Validator
     */
    public function validator(SearchEbayProducts $request): \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($request->all(), [
            'keywords' => ['required'],
            'price_min' => ['numeric'],
            'price_max' => ['numeric'],
            'itemsPerPage' => ['required', 'numeric'],
            'page' => ['required', 'numeric'],
        ]);
    }

}
