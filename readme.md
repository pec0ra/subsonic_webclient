Subsonic webclient
==================

This is a webclient for subsonic written in php and javascript. It uses jPlayer to play the audio.

Features
--------

* Written in php and javascript
* JPlayer to play the audio
* Compatible with major browser on Windows, Linux and Android (never tested on Apple products)
* Responsive design
* Pseudo and password sent (only once) through POST instead of GET

Bugs
----
* This is still a beta version, some functions might be unstable
* Doesn't work on Firefox for Android
* In the "Album" tab, the options on "All" and "Selected" menus don't work

How it works
------------

The subsonic webclient (php server side) makes a bridge between the web browser and the subsonic server. The advantage of this architecture is that, if the subsonic webclient is installed on the same server ( or network ) as the subsonic server, the username and password travel only once per session (at the connection) between the user and the server.
Note : If you use the "Remember" option, the information is stored in the cookies as well and thus still travels between the user and the server. The use of encrypted (SSL) connection is recommended.


**First connection to the subsonic server :**
1. The user enters his username, password and subsonic server's URL in a form
2. This identification is sent to the subsonic webclient server via AJAX in POST variables
3. The subsonic webclient server pings the subsonic server to test the identification
4. The subsonic webclient server saves the identification in the sessions (or cookies if you select the "remember" option)

**Communication with the subsonic server :**
1. The web browser sends a request to the subsonic webclient server via AJAX
2. The subsonic webclient server retrieves username, password and subsonic server's URL from the sessions
3. The subsonic webclient gets the data via the Subsonic API and sends it back to the browser


```
________________________                       ________________________
|                      |     Subsonic API      |                      |
|  Subsonic Webclient  | <-------------------> |   Subsonic server    |
|                      |                       |                      |
̣̣̣̣̣̣̣̣̣̣̣̣̣̣̣________________________                       ________________________
         ^
         |
         | AJAX
         |
         v
________________________
|                      |
|     Web browser      |
|                      |
________________________
```

Installation
------------
1. Clone the repository.
2. Create the tables in the database :
	```
	CREATE TABLE IF NOT EXISTS `ci_sessions` (
	  `id` varchar(40) NOT NULL,
	  `ip_address` varchar(45) NOT NULL,
	  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
	  `data` blob NOT NULL,
	  PRIMARY KEY (`id`),
	  KEY `ci_sessions_timestamp` (`timestamp`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1;
	
	CREATE TABLE IF NOT EXISTS `share` (
	  `hash` varchar(10) NOT NULL,
	  `server` varchar(535) NOT NULL,
	  `pseudo` varchar(100) NOT NULL,
	  `password` varchar(300) NOT NULL,
	  `songs` longtext NOT NULL,
	  PRIMARY KEY (`hash`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1;
	```
3. Change the database settings in `/application/config/database.php`