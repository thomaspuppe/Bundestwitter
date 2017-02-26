<?php
namespace BT\Model;

use BT\Model\DistrictRepository;

class ElectoraldistrictModel extends Model
{
    protected $databaseTableName = 'electoraldistrict';

    protected $id;
    protected $name;
    protected $district_id;
    protected $text;
    protected $topojson;
    protected $geo_center_lat;
    protected $geo_center_lon;
    protected $slug;
    protected $seats;


    public function getDistrict()
    {
        // TODO: lieber Singletonähnliches Ding???
        $districtRepository = new DistrictRepository();
        $district = $districtRepository->findOneBy(array('id' => $this->district_id));
        return $district;
    }
}
