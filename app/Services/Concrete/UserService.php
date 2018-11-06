<?php
namespace App\Services\Concrete;

use App\Services\Contracts\IUserService;
use App\Models\User;
use App\Models\CampaignManager;
use App\Models\Session;
use App\Services\Contracts\IAuthenticate;
use App\Models\Address;
use App\Exceptions\ModelConflictException;
use App\Utility\IHttpClient;

/**
 * This class provides the business logic for user related
 * business logic.  
 */
class UserService implements IUserService, IAuthenticate{


    /**
     * Create a user based on the input array of information.
     * 
     * Create a user based on the input array and store it in the database. The
     * input array should be an associative array with the field name and the value pair.
     * 
     * @param mixed $array Associative array with the field name and value pair
     * 
     * @return boolean The result of saving into database.
     */
    public function create(array $array)
    {
        $user = new User($array);
        $user->storePassword($array["password"]);
        
        if($user->save()){
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * Edit a user based on the input array of information.
     * 
     * Edit a user based on the input array and save it in the database. The
     * input array should be an associative array with the field name the the value pair
     * 
     * @param mixed $array Associative array with the field name and value pair
     * 
     * @return boolean The result of the saving into database.
     */
    public function edit(array $data){
        $user = app(User::class);
        $user->email=$data["email"];
        $user->last_name=$data["last_name"];
        $user->first_name=$data["first_name"];

        if($user->save()){
            return true;
        }

        else{
            return false;
        }
    }

    /**
     * Retrieve a user based on email.
     * 
     * Retreieve a user object associated with the email string. 
     * 
     * @param String $email The email string of the user that is to be retrieve. 
     * 
     * @return User|NULL Return the *User* object or *NULL* if not found.
     * 
     */
    public function getUserByEmail(string $email){
        return User::where('email', $email)->first();
    }

    /**
     * Create a new session for the input user. 
     * 
     * Creates a unique session token associated to the input user and save it into the database. Return the
     * session token as a string.
     * 
     * @param User $user The user instance that an session will be associated with
     * 
     * @return String The session string token.
     */
    public function createNewSessionForUser(User $user){
        $session = new Session();
        $user->session()->save($session);
        return $session->session_token;;
    }

    /**
     * Validate the credentials. 
     * 
     * Validate credentials based on the inputted associative array.
     * $credentials['email'] includes the email string for the user to be validated.
     * $credentials['password'] includes the plain text password string for the user to be validated.
     *
     * @param array $credentials  
     * @return String|NULL Return session token string or null if validation of credentials fail.
     */
    public function login(array $credentials){
        $user = $this->getUserByEmail($credentials['email']);
        if($user == null){
            return null;
        }

        if($user->validatePassword($credentials['password'])){
            return $this->createNewSessionForUser($user);
        }
        else{
            return null;
        }
    }

    /**
     * Add campaign manager role to a user.
     * 
     * A campaign manager record will be created based on the array of attributes and associate it
     * with the user object given and save it into database. If the user is already a campaign manager,
     * a ModelConflictException will be thrown.
     * 
     * @param User $user The user to be associated with a campaign manager information
     * @param array $array The information of the Campaign manager
     * @throws ModelConflictException
     * 
     */
    public function convertCampaignManager(User $user, array $array){
        if(!is_null($user->campaignManager)){
            throw new ModelConflictException("User already a campaign manager!");
        }

        $cm = new CampaignManager($array);
        $cm->cid = $user->id;
        $user->campaignManager()->save($cm);
    }

    /**
     * Retrieve the organization name based on a UEN string.
     *
     * This function make use of the data.gov.sg API to retrieve a UEN. An IHttpClient is injected
     * to this function to make call the data.gov.sg API. The organization name will only be returned if 
     * the requesting organization is still registered. 
     * 
     * @see https://data.gov.sg/dataset/entities-with-unique-entity-number?resource_id=5ab68aac-91f6-4f39-9b21-698610bdf3f7
     * @param IHttpClient $httpClient An instace of IHttpClient
     * @param String $uen The UEN string to be lookup from the data.gov.sg API
     * @return String|null Return the organization name or NULL if not found.
     */
    public function getEntityNameByUEN(IHttpClient $httpClient, String $uen){
        $params = ['resource_id'=>'5ab68aac-91f6-4f39-9b21-698610bdf3f7', 'q'=>$uen];
        $httpClient->request('GET', 'https://data.gov.sg/api/action/datastore_search', $params);

        $response = $httpClient->getResponseBody();
        $results = $response['result']['records'];

        if(is_null($results) || count($results)==0){
            return null;
        }

        foreach($results as $result){
            if($result['uen_status'] === 'R'){
                return $result['entity_name'];
            }
        }

        return null;
    }
}