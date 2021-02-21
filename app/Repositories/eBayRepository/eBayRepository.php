<?php


namespace App\Repositories\eBayRepository;

use Illuminate\Support\Facades\Http;


class eBayRepository implements eBayRepositoryInterface
{

    /**
     * Create a new user node
     *
     * @param $data
     * @return mixed
     */
    public function searchEBay($data)
    {

        // declare properties for api call
        $appId = env('APP_ID');
        $baseUrl = env('BASE_URL');
        $operation = 'X-EBAY-SOA-OPERATION-NAME=findItemsAdvanced';
        $responseType = 'RESPONSE-DATA-FORMAT=JSON';
        $appName = "X-EBAY-SOA-SECURITY-APPNAME={$appId}";
        $keywords = "keywords={$data['keywords']}";
        $firstFilter = "itemFilter(0).name=MaxPrice&itemFilter(0).value={$data['price_max']}";
        $secondFilter = "itemFilter(1).name=MinPrice&itemFilter(1).value={$data['price_min']}";
        $order = "sortOrder={$data['sortOrder']}";
        $itemsPerPage = "paginationInput.entriesPerPage={$data['itemsPerPage']}";
        $page = "paginationInput.pageNumber={$data['page']}";
        $outputSelector = 'outputSelector=PictureURLSuperSize';

        // create url based on properties
        $url = "{$baseUrl}?{$operation}&{$responseType}&$appName&$keywords&{$firstFilter}&$secondFilter&$order&$itemsPerPage&$page&$outputSelector";

        // make request to ebay service
        $response = json_decode(Http::get($url));

        // array to save mapped response
        $mappedResponse = array();

        // if there are not results return json with message
        if ($response->findItemsAdvancedResponse[0]->paginationOutput[0]->totalEntries[0] === "0") {
            return response()->json(['error' => false, 'message' => 'There is no search result']);
        }
        // loop response and create mapped array
        else {

            foreach ($response->findItemsAdvancedResponse[0]->searchResult[0]->item as $item) {
                $object = [
                    'provider' => 'ebay',
                    'item_id' => $item->itemId[0],
                    'click_out_link' => $item->viewItemURL[0],
                    'main_photo_url' => isset($item->pictureURLSuperSize[0]) ? $item->pictureURLSuperSize[0] : "",
                    'price' => $item->sellingStatus[0]->currentPrice[0]->__value__,
                    'price_currency' => $item->sellingStatus[0]->currentPrice[0]->{'@currencyId'},
                    'shipping_price;' => $item->shippingInfo[0]->shippingServiceCost[0]->__value__,
                    'title' => $item->title[0],
                    'description' => isset($item->description[0]) ? $item->description[0] : "",
                    'valid_until' => $item->listingInfo[0]->endTime[0],
                    'brand' => isset($item->brand[0]) ? $item->brand[0] : "",
                ];
                array_push($mappedResponse, $object);
            }

            // create json for response
            $data = [
                'error' => false,
                'message' => 'User Search Result',
                'data' => $mappedResponse,
            ];

            // create json
            return response()->json($data, 200);
        }

    }


}
