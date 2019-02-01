# Technology

---

- [Explained](#explained)
- [Stack](#stack)
- [Timeframe](#timeframe)

<a name="explained"></a>
## Explained

This application is fully independent, it does not consume any third-party API's. Instead, it does all the server tracking itself and serves it's own private API which is then consumed by the DiscordJS script (the bot so to speak).
There are currently no plans to make the API public, if you have questions you can always [contact me](/docs/{{version}}/overview#contact).

<a name="stack"></a>
## Stack

This bot was written and is running using the following technology:
- Backend (server-side)
    - LEMP stack
        - Ubuntu 16
        - NGINX
        - MySQL
        - PHP-FPM 7.3
    - Redis Server (&cli)
    - Laravel 5.7.7
    - Laravel Echo Server (socket.io server)
    - Laravel Snappy (wkhtmltoX wrapper)
    - LaravelUuid
    - PhpSourceQuery
- Frontend
    - Bot
        - DiscordJS
        - Laravel Echo (socket.io client)
        - MomentJS (for timestamps)
        - TextTable (plain text table formatting)
    - Website
        - LaRecipe (documentation)
        - Vue.js
    - Dashboard
        - AdminLTE
        - DataTables
        
        
<a name="timeframe"></a>
## Timeframe

First commit happened `21 Januari 2019`.

The project is currently still under active development