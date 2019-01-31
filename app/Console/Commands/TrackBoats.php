<?php

namespace App\Console\Commands;

use App\Boat;
use App\Events\TrackedServerBoat;
use App\Http\Controllers\SourceQueryController;
use App\Ping;
use App\PlayerPing;
use App\ProximityTrack;
use Illuminate\Console\Command;

class TrackBoats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'atlascctv:trackboats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan the servers that have a proximity alert set up';

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
        $coordinates_to_scan = ProximityTrack::orderByDesc('gamemode')->orderBy('region')->select([
            'coordinate',
            'region',
            'gamemode',
        ])->distinct()->get();
        $proximity_tracks    = ProximityTrack::get();

        $bar = $this->output->createProgressBar(count($coordinates_to_scan) ?: 1);
        $bar->setFormat("%message%\n %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%");

        $bar->start();
        $bar->setMessage('Starting boat tracking process...');

        foreach ($coordinates_to_scan as $coordinate_to_scan) {
            $bar->setMessage('Scanning coordinate ' . $coordinate_to_scan->coordinate . ' (' . $coordinate_to_scan->region . ' ' . $coordinate_to_scan->gamemode . ')');
            // $coordinates_to_scan->coordinate
            // Get list of players from the previous scan
            if ($old_players = Ping::where('coordinates', $coordinate_to_scan->coordinate)->where('region', $coordinate_to_scan->region)->where('gamemode', $coordinate_to_scan->gamemode)->orderByDesc('created_at')->first()) {
                $previous_scanned_players = json_decode($old_players->info, true);
                if ($new_scanned_players = SourceQueryController::getPlayersOnCoordinate($coordinate_to_scan->coordinate, $coordinate_to_scan->region, $coordinate_to_scan->gamemode, true)['players']) {
                    // We only want to compare usernames
                    $previous_scanned_players = array_column($previous_scanned_players, 'Name');
                    $new_scanned_players      = array_column($new_scanned_players, 'Name');

                    $new_players_after_scan = array_diff($new_scanned_players, $previous_scanned_players);
                    //                    dd($new_players_after_scan);

                    if (is_array($new_players_after_scan) && count($new_players_after_scan) >= 2) {
                        // 3 or more new players joined in the past minute!!!
                        // Combine each username with the previous server they were spotted on

                        // Build an array with all r
                        $locations = [];
                        foreach ($new_players_after_scan as $username) {
                            if ($username !== '123' && !empty($username)) {
                                $player_previous_locations = PlayerPing::where('player', '=', $username)->where('region', $coordinate_to_scan->region)->where('gamemode', $coordinate_to_scan->gamemode)->orderByDesc('updated_at')->limit(2)->get([
                                    'player',
                                    'coordinates',
                                    'updated_at',
                                ]);

                                // We need at least two previous locations for this player (the current one and the previous one) before we take action.
                                if ($player_previous_locations->count() >= 2) {
                                    $player_most_recent_previous_location = $player_previous_locations->offsetGet(1);

                                    if (array_key_exists($player_most_recent_previous_location->coordinates, $locations)) {
                                        // Array exists for this location, push into it *lennyface*
                                        array_push($locations[$player_most_recent_previous_location->coordinates], $username);
                                    } else {
                                        // No array for this location exists, make one
                                        $locations[$player_most_recent_previous_location->coordinates] = [$username];
                                    }
                                }
                            }
                        }

                        foreach ($locations as $location => $players) {
                            if (is_array($players) && count($players) >= 2) {
                                // Remove the current alert hit from the next warning
                                unset($locations[$location]);

                                // Only trigger a BOAT alert when the count of players is 2 or more
                                // Trigger 'Boat entered server XXX from XXX'
                                foreach ($proximity_tracks->where('coordinate', $coordinate_to_scan->coordinate) as $proximity_track) {
                                    $boat = Boat::create([
                                        'guild_id'   => $proximity_track->guild_id,
                                        'channel_id' => $proximity_track->channel_id,
                                        'coordinate' => $coordinate_to_scan->coordinate,
                                        'region'     => $coordinate_to_scan->region,
                                        'gamemode'   => $coordinate_to_scan->gamemode,
                                        'from'       => $location,
                                        'players'    => json_encode($players, true),
                                        'count'      => count($players),
                                    ]);

                                    event(new TrackedServerBoat($proximity_track, $players, $location, $boat));
                                }
                            }
                        }
                    }
                } else {
                    // New scan failed (returned false)
                };
            } else {
                // Never had a scan for this server stored in DB?
            }

            $bar->advance();
        }

        $bar->setMessage('Ended boat tracking process...');
        $bar->finish();

        $this->info("");
        $this->info("");

        return true;
    }
}
