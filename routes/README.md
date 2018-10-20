# Charitree Routes Request Documentation

## Content
1. Requests & Responses
    1. User Create
    2. Full User edit
    3. Create Authenticated Session (Login)
    4. Check if session is valid
    5. Create Campaign Manager
    6. Get items.
    7. Create Campaign
    8. Create Donation
    9. Get current Campaign Manager Detail
    10. Get all campaigns.


## Preamble

Take note of the following syntax using in the representation of this document.

{{baseurl}} : **pathtothepublicfolderoftheproject**
# User Create

##### Request Body:
| Field      | Description                                                  |
| ---------- | ------------------------------------------------------------ |
| email      | (Required) User email. Has to be an email format and unique. |
| password   | (Required) User password.                                    |
| first_name | User first name.                                             |
| last_name  | User last name.                                              |


##### Request:
```
POST http://{{baseurl}}/users HTTP/1.1
Content-type: application/json

{
	"email":"test@gmail.com",
	"first_name":"test first name",
	"last_name":"test last name",
	"password":"password"
}
```

##### Possible Response:

###### Success:

```
HTTP/1.0 201 Created
Date: Wed, 17 Oct 2018 15:40:15 GMT
Server: Apache/2.4.25 (Debian)
X-Powered-By: PHP/7.2.10
Cache-Control: no-cache, private
Content-Length: 40
Connection: close
Content-Type: application/json

{
  "status": "1",
  "message": "User created."
}

```

###### Failure:

```
HTTP/1.0 422 Unprocessable Entity
Date: Wed, 17 Oct 2018 15:41:02 GMT
Server: Apache/2.4.25 (Debian)
X-Powered-By: PHP/7.2.10
Cache-Control: no-cache, private
Content-Length: 71
Connection: close
Content-Type: application/json

{
  "status": "0",
  "errors": {
      "email":["..."], //list of error messages for email
	  "first_name":["..."], //list of error messages for first_name
	  "last_name":["..."], //list of error messages for last_name
  }
}
```


# Full User Edit

##### Request Body:
| Field      | Description                                                  |
| ---------- | ------------------------------------------------------------ |
| email      | (Required \| email ) User's email. Has to be an email format and unique. |
| first_name | (Required) User first name.                                  |
| last_name  | (Required) User last name.                                   |


##### Request:
```
PUT http://{{baseurl}}/users HTTP/1.0
Content-Type: {{contentype}}

{
    "email":"tobiaslkj@gmail.com",
    "first_name":"tobias",
    "last_name":"lim"
}
```

##### Possible Response:

###### Success:

```
HTTP/1.0 200 OK
Date: Wed, 17 Oct 2018 16:07:39 GMT
Server: Apache/2.4.25 (Debian)
Vary: Authorization
X-Powered-By: PHP/7.2.10
Cache-Control: no-cache, private
Content-Length: 40
Connection: close
Content-Type: application/json

{
  "status": "1",
  "message": "User updated."
}
```

###### Failure:

```
HTTP/1.0 422 Unprocessable Entity
Date: Wed, 17 Oct 2018 15:41:02 GMT
Server: Apache/2.4.25 (Debian)
X-Powered-By: PHP/7.2.10
Cache-Control: no-cache, private
Content-Length: 71
Connection: close
Content-Type: application/json

{
	"status": "0",
	"errors":[
		"email":["..."], //list of error messages for email
		"first_name":["..."], //list of error messages for first_name
		"last_name":["..."], //list of error messages for last_name
	]
}
```


# Create Authenticated Session

Authenticate and create a session for user.

##### Request Body:

| Field    | Description                     |
| -------- | ------------------------------- |
| email    | (Required \| email) User email. |
| password | (Required) User first name.     |

##### Request:
```
POST http://{{baseurl}}/users HTTP/1.0
Content-type: application/json

{
	"email":"test@gmail.com",
	"password":"password"
}
```

##### Possible Response:

###### Success:

```
HTTP/1.0 201 Created
Date: Wed, 17 Oct 2018 15:46:58 GMT
Server: Apache/2.4.25 (Debian)
X-Powered-By: PHP/7.2.10
Cache-Control: no-cache, private
Content-Length: 86
Connection: close
Content-Type: application/json

{
  "status": "1",
  "user_token": "MnZDWWc1ZmJCQXp5TW9QWVdnSmFNclZPbFpHTHlUTDJUTlF2VUNhRw=="
}
```

