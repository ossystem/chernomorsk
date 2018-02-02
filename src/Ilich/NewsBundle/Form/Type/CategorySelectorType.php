<?php

namespace Ilich\NewsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\OptionsResolver\Options;

/**
 * Description of CategorySelectorType
 *
 * @author ksander
 */
class CategorySelectorType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
                ->setDefaults(array(
                    'empty_value' => 'Choose Category',
                    'class' => 'Ilich\NewsBundle\Entity\Category',
                    'property' => 'title',
                    'choices' => $this->om->getRepository("IlichNewsBundle:Category")->findCategoryByGroup(),
                    'multiple' => true,
                    'expanded' => true,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'entity';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'category_selector';
    }
}
