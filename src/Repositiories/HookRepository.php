<?php

namespace Sahakavatar\Cms\Repositories;

use Sahakavatar\Cms\Models\Hook;
use Sahakavatar\Cms\Models\Templates\Units;


/**
 * Class HookRepository
 * @package Sahakavatar\Cms\Repositories
 */
class HookRepository extends GeneralRepository
{

    /**
     * @return Hook
     */
    public function model()
    {
        return new Hook();
    }

    public function addUnit($id,$variation)
    {
        $hook=$this->model()->find($id);
        $data=($hook->data)??[];
        $data[]=$variation;
        $hook->data=$data;
        $hook->save();
        return $this;
    }

    public function render($id)
    {
        $hook=$this->model()->find($id);
        if(!$hook) return false;
        $data=$hook->data;
        $html='';
        if(is_array($data)){
            foreach ($data as $unit){
               $variation= Units::findVariation($unit);
               if($variation){
                   $html.=$variation->renderVariation();
               }else{
                   $html.='<p>Wrong Unit</p>';
               }

            }
            return $html;
        }
        return false;
    }
}