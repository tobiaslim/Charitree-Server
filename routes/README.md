# Charitree API Request Documentation

## Content
1. Accounts and Session Management
    1. [User Create](#user-create)
    2. [Full user edit](#full-user-edit)
    3. [Create Address for user](#create-address-for-user)
    4. [Get addresses by current user session](#get-addresses-by-current-user-session)
    5. [Create Authenticated Session](#create-authenticated-session)
    6. [Check if session is valid](#check-if-session-is-valid)
    7. [Get organization name based on UEN](#get-organization-name-based-on-uen)
    7. [Create Campaign Manager **(Campaign Manager Only)**](#create-campaign-manager)
    8. [Get current session CM information **(Campaign Manager Only)**](#get-current-session-cm-information)

2. Campaigns Management
    1.  [Get all Campaigns](#get-all-campaigns)
    2.  [Create Campaign **(Campaign Manager Only)**](#create-campaign)
    3.  [Get all Campaigns By Current Campaign Manager Session **(Campaign Manager Only)**](#get-all-campaigns-by-current-campaign-manager-session)
    4. [Get campaign by campaign id **(Campaign Manager Only)**](#get-campaign-by-campaign-id)

3. Donations Management
    1. [Get items](#get-items)
    2. [Create Donation](#create-donation)
    2. [Get donation by donation id](#get-donation-by-donation-id)
    3. [Get all donations by the current user session](#get-all-donations-by-the-current-user-session)
    4. [Get number of successful donations by the current user session](#get-number-of-successful-donations-by-the-current-user-session)
    4. [Get donation by donation ID for Campaign Manager **(Campaign Manager Only)**](#get-donation-by-donation-id-for-campaign-manager)
    5. [Get donations of campaign by campaign id and CM session **(Campaign Manager Only)**](#get-donations-of-campaign-by-campaign-id-and-cm-session)
    6. [Change status of a donation **(Campaign Manager Only)**](#change-status-of-a-donation)


## Preamble

Take note of the following syntax using in the representation of this document.

{{baseurl}} : **pathtothepublicfolderoftheproject**

**TAKE NOTE: status and Http status code (or http code or http status) used in the context of this document refers two different attributes.**


# All Routes:
When requesting from a resource from the server, the response object will always carry a ```status``` attribute of either **1 or 0**. If 1 is returned, a description of the outcome of the operation will be provided in the ```message``` attribute of the response object. If 0 is returned, an ```errors``` object will be returned to describe the error(s) occured. The ```errors``` ojbect will provide a ```message``` attribute to describe the general error. Request parameters that  triggers an error will exist as an attribute with the parameter name. The value of the attribute will carry an array of error messages for that parameter.

```
{
  "status" : "0 or 1" 
  "message" : "Success message" //exists if status = 1
  "errors" : {
    "message":"General description of the error", //exists if status = 0
    ...
    "email": ["Must be an email format", "Must not be longer than 100 characters."]
  }
}
```

Generally, when a status has a value of 1, it Http code will be in the 2xx range. Whereas for status with a value 0, it will be in the 4xx range. 

| HTTP CODE | Status code | message exist? | errors.message exist?                 |
| --------- | ----------- | -------------- | ------------------------------------- |
| 200       | 1           | Yes            | No                                    |
| 201       | 1           | Yes            | No                                    |
| 403       | 0           | No             | Yes                                   |
| 404       | 0           | No             | Yes                                   |
| 415       | 0           | No             | Yes                                   |
| 422       | 0           | No             | Yes (Check violating parameters also) |

Additionally, all request should be send with a header of ```Content-Type: application/json```. If the headers received by the servers does not consist of the header, Http status code 415 will be returned.

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
    "message": "Not able to process the request parameters."
    "email":["..."], //list of error messages for email
	  "first_name":["..."], //list of error messages for first_name
	  "last_name":["..."], //list of error messages for last_name
  }
}
```


# Full User Edit

##### Request Body:
| Field      | Description                                                              |
| ---------- | ------------------------------------------------------------------------ |
| email      | (Required \| email ) User's email. Has to be an email format and unique. |
| first_name | (Required) User first name.                                              |
| last_name  | (Required) User last name.                                               |


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
    "message": "Not able to process the request parameters.",
		"email":["..."], //list of error messages for email
		"first_name":["..."], //list of error messages for first_name
		"last_name":["..."], //list of error messages for last_name
	]
}
```

# Create Address for user
| Field                    | Description                                                             |
| ------------------------ | ----------------------------------------------------------------------- |
| addresses                | (required \| array) Store addresses to be created                       |
| addresses.\*             | (object) Address objects to be created                                  |
| addresses.\*.street_name | (required \| alpha numeric with space string \| max:45 ) Street address |
| addresses.\*.unit        | (optional \| max:10 ) Unit number                                       |
| addresses.\*.zip         | (required \| size exactly 6 ) Postal code excluding 'S'                 |

##### Request
```
POST http://{{baseurl}}/addresses HTTP/1.0
Authorization: Basic dG9iaWFzbGtqQG1haWwuY29tOlJtUk5RMEpSV0VsU1JXeEtiMDkzUjBKNWFqZDRkRTQ0VWxZM1JUSnhlVlo0Ym10MGNtcHJOUT09
Content-type: application/json
```

##### Response:
###### Success

```
HTTP/1.0 201 Created
Date: Wed, 24 Oct 2018 05:45:36 GMT
Server: Apache/2.4.25 (Debian)
Vary: Authorization
X-Powered-By: PHP/7.2.10
Cache-Control: no-cache, private
Content-Length: 228
Connection: close
Content-Type: application/json

{
  "status": 1,
  "message": "Addresses created",
  "addresses": [{
    "id": 29,
    "street_name": "Block 469 AdmiraltyDrive",
    "unit": "#16-55",
    "zip": "750469",
    "user_id": 2
  }, {
    "id": 30,
    "street_name": "Bhehehee",
    "unit": "#16-25",
    "zip": "750322",
    "user_id": 2
  }]
}
```

# Get addresses by current user session
Get all the addresses of the current user by the session token

#### Request
```
GET http://{{baseurl}}/addresses HTTP/1.0
Authorization: Basic dG9iaWFzbGtqQG1haWwuY29tOlJtUk5RMEpSV0VsU1JXeEtiMDkzUjBKNWFqZDRkRTQ0VWxZM1JUSnhlVlo0Ym10MGNtcHJOUT09
Content-type: application/json
```

#### Response
##### Success
```
HTTP/1.0 200 OK
Date: Wed, 31 Oct 2018 16:14:31 GMT
Server: Apache/2.4.25 (Debian)
Vary: Authorization
X-Powered-By: PHP/7.2.10
Cache-Control: no-cache, private
Content-Length: 565
Connection: close
Content-Type: application/json

{
  "status": 1,
  "message": "User's addresses",
  "addresses": [{
    "id": 1,
    "street_name": "Block 469 AdmiraltyDrive",
    "unit": "#16-55",
    "zip": "750469",
    "user_id": 1
  }, {
    "id": 2,
    "street_name": "Bhehehee",
    "unit": "#16-25",
    "zip": "750322",
    "user_id": 1
  }, {
    "id": 3,
    "street_name": "Block 469 AdmiraltyDrive",
    "unit": "#16-55",
    "zip": "750469",
    "user_id": 1
  }, {
    "id": 4,
    "street_name": "Bhehehee",
    "unit": "#16-25",
    "zip": "750322",
    "user_id": 1
  }, {
    "id": 5,
    "street_name": "Block 469 AdmiraltyDrive",
    "unit": "#16-55",
    "zip": "750469",
    "user_id": 1
  }, {
    "id": 6,
    "street_name": "Bhehehee",
    "unit": "#16-25",
    "zip": "750322",
    "user_id": 1
  }]
}
```
##### Failure
```
HTTP/1.0 404 Not Found
Date: Wed, 31 Oct 2018 16:15:53 GMT
Server: Apache/2.4.25 (Debian)
Vary: Authorization
X-Powered-By: PHP/7.2.10
Cache-Control: no-cache, private
Content-Length: 54
Connection: close
Content-Type: application/json

{
  "status": 0,
  "errors": {
    "message": "No addresses found"
  }
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
POST http://{{baseurl}}/sessions HTTP/1.0
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
  "message":"Session Created",
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
  "status": "0",
  "errors": {
    "message":"Username or password not found."
  }
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

# Get organization name based on UEN
Pass in UEN to retrieve organization name.

#### Request
```
GET http://{{baseurl}}/uen HTTP/1.0
Content-type: application/json

{
	"uen":"T18SS0068E"
}
```

#### Response

##### Success
```
HTTP/1.0 200 OK
Date: Thu, 01 Nov 2018 08:07:00 GMT
Server: Apache/2.4.25 (Debian)
X-Powered-By: PHP/7.2.10
Cache-Control: no-cache, private
Content-Length: 106
Connection: close
Content-Type: application/json

{
  "status": 1,
  "message": "Organization name found.",
  "organization_name": "YISHUN ORCHID RESIDENTS' COMMITTEE"
}
```

##### Failure
```
HTTP/1.0 404 Not Found
Date: Thu, 01 Nov 2018 08:07:58 GMT
Server: Apache/2.4.25 (Debian)
X-Powered-By: PHP/7.2.10
Cache-Control: no-cache, private
Content-Length: 73
Connection: close
Content-Type: application/json

{
  "status": "0",
  "errors": {
    "message": "UEN not found or have deregistered."
  }
}
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
POST http://{{baseurl}}/users/campaignmanagers HTTP/1.1
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
    "message": "Not able to process the request parameters.",
		"UEN":["..."], //list of error messages for UEN
		"organization_name":["..."] //list of error messages for organization_name
	]
}
```

# Get current session CM information

You can use this API to check if the current session is a CM

##### Request:
```
GET http://{{baseurl}}/users/campaignmanagers HTTP/1.1
Content-type: application/json
Authorization: Basic dG9iaWFzbGtqQGdtYWlsLmNvbTpiVmh2Y3pKV1dVcHVORlp3ZUdoemFVTjJZbEJzY1VnMlYzaGpWMnBxZW01b2NVRnZibWR4ZHc9PQ==
```

#####  Response:
###### Success:
```
HTTP/1.0 200 OK
Date: Sun, 21 Oct 2018 04:31:02 GMT
Server: Apache/2.4.25 (Debian)
Vary: Authorization
X-Powered-By: PHP/7.2.10
Cache-Control: no-cache, private
Content-Length: 90
Connection: close
Content-Type: application/json

{
  "status": "1",
  "campaign_manager": {
    "cid": 3,
    "UEN": "T19932220",
    "organization_name": "Tobias"
  }
}
```
###### Failure:
```
HTTP/1.0 403 Forbidden
Date: Sun, 21 Oct 2018 05:20:55 GMT
Server: Apache/2.4.25 (Debian)
Vary: Authorization
X-Powered-By: PHP/7.2.10
Cache-Control: no-cache, private
Content-Length: 72
Connection: close
Content-Type: application/json

{
  "status": "0",
  "message": {
    "message": "Only allowed for campaign manager"
  }
}
```
# Get all Campaigns

Retrieve campaigns.


##### Request body:
| Field | Description                                                                              |
| ----- | ---------------------------------------------------------------------------------------- |
| max   | (int) Get a maximum number of campaigns. If not specified, all campaigns will be return. |

##### Request:
```
GET http://{{baseurl}}/campaigns HTTP/1.0
Content-type: application/json

{
  "max":5
}
```
##### Possible Response:

###### Success:

```
HTTP/1.0 200 OK
Date: Wed, 24 Oct 2018 12:38:26 GMT
Server: Apache/2.4.25 (Debian)
X-Powered-By: PHP/7.2.10
Cache-Control: no-cache, private
Content-Length: 978
Connection: close
Content-Type: application/json

{
  "status": "1",
  "messages": "All campaigns.",
  "campaigns": [{
    "id": 2,
    "name": "Run For Charity",
    "start_date": "2018-10-25",
    "end_date": "2018-10-26",
    "start_time": 12,
    "end_time": 17,
    "description": "wtfffff",
    "collection_point": "Block ass",
    "postal_code": "750469",
    "cid": 1,
    "campaign_manager": {
      "cid": 1,
      "UEN": "T19932220",
      "organization_name": "Tobias",
      "name": "test first name test last name"
    },
    "accepted_items": [{
      "key": 1,
      "value": "Newspaper"
    }, {
      "key": 2,
      "value": "Glass"
    }, {
      "key": 3,
      "value": "Cardboard"
    }, {
      "key": 4,
      "value": "Toys"
    }],
    "days_left": 0
  }, {
    "id": 1,
    "name": "Run For Charity",
    "start_date": "2019-08-20",
    "end_date": "2019-08-21",
    "start_time": 12,
    "end_time": 17,
    "description": "wtfffff",
    "collection_point": "Block ass",
    "postal_code": "750469",
    "cid": 1,
    "campaign_manager": {
      "cid": 1,
      "UEN": "T19932220",
      "organization_name": "Tobias",
      "name": "test first name test last name"
    },
    "accepted_items": [{
      "key": 1,
      "value": "Newspaper"
    }, {
      "key": 2,
      "value": "Glass"
    }, {
      "key": 3,
      "value": "Cardboard"
    }, {
      "key": 4,
      "value": "Toys"
    }],
    "days_left": 299
  }]
}
```
###### Failure:

```
HTTP/1.0 404 Not Found
Date: Tue, 23 Oct 2018 07:57:00 GMT
Server: Apache/2.4.25 (Debian)
X-Powered-By: PHP/7.2.10
Cache-Control: no-cache, private
Content-Length: 61
Connection: close
Content-Type: application/json

{
  "status": "0",
  "error": "No campaigns found.",
  "campaigns": null
}
```

# Create Campaign
Create a campaign.


##### Request Body:
| Field            | Description                                                                            |
| ---------------- | -------------------------------------------------------------------------------------- |
| name             | (Required) Campaign name.                                                              |
| start_date       | (Required \| yyyy-mm-dd) Campaign Start Date                                           |
| end_date         | (Required \| yyyy-mm-dd) Campaign End Date                                             |
| accepted_items   | (Required \| array | int ) List of items the campaign requires                         |
| start_time       | (Required \| int \| 0 - 23) Hours from 0000HRS.                                        |
| end_time         | (Required \| int \| 0 - 24) Hours from 0000HRS. Must be greater or equal to start_time |
| description      | (Required \| string \| max:255 )                                                       |
| collection_point | (required \| alpha numeric with space string \| max:45 ) Street address                |
| postal_code      | (Required \| string \| size:6) Postal code of collection point                         |


##### Request:
```
POST http://{{baseurl}}/campaigns
POST {{baseurl}}/campaigns HTTP/1.0
{{authorization}}
{{contentype}}

{
    "name": "Run For Charity",
    "start_date": "2018-10-25",
    "end_date": "2018-10-26",
    "accepted_items": [1,2,3,4],
	  "start_time": "12",
	  "end_time": "17",
	  "description": "wtfffff",
	  "collection_point": "Block ass",
	  "postal_code": "750469"
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
    "message": "Not able to process the request parameters.",
		"campaign_name":["..."], //list of error messages for campaign_name
		"start_date":["..."],  //list of error messages for start_date
		"end_date":"["..."]  //list of error messages for end_date
	]
}
```
# Get all Campaigns By Current Campaign Manager Session

Retrieve campaigns by current campaign manager session.
##### Request
```
GET http://{{baseurl}}/campaigns/campaignmanagers HTTP/1.0
Authorization: Basic dG9iaWFzbGtqQG1haWwuY29tOlJtUk5RMEpSV0VsU1JXeEtiMDkzUjBKNWFqZDRkRTQ0VWxZM1JUSnhlVlo0Ym10MGNtcHJOUT09
Content-type: application/json
```
##### Possible Response

##### Success
```
HTTP/1.0 200 OK
Date: Mon, 05 Nov 2018 16:24:13 GMT
Server: Apache/2.4.25 (Debian)
Vary: Authorization
X-Powered-By: PHP/7.2.10
Cache-Control: no-cache, private
Content-Length: 583
Connection: close
Content-Type: application/json

{
  "status": "1",
  "messages": "All campaigns.",
  "campaigns": [{
    "id": 1,
    "name": "Run For Charity",
    "start_date": "2018-11-03",
    "end_date": "2018-11-04",
    "start_time": 12,
    "end_time": 17,
    "description": "wtfffff",
    "collection_point": "Block ass",
    "postal_code": "750469",
    "cid": 1,
    "total_donations": 2,
    "pending_donations": 0,
    "inprogress_donations": 1,
    "campaign_manager": {
      "cid": 1,
      "UEN": "T19932220",
      "organization_name": "Tobias",
      "name": "test first name test last name"
    },
    "accepted_items": [{
      "key": 1,
      "value": "Newspaper"
    }, {
      "key": 2,
      "value": "Glass"
    }, {
      "key": 3,
      "value": "Cardboard"
    }, {
      "key": 4,
      "value": "Toys"
    }],
    "days_left": 2
  }]
}
```

##### Failed
```
{
    "status": "0",
    "errors": {
        "message": "No campaigns found"
    }
}

```

# Get Campaign by Campaign ID
Get a single campaign by a campaign ID.

##### Request
```
GET http://{{baseurl}}/campagins/{campaignID} HTTP/1.0
Content-type: application/json
```

##### Success
```
HTTP/1.0 200 OK
Date: Fri, 02 Nov 2018 16:12:26 GMT
Server: Apache/2.4.25 (Debian)
X-Powered-By: PHP/7.2.10
Cache-Control: no-cache, private
Content-Length: 491
Connection: close
Content-Type: application/json

{
  "status": 1,
  "message": "Campaign found.",
  "campaign": {
    "id": 1,
    "name": "Run For Charity",
    "start_date": "2018-11-03",
    "end_date": "2018-11-04",
    "start_time": 12,
    "end_time": 17,
    "description": "wtfffff",
    "collection_point": "Block ass",
    "postal_code": "750469",
    "accepted_items": [{
      "key": 1,
      "value": "Newspaper"
    }, {
      "key": 2,
      "value": "Glass"
    }, {
      "key": 3,
      "value": "Cardboard"
    }, {
      "key": 4,
      "value": "Toys"
    }],
    "campaign_manager": {
      "id": 1,
      "email": "tobias123@gmail.com",
      "first_name": "test first name",
      "last_name": "test last name"
    }
  }
}
```

##### Failure
```
HTTP/1.0 404 Not Found
Date: Fri, 02 Nov 2018 16:14:42 GMT
Server: Apache/2.4.25 (Debian)
X-Powered-By: PHP/7.2.10
Cache-Control: no-cache, private
Content-Length: 55
Connection: close
Content-Type: application/json

{
  "status": 0,
  "errors": {
    "message": "Campaign not found."
  }
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
  "message": "Collect your items."
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
# Create Donation
Donation can be created for a certain campaign. Pass in the campaign ID as part of the request URL to create a donation for a specific campaign. 

##### Request Body:
| Field        | Description                                             |
| ------------ | ------------------------------------------------------- |
| address_id   | (Required \| int ) Address user choose for pick up.     |
| items        | (Required \| Object)                                    |
| items.keys   | (Required \| array \| int) Items keys array.            |
| items.values | (Required \| array \| int) Array of corresponding value |
| pickup_date  | (Required)                                            |
| pickup_time  | (Required)                                            |

Request:
```
POST http://{{baseurl}}/donations/campaigns/{campaignID} HTTP/1.0
Authorization: Basic dG9iaWFzbGtqQG1haWwuY29tOlJtUk5RMEpSV0VsU1JXeEtiMDkzUjBKNWFqZDRkRTQ0VWxZM1JUSnhlVlo0Ym10MGNtcHJOUT09
Content-type: application/json
{
	"address_id":31,
    "pickup_date":"01/11/2018",
    "pickup_time":"12:39",
	"items":{
		"keys":[1,2,4],
		"values":[4,4,5]
	}
}
```
#### Response:
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
  "errors": {
    "message": "Campaign not found or has expired."
  }
}
```
# Get donation by donation ID

##### Request
```
GET http://{{baseurl}}/donations/{donationID} HTTP/1.0
Authorization: Basic dG9iaWFzbGtqQG1haWwuY29tOlJtUk5RMEpSV0VsU1JXeEtiMDkzUjBKNWFqZDRkRTQ0VWxZM1JUSnhlVlo0Ym10MGNtcHJOUT09
Content-type: application/json
```
##### Response:
###### Success
```
HTTP/1.0 200 OK
Date: Mon, 05 Nov 2018 15:50:28 GMT
Server: Apache/2.4.25 (Debian)
Vary: Authorization
X-Powered-By: PHP/7.2.10
Cache-Control: no-cache, private
Content-Length: 523
Connection: close
Content-Type: application/json

{
  "status": "1",
  "message": "Donation returned",
  "donation": {
    "did": 1,
    "status": "Cancelled",
    "volunteer_name": "",
    "volunteer_HP": "",
    "pickup_date": "date",
    "pickup_time": "time",
    "Campaign_id": 1,
    "User_id": 2,
    "Address_id": 1,
    "address": {
      "id": 1,
      "street_name": "Block 469 AdmiraltyDrive",
      "unit": "#16-55",
      "zip": "750469",
      "user_id": 2
    },
    "campaign": {
      "id": 1,
      "name": "Run For Charity",
      "start_date": "2018-11-03",
      "end_date": "2018-11-04",
      "start_time": 12,
      "end_time": 17,
      "description": "wtfffff",
      "collection_point": "Block ass",
      "postal_code": "750469",
      "cid": 1
    }
  }
}
```

###### Failure
```
HTTP/1.0 404 Not Found
Date: Wed, 31 Oct 2018 13:07:06 GMT
Server: Apache/2.4.25 (Debian)
Vary: Authorization
X-Powered-By: PHP/7.2.10
Cache-Control: no-cache, private
Content-Length: 79
Connection: close
Content-Type: application/json

{
  "status": "0",
  "errors": {
    "message": "This user does not have this donation ID."
  }
}
```
# Get all donations by the current user session

##### Request
```
GET http://{{baseurl}}/donations HTTP/1.0
Authorization: Basic dG9iaWFzbGtqQG1haWwuY29tOlJtUk5RMEpSV0VsU1JXeEtiMDkzUjBKNWFqZDRkRTQ0VWxZM1JUSnhlVlo0Ym10MGNtcHJOUT09
Content-type: application/json
```

##### Possible Response

##### Success
```
{
  "status": 1,
  "message": "Donations of a user.",
  "donations": [{
    "did": 1,
    "status": "In Progress",
    "volunteer_name": "tobias lim",
    "volunteer_HP": "83669795",
    "pickup_date": "date",
    "pickup_time": "time",
    "Campaign_id": 1,
    "User_id": 3,
    "Address_id": 2,
    "items": [{
      "id": 1,
      "name": "Newspaper",
      "qty": 4
    }, {
      "id": 2,
      "name": "Glass",
      "qty": 4
    }, {
      "id": 4,
      "name": "Toys",
      "qty": 5
    }],
    "campaign": {
      "id": 1,
      "name": "Hair For Charity",
      "start_date": "2018-11-05",
      "end_date": "2018-11-06",
      "start_time": 12,
      "end_time": 17,
      "description": "wtfffff",
      "collection_point": "Block ass",
      "postal_code": "750469",
      "cid": 2
    },
    "address": {
      "id": 2,
      "street_name": "Bhehehee",
      "unit": "#16-25",
      "zip": "750322",
      "user_id": 3
    }
  }]
}
```
##### Failed
```
HTTP/1.0 404 Not Found
Date: Tue, 23 Oct 2018 16:27:47 GMT
Server: Apache/2.4.25 (Debian)
Vary: Authorization
X-Powered-By: PHP/7.2.10
Cache-Control: no-cache, private
Content-Length: 72
Connection: close
Content-Type: application/json

{
  "status": 0,
  "errors": {
    "message": "No donations found."
  },
  "donations": null
}
```
# Get number of successful donations by the current user session
| Field        | Description                                             |
| ------------ | ------------------------------------------------------- |
| countBy   | (Required \| string \| ['Completed', 'Rejected', 'Cancelled', 'Pending'] ) Status of donations that are intended to count.     |
##### Request
GET http://{{baseurl}}/donations/count HTTP/1.0
Authorization: Basic dG9iaWFzbGtqQG1haWwuY29tOlJtUk5RMEpSV0VsU1JXeEtiMDkzUjBKNWFqZDRkRTQ0VWxZM1JUSnhlVlo0Ym10MGNtcHJOUT09
Content-type: application/json

{
  'countBy': "Completed"
}

##### Response
###### Success
```
HTTP/1.0 200 OK
Date: Mon, 05 Nov 2018 03:52:52 GMT
Server: Apache/2.4.25 (Debian)
Vary: Authorization
X-Powered-By: PHP/7.2.10
Cache-Control: no-cache, private
Content-Length: 44
Connection: close
Content-Type: application/json

{
  "status": 1,
  "count": 0,
  "countBy": "Completed"
}
```
###### Failure
```
HTTP/1.0 422 Unprocessable Entity
Date: Mon, 05 Nov 2018 03:50:39 GMT
Server: Apache/2.4.25 (Debian)
Vary: Authorization
X-Powered-By: PHP/7.2.10
Cache-Control: no-cache, private
Content-Length: 108
Connection: close
Content-Type: application/json

{
  "status": "0",
  "errors": {
    "countBy": ["The selected count by is invalid."],
    "message": "Unproccessable request"
  }
}
```

# Get donation by donation ID for Campaign Manager
##### Request 
GET http://{{baseurl}}/donations/{donationID}/campaignmanagers

##### Response
###### Success
```
HTTP/1.0 200 OK
Date: Mon, 05 Nov 2018 15:46:20 GMT
Server: Apache/2.4.25 (Debian)
Vary: Authorization
X-Powered-By: PHP/7.2.10
Cache-Control: no-cache, private
Content-Length: 498
Connection: close
Content-Type: application/json

{
  "status": 1,
  "donation": {
    "did": 2,
    "status": "Rejected",
    "volunteer_name": null,
    "volunteer_HP": null,
    "pickup_date": "date",
    "pickup_time": "time",
    "Campaign_id": 1,
    "User_id": 2,
    "Address_id": 1,
    "user": {
      "id": 2,
      "email": "tobiaslkj@gmail.com",
      "first_name": "test first name",
      "last_name": "test last name"
    },
    "address": {
      "id": 1,
      "street_name": "Block 469 AdmiraltyDrive",
      "unit": "#16-55",
      "zip": "750469",
      "user_id": 2
    },
    "items": [{
      "id": 1,
      "name": "Newspaper",
      "qty": 4
    }, {
      "id": 2,
      "name": "Glass",
      "qty": 4
    }, {
      "id": 4,
      "name": "Toys",
      "qty": 5
    }]
  }
}

```
# Get donations of campaign by campaign ID and CM session

#### Request
```
GET http://{{baseurl}}/donations/campaignmanagers/campaigns/{campaignID} HTTP/1.0
Content-type: application/json
Authorization: Basic dG9iaWFzbGtqQG1haWwuY29tOlJtUk5RMEpSV0VsU1JXeEtiMDkzUjBKNWFqZDRkRTQ0VWxZM1JUSnhlVlo0Ym10MGNtcHJOUT09
```
#### Response

##### Success
```
{
  "status":1,
  "message": "List of donations for campaign id 9",
  "donations":[{
    "did": 12,
    "status": "Pending",
    "pickup_date": "date",
    "pickup_time": "time",
    "volunteer_name": null,
    "volunteer_HP": null,
    "Campaign_id": 9,
    "User_id": 1,
    "Address_id": 1,
    "address": {
      "id": 1,
      "street_name": "Block 469 AdmiraltyDrive",
      "unit": "#16-55",
      "zip": "750469",
      "user_id": 1
    },
    "user": {
      "id": 1,
      "email": "tobiaslkj@gmail.com",
      "first_name": "ha",
      "last_name": "woo"
    },
    "items": [{
      "id": 1,
      "name": "Newspaper",
      "qty": 4
    }, {
      "id": 2,
      "name": "Glass",
      "qty": 4
    }, {
      "id": 4,
      "name": "Toys",
      "qty": 5
    }]
  }, {
    "did": 13,
    "status": "Pending",
    "pickup_date": "date",
    "pickup_time": "time",
    "volunteer_name": null,
    "volunteer_HP": null,
    "Campaign_id": 9,
    "User_id": 1,
    "Address_id": 1,
    "address": {
      "id": 1,
      "street_name": "Block 469 AdmiraltyDrive",
      "unit": "#16-55",
      "zip": "750469",
      "user_id": 1
    },
    "user": {
      "id": 1,
      "email": "tobiaslkj@gmail.com",
      "first_name": "ha",
      "last_name": "woo"
    },
    "items": [{
      "id": 1,
      "name": "Newspaper",
      "qty": 4
    }, {
      "id": 2,
      "name": "Glass",
      "qty": 4
    }, {
      "id": 4,
      "name": "Toys",
      "qty": 5
    }]
  }]
}
```

##### Failure
```
HTTP/1.0 404 Not Found
Date: Thu, 01 Nov 2018 17:00:28 GMT
Server: Apache/2.4.25 (Debian)
Vary: Authorization
X-Powered-By: PHP/7.2.10
Cache-Control: no-cache, private
Content-Length: 73
Connection: close
Content-Type: application/json

{
  "status": "0",
  "errors": {
    "message": "Campaign not found or it has ended."
  }
}
```

```
HTTP/1.0 404 Not Found
Date: Thu, 01 Nov 2018 17:00:28 GMT
Server: Apache/2.4.25 (Debian)
Vary: Authorization
X-Powered-By: PHP/7.2.10
Cache-Control: no-cache, private
Content-Length: 73
Connection: close
Content-Type: application/json

{
  "status": "0",
  "errors": {
    "message": "No donations found."
  }
}
```
# Change status of a donation

| Field                    | Description                                                             |
| ------------------------ | ----------------------------------------------------------------------- |
| action                   | (required \| ['approve', 'reject', 'cancel', 'in-progress','complete'] ) Action to be done |
| volunteer_name           |  (required if action is 'in-progress') Volunteer's name|
| volunteer_HP             | (required if action is 'in-progress') Volunteer's phone number|


#### Request
*Approve a donation*
```
PATCH http://{{baseurl}}/donations/{donationID}/campaignmanagers HTTP/1.0
Content-type: application/json
Authorization: Basic dG9iaWFzbGtqQG1haWwuY29tOlJtUk5RMEpSV0VsU1JXeEtiMDkzUjBKNWFqZDRkRTQ0VWxZM1JUSnhlVlo0Ym10MGNtcHJOUT09

{
  "action":"approve"
}
```

*Reject a donation*
```
PATCH http://{{baseurl}}/donations/{donationID}/campaignmanagers HTTP/1.0
Content-type: application/json
Authorization: Basic dG9iaWFzbGtqQG1haWwuY29tOlJtUk5RMEpSV0VsU1JXeEtiMDkzUjBKNWFqZDRkRTQ0VWxZM1JUSnhlVlo0Ym10MGNtcHJOUT09

{
  "action":"reject"
}
```

*Assign volunteer to a donation and make it in-progress*
```
PATCH http://{{baseurl}}/donations/{donationID}/campaignmanagers HTTP/1.0
Content-type: application/json
Authorization: Basic dG9iaWFzbGtqQG1haWwuY29tOlJtUk5RMEpSV0VsU1JXeEtiMDkzUjBKNWFqZDRkRTQ0VWxZM1JUSnhlVlo0Ym10MGNtcHJOUT09

{
  "action":"in-progress",
  "volunteer_name":"Redmond Aldric Goon",
  "volunteer_HP":"85219821"
}
```

*Cancel a donation*
```
PATCH http://{{baseurl}}/donations/{donationID}/campaignmanagers HTTP/1.0
Content-type: application/json
Authorization: Basic dG9iaWFzbGtqQG1haWwuY29tOlJtUk5RMEpSV0VsU1JXeEtiMDkzUjBKNWFqZDRkRTQ0VWxZM1JUSnhlVlo0Ym10MGNtcHJOUT09

{
  "action":"cancel"
}
```

*Complete a donation*
```
PATCH http://{{baseurl}}/donations/{donationID}/campaignmanagers HTTP/1.0
Content-type: application/json
Authorization: Basic dG9iaWFzbGtqQG1haWwuY29tOlJtUk5RMEpSV0VsU1JXeEtiMDkzUjBKNWFqZDRkRTQ0VWxZM1JUSnhlVlo0Ym10MGNtcHJOUT09

{
  "action":"complete"
}
```

#### Response
##### Success
```
HTTP/1.0 200 OK
Date: Fri, 02 Nov 2018 16:12:26 GMT
Server: Apache/2.4.25 (Debian)
X-Powered-By: PHP/7.2.10
Cache-Control: no-cache, private
Content-Length: 51
Connection: close
Content-Type: application/json

{
  "status": 1,
  "message": "Donation updated!"
}
```
##### Failure

*Approve Donation*
```
HTTP/1.0 404 Not Found
Date: Sun, 04 Nov 2018 14:56:43 GMT
Server: Apache/2.4.25 (Debian)
Vary: Authorization
X-Powered-By: PHP/7.2.10
Cache-Control: no-cache, private
Content-Length: 126
Connection: close
Content-Type: application/json

{
  "status": 0,
  "errors": {
    "message": "Either donation is already approved, or it does not exist as your donations."
  }
}
```

*Reject Donation*
```
HTTP/1.0 404 Not Found
Date: Sun, 04 Nov 2018 14:56:43 GMT
Server: Apache/2.4.25 (Debian)
Vary: Authorization
X-Powered-By: PHP/7.2.10
Cache-Control: no-cache, private
Content-Length: 126
Connection: close
Content-Type: application/json

{
  "status": 0,
  "errors": {
    "message": "Either donation is already rejected or in-progress, or it does not exist as your donations."
  }
}
```

*Assign volunteer to a donation and make it in-progress*
```
HTTP/1.0 404 Not Found
Date: Sun, 04 Nov 2018 14:56:43 GMT
Server: Apache/2.4.25 (Debian)
Vary: Authorization
X-Powered-By: PHP/7.2.10
Cache-Control: no-cache, private
Content-Length: 126
Connection: close
Content-Type: application/json

{
  "status": 0,
  "errors": {
    "message": "Either donation is already in-progress, or it does not exist as your donations."
  }
}
```

```
HTTP/1.0 422 Unprocessable Entity
Date: Sun, 04 Nov 2018 14:59:16 GMT
Server: Apache/2.4.25 (Debian)
Vary: Authorization
X-Powered-By: PHP/7.2.10
Cache-Control: no-cache, private
Content-Length: 230
Connection: close
Content-Type: application/json

{
  "status": "0",
  "errors": {
    "volunteer_name": ["The volunteer name field is required when action is in-progress."],
    "volunteer_HP": ["The volunteer  h p field is required when action is in-progress."],
    "message": "Unproccessable request"
  }
}
```

*Complete Donation*
```
HTTP/1.0 404 Not Found
Date: Sun, 04 Nov 2018 14:56:43 GMT
Server: Apache/2.4.25 (Debian)
Vary: Authorization
X-Powered-By: PHP/7.2.10
Cache-Control: no-cache, private
Content-Length: 126
Connection: close
Content-Type: application/json

{
  "status": 0,
  "errors": {
    "message": "Either donation is already complete, or it does not exist as your donations."
  }
}
```

*Cancel Donation*
```
HTTP/1.0 404 Not Found
Date: Sun, 04 Nov 2018 14:56:43 GMT
Server: Apache/2.4.25 (Debian)
Vary: Authorization
X-Powered-By: PHP/7.2.10
Cache-Control: no-cache, private
Content-Length: 126
Connection: close
Content-Type: application/json

{
  "status": 0,
  "errors": {
    "message": "Either donation is already completed or cancelled, or it does not exist as your donations."
  }
}
```