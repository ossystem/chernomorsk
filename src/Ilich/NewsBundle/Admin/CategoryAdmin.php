<?php

namespace Ilich\NewsBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * Description of CategoryAdmin
 *
 * @author Valentyn Diduryk <stranger@ossystem.com.ua>
 */
class CategoryAdmin  extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('slug', 'text', array('label' => 'ilich_news.category.slug'))
            ->add('parent', null, array('label' => 'ilich_news.category.parent'))
            ->add('title', 'text', array('label' => 'ilich_news.category.title'))
            ->add('description', 'textarea', array('label' => 'ilich_news.category.description'))
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('slug', null, array('label' => 'ilich_news.category.slug'), null, array())
            ->add('title', null, array('label' => 'ilich_news.category.title'), null, array())
            ->add('description', null, array('label' => 'ilich_news.category.description'), null, array())
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title', null, array('label' => 'ilich_news.category.title'))
            ->add('parent', null, array('label' => 'ilich_news.category.parent'))
            ->add('slug', null, array('label' => 'ilich_news.category.slug'))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                ),
                'label' => 'ilich_news.actions'
            ))
        ;
    }
}