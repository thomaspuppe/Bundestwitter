<?php
namespace BT\Model;

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
}
