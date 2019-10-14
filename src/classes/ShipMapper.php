<?php
class ShipMapper extends Mapper
{


    /**
     * Get one ship by its ID
     * for test purposes only 
     * @param int $id The ID of the ship
     * @return ShipEntity  The ship
     */
    public function getShipById($id) {
        $sql = "SELECT *
        FROM ships_positions s_p WHERE s_p.id = :id";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute(["id" => $id]);
        if($result) {
            return new ShipEntity($stmt->fetch());
        }

    }
    
    /**
     * Get all ships with same mmsi
     * 
     * @param int $mmsi The mmsi of the ship
     * @return Array   array with the ship records
     */

    public function getShipsByMmsi($mmsi) {
        $sql = "SELECT *
            FROM ships_positions s_p
            WHERE s_p.mmsi = :mmsi";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute(["mmsi" => $mmsi]);
        $results = [];
        while($row = $stmt->fetch()) {
            $results[] = new ShipEntity($row);
        }
        return $results;
    }

    /**
     * Get records withing the coordinates range
     * 
     * @param array $params coordinates ranges 
     * @return  Array   array with the ship records within coordinates range
     */
    public function getShipsByCoordinates($params) {
        $sql = "SELECT *
            FROM ships_positions s_p
            WHERE s_p.lat >= :minLat AND s_p.lat <= :maxLat AND s_p.lon >= :minLon AND s_p.lon <= :maxLon" ;
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute($params);
        $results = [];
        while($row = $stmt->fetch()) {
            $results[] = new ShipEntity($row);
        }
        return $results;
    }

    /**
     * Get records withing the time interval
     * 
     * @param array $params time ranges 
     * @return Array   array with the ship records within time range
     */

    public function getShipsByDateInterval($params) {
        $sql = "SELECT *
            FROM ships_positions s_p
            WHERE UNIX_TIMESTAMP(s_p.timestamp) >= :minDate AND UNIX_TIMESTAMP(s_p.timestamp) <= :maxDate " ;
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute($params);
        $results = [];
        while($row = $stmt->fetch()) {
            $results[] = new ShipEntity($row);
        }
        return $results;
    }
}