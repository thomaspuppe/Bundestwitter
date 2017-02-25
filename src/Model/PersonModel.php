<?php
namespace BT\Model;

class PersonModel extends Model
{
    protected $databaseTableName = 'person';

    protected $id;
    protected $firstname;
    protected $lastname;
    protected $title;
    protected $party_slug;
    protected $text;
    protected $image_url;
    protected $gender;
    protected $birthday;
    protected $website;
    protected $slug;
    protected $email;
    protected $list;
    protected $marital_status;
    protected $children;
    protected $education;
    protected $profession;
    protected $district_id;
    protected $electoraldistrict_id;
    protected $bt18_member;
    protected $bt19_candidate;
    protected $bt19_member;
    protected $online_profiles;


    public function getName()
    {
        return trim($this->firstname . ' ' . $this->lastname);
    }
}
