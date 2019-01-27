#!/usr/bin/env node
/*jshint esversion: 6 */

const Discord = require('discord.js');
const axios = require('axios');
const table = require('text-table');
const moment = require('moment');
const config = require('./config.json');
const client = new Discord.Client();
const Echo = require('laravel-echo');
const contactHook = new Discord.WebhookClient(config.mentionwebhook.id, config.mentionwebhook.token);
const updateHook = new Discord.WebhookClient(config.updatewebhook.id, config.updatewebhook.token);

const key = config.axioskey;

// axios.defaults.params['key'] = config.axioskey;

// Random 'Processing...' messages
const procMsgs = [
    'Warming up Beric\'s mum...',
    'Sinking some Kraken ships...',
    'Browsing /r/playatlas...',
    'Scratching my balls...',
    'Pulling a fast wank...',
    'Waking up from a drug-fueled slumber...',
    'Quickly closing the incognito window...',
    'Wobbling to 299%...',
    'ATLAS CCTV REQUIRES MORE MINERALS...',
    'Untap, Upkeep, Draw...',
    'Traveling to Hanamura...',
    'TIME\'S UP - LET\'S DO THIS!...',
    'This loading is a line...',
    'They see me loading, They waiting...',
    'Start your engines...',
    'Skipping cutscenes...',
    'Shuffling the deck...',
    'Reviving dead memes...',
    'Returning the slab...',
    'Recombobulating Discombobulators...',
    'now with scratch and sniff...',
    'Now with 100% more Screenshare!...',
    'Dropping in Pochinki...',
    'Looking for the power button...',
    'Look behind you...',
    'Locating Wumpus...',
    'Loading your digital hug...',
    'Loading Simulation...',
    'Jumping to hyperspace...',
    'Is this thing on?...',
    'Initiating launch sequence...',
    'Initializing socialization...',
    'If you are reading this, you can read...',
    'I swear it\'s around here somewhere...',
    'i need healing...',
    'how do i turn this thing on...',
    'Loading machine broke...',
    'Get ready for a surprise!...',
    'Finishing this senta......',
    'Dusting the cobwebs...',
    'Do you even notice these?...',
    'Opening the loading bay doors...',
    'Atlas CCTV is my city...',
    'Disconnecting from Reality...',
    'Charging spirit bomb...',
    'Charging Limit Break...',
    'Calibrating flux capacitors...',
    'Buckle up!...',
    'Assembling Voltron...',
    'Are we there yet?...',
    'A brawl is surely brewing!...',
    'LOADING 001: ARP 303 Saw...',
    '*Elevator Music Plays*',
    'Researching cheat codes...',
    'Wizard needs food badly...',
    'Decrypting Engrams...',
    'And now for something completely different...',
    'Stopping to smell the flowers...',
    'Achieving Nirvana...',
    'Managing Inventory...',
    'Grinding up some leprechauns to make Nutella...',
    'Putting the D into the V...',
];

client.on('ready', () => {
    console.log(`Bot has started, with ${client.users.size} users, in ${client.channels.size} channels of ${client.guilds.size} guilds.`);
    // client.user.setPresence({
    //     game: {
    //         name: 'iShot#5449',
    //         type: 'LISTENING',
    //     },
    //     status: 'idle',
    // });
    client.user.setActivity(`!help`);
});

