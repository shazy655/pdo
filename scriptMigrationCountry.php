<?php

class pdoConnect
{
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $db;

    public function __construct()
    {
        try {
            $this->db = new PDO("mysql:host=$this->servername;dbname=modelique_live", $this->username, $this->password);
            // set the PDO error mode to exception
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }

    }

    function getData($table)
    {
        $stmt = $this->db->query("SELECT * FROM $table");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getWhereData($table, $whereArray)
    {
        $whereString = $this->_makeWhereStringWithArray($whereArray);
        $stmt = $this->db->query("SELECT * FROM $table where $whereString");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getWhereLikeData($table, $whereArray)
    {
        $whereString = $this->_makeWhereLikeStringWithArray($whereArray);
        $stmt = $this->db->query("SELECT * FROM $table where $whereString");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function updateData($table, $array, $whereArray)
    {
        $setString = $this->_makeSetStringWithArray($array);
        $whereString = $this->_makeWhereStringWithArray($whereArray);
        $sql = "update $table SET $setString where $whereString";
        $stmt = $this->db->query($sql);
    }

    /**
     * @param $array
     */
    private function _makeSetStringWithArray($array)
    {
        $setString = "";
        foreach ($array as $arrayKey => $arrayValue) {
            $setString .= $arrayKey . '=' . $arrayValue . ',';
        }
        return $setString = rtrim($setString, ',');
    }

    /**
     * @param $array
     */
    private function _makeWhereStringWithArray($array)
    {
        $setString = "";
        foreach ($array as $arrayKey => $arrayValue) {
            $setString .= $arrayKey . "='" . $arrayValue . "'" . " and";
        }
        return $setString = rtrim($setString, 'and');
    }

    private function _makeWhereLikeStringWithArray($array)
    {
        $setString = "";
        foreach ($array as $arrayKey => $arrayValue) {
            $setString .= $arrayKey . "like '%" . $arrayValue . "%'" . " and";
        }
        return $setString = rtrim($setString, 'and');
    }

}

$pdoConnect = new pdoConnect();
$users = $pdoConnect->getData('users');
foreach ($users as $user) {
    if (!is_null($user['country'])) {
        $countryData = $pdoConnect->getWhereData('countries', array('code' => $user['country']));
        if (count($countryData) > 0)
            $pdoConnect->updateData('users', array('countryId' => $countryData[0]['id']), array('id' => $user['id']));

    }
    if (!is_null($user['state'])) {

        $stateData = $pdoConnect->getWhereData('states', array('name' => $user['state']));
        if (count($stateData) > 0)
            $pdoConnect->updateData('users', array('stateId' => $stateData[0]['id']), array('id' => $user['id']));

    }

    if (!is_null($user['city'])) {

        $cityData = $pdoConnect->getWhereData('cities', array('name' => $user['city']));

        if (count($cityData) > 0){

            $pdoConnect->updateData('users', array('cityId' => $cityData[0]['id'], 'stateId' => $cityData[0]['state_id']), array('id' => $user['id']));
        }

    }
}
?>