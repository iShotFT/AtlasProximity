<?php

namespace App\Console\Commands;

use App\Http\Controllers\SourceQueryController;
use Illuminate\Console\Command;

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
     * @throws \xPaw\SourceQuery\Exception\InvalidArgumentException
     * @throws \xPaw\SourceQuery\Exception\InvalidPacketException
     * @throws \xPaw\SourceQuery\Exception\TimeoutException
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

        $servers_matrix = SourceQueryController::getAllServers($region, $gamemode);

        $bar = $this->output->createProgressBar(array_sum(array_map("count", $servers_matrix)));
        $bar->setFormat("%message%\n %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%");

        $bar->start();
        foreach ($servers_matrix as $x => $servers) {
            foreach ($servers as $y => $server) {
                $bar->setMessage('Scanning ' . $x . $y . ' (' . $region . ' ' . $gamemode . ')');
                SourceQueryController::getPlayersOnCoordinate($x . $y, $region, $gamemode);
                $bar->advance();
            }
        };

        $bar->finish();
        $this->info("");
        $this->info("");

        return true;
    }
}