###### Failure:
```
HTTP/1.0 404 Not Found
Date: Wed, 17 Oct 2018 15:43:49 GMT
Server: Apache/2.4.25 (Debian)
X-Powered-By: PHP/7.2.10
Cache-Control: no-cache, private
Content-Length: 13
Connection: close
Content-Type: text/html; charset=UTF-8

{
  "status": "0"
}
```

# Check if session is valid

```
GET http://{{baseurl}}/sessions HTTP/1.1
Authorization: Basic dG9iaWFzbGtqQGdtYWlsLmNvbTpiVmh2Y3pKV1dVcHVORlp3ZUdoemFVTjJZbEJzY1VnMlYzaGpWMnBxZW01b2NVRnZibWR4ZHc9PQ==
```
##### Possible Response:
###### Success:
```
HTTP/1.0 200 OK
Date: Wed, 17 Oct 2018 16:42:07 GMT
Server: Apache/2.4.25 (Debian)
Vary: Authorization
X-Powered-By: PHP/7.2.10
Cache-Control: no-cache, private
Content-Length: 70
Connection: close
Content-Type: application/json

{
  "status": "1",
  "message": "If you see this, you pass the authorization"
}
```
###### Failure:
```
HTTP/1.0 401 Unauthorized
Date: Wed, 17 Oct 2018 16:39:27 GMT
Server: Apache/2.4.25 (Debian)
Vary: Authorization
X-Powered-By: PHP/7.2.10
Cache-Control: no-cache, private
Content-Length: 13
Connection: close
Content-Type: text/html; charset=UTF-8

Unauthorized.

```
# Create Campaign Manager
Create a campaign manager based on a registered user.

##### Request Body:

| Field             | Description                                                          |
| ----------------- | -------------------------------------------------------------------- |
| UEN               | (Required \| 9 to 10 characters) Unique entity number.               |
| organization_name | (Required) Name of the organization the campaign manager belongs to. |

##### Request:
```
POST http://{{baseurl}}/users/campaignmanager HTTP/1.1
Authorization: Basic dG9iaWFzbGtqQGdtYWlsLmNvbTpWVkp5VUVkT2VUSllkVlY1WWpWb1YxbzBZV2xzWW1KTGJuWmFSRlIwY2tkVmRrTk5ZVkZoYVE9PQ==
Content-Type: application/json

{
	"UEN":"T1993113M",
	"organization_name":"Toby's Holdings"
}
```

##### Possible Response:

###### Success:

```
HTTP/1.0 200 OK
Date: Wed, 17 Oct 2018 16:24:07 GMT
Server: Apache/2.4.25 (Debian)
Vary: Authorization
X-Powered-By: PHP/7.2.10
Cache-Control: no-cache, private
Content-Length: 51
Connection: close
Content-Type: application/json

{
  "status": "1",
  "message": "Campaign Manager Created"
}
```

###### Failure:

```
HTTP/1.0 422 Unprocessable Entity
Date: Wed, 17 Oct 2018 15:41:02 GMT
Server: Apache/2.4.25 (Debian)
X-Powered-By: PHP/7.2.10
Cache-Control: no-cache, private
Content-Length: 71
Connection: close
Content-Type: application/json

{
	"status": "0",
	"errors":[
		"UEN":["..."], //list of error messages for UEN
		"organization_name":["..."] //list of error messages for organization_name
	]
}
```

# Get items
Get all possible categories of donable items.


##### Request:
```
GET http://{{baseurl}}/items
Authorization: Basic dG9iaWFzbGtqQGdtYWlsLmNvbTpWVkp5VUVkT2VUSllkVlY1WWpWb1YxbzBZV2xzWW1KTGJuWmFSRlIwY2tkVmRrTk5ZVkZoYVE9PQ==
Content-type: application/json
```

##### Possible Response:

###### Success:

```
HTTP/1.0 200 OK
Date: Thu, 18 Oct 2018 13:20:32 GMT
Server: Apache/2.4.25 (Debian)
X-Powered-By: PHP/7.2.10
Cache-Control: no-cache, private
Content-Length: 206
Connection: close
Content-Type: application/json

{
  "status": "1",
  "items": [{
    "id": 1,
    "name": "Newspaper"
  }, {
    "id": 2,
    "name": "Glass"
  }, {
    "id": 3,
    "name": "Cardboard"
  }, {
    "id": 4,
    "name": "Toys"
  }, {
    "id": 5,
    "name": "Furniture"
  }, {
    "id": 6,
    "name": "Plastic"
  }, {
    "id": 7,
    "name": "Metals"
  }]
}
```

# Create Campaign
Create a campaign.


