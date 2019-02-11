<?php

namespace App\Console\Commands;

use App\Classes\Coordinate;
use App\Ping;
use GameQ\GameQ;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ScanRegion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'atlascctv:scanregion
        {region=eu : The region of the target servers that need to be scanned} 
        {gamemode=pvp : The gamemode of the target servers that need to be scanned}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan all servers in a certain region of a certain gamemode';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    protected $servers;

    public function __construct()
    {
        $this->servers = config('atlas.servers');

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Exception
     */
    public function handle()
    {
        $region   = $this->argument('region') ?? 'eu';
        $gamemode = $this->argument('gamemode') ?? 'pvp';

        $this->info("");
        // Check if selected region & gamemode exist in the config array
        if (!array_key_exists($region, $this->servers)) {
            $this->error('The region ' . $region . ' doesn\'t exist in the atlas.php configuration file');

            return false;
        }

        if (!array_key_exists($gamemode, $this->servers[$region])) {
            $this->error('The gamemode ' . $gamemode . ' doesn\'t exist in the atlas.php configuration file for region ' . $region);

            return false;
        }

        $servers_matrix = Coordinate::getAllServers($region, $gamemode);

        $bar = $this->output->createProgressBar(array_sum(array_map("count", $servers_matrix)));
        $bar->setFormat("%message%\n %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%");

        $bar->setMessage('Building target server list');
        $GameQ = new GameQ();
        foreach ($servers_matrix as $x => $row) {
            foreach ($row as $y => $server) {
                $GameQ->addServer([
                    'type'    => 'source',
                    'host'    => $server['ip'] . ':' . $server['port'],
                    'options' => [
                        'query_port' => $server['port'],
                    ],
                ]);
            }
        }

        $GameQ->setOption('timeout', 3);
        $bar->setMessage('Scanning servers');
        $results = $GameQ->process();

        $bar->setMessage('Building cache with server info');
        foreach ($results as $ipport => $info) {
            $bar->setMessage('Building cache info for ' . $ipport);
            Cache::put('previousServerInfo' . $ipport, Cache::pull('serverInfo' . $ipport), 5);
            Cache::put('serverInfo' . $ipport, $info, 5);
            $bar->setMessage('Inserting ping into DB for ' . $ipport);
            Ping::create([
                'ip'          => explode(':', $ipport)[0],
                'port'        => explode(':', $ipport)[1],
                'region'      => $region,
                'gamemode'    => $gamemode,
                'coordinates' => Coordinate::getCoordinateFromIpPort(explode(':', $ipport)[0], explode(':', $ipport)[1]),
                'online'      => $info['gq_online'],
                'players'     => $info['gq_numplayers'],
                'info'        => json_encode($info['players'], true),
            ]);
            $bar->advance();
        }
        $bar->setMessage('Finished building cache');

        $bar->finish();
        $this->info("");
        $this->info("");

        return true;
    }
}
