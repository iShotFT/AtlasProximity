#!/usr/bin/env node
/*jshint esversion: 6 */

const Discord = require('discord.js');
const axios = require('axios');
const table = require('text-table');
const moment = require('moment');
const config = require('./config.json');
const client = new Discord.Client();
const Echo = require('laravel-echo');

client.on('ready', () => {
    console.log(`Bot has started, with ${client.users.size} users, in ${client.channels.size} channels of ${client.guilds.size} guilds.`);
    client.user.setActivity(`!help | !track | !pop | !grid | !players`);
});

client.on('message', msg => {
    // Ignore all bot messages
    if (msg.author.bot) {
        return;
    }

    // Ignore all messages not starting with a prefix
    if (msg.content.indexOf(config.prefix) !== 0) {
        return;
    }

    // Get arguments and command
    const args = msg.content.slice(config.prefix.length).trim().split(/ +/g);
    const command = args.shift().toLowerCase();

    console.log('Message received from server ' + msg.guild.name + ' by user ' + msg.author.username + '#' + msg.author.discriminator + ':\n > ' + msg.content);

    if (command === 'help' || command === 'cmdlist' || command === 'commands' || command === 'bot' || command === 'info') {
        msg.channel.send('Processing... beep boop...').then((msg) => {
            msg.edit('```---\n' + config.prefix + 'players <SERVER:A1> [REGION:eu] [GAMEMODE:pvp]\n---\n> Show a list of usernames in the given server and their playtime\n\n---\n' + config.prefix + 'pop <SERVER:A1> [REGION:eu] [GAMEMODE:pvp]\n---\n> Show the population of the given server and all servers around it in a list with directions\n\n---\n' + config.prefix + 'grid <SERVER:A1> [REGION:eu] [GAMEMODE:pvp]\n---\n> Show the population of the given server and all servers around it formatted as a table\n\n---\n' + config.prefix + 'find <NAME:iShot>\n---\n> Show the latest information of this player (STEAM NAME ONLY)\n\n---\n' + config.prefix + 'track <MINUTES:30> <NAME:iShot>\n---\n> Track this player\'s movement for the next XX minutes. You\'ll receive warnings when we see the player skip servers.```');
        });

        return false;
    }

    if (command === 'player' || command === 'players') {
        msg.channel.send('Processing... beep boop...').then((msg) => {
            // If no arguments, send back the usage of the command
            if (args.length === 0) {
                // No parameters given
                msg.edit(config.prefix + 'players <SERVER:A1> [REGION:eu] [GAMEMODE:pvp]');
                return false;
            }

            let [server, region, gamemode] = args;

            // Server (eg B4)
            if (server === undefined) {
                server = 'A1';
            } else {
                server = server.toUpperCase();
            }

            if (region === undefined) {
                region = 'eu';
            }

            if (gamemode === undefined) {
                gamemode = 'pvp';
            }

            // Poll the API for the information requested
            var ogserver = server;
            axios.get(config.url + '/api/players', {
                params: {
                    server: server,
                    region: region,
                    gamemode: gamemode,
                },
            }).then(function (response) {
                // var message = '';
                var array = [];

                array.push(['Username', 'Playtime']);
                for (var player in response.data.players) {
                    if (!response.data.players.hasOwnProperty(player)) {
                        continue;
                    }

                    array.push([response.data.players[player].Name, response.data.players[player].TimeF]);
                }

                console.log('Sent a message to ' + msg.guild.name);
                msg.edit('\nThese are the ' + response.data.count + ' players on ' + ogserver + ':\n```' + table(array) + '```\n\n*We do not track players with the following names: \'123\' and empty names*');
            });
        });

        return false;
    }

    if (command === 'pop' || command === 'population') {
        msg.channel.send('Processing... beep boop...').then((msg) => {
            // If no arguments, send back the usage of the command
            if (args.length === 0) {
                // No parameters given
                msg.edit(config.prefix + 'pop <SERVER:A1> [REGION:eu] [GAMEMODE:pvp]');
                return false;
            }

            let [server, region, gamemode] = args;

            // Server (eg B4)
            if (server === undefined) {
                server = 'A1';
            } else {
                server = server.toUpperCase();
            }

            if (region === undefined) {
                region = 'eu';
            }

            if (gamemode === undefined) {
                gamemode = 'pvp';
            }

            // Poll the API for the information requested
            var ogserver = server;
            axios.get(config.url + '/api/population', {
                params: {
                    server: server,
                    region: region,
                    gamemode: gamemode,
                },
            }).then(function (response) {
                // var message = '';
                var array = [];

                array.push(['Server', 'Players', 'Direction', '']);
                for (var server in response.data) {
                    if (!response.data.hasOwnProperty(server)) {
                        continue;
                    }

                    array.push([server, response.data[server].count, response.data[server].direction, String.fromCodePoint('0x' + response.data[server].unicode)]);
                }

                console.log('Sent a message to ' + msg.guild.name);
                msg.edit('\nThese are the amount of players on and around ' + ogserver + ':\n```' + table(array) + '```\n\nWant to see this data as a table? Try \'!grid ' + ogserver + '\'');
            });
        });

        return false;
    }

    if (command === 'grid') {
        msg.channel.send('Processing... beep boop...').then((msg) => {
            // If no arguments, send back the usage of the command
            if (args.length === 0) {
                // No parameters given
                msg.edit(config.prefix + 'pop <SERVER:A1> [REGION:eu] [GAMEMODE:pvp]');
                return false;
            }

            let [server, region, gamemode] = args;

            // Server (eg B4)
            if (server === undefined) {
                server = 'A1';
            } else {
                server = server.toUpperCase();
            }

            if (region === undefined) {
                region = 'eu';
            }

            if (gamemode === undefined) {
                gamemode = 'pvp';
            }

            var ogserver = server;
            // Poll the API for the information requested
            axios.get(config.url + '/api/population', {
                params: {
                    server: server,
                    region: region,
                    gamemode: gamemode,
                },
            }).then(function (response) {
                // var message = '';
                var array = [];
                var order = [[2, 3, 4], [1, 0, 5], [8, 7, 6]];

                for (var row in order) {
                    if (!order.hasOwnProperty(row)) {
                        continue;
                    }

                    var headers = [];
                    var items = [];
                    for (var column in order[row]) {
                        if (!order[row].hasOwnProperty(column)) {
                            continue;
                        }

                        var server = Object.keys(response.data)[order[row][column]];
                        headers.push(server);
                        items.push(response.data[server].count);
                    }

                    array.push(headers);
                    array.push(items);
                    if (row < 2) {
                        array.push(['', '', '']);
                    }
                }

                var message = '\nThese are the amount of players on and around ' + ogserver + ':\n```' + table(array) + '```\n\nWant to see this data as a list? Try \'!pop ' + ogserver + '\'';
                console.log('Sent a message to ' + msg.guild.name + ':' + message);
                msg.edit(message);
            });
        });

        return false;
    }

    if (command === 'find' || command === 'search' || command === 'whereis') {
        msg.channel.send('Processing... beep boop...').then((msg) => {
            // If no arguments, send back the usage of the command
            if (args.length === 0) {
                // No parameters given
                msg.edit(config.prefix + 'find <NAME:iShot>');
                return false;
            }

            let [username] = [args.join(' ')];

            // Poll the API for the information requested
            axios.get(config.url + '/api/find', {
                params: {
                    username: username,
                },
            }).then(function (response) {
                // var message = '';
                var array = [];
                var message = '';

                if (response.data.length) {
                    array.push(['Server', 'Username', 'Last seen']);
                    for (var player in response.data) {
                        if (!response.data.hasOwnProperty(player)) {
                            continue;
                        }

                        // 2019-01-23 19:34:39
                        array.push([response.data[player].coordinates, response.data[player].player, moment(response.data[player].updated_at, 'YYYY-MM-DD HH:mm:ss').fromNow()]);
                    }

                    message = '\nWe found the following information for player ' + username + ' (limited to 5 last entries)\n```' + table(array) + '```';
                    console.log('Sent a message to ' + msg.guild.name + ':' + message);
                    msg.edit(message);
                } else {
                    message = '\n```> No players found with this name```';
                    console.log('Sent a message to ' + msg.guild.name + ':' + message);
                    msg.edit(message);
                }
            });
        });

        return false;
    }

    if (command === 'track' || command === 'stalk' || command === 'follow') {
        msg.channel.send('Processing... beep boop...').then((msg) => {
            // If no arguments, send back the usage of the command
            if (args.length === 0 || args[1] === undefined) {
                // No parameters given
                var message = '';
                message = message + config.prefix + 'track <MINUTES:30> <NAME:iShot>';

                // Get current tracks for this guild...
                axios.get(config.url + '/api/track/list', {
                    params: {
                        guildid: msg.guild.id,
                    },
                }).then(function (response) {
                    message = message + '\n\n';

                    if (response.data.length) {
                        var array = [];
                        array.push(['Username', 'Last Location', 'Last Seen', 'Expires']);
                        for (var track in response.data) {
                            if (!response.data.hasOwnProperty(track)) {
                                continue;
                            }

                            var last_coordinate = response.data[track].last_coordinate;
                            if (last_coordinate === null) {
                                last_coordinate = 'Unknown';
                            }
                            array.push([response.data[track].player, last_coordinate, moment(response.data[track].updated_at, 'YYYY-MM-DD HH:mm:ss').fromNow(), moment(response.data[track].until, 'YYYY-MM-DD HH:mm:ss').fromNow()]);
                        }

                        message = message + table(array);
                    } else {
                        // No active tracks
                        message = message + 'No active trackings found';
                    }

                    msg.edit('```' + message + '```');
                });

                return false;
            }

            let [minutes, username] = [args[0], args.slice(1).join(' ')];

            console.log(username, minutes, msg.guild.id, msg.channel.id, config.url + '/api/track/add');
            // Poll the API for the information requested
            axios.post(config.url + '/api/track/add', {
                username: username,
                minutes: minutes,
                guildid: msg.guild.id,
                channelid: msg.channel.id,
            }).then(function (response) {
                msg.edit('```' + 'Now tracking ' + username + ' for the next ' + minutes + ' minute(s)' + '```');
            }).catch(function (response) {
                msg.edit('```' + response.response.data.message + '```');
            });
        });

        return false;
    }
});

client.login(config.token);

// ECHO
const io = require('socket.io-client');
this.Echo = new Echo({
    broadcaster: 'socket.io',
    host: config.url + ':' + config.socketport,
    client: io,
});

this.Echo.channel(`public`)
    .listen('.tracked.player.moved', (e) => {
        console.log('WebSocket: [TRACKING] Sent message to ' + e.guildid + ' about player ' + e.player);
        client.channels.get(e.channelid).send(':warning: Tracked player `' + e.player + '` has moved from `' + e.from + '` to `' + e.to + '` heading `' + e.direction + '`');
    })
    .listen('.track.expired', (e) => {
        console.log('WebSocket: [TRACKING] Sent track expired message to ' + e.guildid + ' about player ' + e.player);
        client.channels.get(e.channelid).send(':warning: Tracking for player `' + e.player + '` has expired. Last known location: `' + e.last + '`');
    });