##### Request Body:
| Field      | Description                                   |
| ---------- | --------------------------------------------- |
| name       | (Required) Campaign name.                     |
| start_date | (Required \| dd-MMM-yyyy) Campaign Start Date |
| end_date   | (Required \| dd-MMM-yyyy) Campaign End Date   |
|accepted_items|(Required \| array) List of items the campaign requires


##### Request:
```
POST http://{{baseurl}}/campaigns
Authorization: Basic dG9iaWFzbGtqQGdtYWlsLmNvbTpWVkp5VUVkT2VUSllkVlY1WWpWb1YxbzBZV2xzWW1KTGJuWmFSRlIwY2tkVmRrTk5ZVkZoYVE9PQ==
Content-type: application/json

{
	"name":"Run for Harri",
	"start_date":"06-Oct-1995",
	"end_date":"07-Oct-1997",
    "accepted_items":[1,2,3,4]
}
```

##### Possible Response:

###### Success:

```
HTTP Status: 200 

{
	"status": "1",
	"message": "Campaign Created"
}
```

###### Failure:

```
HTTP Status: 422

{
	"status": "0",
	"errors":[
		"campaign_name":["..."], //list of error messages for campaign_name
		"start_date":["..."],  //list of error messages for start_date
		"end_date":"["..."]  //list of error messages for end_date
	]
}
```
# Create Donation
Donation can be created for a certain campaign. Pass in the ID as part of the request URL to create a donation for a specific campaign. 

##### Request Body:
| Field      | Description                                   |
| ---------- | --------------------------------------------- |
| items       | (Required \| Object)                       |
| items.keys | (Required \| array \| int) Items keys array.|
| items.values   | (Required \| array \| int) Array of corresponding value   |

Request:
```
POST http://{{baseurl}}/campaigns/24/donations HTTP/1.0
Authorization: Basic dG9iaWFzbGtqQG1haWwuY29tOlJtUk5RMEpSV0VsU1JXeEtiMDkzUjBKNWFqZDRkRTQ0VWxZM1JUSnhlVlo0Ym10MGNtcHJOUT09
Content-type: application/json
{
	"items":{
		"keys":[1,2,4],
		"values":[4,4,5]
	}
}
```
Response:
###### Success:
```
HTTP/1.0 201 Created
Date: Sat, 20 Oct 2018 03:50:35 GMT
Server: Apache/2.4.25 (Debian)
Vary: Authorization
X-Powered-By: PHP/7.2.10
Cache-Control: no-cache, private
Content-Length: 42
Connection: close
Content-Type: application/json

{
  "status": "1",
  "message": "Donation added."
}
```
###### Failure:
```
HTTP/1.0 404 Not Found
Date: Sat, 20 Oct 2018 04:04:51 GMT
Server: Apache/2.4.25 (Debian)
Vary: Authorization
X-Powered-By: PHP/7.2.10
Cache-Control: no-cache, private
Content-Length: 66
Connection: close
Content-Type: application/json

{
  "status": "0",
  "errors": "Request campaign not found or has ended."
}
```

# Get current Campaign Manager Detail
Get the current session's campaign manager detail. If current session is not a campaign manager, 404 will be returned.

Request:
```
GET http://{{baseurl}}/campaignmanagers HTTP/1.0
Authorization: Basic dG9iaWFzbGtqQG1haWwuY29tOlJtUk5RMEpSV0VsU1JXeEtiMDkzUjBKNWFqZDRkRTQ0VWxZM1JUSnhlVlo0Ym10MGNtcHJOUT09
```
Response:
###### Success:
```
HTTP/1.0 200 OK
Date: Sat, 20 Oct 2018 05:14:32 GMT
Server: Apache/2.4.25 (Debian)
Vary: Authorization
X-Powered-By: PHP/7.2.10
Cache-Control: no-cache, private
Content-Length: 87
Connection: close
Content-Type: application/json

{
  "status": "1",
  "campaign_manager": {
    "cid": 1,
    "UEN": "S91291232",
    "organization_name": "www"
  }
}
```
###### Failure:
```
HTTP/1.0 403 Forbidden
Date: Sat, 20 Oct 2018 05:12:04 GMT
Server: Apache/2.4.25 (Debian)
Vary: Authorization
X-Powered-By: PHP/7.2.10
Cache-Control: no-cache, private
Content-Length: 60
Connection: close
Content-Type: application/json

{
  "status": "0",
  "message": "Only allowed for campaign manager"
}

```



# Get all Campaigns

Retrieve all campaigns.


##### Request:
To be added.

##### Possible Response:

###### Success:

```

```

###### Failure:

```

```



