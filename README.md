# planet-server
A server for the Turtle/Music Blocks Planet.

To use, import the planet.sql database schema into MySQL and create a config.php file in libs/ with

```php
<?php
define("DB_HOST", "localhost");
define("DB_USER", "YOUR_MYSQL_USERNAME_HERE");
define("DB_PASSWORD", "YOUR_PASSWORD_HERE");
define("DB_DATABASE", "planet");
define("API_KEY", "YOUR_API_KEY_HERE");
define("SERVER_SECRET", "YOUR_SERVER_SECRET_HERE");
?>
```


## API Documentation:
HTTP POST requests to index.php (with cookie "UserID" set to user ID generated client-side)

Include `api-key` parameter for all requests.

If `"success"=false`, an error message will be available at `"error"`.

### Add a New Project:

#### Query Parameters
`action`: `addProject`

`ProjectJSON`: JSON data for project being uploaded (TB data in b64) e.g.
```JSON
{
	"ProjectID": "1245234",
	"ProjectName": "My Project Name",
	"ProjectDescription": "Lorem ipsum dolor sit amet",
	"ProjectSearchKeywords": "tone vibrato timbre",
	"ProjectData": "W3NvbWVfdGJfZGF0YV9oZXJlXQ==",
	"ProjectImage": "data:image/png;base64,aW1hZ2UgaGVyZQ==", <or "" if none>
	"ProjectIsMusicBlocks": 1,
	"ProjectCreatorName": "anonymous",
	"ProjectTags": ["124","435","234","253","435"]
}
```

#### Response
`{"success": true}` if project upload successful

#### Possible Errors
`ERROR_INVALID_PARAMETERS` - validation error

### Download Project List:

#### Query Parameters
`action`: `downloadProjectList`

`ProjectTags`: array of tag IDs to search by e.g. `["1","2","5"]` OR one of the following:

* `ALL_PROJECTS` - returns all projects, regardless of tags

* `USER_PROJECTS` - returns all projects created by UserID in cookie

`ProjectSort`: field to sort by - `RECENT`, `LIKED`, `DOWNLOADED`, `ALPHABETICAL`

`Start`: index of first project to be returned (inclusive) e.g. `0`

`End`: index of last project to be returned + 1 (exclusive) e.g. `25`

#### Response (array of IDs of projects matching parameters)
```JSON
{
	"success":true,
	"data":[["1245234","2017-12-27 11:30:51"],["3245234325","2017-12-27 11:50:24"]]
}
```
if projects successfully found, empty `"data"` array if no projects found

#### Possible Errors
`ERROR_INVALID_PARAMETERS` - validation error

`ERROR_INTERNAL_DATABASE` - internal server error

### Get Project Details:

#### Query Parameters
`action`: `getProjectDetails`

`ProjectID`: ID of project to get details for e.g. `1245234`

#### Response (more information about the selected project)
```JSON
{
	"success":true,
	"data":{
		"UserID":"1234567890",
		"ProjectName":"My Project Name",
		"ProjectDescription":"Lorem ipsum dolor sit amet",
		"ProjectImage":"data:image/png;base64,aW1hZ2UgaGVyZQ==", <or "">
		"ProjectIsMusicBlocks":1,
		"ProjectCreatorName":"anonymous",
		"ProjectDownloads":2,
		"ProjectLikes":0,
		"ProjectTags":[1,4,2,6],
		"ProjectLastUpdated":"2017-12-27 11:30:51",
		"ProjectCreatedDate":"2017-12-27 11:30:51"
	}
}
```
if project successfully found

#### Possible Errors
`ERROR_INVALID_PARAMETERS` - validation error

`ERROR_PROJECT_NOT_FOUND` - the project requested was not found

### Download Project:

#### Query Parameters
`action`: `downloadProject`

`ProjectID`: ID of project to download e.g. `1245234`

#### Response (TB data in b64)
```JSON
{
	"success":true,
	"data":"W3NvbWVfdGJfZGF0YV9oZXJlXQ=="
}
```
if project data successfully found

#### Possible Errors
`ERROR_INVALID_PARAMETERS` - validation error

`ERROR_PROJECT_NOT_FOUND` - the project requested was not found

### Get Tag Manifest:

#### Query Parameters
`action`: `getTagManifest`

