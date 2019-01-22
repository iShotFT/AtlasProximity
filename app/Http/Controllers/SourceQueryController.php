<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Http\Client\Exception;
use xPaw\SourceQuery\SourceQuery;
use Illuminate\Http\Request;

class SourceQueryController extends Controller
{
    protected $server_timeout = 1;
    protected $server_engine = SourceQuery::SOURCE;

    /**
     * @param \Illuminate\Http\Request $request
     * @param string                   $server
     *
     * @throws \xPaw\SourceQuery\Exception\InvalidArgumentException
     * @throws \xPaw\SourceQuery\Exception\InvalidPacketException
     * @throws \xPaw\SourceQuery\Exception\TimeoutException
     */
    public function serverGetPlayers(Request $request, $server = 'A1')
    {
        if ($request->has('server')) {
            $server = strtoupper($request->get('server'));
        }

        $server_y = substr($server, 0, 1); // A
        $server_x = substr($server, 1, strlen($server) - 1); // 15

        $server_connection = $this->buildIps()[$server_y][$server_x];

        dd($this->getPlayers($server_connection['ip'], $server_connection['port']));
    }

    /**
     * @param string $region
     * @param string $mode
     *
     * @return array
     */
    public function buildIps($region = 'eu', $mode = 'pvp')
    {
        $servers = config('atlas.servers.' . $region . '.' . $mode);

        $servers_list = array();
        foreach ($servers['ip'] as $ip) {
            foreach ($servers['port'] as $port) {
                array_push($servers_list, [
                    'ip'   => $ip,
                    'port' => $port,
                ]);
            }
        }

        $return     = array();
        $itteration = 0;
        for ($x = 1; $x <= 15; $x++) {
            $character          = chr($x + 64);
            $return[$character] = array();
            for ($y = 1; $y <= 15; $y++) {
                $return[$character][$y] = $servers_list[$itteration];
                $itteration++;
            }
        }

        return $return;

    }

    /**
     * @param string $ip
     * @param string $port
     *
     * @return array|bool|string
     * @throws \xPaw\SourceQuery\Exception\InvalidArgumentException
     * @throws \xPaw\SourceQuery\Exception\InvalidPacketException
     * @throws \xPaw\SourceQuery\Exception\TimeoutException
     */
    public function getPlayers($ip = '46.251.238.59', $port = '57555')
    {
        $Query  = new SourceQuery();
        $return = '';
        try {
            $Query->Connect($ip, $port, $this->server_timeout, $this->server_engine);
            $return = $Query->GetPlayers();
        } catch (Exception $e) {
            dump($e->getMessage());
        }
        finally {
            $Query->Disconnect();
        }

        return $return;
    }

    public function getSurroundingServers($center = 'A1', $region = 'eu', $mode = 'pvp')
    {
        $size   = explode('x', config('atlas.servers.' . $region . '.' . $mode . '.size'));
        $size_x = $size[0];
        $size_y = $size[1];

        
    }
}
