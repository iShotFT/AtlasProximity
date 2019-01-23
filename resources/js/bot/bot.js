const Discord = require('discord.js');
const axios = require('axios');
const table = require('text-table');
const config = require('./config.json');
const client = new Discord.Client();

client.on('ready', () => {
    console.log(`Logged in as ${client.user.tag}!`);
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

    if (command === 'pop') {
        // If no arguments, send back the usage of the command
        if (args.length === 0) {
            // No parameters given
            msg.channel.send(config.prefix + 'pop <SERVER:A1> [REGION:eu] [GAMEMODE:pvp]');
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
        axios.get('/api/population', {
            params: {
                server: server,
                region: region,
                gamemode: gamemode,
            },
        }).then(function (response) {
            // var message = '';
            var array = [];

            for (var server in response.data) {
                if (!response.data.hasOwnProperty(server)) {
                    continue;
                }

                array.push([server, response.data[server].count, response.data[server].direction, String.fromCodePoint('0x' + response.data[server].unicode)]);
            }

            msg.reply('\n```' + table(array) + '```');
        });
    }
});

client.login(config.token);