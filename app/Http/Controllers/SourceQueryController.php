<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Http\Client\Exception;
use xPaw\SourceQuery\SourceQuery;
use Illuminate\Http\Request;

class SourceQueryController extends Controller
{
    protected $server_ip = '46.251.238.65';
    protected $server_port = 57555;
    protected $server_timeout = 1;
    protected $server_engine = SourceQuery::SOURCE;

    public function test(Request $request)
    {
        $Query    = new SourceQuery();
        $input_ip = config('atlas.test.eu');

        $servers = array();
        for ($i = 1; $i <= 255; $i++) {
            array_push($servers, $input_ip[0] . '.' . $i);
        }

        $server_info = array();
        foreach ($servers as $ip) {
            try {
                $Query->Connect($ip, $this->server_port, $this->server_timeout, $this->server_engine);
                $server_info[$ip] = $Query->GetRules();
            } catch (Exception $e) {
                dump($e->getMessage());
            }
            finally {
                $Query->Disconnect();
            }
        }
        //
        $server_info_filtered = array_filter($server_info, function ($v, $k) {
            return $v['ATLASFRIENDLYNAME_s'] === '[EU PVP] The Whale\'s Wrath';
        }, ARRAY_FILTER_USE_BOTH);

        $new_array = array();
        foreach ($server_info_filtered as $ip => $info) {
            $new_array[$ip] = $info['CUSTOMSERVERNAME_s'];
        }


        file_put_contents(storage_path('logs/atlas/debug-' . Carbon::now()->timestamp), print_r($server_info, true));
        file_put_contents(storage_path('logs/atlas/debug-' . Carbon::now()->timestamp . '-filtered'), print_r($server_info_filtered, true));
        file_put_contents(storage_path('logs/atlas/debug-' . Carbon::now()->timestamp . '-newarray'), print_r($new_array, true));
        dd($new_array);
    }
}
