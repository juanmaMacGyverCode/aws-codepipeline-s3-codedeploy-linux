<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class TestEncrypt extends TestCase
{
    public function testThereIsThatIDTrue1(): void
    {
        $customer = array(new Costumer(1, encrypt("Fernando", "1235@"), encrypt("1234", "1235@"), encrypt("Fernando Gomez", "1235@"), 1, 1));

        $this->assertNotFalse(thereIsThatID(2, $customer));
    }

    public function testThereIsThatIDTrue2(): void
    {
        $customer = array(new Costumer(24, encrypt("", "1235@"), encrypt("1234", "1235@"), encrypt("Fernando Gomez", "1235@"), 1, 1));

        $this->assertNotFalse(thereIsThatID(6, $customer));
    }

    public function testThereIsThatIDTrue3(): void
    {
        $customer = array(new Costumer(3146, encrypt("asd123", "1235@"), encrypt("1234", "1235@"), encrypt("Fernando Gomez", "1235@"), 1, 1));

        $this->assertNotFalse(thereIsThatID(354, $customer));
    }

    public function testThereIsThatIDTrue4(): void
    {
        $customer = array(new Costumer(1346, encrypt("aaaaddddffffgggghhhh", "1235@"), encrypt("1234", "1235@"), encrypt("Fernando Gomez", "1235@"), 1, 1));

        $this->assertNotFalse(thereIsThatID(346, $customer));
    }

    public function testThereIsThatIDFalse1(): void
    {
        $customer = array(new Costumer(1, encrypt("Fernando", "1235@"), encrypt("1234", "1235@"), encrypt("Fernando Gomez", "1235@"), 1, 1));

        $this->assertFalse(thereIsThatID(1, $customer));
    }

    public function testThereIsThatIDFalse2(): void
    {
        $customer = array(new Costumer(2, encrypt("", "1235@"), encrypt("1234", "1235@"), encrypt("Fernando Gomez", "1235@"), 1, 1));

        $this->assertFalse(thereIsThatID(2, $customer));
    }
}

function thereIsThatID($idCustomer, $allCustomers)
{
    foreach ($allCustomers as $customerObject) {
        if ($idCustomer == $customerObject->getIdCostumer()) {
            return false;
        }
    }
    return true;
}

function encrypt($data, $key)
{
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    $encrypted = openssl_encrypt($data, "aes-256-cbc", $key, 0, $iv);
    return base64_encode($encrypted . "::" . $iv);
}

function decrypt($data, $key)
{
    list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
    return openssl_decrypt($encrypted_data, 'aes-256-cbc', $key, 0, $iv);
}

class Costumer
{
    private $idCostumer;
    private $nameCostumer;
    private $surname;
    private $image;
    private $idUserCreator;
    private $idUserLastModify;

    public function __construct($idCostumer, $nameCostumer, $surname, $image, $idUserCreator, $idUserLastModify)
    {
        $this->idCostumer = $idCostumer;
        $this->nameCostumer = $nameCostumer;
        $this->surname = $surname;
        $this->image = $image;
        $this->idUserCreator = $idUserCreator;
        $this->idUserLastModify = $idUserLastModify;
    }

    /* Getters */
    public function getIdCostumer()
    {
        return $this->idCostumer;
    }
    public function getNameCostumer()
    {
        return $this->nameCostumer;
    }
    public function getSurname()
    {
        return $this->surname;
    }
    public function getImage()
    {
        return $this->image;
    }
    public function getIdUserCreator()
    {
        return $this->idUserCreator;
    }
    public function getIdUserLastModify()
    {
        return $this->idUserLastModify;
    }

