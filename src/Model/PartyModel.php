<?php
namespace BT\Model;

class PartyModel extends Model
{
    protected $databaseTableName = 'party';

    protected $id;
    protected $name;
    protected $shortname;
    protected $slug;
    protected $website;
    protected $text;
    protected $bt18_member;
    protected $bt19_member;
}
