# Overview

---

- [Introduction](#introduction)
- [Contact](#contact)
- [Development progress](#progress)
- [Background](#background)

<a name="introduction"></a>
## Introduction

The Atlas Discord Bot *(ADB)* is an automated way to track players, alert when boats enter your coordinate and much more for the [WildCard game Atlas](https://playatlas.com).

<a name="contact"></a>
## Contact

Contact the developer through any of the following methods:

**Discord (private):** iShot#5449

**Discord (server):** [ATLAS CCTV](https://discord.gg/KMHkqtb)

<a name="progress"></a>
## Development Progress

<larecipe-card>
    Error Catching
    <larecipe-progress type="warning" :value="25"></larecipe-progress>
</larecipe-card>

<larecipe-card>
    Documentation
    <larecipe-progress type="success" :value="79"></larecipe-progress>
</larecipe-card>


<a name="background"></a>
## Background

I'm currently working as a full-stack developer. In my spare time I still like to experiment around with new technologies and create tools that help with getting and spreading information about games I currently play.

My previous project was for [Albion Online](https://albiononline.com/en/home), this project is a bot that sends kills and battle information to your Discord server 5 min after the kill / battle happens. The project recently converted into a subscription-only model to make sure I can keep up with the server cost it's running on.

Using the [Valve Source Query protocol](https://developer.valvesoftware.com/wiki/Server_queries) we're able to pull player information from the official (and unofficial) Atlas servers. The only information we can access is the `steam username` and the `time spent` on this particular server.