    /* Setters */
    public function setIdCostumer($idCostumer)
    {
        $this->idCostumer = $idCostumer;
    }
    public function setNameCostumer($nameCostumer)
    {
        $this->nameCostumer = $nameCostumer;
    }
    public function setSurname($surname)
    {
        $this->surname = $surname;
    }
    public function setImage($image)
    {
        $this->image = $image;
    }
    public function setIdUserCreator($idUserCreator)
    {
        $this->idUserCreator = $idUserCreator;
    }
    public function setIdUserLastModify($idUserLastModify)
    {
        $this->idUserLastModify = $idUserLastModify;
    }

    //Other functions
    public function dataSheetCostumer($allUsers)
    {
        $userCreator = null;
        $userLastModify = null;
        foreach ($allUsers as $userObject) {
            if ($userObject->getIdUser() == $this->idUserCreator) {
                $userCreator = $userObject;
            }
            if ($userObject->getIdUser() == $this->idUserLastModify) {
                $userLastModify = $userObject;
            }
        }

        $cardLastModify = "";
        if ($userLastModify != null) {
            $cardLastModify = "<p class=\"card-text\"><span class=\"font-weight-bold\">ID user update:</span> " . $this->idUserLastModify . ". <span class=\"font-weight-bold\">Username:</span> " . decrypt($userLastModify->getUsername(), "1235@") . "</p>";
        } else {
            $cardLastModify = "<p class=\"card-text\"><span class=\"font-weight-bold\">ID user update:</span> EMPTY. <span class=\"font-weight-bold\">Username:</span> EMPTY. </p>";
        }

        $image = "";
        if (strlen($this->image) > 1) {
            //$image = "\"..\\uploads\\" . decrypt($this->image, "1235@") . "\"";
            $image = "<div class=\"col-md-4\">
                  <img src=\"..\\uploads\\" . decrypt($this->image, "1235@") . "\" class=\"card-img\" alt=\"File Not Found\">
                </div>";
        } else {
            $image = "<div class=\"col-md-4\"><i class='fas fa-user' style='font-size:15em;color:red'></i></div>";
        }

        /*$dataSheet =
            "<div class=\"card mb-3 mt-3 mx-auto w-100 text-left\">
              <div class=\"row no-gutters\">
                <div class=\"col-md-4\">
                  <img src=" . $image . " class=\"card-img\" alt=\"File Not Found\">
                </div>
                <div class=\"col-md-8\">
                  <div class=\"card-header\">
                    <h5 class=\"card-title\">Id: " . $this->idCostumer . "</h5>
                  </div>
                  <div class=\"card-body\">
                    <h5 class=\"card-title\">Surname: " . decrypt($this->surname, "1235@") . "</h5>
                    <h5 class=\"card-title\">Name: " . decrypt($this->nameCostumer, "1235@") . "</h5>
                    <hr>
                    <p class=\"card-text\"><span class=\"font-weight-bold\">ID user creator:</span> " . $this->idUserCreator . ". <span class=\"font-weight-bold\">Username:</span> " . decrypt($userCreator->getUsername(), "1235@") . "</p>
                    " . $cardLastModify . "
                  </div>
                </div>
              </div>
            </div>";*/

        $dataSheet =
            "<div class=\"card mb-3 mt-3 mx-auto w-100 text-left\">
              <div class=\"row no-gutters\">
                " . $image . "
                <div class=\"col-md-8\">
                  <div class=\"card-header\">
                    <h5 class=\"card-title\">Id: " . $this->idCostumer . "</h5>
                  </div>
                  <div class=\"card-body\">
                    <h5 class=\"card-title\">Surname: " . decrypt($this->surname, "1235@") . "</h5>
                    <h5 class=\"card-title\">Name: " . decrypt($this->nameCostumer, "1235@") . "</h5>
                    <hr>
                    <p class=\"card-text\"><span class=\"font-weight-bold\">ID user creator:</span> " . $this->idUserCreator . ". <span class=\"font-weight-bold\">Username:</span> " . decrypt($userCreator->getUsername(), "1235@") . "</p>
                    " . $cardLastModify . "
                  </div>
                </div>
              </div>
            </div>";

        return $dataSheet;
    }
}