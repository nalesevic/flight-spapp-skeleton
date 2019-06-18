<?php
class PersistanceManager{
  private $pdo;

  public function __construct($params){
    $this->pdo = new PDO('mysql:host='.$params['host'].';dbname='.$params['scheme'], $params['username'], $params['password']);
    $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
  }

  public function register($user){
    $checkEmail = $user['email'];
    $query = "SELECT email FROM users WHERE email=?";
    $statement = $this->pdo->prepare($query);
    $statement->execute([$checkEmail]);
    $queryEmail = $statement->fetch();
    $existEmail = $queryEmail['email'];
    if($existEmail != $checkEmail) {
      $query = "INSERT INTO users
              (email,
               password)
              VALUES (:email,
                      :password)";
      $statement = $this->pdo->prepare($query);
      $statement->execute($user);
      $user['id'] = $this->pdo->lastInsertId();
      Flight::json("User added");
    } else
        Flight::json("Email exists");
  }

  public function get_user($user){
    $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email;");
    $stmt->execute(array("email" => $user["email"]));
    $response = $stmt->fetch();
    if($response) {
      $response["status"] = "success";
      return $response;
    } else {
      $response["status"] = "fail";
      return $response;
    }
  }

  public function add_baby($baby){
      $query = "INSERT INTO babies
              (firstName,
               lastName,
               mother)
              VALUES (:firstName,
                      :lastName,
                      :mother)";
      $statement = $this->pdo->prepare($query);
      $statement->execute($baby);
      $baby['id'] = $this->pdo->lastInsertId();
  }

  public function get_baby($id){
    $query = "SELECT id, firstname, lastname, mother FROM babies WHERE id = :id";
    $statement = $this->pdo->prepare($query);
    $statement->execute(['id' => $id]);
    $baby = $statement->fetch();
    return $baby;
  }


  public function get_babies(){
    $query = "SELECT * FROM babies";
    return $this->pdo->query($query)->fetchAll();
  }

  public function update_baby($id, $request){
    $firstname = $request["firstname"];
    $lastname = $request["lastname"];
    $mother = $request["mother_name"];
    $query = "UPDATE babies SET firstname = :firstname, lastname = :lastname, mother = :mother WHERE id = :id";
    $statement = $this->pdo->prepare($query);
    $statement->execute(["id" => $id, "firstname" => $firstname, "lastname" => $lastname, "mother" => $mother]);
  }

  public function delete_baby($id){
    $query = "DELETE FROM babies WHERE id = ?";
    $statement = $this->pdo->prepare($query);
    $statement->execute([$id]);
    return $statement;
  }

}

?>
