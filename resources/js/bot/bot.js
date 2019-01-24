#!/usr/bin/env node
/*jshint esversion: 6 */

const Discord = require('discord.js');
const axios = require('axios');
const table = require('text-table');
const moment = require('moment');
const config = require('./config.json');
const client = new Discord.Client();

client.on('ready', () => {
    console.log(`Bot has started, with ${client.users.size} users, in ${client.channels.size} channels of ${client.guilds.size} guilds.`);
    // setActivity like 'Playing servering 1 servers'
    // client.user.setActivity(`Serving ${client.guilds.size} servers`);
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

    console.log('Message received from server ' + msg.guild.name + ', user' + msg.client.name + ':\n >' + msg.content);

    if (command === 'help' || command === 'cmdlist' || command === 'commands' || command === 'bot' || command === 'info') {
        msg.channel.send('Processing... beep boop...').then((msg) => {
            msg.edit(config.prefix + '```pop <SERVER:A1> [REGION:eu] [GAMEMODE:pvp]\n --- Show the population of the given server and all servers around it\n\n' + config.prefix + 'find <NAME:iShot>\n --- Show the latest information of this player (STEAM NAME ONLY)```');
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

                var message = '\nThese are the amount of players on and around ' + ogserver + '\n```' + table(array) + '```';
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
});

client.login(config.token);