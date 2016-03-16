<?php
namespace Gis\Models\Entities;

use Carbon\Carbon;
use Gis\Models\Repositories\CropFacade;
use Gis\Models\Repositories\FertilizerMapInfoFacade;
use Gis\Models\Repositories\FertilizerUnitPriceFacade;
use Illuminate\Database\Eloquent\Model;

/**
 * Fertilizer Map Entity
 * Class FertilizerMap
 *
 * @package Gis\Entities
 */
class FertilizerMap extends Model
{

    protected $table = 'fertilizer_maps';

    public $timestamps = false;

    protected $fillable = array(
        'user_id',
        'price',
        'is_paid',
        'layer_id',
        'geo',
        'fertility_map_id',
        'ins_user',
        'upd_user',
        'ins_time',
        'upd_time',
        'id'
    );

    private $meshSize;

    private $cropName;
    // private $numberOfMesh;
    private $unitPrice;

    private $price;

    public function getArea()
    {
        $this->meshSize = $this->fertilizerMapProperty->mesh_size;
        $this->numberOfMesh = FertilizerMapInfoFacade::getNumberOfMesh($this->id);
        return ($this->numberOfMesh * pow(($this->meshSize), 2)) / 1000;//return [a]
    }

    public function getUnitPrice()
    {
        $this->unitPrice = FertilizerUnitPriceFacade::getUnitPrice(Carbon::now()->format('Y-m-d 00:00:00.0'));
        ;
        return $this->unitPrice;
    }

    public function getPrice()
    {
        $this->meshSize = $this->fertilizerMapProperty->mesh_size;
        $numberOfMesh = FertilizerMapInfoFacade::getNumberOfMesh($this->id);
        $this->price = ($numberOfMesh * pow(($this->meshSize), 2) * $this->unitPrice) / 1000;
        return $this->price;
    }

    public function getMeshSize()
    {
        $this->meshSize = $this->fertilizerMapProperty->mesh_size;
        return $this->meshSize;
    }

    public function fertilizerMapInfo()
    {
        return $this->hasMany('Gis\Models\Entities\FertilizerMapInfo', 'fertilizer_id');
    }

    public function fertilizerMapProperty()
    {
        return $this->hasOne('Gis\Models\Entities\FertilizerMapProperty', 'fertilizer_map_id');
    }

    public function fertilizerMapPayment()
    {
        return $this->hasMany('Gis\Models\Entities\FertilizerMapPayment', 'fertilizer_id');
    }

    public function fertilizerStage()
    {
        return $this->hasMany('Gis\Models\Entities\FertilizerStage', 'fertilizer_map_id')->orderBy("id","DESC");
    }

    public function organicMatterField()
    {
        return $this->hasMany('Gis\Models\Entities\OrganicMatterField', 'fertilizer_map_id');
    }

    public function folderLayer()
    {
        return $this->belongsTo('Gis\Models\Entities\FolderLayer', 'layer_id');
    }

    public function fertilityMap()
    {
        return $this->belongsTo('Gis\Models\Entities\FertilityMap');
    }

    public function user()
    {
        return $this->belongsTo('Gis\Models\Entities\User', 'user_id');
    }

    public function FertilityMapSelection()
    {
        return $this->hasOne('Gis\Models\Entities\FertilityMapSelection', 'fertilizer_map_id');
    }
}