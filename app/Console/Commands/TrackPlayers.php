<?php

namespace App\Console\Commands;

use App\Classes\Coordinate;
use App\Events\TrackedPlayerLost;
use App\Events\TrackedPlayerMoved;
use App\Events\TrackedPlayerRefound;
use App\Events\TrackExpired;
use App\Http\Controllers\SourceQueryController;
use App\PlayerPing;
use App\PlayerTrack;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TrackPlayers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'atlascctv:trackplayers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hit the tracking logic for all actively tracked players';

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

        $bar = $this->output->createProgressBar(PlayerTrack::count() ?: 1);
        $bar->setFormat("%message%\n %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%");

        $bar->start();
        $bar->setMessage('Starting tracking process...');
        // PVP EU, PVP NA, PVE EU, PVE NA
        foreach (PlayerTrack::orderByDesc('gamemode')->orderBy('region')->get() as $player_track) {
            $bar->setMessage('Checking track for ' . $player_track->player . ' (' . $player_track->region . ' ' . $player_track->gamemode . ')');
            if ($player_track->until <= Carbon::now()) {
                // remove track
                $player_track->delete();
                event(new TrackExpired($player_track));
            } else {
                // Valid track
                if ($player_ping = PlayerPing::where('player', $player_track->player)->orderByDesc('updated_at')->first()) {
                    // Scan the server the player was last seen on
                    $remained_stationairy = false;
                    $bar->setMessage('Scanning coordinate ' . $player_track->last_coordinate . ' ' . '(' . $player_track->region . ' ' . $player_track->gamemode . ') for  ' . $player_track->player);
                    foreach (SourceQueryController::getPlayersOnCoordinate($player_track->last_coordinate, $player_track->region, $player_track->gamemode)['players'] as $player) {
                        // Is the tracked player still on the server we last saw him on?
                        if ($player_track->player === $player['Name']) {
                            $remained_stationairy = true;
                        }
                    }

                    if ($remained_stationairy) {
                        // We found the player on the same server as last time we spotted him
                        // No need for alert, we will update the playerping so we know we checked him out
                        // Trigger online warning
                        if ($player_track->last_status === 0 && $player_ping->updated_at >= Carbon::now()->subMinutes(10)) {
                            // User came back online!
                            event(new TrackedPlayerRefound($player_track));
                        }

                        $player_track->update([
                            'updated_at'  => Carbon::now(),
                            'last_status' => 1,
                        ]);
                    } else {
                        // The player is no longer on the same server where we last spotted him... He might have gone offline or he might have moved
                        // Scan the servers around the last_coordinate for his name
                        $original_coordinate = $player_track->last_coordinate;
                        $found_in            = false;

                        $bar->setMessage('Scanning all coordinates around ' . $player_track->last_coordinate . ' ' . '(' . $player_track->region . ' ' . $player_track->gamemode . ') for  ' . $player_track->player);
                        foreach (SourceQueryController::getPlayersOnCoordinateWithSurrounding($player_track->last_coordinate, $player_track->region, $player_track->gamemode) as $coordinate => $server_info) {
                            foreach ($server_info['players'] as $player) {
                                if ($player_track->player === $player['Name']) {
                                    $found_in = $coordinate;
                                }
                            }
                        }

                        if ($found_in) {
                            // Player with the same name was found in a neighbouring server
                            $current_coordinate = $found_in;
                        } else {
                            // Player was not found in the original server, nor in any of the 8 servers around it
                            // Player might have gone offline or teleported / died
                            $current_coordinate = $player_ping->coordinates;
                        }

                        list ($x1, $y1) = Coordinate::textToXY($original_coordinate);
                        list ($x2, $y2) = Coordinate::textToXY($current_coordinate);
                        $last_direction = Coordinate::cardinalDirectionBetween($x1, $y1, $x2, $y2);

                        $update_info = [
                            'last_coordinate' => $current_coordinate,
                            'last_direction'  => $last_direction,
                        ];

                        if ($player_track->last_status === 0 && $player_ping->updated_at >= Carbon::now()->subMinutes(10)) {
                            // User came back online!
                            $update_info['last_status'] = 1;
                            event(new TrackedPlayerRefound($player_track));
                        }

                        $player_track->update($update_info);

                        // Player moved since last track
                        // Trigger event to warn the tracking server about this movement
                        if ($player_track->last_coordinate !== $original_coordinate) {
                            event(new TrackedPlayerMoved($player_track, $original_coordinate));
                        } else {
                            // If the player ping is older than 15 minutes we can suspect the player went offline.
                            if ($player_ping->updated_at <= Carbon::now()->subMinutes(10) && $player_track->last_status === 1) {
                                // We suspect player went offline
                                event(new TrackedPlayerLost($player_track, $player_ping->updated_at));
                                $player_track->update([
                                    'last_status' => 0,
                                ]);
                            }
                        }
                    }
                } else {
                    // No player in playerping found with this name???
                }
            }

            $bar->advance();
        }

        $bar->setMessage('Ended tracking process...');
        $bar->finish();

        $this->info("");
        $this->info("");

        return true;
    }
}
