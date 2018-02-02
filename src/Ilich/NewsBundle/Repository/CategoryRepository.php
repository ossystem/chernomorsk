<?php

namespace Ilich\NewsBundle\Repository;

use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

/**
 * Repository for Category Entity
 */
class CategoryRepository extends NestedTreeRepository
{

    /**
     *
     */
    public function findCategoryByGroup()
    {
        $ret = array();
        $categories = $this->findAll(array('title' => 'ASC'));
        
        foreach ($categories as $cat) {
            $cGroup = $cat->getParent();
            
            if (!$cGroup) {
                $ret[0] = array();
                continue;
            }
            
            $ret[$cGroup->getId()][$cat->getId()] = $cat;
        }
        
        //do magic
        foreach ($ret as $idkey => $categories) {
            $namekey = 'categoryGroup';
            
            if (reset($categories)) {
                $namekey = reset($categories)->getParent()->getTitle();
            }
            
            $ret[$namekey] = $ret[$idkey];
            unset($ret[$idkey]);
        }

        return $ret;
    }
    
    public function getCategoryList()
    {
        return $this->findAll();
    }
}