#### Response (all tags with data, indexed by tag ID)
```JSON
{
	"success":true,
	"data":{
		"1":{
			"TagName":"Examples",
			"IsTagUserAddable":"0",
			"IsDisplayTag":"1"
		},
		"2":{
			"TagName":"Music",
			"IsTagUserAddable":"1",
			"IsDisplayTag":"1"
		},
		"3":{
			"TagName":"Art",
			"IsTagUserAddable":"1",
			"IsDisplayTag":"1"
		}
	}
}
```
if tags successfully found

#### Possible Errors
`ERROR_INTERNAL_DATABASE` - internal server error

### Search Projects:

#### Query Parameters
`action`: `searchProjects`

`Search`: search string e.g. `music timbre`

`ProjectSort`: field to sort by - `RECENT`, `LIKED`, `DOWNLOADED`, `ALPHABETICAL`

`Start`: index of first project to be returned (inclusive) e.g. `0`

`End`: index of last project to be returned + 1 (exclusive) e.g. `25`

#### Response (array of IDs of projects matching any of the search terms, most recent first)
```JSON
{
	"success":true,
	"data":[["1245234","2017-12-27 11:30:51"],["3245234325","2017-12-27 11:50:24"]]
}
```
if projects successfully found matching search keywords; empty `"data"` array if no projects found

#### Possible Errors
`ERROR_INVALID_PARAMETERS` - validation error

`ERROR_INTERNAL_DATABASE` - internal server error

### Check If Published:

#### Query Parameters
`action`: `checkIfPublished`

`ProjectIDs`: array of project IDs to check whether or not they are published e.g. `["124523", "5324646"]`

#### Response (JSON object with keys of published project IDs; empty object if no project is published)
```JSON
{
	"success":true,
	"data":{
		"124523":true
	}
}
```
if query successfully completed

#### Possible Errors
`ERROR_INVALID_PARAMETERS` - validation error

`ERROR_INTERNAL_DATABASE` - internal server error

### Like Project:

#### Query Parameters
`action`: `likeProject`

`ProjectID`: ID of project to be (un)liked e.g. `124523`

`Like`: `true` if user is liking project, `false` if user is unliking project

#### Response
`{"success": true}` if like/unlike successful

#### Possible Errors
`ERROR_INVALID_PARAMETERS` - validation error

`ERROR_INTERNAL_DATABASE` - internal server error

`ERROR_ACTION_NOT_PERMITTED` - the action the user is trying to perform is not permitted (n.b. each user can only like a project once - once a user has liked a project, all further like requests will return false until they have unliked it)

### Report Project:

#### Query Parameters
`action`: `reportProject`

`ProjectID`: ID of project to be reported e.g. `124523`

`Description`: a 1000char-max description of why the user is reporting the project

#### Response
`{"success": true}` if report successfully made

#### Possible Errors
`ERROR_INVALID_PARAMETERS` - validation error

`ERROR_INTERNAL_DATABASE` - internal server error

`ERROR_ACTION_NOT_PERMITTED` - if a user tries to report a project multiple times (a user can only report a project once).

### Convert Data:

#### Query Parameters
`action`: `convertData`

`From`: Data format to convert from e.g. `LY`

`To`: Data format to convert to e.g. `PDF`

`Data`: Data to convert (b64 encoded)

#### Possible Conversion Pairs

LilyPond (`.ly`) to PDF (`.PDF`)

#### Response
```JSON
{
	"success":true,
	"data":{
		"contenttype":"file content type e.g. application/pdf",
		"blob":"b64 encoded file data"
	}
}
```

#### Possible Errors
`ERROR_FEATURE_NOT_IMPLEMENTED` - feature has not yet been implemented
`ERROR_CONVERSION_FAILURE` - failed to convert file

## Conversion Features

### Ly2PDF

#### Implementation

Setup server:

Make new group convert - add www-data to group.

`sudo chown -R www-data convert/`

`sudo chgrp -R convert convert/`

`sudo chmod -R 775 convert/`

Make new user lilypond - low privileges. In convert group.

Add to /etc/sudoers 

`php ALL=(lilypond) NOPASSWD: <path-to-lilypond-bin>`

IMPORTANT: Lilypond to be run in CHROOT JAIL on server - this is a security issue if this step is overlooked. http://lilypond.org/doc/v2.18/Documentation/usage/command_002dline-usage.html#lilypond-in-chroot-jail