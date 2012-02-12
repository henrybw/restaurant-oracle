Restaurant Oracle
=================

Web application powered by [RevMiner](http://www.revminer.com/) for providing restaurant suggestions based on group dining preferences. Suggestions are determined based on profiles which indicate each person's likes and dislikes, as well as current information (such as location, time, dining cost, etc.).

Directory Structure
-------------------

* `src/`
	* Contains source files for the web application (including its front-end interface) and the Android front-end application.
* `schema/`
	* Contains SQL files that describe the table structure of the database. These can be run directly to recreate the database layout.
* `tools/`
	* Contains miscellaneous scripts for website maintenance. For example, scripts to populate the database from raw JSON data live here.
* `data`
	* Contains SQL and JSON data dumps that we use to populate the database. These are more miscellaneous data dumps; the actual database dump is in `schema/`.