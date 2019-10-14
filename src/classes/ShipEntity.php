<?php
class ShipEntity
{
    protected $id;
    protected $mmsi;
    protected $status;
    protected $stationId;
    protected $speed;
    protected $lat;
    protected $lon;
    protected $course;
    protected $heading;
    protected $rot;
    protected $timestamp;
    /**
     * Accept an array of data matching properties of this class
     * and create the class
     *
     * @param array $data The data to use to create
     */
    public function __construct(array $data) {
        // no id if we're creating
        if(isset($data['id'])) {
            $this->id = $data['id'];
        }
        $this->mmsi = $data['mmsi'];
        $this->status = $data['status'];
        $this->stationId = $data['stationId'];
        $this->speed = $data['speed'];
        $this->lat = $data['lat'];
        $this->lon = $data['lon'];
        $this->course = $data['course'];
        $this->heading = $data['heading'];
        $this->rot = $data['rot'];
        $this->timestamp = $data['timestamp'];

    }
    public function getId() {
        return $this->id;
    }
    public function getMmsi() {
        return $this->mmsi;
    }
    public function getStatus() {
        return $this->status;
    }
    public function getStationId() {
        return $this->stationId;
    }
    public function getSpeed() {
        return $this->speed;
    }
    public function getLat() {
        return $this->lat;
    }
    public function getLon() {
        return $this->lon;
    }
    public function getCourse() {
        return $this->course;
    }
    public function getHeading() {
        return $this->heading;
    }
    public function getRot() {
        return $this->rot;
    }
    public function getTimestamp() {
        return $this->timestamp;
    }

    /**
     * Get an array representation of ShipEntity object 
     * for easy encoding 
     * @param 
     * @return  Array   array with the ShipEntity key-value binding 
     */
    public function getShipArray(){
        $ship = array('id' =>$this->id,
            'mmsi'=>$this->mmsi,
            'status' =>$this->status,
            'station'=>$this->stationId,
            'speed' =>$this->speed,
            'lat'=>$this->lat,
            'lon' =>$this->lon,
            'course'=>$this->course,
            'heading' =>$this->heading,
            'rot'=>$this->rot,
            'timestamp' =>$this->timestamp);
        return $ship;
    }
}