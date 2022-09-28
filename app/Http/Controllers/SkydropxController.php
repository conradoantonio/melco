<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Carbon\Carbon;

class SkydropxController extends Controller
{
    // Production Credentials
    private $apiKey = "QFkZqkFAu9RWrDPVbaWB94fHLZwIifss2Ds2lzFRV58t";

    // Pre-Production URL
    private $uri = "https://api-demo.skydropx.com/";

    // Production URL
    // private $uri = "https://api.hotelbeds.com/";


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $request_uri = "{$this->uri}v1/labels";
        $client = new Client();
        $action = "GET";
        $title = "/labels";

        $response = $client->request($action, $request_uri, [
            'headers'        => [
                        'Access-Control-Request-Method' => 'POST, GET, OPTIONS, DELETE',
                        'Access-Control-Allow-Origin' => '*',
                        'Access-Control-Allow-Headers' => 'x-requested-with, Content-Type',
                        'Api-key' => $this->apiKey,
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'Accept-Encoding' => 'gzip',
                        'Authorization' => 'Token token="QFkZqkFAu9RWrDPVbaWB94fHLZwIifss2Ds2lzFRV58t"'
            ],
            'allow_redirects' => true,
            'timeout' => 2000,
            'http_errors' => true,
            'connect_timeout' => 10
        ]);
        // $response = $request->send();

        $data = $response->getBody();
        // $data = json_encode($stream->getContents(), JSON_PRETTY_PRINT);
        $status_code = $response->getStatusCode();

        $dataObject = new \stdClass();
        $dataObject -> request_uri = $request_uri;
        $dataObject -> data = json_decode($data);
        $dataObject -> status_code = $status_code;
        $dataObject -> action = $action;
        $dataObject -> title = $title;

        // dd($dataObject->data->data);

        $labels = $dataObject->data->data;

        // dd($labels);

        $title = "Guías Skydropx";
        $menu = "Guías Skydropx";

        return view('skydropx.index', compact('title', 'menu', 'labels'));
    }

    public function labels()
    {
        $request_uri = "{$this->uri}v1/labels";
        $client = new Client();
        $action = "GET";
        $title = "/labels";

        $response = $client->request($action, $request_uri, [
            'headers'        => [
                        'Access-Control-Request-Method' => 'POST, GET, OPTIONS, DELETE',
                        'Access-Control-Allow-Origin' => '*',
                        'Access-Control-Allow-Headers' => 'x-requested-with, Content-Type',
                        'Api-key' => $this->apiKey,
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'Accept-Encoding' => 'gzip',
                        'Authorization' => 'Token token="QFkZqkFAu9RWrDPVbaWB94fHLZwIifss2Ds2lzFRV58t"'
            ],
            'allow_redirects' => true,
            'timeout' => 2000,
            'http_errors' => true,
            'connect_timeout' => 10
        ]);
        // $response = $request->send();

        $data = $response->getBody();
        // $data = json_encode($response->getContents(), JSON_PRETTY_PRINT);
        // dd($data);

        // dd(json_decode($data));
        $status_code = $response->getStatusCode();

        $dataObject = new \stdClass();
        $dataObject -> request_uri = $request_uri;
        $dataObject -> data = $data;
        $dataObject -> status_code = $status_code;
        $dataObject -> action = $action;
        $dataObject -> title = $title;

        return $dataObject;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

      /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }
     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function quotations(Request $request)
    {
        try {
            $request_uri = "{$this->uri}v1/quotations";
            $client = new Client();
            $action = "POST";
            $title = "/quotations"; // Quotations

            $request_body = $request->all();

            $response = $client->post( $request_uri, [
                 'headers' => [
                            'Accept' => 'application/json',
                            'Content-Type' => 'application/json',
                            'Accept-Encoding' => 'gzip',
                            'Authorization' => 'Token token="QFkZqkFAu9RWrDPVbaWB94fHLZwIifss2Ds2lzFRV58t"'
                ],
                'json' => $request_body,
                'allow_redirects' => true,
                'timeout' => 2000,
                'http_errors' => true,
                'connect_timeout' => 10
            ]);
            $data = $response->getBody();
            $status_code = $response->getStatusCode();
        } catch (RequestException $e) {
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->getResponse()->getStatusCode() == '400') {
                $status_code = "400";
            }
            //dd($e->getResponseBodySummary($e->getResponse()));
        } catch (\Exception $e) {
            // There was another exception.
            $status_code = "400";
            //dd($e);
        }
        // dd(json_encode($data));

        $dataObject = new \stdClass();
        $dataObject -> request_uri = $request_uri;
        $dataObject -> request_body = $request_body;
        $dataObject -> data = $data;
        $dataObject -> status_code = $status_code;
        $dataObject -> action = $action;
        $dataObject -> title = $title;

        return $dataObject;
    }

    public function shipments(Request $request, $request_body)
    {
        dd($request->all());
        // dd($request_body);
        $request_body = json_encode($request_body);

        try {
            $client = new \GuzzleHttp\Client();
            

            $request_body = $request->all();
        
            $r = $client->request('POST', $request_uri, [
                'headers'        => [
                            'Access-Control-Request-Method' => 'POST, GET, OPTIONS, DELETE',
                            'Access-Control-Allow-Origin' => '*',
                            'Access-Control-Allow-Headers' => 'x-requested-with, Content-Type',
                            'Api-key' => $this->apiKey,
                            'Accept' => 'application/json',
                            'Content-Type' => 'application/json',
                            'Accept-Encoding' => 'gzip',
                            'Authorization' => 'Token token="QFkZqkFAu9RWrDPVbaWB94fHLZwIifss2Ds2lzFRV58t"'
                ],
                'body' => $request_body
            ]);

            // dd($r);

            // $response = $client->post( $request_uri, [
            //      'headers'        => [
            //                 'Access-Control-Request-Method' => 'POST, GET, OPTIONS, DELETE',
            //                 'Access-Control-Allow-Origin' => '*',
            //                 'Access-Control-Allow-Headers' => 'x-requested-with, Content-Type',
            //                 'Api-key' => $this->apiKey,
            //                 'Accept' => 'application/json',
            //                 'Content-Type' => 'application/json',
            //                 'Accept-Encoding' => 'gzip',
            //                 'Authorization' => 'Token token="QFkZqkFAu9RWrDPVbaWB94fHLZwIifss2Ds2lzFRV58t"'
            //     ],
            //     'json' => $request_body,
            //     'allow_redirects' => true,
            //     'timeout' => 200,
            //     'http_errors' => true,
            //     'connect_timeout' => 10
            // ]);

            return $response;
            // $data = $response->getBody();
            $status_code = $response->getStatusCode();

            // dd(json_encode($data));

        } catch (RequestException $e) {
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->getResponse()->getStatusCode() == '400') {
                $status_code = "400";
            }
            //dd($e->getResponseBodySummary($e->getResponse()));
        } catch (\Exception $e) {
            // There was another exception.
            $status_code = "400";
            //dd($e);
        }

        // $dataObject = new \stdClass();
        // $dataObject->request_uri = $request_uri;
        // $dataObject->request_body = $request_body;
        // $dataObject->data = $data;
        // $dataObject->status_code = $status_code;
        // $dataObject->action = $action;
        // $dataObject->title = $title;

        // return response()->json(['dataObject' => $dataObject], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
