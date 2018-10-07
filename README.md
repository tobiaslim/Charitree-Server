# Charitree-server
Description: Codes for Charitree server side.

## To clone (download) the repository
+ Git must be installed on your machine. Check by running the following command. You should see your git version

`git --version`

### Steps:
1. Change directory where you want the project to be cloned to.

`cd desiredpath/`

2. Clone the project

`git clone https://github.com/tobiaslim/Charitree-Server.git`

3. Install dependecies.
Open up your preferred and cd to the root of the project. Then run the following command.
`composer install`

4. Define your database connection.
Open up `.env` in the root of the project and edit the DB information. 


# Project Structure
Important project directories that you might need to use:

| Directory                | Purpose                                                    |
|--------------------------|------------------------------------------------------------|
| routes/web.php           | Register your routes                                       |
| app/Http/Controllers     | Create your controller here.                               |
| app/Models               | Create your Eloquent Models here                           |
| app/Contracts            | Create contracts for services.                             |
| app/Services             | Create services that are required                          |
| app/Services/Repository  | Create a repository service if needed.                     |
| app/Contracts/Repository | Contracts for repository services                          |
| app/Providers            | To register your services                                  |
| app/bootstrap/app.php    | To register service providers or make other configurations |