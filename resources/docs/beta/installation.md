# Installation

---

- [Installation](#installation)
- [Permissions](#permissions)
- [Configuration](#configuration)

<a name="installation"></a>
## Installation

> {info} A typical Discord bot can read all messages in the channels it has been added to. This bot only registers messages that start with the command prefix `!`. I have no interest in your Discord server private conversations. If you don't trust the bot you can always put it in a new channel where it can read and take away it's reading permissions from other channels.

Installing the bot (adding it to your server) is very simple. If you're an admin on a Discord server you can follow the link below to invite the bot to your server.

[Invite bot to your Discord server](https://atlasdiscordbot.com/get?src=docsbeta)

<a name="permissions"></a>
## Permissions

These are the limited required permissions for the Discord bot in the channel you want to have it operating, without all these permissions some or all functionality might be broken.

- Read Messages
- Send Messages
- Manage Messages (required for `!purge` command)
- Embed Links
- Attach Files (required for `!map` and `!stats` commands)


<a name="configuration"></a>
## Configuration

> {warning} Currently this bot only operates on both NA and EU server clusters but only scans the PvP servers. In the future the bot will work for PvE too (if anyone is remotely interested).

After adding the bot to your Discord server it needs a small bit of configuration to work correctly.

1. `!setting region (eu|na)` - Select the region (eu or na) your company / guild is active on.
2. `!setting gamemode (pvp)` - Select the gamemode of the server you're playing on.

After setting these parameters you can check the current bot configuration for your server using `!settings`.

Your bot will __**not work**__ until these settings have been correctly entered.