client.on('message', msg => {
    var author = msg.author;
    var message = msg;

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

    if (msg.channel.type === 'dm') {
        // Private message
        console.log('Private message received from ' + msg.author.username + '#' + msg.author.discriminator + ':\n > ' + msg.content);
        msg.author.send('Sorry, I don\'t do private messages, use me in a server ( ͡° ͜ʖ ͡°)');
        return;
    } else {
        console.log('Message received from server ' + msg.guild.name + ' by user ' + msg.author.username + '#' + msg.author.discriminator + ':\n > ' + msg.content);
    }


    // if (command === 'test') {
    //     msg.channel.send(procMsgs[Math.floor(Math.random() * procMsgs.length)] + ' (processing, please wait)').then((msg) => {
    //
    //     });
    //
    //     return false;
    // }

    if (command === 'help' || command === 'cmdlist' || command === 'commands' || command === 'bot' || command === 'info') {
        msg.delete();
        msg.author.send(procMsgs[Math.floor(Math.random() * procMsgs.length)] + ' (processing, please wait)').then((msg) => {
            axios.get(config.url + '/api/help', {
                params: {
                    key: key,
                },
            }).then(function (response) {
                // var message = '';
                if (response.data) {
                    msg.delete();
                    // array.push(['COMMAND', 'EXPLANATION', 'ALIASES']);
                    var array = [];
                    for (var command in response.data) {
                        if (!response.data.hasOwnProperty(command)) {
                            continue;
                        }

                        var strCommand = config.prefix + command;
                        if (response.data[command]['arguments'].length) {
                            strCommand = strCommand + ' ' + response.data[command]['arguments'].join(' ');
                        }

                        array.push([strCommand]);
                        array.push([response.data[command].explanation]);
                        if (response.data[command].aliases.length) {
                            array.push(['Aliases: !' + response.data[command].aliases.join(' !')]);
                        }
                        array.push([]);

                        if (array.length >= 16) {
                            console.log(array.length);
                            // Bind help information together
                            author.send('```' + table(array) + '```');
                            array = [];
                        }
                    }

                    if (array.length >= 1) {
                        // Send last part
                        author.send('```' + table(array) + '```');
                        array = [];
                    }

                    console.log('Sent a private messages to ' + author.username + ' with the help information');
                } else {
                    message = '\n> Something went wrong when pulling the help information';
                    console.log('Sent a private message to ' + msg.author.username + ':' + message);
                    msg.edit('```' + message + '```');
                }
            });
        });

        return false;
    }

    if (command === 'version' || command === 'v') {
        msg.channel.send(procMsgs[Math.floor(Math.random() * procMsgs.length)] + ' (processing, please wait)').then((msg) => {
            // Poll the API for the information requested
            axios.get(config.url + '/api/version', {
                params: {
                    key: key,
                },
            }).then(function (response) {
                // var message = '';
                var array = [];
                var multiple = false;

                if (response.data.version !== undefined) {
                    msg.edit(':information_source: Current bot version `' + response.data.version + '`, this update happened `' + moment(response.data.created_at * 1000).fromNow() + '`\n\n' + response.data.changes);
                } else {
                    msg.edit(':skull_crossbones: Something went wrong while trying to pull the version information');
                }

            });
        });

        return false;
    }

    if (command === 'faq' || command === 'frequentlyaskedquestions') {
        msg.channel.send(procMsgs[Math.floor(Math.random() * procMsgs.length)] + ' (processing, please wait)').then((msg) => {
            // Poll the API for the information requested
            axios.get(config.url + '/api/faq', {
                params: {
                    key: key,
                },
            }).then(function (response) {
                // var message = '';
                msg.delete();
                message.delete();
                var array = [];
                var multiple = false;

                author.send(':question: These are currently the most asked questions about the ATLAS CCTV bot:');
                for (var faq in response.data) {
                    if (!response.data.hasOwnProperty(faq)) {
                        continue;
                    }

                    array.push(['Question: `' + response.data[faq].question + '`']);
                    array.push(['Answer: ' + response.data[faq].answer + '']);
                    array.push(['']);

                    // If the array is larger than 25 lines, push it as a message and restart the array
                    if (array.length >= 10) {
                        author.send('' + table(array) + '');
                        multiple = true;

                        array = [];
                    }
                }

                console.log('Sent a message to ' + msg.guild.name);
                if (array.length >= 1) {
                    author.send('' + table(array) + '');
                }
            });
        });

        return false;
    }

    if (command === 'ask' || command === 'feedback' || command === 'contact' || command === 'question') {
        msg.channel.send(procMsgs[Math.floor(Math.random() * procMsgs.length)] + ' (processing, please wait)').then((msg) => {
            // If no arguments, send back the usage of the command
            if (args.length === 0) {
                // No parameters given
                var message = '';
                message = message + config.prefix + 'contact [MESSAGE]';
                msg.edit('```' + message + '```');
                return false;
            }

            let input = args.join(' ');

            // Send a message to the bot owner
            console.log('User ' + author.username + '#' + author.discriminator + ' sent a message to the devs using !contact');
            msg.edit(':microphone2: We have sent your message to the bot owner!');
            contactHook.send('Someone sent you a message using the !contact command\n``` > Message: ' + input + '\n > User: ' + author.username + '#' + author.discriminator + '\n > Origin server: ' + msg.guild.name + '\n > Origin channel: ' + msg.channel.name + '```');
            //
            //
            // console.log(config.ownerid);
            // console.log(author.id);
            // console.log(client.users.get(config.ownerid.toString()));
            // client.users.get(config.ownerid.toString()).send();

        });

        return false;
    }

    if (command === 'map' || command === 'world') {
        msg.channel.send(procMsgs[Math.floor(Math.random() * procMsgs.length)] + ' (processing, please wait)').then((msg) => {
            // If no arguments, send back the usage of the command
            // if (args.length === 0) {
            //     // No parameters given
            //     var message = '';
            //     message = message + config.prefix + 'map [REGION:eu] [GAMEMODE:pvp]';
            //     msg.edit('```' + message + '```');
            //     return false;
            // }

            let [region, gamemode] = args;

            if (region === undefined) {
                region = 'eu';
            }

            if (gamemode === undefined) {
                gamemode = 'pvp';
            }

            // Poll the API for the information requested
            axios.get(config.url + '/api/map', {
                params: {
                    key: key,
                    region: region,
                    gamemode: gamemode,
                },
            }).then(function (response) {
                // var message = '';
                var array = [];
                var multiple = false;

                if (response.data.image !== undefined) {
                    msg.edit(':map: This is the current map of the ' + region + ' ' + gamemode + ' server:');
                    msg.channel.send('', {
                        file: response.data.image, // Or replace with FileOptions object
                    });
                } else {
                    msg.edit(':skull_crossbones: Something went wrong while trying to pull the map information');
                }

            });
        });

        return false;
    }

    if (command === 'purge' || command === 'clean' || command === 'clear') {
        if (message.member.guild.me.hasPermission('ADMINISTRATOR') || message.member.guild.me.hasPermission('MANAGE_MESSAGES')) {
            if (message.member.hasPermission('ADMINISTRATOR') || message.member.hasPermission('MANAGE_MESSAGES')) {
                async function clear() {
                    msg.delete();
                    const fetched = await msg.channel.fetchMessages({limit: 100});
                    msg.channel.bulkDelete(fetched);
                }

                clear();
            } else {
                msg.edit('You do not have the correct permissions to use !purge (You need to be able to delete messages)');
            }
        } else {
            msg.edit('This bot doesn\'t have permissions to remove messages');
        }

        return false;
    }

    if (command === 'player' || command === 'players') {
        msg.channel.send(procMsgs[Math.floor(Math.random() * procMsgs.length)] + ' (processing, please wait)').then((msg) => {
            // If no arguments, send back the usage of the command
            if (args.length === 0) {
                // No parameters given
                var message = '';
                message = message + config.prefix + 'players <SERVER:A1> [REGION:eu] [GAMEMODE:pvp]';
                msg.edit('```' + message + '```');
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
                    key: key,
                    server: server,
                    region: region,
                    gamemode: gamemode,
                },
            }).then(function (response) {
                // var message = '';
                var array = [];
                var multiple = false;

                if (response.data.players !== false) {
                    msg.edit('These are the `' + response.data.players.length + '` players on `' + ogserver + '`');
                    array.push(['USERNAME', 'PLAYTIME']);
                    for (var player in response.data.players) {
                        if (!response.data.players.hasOwnProperty(player)) {
                            continue;
                        }

                        array.push([response.data.players[player].Name, response.data.players[player].TimeF]);

                        // If the array is larger than 25 lines, push it as a message and restart the array
                        if (array.length >= 25) {
                            msg.channel.send('```' + table(array) + '```');
                            multiple = true;

                            array = [];
                            array.push(['USERNAME', 'PLAYTIME']);
                        }
                    }

                    console.log('Sent a message to ' + msg.guild.name);
                    if (array.length >= 1) {
                        msg.channel.send('```' + table(array) + '```');
                    }
                } else {
                    msg.edit(':skull_crossbones: Server ' + ogserver + ' seems to be offline');
                }

            });
        });

        return false;
    }

    if (command === 'pop' || command === 'population') {
        msg.channel.send(procMsgs[Math.floor(Math.random() * procMsgs.length)] + ' (processing, please wait)').then((msg) => {
            // If no arguments, send back the usage of the command
            if (args.length === 0) {
                // No parameters given
                var message = '';
                message = message + config.prefix + 'pop <SERVER:A1> [REGION:eu] [GAMEMODE:pvp]';
                msg.edit('```' + message + '```');
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
                    key: key,
                    server: server,
                    region: region,
                    gamemode: gamemode,
                },
            }).then(function (response) {
                // var message = '';
                var array = [];

                array.push(['COORDINATE', 'PLAYERS', 'DIRECTION', '']);
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
        msg.channel.send(procMsgs[Math.floor(Math.random() * procMsgs.length)] + ' (processing, please wait)').then((msg) => {
            // If no arguments, send back the usage of the command
            if (args.length === 0) {
                // No parameters given

                var message = '';
                message = message + config.prefix + 'pop <SERVER:A1> [REGION:eu] [GAMEMODE:pvp]';
                msg.edit('```' + message + '```');
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
                    key: key,
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
        msg.channel.send(procMsgs[Math.floor(Math.random() * procMsgs.length)] + ' (processing, please wait)').then((msg) => {
            // If no arguments, send back the usage of the command
            if (args.length === 0) {
                // No parameters given
                var message = '';
                message = message + config.prefix + 'find <NAME:iShot>';
                msg.edit('```' + message + '```');
                return false;
            }

            let [username] = [args.join(' ')];

            // Poll the API for the information requested
            axios.get(config.url + '/api/find', {
                params: {
                    key: key,
                    username: username,
                },
            }).then(function (response) {
                // var message = '';
                var array = [];
                var message = '';

                if (response.data.length) {
                    array.push(['COORDINATE', 'USERNAME', 'LAST SEEN']);
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

    if (command === 'unproximity' || command === 'unprox' || command === 'unalert') {
        msg.channel.send(procMsgs[Math.floor(Math.random() * procMsgs.length)] + ' (processing, please wait)').then((msg) => {
            // If no arguments, send back the usage of the command
            if (args.length === 0) {
                // No parameters given
                var message = '';
                message = message + config.prefix + 'unprox <SERVER:B4>';
                msg.edit('```' + message + '```');
                return false;
            }

            let [server] = args;

            console.log(server, config.url + '/api/proximity/remove');
            // Poll the API for the information requested
            axios.post(config.url + '/api/proximity/remove', {
                key: key,
                coordinate: server,
                guildid: msg.guild.id,
                channelid: msg.channel.id,
            }).then(function (response) {
                msg.edit('```' + 'No longer alerting on server ' + server + '```');
            }).catch(function (response) {
                msg.edit('```' + response.response.data.message + '```');
            });
        });

        return false;
    }
    ;

    if (command === 'proximity' || command === 'prox' || command === 'alert') {
        msg.channel.send(procMsgs[Math.floor(Math.random() * procMsgs.length)] + ' (processing, please wait)').then((msg) => {
            // If no arguments, send back the usage of the command
            if (args.length === 0) {
                // No parameters given
                var message = '';
                message = message + config.prefix + 'alert <SERVER:B4>';

                // Get current tracks for this guild...
                axios.get(config.url + '/api/proximity/list', {
                    params: {
                        key: key,
                        guildid: msg.guild.id,
                    },
                }).then(function (response) {
                    message = message + '\n\n';

                    if (response.data.length) {
                        var array = [];
                        array.push(['COORDINATE', 'ADDED']);
                        for (var server in response.data) {
                            if (!response.data.hasOwnProperty(server)) {
                                continue;
                            }

                            array.push([response.data[server].coordinate, moment(response.data[server].updated_at, 'YYYY-MM-DD HH:mm:ss').fromNow()]);
                        }

                        message = message + table(array);
                    } else {
                        // No active tracks
                        message = message + 'No active proximity alerts found';
                    }

                    msg.edit('These are the active proximity alerts:\n```' + message + '```');
                });

                return false;
            }

            let [server] = args;

            console.log(server, config.url + '/api/proximity/add');
            // Poll the API for the information requested
            axios.post(config.url + '/api/proximity/add', {
                key: key,
                coordinate: server.toUpperCase(),
                guildid: msg.guild.id,
                channelid: msg.channel.id,
            }).then(function (response) {
                msg.edit('```' + 'Now alerting about ships entering server ' + server + '```');
            }).catch(function (response) {
                msg.edit('```' + response.response.data.message + '```');
            });
        });

        return false;
    }

    if (command === 'untrack' || command === 'unstalk' || command === 'unfollow') {
        msg.channel.send(procMsgs[Math.floor(Math.random() * procMsgs.length)] + ' (processing, please wait)').then((msg) => {
            // If no arguments, send back the usage of the command
            if (args.length === 0) {
                // No parameters given
                var message = '';
                message = message + config.prefix + 'untrack <NAME:iShot>';
                msg.edit('```' + message + '```');
                return false;
            }

            let [username] = [args.join(' ')];

            console.log(username, msg.guild.id, msg.channel.id, config.url + '/api/track/remove');
            // Poll the API for the information requested
            axios.post(config.url + '/api/track/remove', {
                key: key,
                username: username,
                guildid: msg.guild.id,
                // channelid: msg.channel.id,
            }).then(function (response) {
                msg.edit('```' + 'No longer tracking ' + username + '```');
            }).catch(function (response) {
                msg.edit('```' + response.response.data.message + '```');
            });
        });

        return false;
    }

    if (command === 'track' || command === 'stalk' || command === 'follow') {
        msg.channel.send(procMsgs[Math.floor(Math.random() * procMsgs.length)] + ' (processing, please wait)').then((msg) => {
            // If no arguments, send back the usage of the command
            if (args.length === 0 || args[1] === undefined) {
                // No parameters given
                var message = '';
                message = message + config.prefix + 'track <MINUTES:30> <NAME:iShot>';

                // Get current tracks for this guild...
                axios.get(config.url + '/api/track/list', {
                    params: {
                        key: key,
                        guildid: msg.guild.id,
                    },
                }).then(function (response) {
                    message = message + '\n\n';

                    if (response.data.length) {
                        var array = [];
                        array.push(['USERNAME', 'LAST LOCATION', 'LAST SEEN', 'EXPIRES']);
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
                key: key,
                username: username,
                minutes: minutes,
                guildid: msg.guild.id,
                channelid: msg.channel.id,
            }).then(function (response) {
                msg.edit('```' + 'Now tracking ' + username + ' for the next ' + minutes + ' minute(s). We\'ll post a message each time we see the player move servers.' + '```');
            }).catch(function (response) {
                msg.edit('```' + response.response.data.message + '```');
            });
        });

        return false;
    }
});

client.on('error', (e) => console.error(e));

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
        client.channels.get(e.channelid).send(':spy::skin-tone-4: Tracked player `' + e.player + '` has moved from `' + e.from + '` to `' + e.to + '` heading `' + e.direction + '`');
    })
    .listen('.tracked.player.lost', (e) => {
        console.log('WebSocket: [TRACKING] Sent tracking lost message to ' + e.guildid + ' about player ' + e.player);
        client.channels.get(e.channelid).send(':sleeping: We suspect that tracked player `' + e.player + '` has gone offline. Last known location: `' + e.last + '`');
    })
    .listen('.tracked.player.refound', (e) => {
        console.log('WebSocket: [TRACKING] Sent tracking refound message to ' + e.guildid + ' about player ' + e.player);
        client.channels.get(e.channelid).send(':spy::skin-tone-4: Tracked player `' + e.player + '` came back online in location: `' + e.last + '`');
    })
    .listen('.track.expired', (e) => {
        console.log('WebSocket: [TRACKING] Sent track expired message to ' + e.guildid + ' about player ' + e.player);
        client.channels.get(e.channelid).send(':timer: Tracking for player `' + e.player + '` has expired. Last known location: `' + e.last + '`');
    })
    .listen('.bot.updated', (e) => {
        console.log('WebSocket: [UPDATE] We noticed an update happened and sent a message to the webhook');
        updateHook.send(':satellite: The ATLAS CCTV bot has just been updated!\n > Current version: `' + e.version + '`\n\n' + e.changes + '');
    })
    .listen('.faq.created', (e) => {
        console.log('WebSocket: [FAQ] We noticed a new FAQ appeared!');
        updateHook.send(':question: A new frequently asked question was added to the !faq command!\n\n`' + e.question + '`\n' + e.answer + '');
    })
    .listen('.tracked.server.boat', (e) => {
        console.log('WebSocket: [TRACKING] Sent boat warning message to ' + e.guildid + ' about coordinate ' + e.to);
        client.channels.get(e.channelid).send(':anchor: A suspected boat entered coordinate `' + e.to + '`. They came from the `' + e.direction + '` (`' + e.from + '`). Player(s) on the boat:\n```\n' + e.players.join('\n') + '```');
    });