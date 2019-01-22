<?php

namespace App\Http\Controllers;

use Http\Client\Exception;
use Illuminate\Http\Request;
use xPaw\SourceQuery\SourceQuery;

class SourceQueryController extends Controller
{
    protected $server_timeout = 1;
    protected $server_engine = SourceQuery::SOURCE;

    public function test(Request $request)
    {
        $server = 'B4';
        if ($request->has('server')) {
            $server = strtoupper($request->get('server'));
        }

        $coord = new CoordinateController([
            1,
            15,
        ]);
        dd($coord->getSurrounding(), $this->buildIps());
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param string                   $server
     *
     * @return array|bool|string
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

        return $this->getPlayers($server_connection['ip'], $server_connection['port']);
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
        $max_x   = explode('x', $servers['size'])[0];
        $max_y   = explode('x', $servers['size'])[1];

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
        for ($x = 1; $x <= $max_x; $x++) {
            $character          = chr($x + 64);
            $return[$character] = array();
            for ($y = 1; $y <= $max_y; $y++) {
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
}
