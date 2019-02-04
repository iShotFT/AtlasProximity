<?php

namespace App\Console\Commands;

use App\Http\Controllers\SourceQueryController;
use App\Monitor;
use App\Ping;
use Illuminate\Console\Command;

class TrackMonitors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'atlascctv:trackmonitors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan the servers that have a monitor alert set up';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
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
        $coordinates_to_scan = Monitor::orderByDesc('gamemode')->orderBy('region')->select([
            'coordinate',
            'region',
            'gamemode',
        ])->distinct()->get();
        $proximity_tracks    = Monitor::get();

        $bar = $this->output->createProgressBar(count($coordinates_to_scan) ?: 1);
        $bar->setFormat("%message%\n %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%");

        $bar->start();
        $bar->setMessage('Starting monitor tracking process...');

        foreach ($coordinates_to_scan as $coordinate_to_scan) {
            $bar->setMessage('Scanning coordinate ' . $coordinate_to_scan->coordinate . ' (' . $coordinate_to_scan->region . ' ' . $coordinate_to_scan->gamemode . ')');
            // $coordinates_to_scan->coordinate
            // Get list of players from the previous scan
            if ($old_players = Ping::where('coordinates', $coordinate_to_scan->coordinate)->where('region', $coordinate_to_scan->region)->where('gamemode', $coordinate_to_scan->gamemode)->orderByDesc('created_at')->first()) {
                $previous_scanned_players = json_decode($old_players->info, true);
                if ($new_scanned_players = SourceQueryController::getPlayersOnCoordinate($coordinate_to_scan->coordinate, $coordinate_to_scan->region, $coordinate_to_scan->gamemode, false)['players']) {
                    // We only want to compare usernames
                    $previous_scanned_players = array_column($previous_scanned_players, 'Name');
                    $new_scanned_players      = array_column($new_scanned_players, 'Name');

                    $new_players_after_scan  = array_diff($new_scanned_players, $previous_scanned_players);
                    $left_players_after_scan = array_diff($previous_scanned_players, $new_scanned_players);

                    if (is_array($new_players_after_scan)) {
                        // A player joined the server since last scan
                        $this->info(json_encode($new_players_after_scan));
                    }

                    if (is_array($left_players_after_scan)) {
                        // A player left the server since last scan
                        $this->info(json_encode($left_players_after_scan));
                    }
                } else {
                    // New scan failed (returned false)
                };
            } else {
                // Never had a scan for this server stored in DB?
            }

            $bar->advance();
        }

        $bar->setMessage('Ended monitor tracking process...');
        $bar->finish();

        $this->info("");
        $this->info("");

        return true;
    }
}
