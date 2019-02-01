# Commands

---

- [Overview](#overview)
- [!settings](#cmdsettings)
- [!help](#cmdhelp)
- [!version](#cmdversion)
- [!ask](#cmdask)
- [!purge](#cmdpurge)
- [!map](#cmdmap)
- [!stats](#cmdstats)
- [!grid](#cmdgrid)
- [!find](#cmdfind)
- [!alert](#cmdalert)
- [!track](#cmdtrack)
- [!findboat](#cmdfindboat)

<a name="overview"></a>
## Overview

> {warning} Before being able to use any commands right after adding the bot to your server you'll need to use the [`!config`](#cmdsettings) command to change your settings.

Click on the command (eg. [`!settings`](#cmdsettings)) to go to the full explanation of the command.

| Commands                    | Explanation                                                                                                                                                                                                                                                               | Arguments             | Commandaliases                           |
|-----------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|-----------------------|------------------------------------------|
| [`!config`](#cmdsettings)   | Get or set the settings of the bot in your Discord server. This is required to be used before the bot can work at all. You can set your region (eu or na) and gamemode (pvp or pve)                                                                                       | *multiple*            | `!setting`, `!configs`, `!config`        |
| [`!help`](#cmdhelp)         | Return a link to the documentation page for the current bot version                                                                                                                                                                                                       | *none*                | `!cmdlist`, `!commands`, `!bot`, `!info` |
| [`!version`](#cmdversion)   | Returns the current version of the Discord bot and the most recent changes (update log)                                                                                                                                                                                   | *none*                | `!v`                                     |
| [`!ask`](#cmdask)           | Everything typed after this command will be sent to the bot developer, you can use this to ask question, give feedback, suggestions and more                                                                                                                              | *any*                 | `!feedback`, `!contact`, `!question`     |
| [`!purge`](#cmdpurge)       | Remove the most recent 100 messages from the channel this command was used in                                                                                                                                                                                             | *none*                | `!clean`, `!clear`                       |
| [`!map`](#cmdmap)           | Show a visual representation of the current players on the full map of the server. A color indication shows what servers are busy                                                                                                                                         | *none*                | `!world`                                 |
| [`!stats`](#cmdstats)       | Show a visual representation of the amount of players on the coordinate of your choice over the past 24 hours.                                                                                                                                                            | &lt;COORDINATE&gt;    | `!chart`                                 |
| [`!players`](#cmdplayers)   | Show a list of the current (steam) usernames and the playtime of the people on the coordinate of your choice.                                                                                                                                                             | &lt;COORDINATE&gt;    | `!player`                                |
| [`!pop`](#cmdpop)           | Show a list of the players on the coordinate of your choice and the players of all 8 coordinates around that.                                                                                                                                                             | &lt;COORDINATE&gt;    | `!population`                            |
| [`!grid`](#cmdgrid)         | Very similar to the [`!pop`](#cmdpop) command but represented in a 3x3 table                                                                                                                                                                                              | &lt;COORDINATE&gt;    | *none*                                   |
| [`!find`](#cmdfind)         | Search for a specific (steam) username on the whole server. This returns a list (if the player was found) of the 5 most recent locations this player was spotted in.                                                                                                      | &lt;STEAMUSERNAME&gt; | `!search`, `!whereis`                    |
| [`!alert`](#cmdalert)       | Adding an alert to a coordinate. When a boat (more than 2 players from the same location at the same time) enters this coordinate the bot will post a message with information in the channel this command was used in.                                                   | &lt;COORDINATE&gt;    | `!prox`, `!proximity`                    |
| [`!track`](#cmdtrack)       | Add a (steam) username to the tracking list. Every time we see that username change coordinates we'll post a message in the channel this command was used in.                                                                                                             | &lt;STEAMUSERNAME&gt; | `!stalk`, `!follow`                      |
| [`!findboat`](#cmdfindboat) | When the [`!alert`](#cmdalert) command posts a message it will also give you a boat ID. Using this boat ID you can find out the current locations of all players that were spotted on that boat. This is useful to see if the boat is still around or where it moved too. | &lt;BOATID&gt;        | `!searchboat`, `!whereisboat`              |

<a name="cmdsettings"></a>
## `!config`
<larecipe-badge type="success">Completed</larecipe-badge>
>
> {primary} This command requires you to have `ADMINISTRATOR` privileges on the server / channel used.

##### Explanation
Use this command without arguments to get a list of all current settings the bot has stored for your server. When used in combination with a single parameter it will return the current value of that setting for your server. When used in combination with both a parameter and a value it will set that parameter to the given value.

##### Example
`!config region eu`

`!config gamemode pvp`

##### Example output
![image](https://i.imgur.com/yEKfjVk.png)

<a name="cmdhelp"></a>
## `!help`
>
<larecipe-badge type="warning">Undergoing changes</larecipe-badge>

##### Explanation
Using this command will return a link directly to the documentation page (where you are right now).

##### Example input
`!help`

##### Example output
**PLACEHOLDER**

<a name="cmdversion"></a>
## `!version`
>
<larecipe-badge type="success">Completed</larecipe-badge>

##### Explanation
This command will return the current version the bot is running on, including the most recent changes the bot had. If you want to stay up-to-date on all changes you can [join our Discord server](https://discord.gg/KMHkqtb)

##### Example input
`!version`

##### Example output
![image](https://i.imgur.com/pa0arnU.png)

<a name="cmdask"></a>
## `!ask`
>
<larecipe-badge type="success">Completed</larecipe-badge>

##### Explanation
Use this command if you want to ask a question, send feedback or give suggestions to the developer of the bot. You can also alway get into touch with the developer by [joining our Discord server](https://discord.gg/KMHkqtb)

##### Example input
`!ask What is the meaning of life?`

##### Example output
![image](https://i.imgur.com/UdYDdj1.png)

<a name="cmdpurge"></a>
## `!purge`
<larecipe-badge type="success">Completed</larecipe-badge>
>
> {primary} This command requires you to have `ADMINISTRATOR` or `MANAGE_MESSAGES` privileges on the server / channel used and the bot to have `MANAGE_MESSAGES` privileges.

##### Explanation
Use this command at your own risk. Using this command will remove the 100 most recent messages from the channel it's being used in. This will not work without the correct permissions

##### Example input
`!purge`

##### Example output
*none*

<a name="cmdmap"></a>
## `!map`
<larecipe-badge type="success">Completed</larecipe-badge>
>
> {primary} This command requires the bot to have `ATTACH_FILES` privileges on the server / channel.

##### Explanation
Returns an image with the current map of the region you're playing in. Colors will indicate what servers are the most or least busy.

##### Example input
`!map`

##### Example output
![image](https://i.imgur.com/zskUUBD.png)

<a name="cmdstats"></a>
## `!stats`
<larecipe-badge type="warning">Undergoing changes</larecipe-badge>
>
> {primary} This command requires the bot to have `ATTACH_FILES` privileges on the server / channel.

##### Explanation
Get a statistical line graph overview of the players in given coordinate in the past 24 hours.

##### Example input
`!stats A9`

##### Example output
![image](https://i.imgur.com/ba6NY6X.png)

<a name="cmdplayers"></a>
## `!players`
>
<larecipe-badge type="success">Completed</larecipe-badge>

##### Explanation
Get a list of the current players on a certain coordinate

##### Example input
`!players A9`

##### Example output
![image](https://i.imgur.com/zO6qm2f.png)

<a name="cmdpop"></a>
## `!pop`
>
<larecipe-badge type="success">Completed</larecipe-badge>

##### Explanation
Get a list of the number of players on the server on the chosen coordinate and all 8 coordinates around that coordinate, this includes a direction indicator.

##### Example input
`!pop A9`

##### Example output
![image](https://i.imgur.com/kQLbKZX.png)

<a name="cmdgrid"></a>
## `!grid`
>
<larecipe-badge type="success">Completed</larecipe-badge>

##### Explanation
Very similar to the `!pop` command but shows the data in a 3x3 grid.

##### Example input
`!pop A9`

##### Example output
![image](https://i.imgur.com/nNiAINS.png)

<a name="cmdfind"></a>
## `!find`
>
<larecipe-badge type="success">Completed</larecipe-badge>

##### Explanation
Find a player (steamname only) in your region and their 5 most recent locations.

##### Example input
`!find iShot`

##### Example output
![image](https://i.imgur.com/GF37uyj.png)

<a name="cmdalert"></a>
## `!alert`
>
<larecipe-badge type="success">Completed</larecipe-badge>

##### Explanation
Adding an alert to a coordinate. When a boat (more than 2 players from the same location at the same time) enters this coordinate the bot will post a message with information in the channel this command was used in.

##### Example input
`!alert C4`

##### Example output
![image](https://i.imgur.com/MpMlcBe.png)

*When a ship enters the coordinate you're alerting on:*

![image](https://i.imgur.com/nzYHQzf.png)

<a name="cmdtrack"></a>
## `!track`
>
<larecipe-badge type="success">Completed</larecipe-badge>

##### Explanation
Add a (steam) username to the tracking list. Every time we see that username change coordinates we'll post a message in the channel this command was used in.

##### Example input
`!track 120 THISKK`

*The 120 in this example is the amount of **minutes** you want to track the user for*

##### Example output
![image](https://i.imgur.com/ZbZUCEA.png)

*When a player moves servers / coordinates:*

![image](https://i.imgur.com/t2ZcriX.png)

<a name="cmdfindboat"></a>
## `!findboat`
>
<larecipe-badge type="success">Completed</larecipe-badge>

##### Explanation
When the [`!alert`](#cmdalert) command posts a message it will also give you a boat ID. Using this boat ID you can find out the current locations of all players that were spotted on that boat. This is useful to see if the boat is still around or where it moved too.

This command can only be used on the boatIDs that have been tracked by your server. If you use an ID that was not tracked by your own server it'll not be able to find the boat.

Read the example of the [`!alert`](#cmdalert) command to see how to get the boat ID.

##### Example input
`!findboat 2907`

##### Example output
![image](https://i.imgur.com/hVjciXK.png)



