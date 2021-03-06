<?php
/**
 * Checks for a pre-existing token in the cookies
 *
 * Creates a token_valid variable, true if the token is valid, false if the token is not valid
 * If the token is valid, the user_id value will equal the logged in user's id.
 */

require_once "db.php";
require_once 'crud.php';
require_once "util.php";
require_once '../vendor/autoload.php';

$token = filter_input(INPUT_COOKIE, 'token');
$token_valid = false;
$user_id = null;

if(strlen($token) == TOKEN_LENGTH) {
    try {
        $stmt = $db->prepare('SELECT UserId, SupplyDate FROM Sessions WHERE Token = :token');
        $stmt->bindValue(':token', $token);
        $stmt->execute();

        if ($stmt->rowCount() != 1) {
            $token_valid = false;
        } else {
            $row = $stmt->fetch();

            $token_valid = true;
            $user_id = $row['UserId'];
            $current_user = getUser($user_id);
        }
    } catch (PDOException $e) {
        //TODO handle error
        exit;
    }
} else {
    $token_valid = false;
    setcookie('token', '', 0);
}

function validateToken($token) {
    if(strlen($token) == TOKEN_LENGTH) {
        $query = getSingle('SELECT UserId, SupplyDate FROM Sessions WHERE Token = :token', ['token'=>$token]);

        if(getUser($query['UserId']) != null) {
            return true;
        }
    }

    return false;
}

function getUserByToken($token) {
    if(strlen($token) == TOKEN_LENGTH) {
        $query = getSingle('SELECT UserId, SupplyDate FROM Sessions WHERE Token = :token', ['token'=>$token]);

        if(getUser($query['UserId']) != null) {
            return getUser($query['UserId']);
        }
    }

    return null;
}

function newToken($uid) {
    global $db;

    $token = random_text('alnum', TOKEN_LENGTH);

    try {
        $stmt = $db->prepare('REPLACE INTO Sessions (Token, UserId) VALUES (:token, :uid)');
        $stmt->bindValue(':token', $token);
        $stmt->bindValue(':uid', $uid, PDO::PARAM_INT);
        $stmt->execute();
        return $token;
    } catch (PDOException $e) {
        echo $e->getMessage();
        return false;
    }
}

?>