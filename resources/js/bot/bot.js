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

    if (command === 'help' || command === 'cmdlist' || command === 'commands' || command === 'bot' || command === 'info') {
        msg.channel.send('Processing... beep boop...').then((msg) => {
            msg.edit(config.prefix + 'pop <SERVER:A1> [REGION:eu] [GAMEMODE:pvp] --- Show the population of the given server and all servers around it\n' + config.prefix + 'find <NAME:iShot> --- Show the latest information of this player (STEAM NAME ONLY)');
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
                msg.edit('\n```' + table(array) + '```');
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

            let [username] = args;

            // Poll the API for the information requested
            axios.get(config.url + '/api/find', {
                params: {
                    username: username,
                },
            }).then(function (response) {
                // var message = '';
                var array = [];

                console.log(response.data);
                if (response.data.length) {
                    array.push(['Server', 'Username', 'Last detected']);
                    for (var player in response.data) {
                        if (!response.data.hasOwnProperty(player)) {
                            continue;
                        }

                        // 2019-01-23 19:34:39
                        array.push([response.data[player].coordinates, response.data[player].player, moment(response.data[player].created_at, 'YYYY-MM-DD HH:mm:ss').fromNow()]);
                    }

                    console.log('Sent a message to ' + msg.guild.name);
                    msg.edit('\n```' + table(array) + '```');
                } else {
                    console.log('Sent a message to ' + msg.guild.name);
                    msg.edit('\nNo players found with this name');
                }
            });
        });

        return false;
    }
});

function timeSince(date) {

    var seconds = Math.floor((new Date() - date) / 1000);

    var interval = Math.floor(seconds / 31536000);

    if (interval > 1) {
        return interval + ' years';
    }
    interval = Math.floor(seconds / 2592000);
    if (interval > 1) {
        return interval + ' months';
    }
    interval = Math.floor(seconds / 86400);
    if (interval > 1) {
        return interval + ' days';
    }
    interval = Math.floor(seconds / 3600);
    if (interval > 1) {
        return interval + ' hours';
    }
    interval = Math.floor(seconds / 60);
    if (interval > 1) {
        return interval + ' minutes';
    }
    return Math.floor(seconds) + ' seconds';
}

client.login(config.token);