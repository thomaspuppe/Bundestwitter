<?php
namespace BT\Model;

use BT\Model\DistrictRepository;
use BT\Service\MemcachedService;

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
        $this->memcachedService = MemcachedService::getInstance();
        $cacheKey = 'Model_District_' . $this->district_id;
        $district = $this->memcachedService->get($cacheKey);
        if (!$district) {
            // OPTIMIZE: lieber Singletonähnliches Ding???
            $districtRepository = new DistrictRepository();
            $district = $districtRepository->findOneBy(array('id' => $this->district_id));
            $this->memcachedService->set($cacheKey, $district);
        }
        return $district;
    }
}
