# ProBan Plugin 

<p align="center">
  <img src="https://raw.githubusercontent.com/RajadorDev/ProBan/c1fe4f9bc6a26a530a213abdb6ca85ef8479def5/img/balance.png">
</p>

## About:

This plugin can **ban** and **expel** players, with personalized texts and **discord webhook and UI support**.

## How to use?


Just add the plugin and it will replace PocketMine's standard ban and expulsion commands.

To configure the webhook for your desired **Discord** channel, simply use:

- `/pb webhook <url: string>`

Where `url` should be the url of your webhook.

You can Edit the messages in the file `config.yml` and use `/pb realod`after or restarting after too.

## Commands:

- `/pb`:
  - `info`: Show information about the plugin
  - `webhook <url: string>`: Set your discord webhook link.
  - `reload`: Reload your plugin settings (the **config.yml**)
  - `deletehook`: Will delete the current webhook saved

<br>

- `/ban <player: string> <reason: string>`: Will ban the player by his username

<br>

- `/kick <player: string> <reason: string>`: Will look for a player with the prefix given and kick him. 

If you are using the command in game, and you is not give none args to these commands: `/kick`, `/ban`, `/unban` will open a **UI**

