<?php

namespace Ilich\NewsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Widop\PluploadBundle\Form\Type\ImageType;
use Ilich\NewsBundle\Entity\Post;


class PostType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title','text', array('translation_domain' => 'IlichNewsBundle', 'label' => 'ilich_news.post.title', 'required' => true))
            ->add('content', 'textarea', array('translation_domain' => 'IlichNewsBundle', 'label' => 'ilich_news.post.content'))
            ->add('user','hidden', array('translation_domain' => 'IlichNewsBundle', 'label' => 'ilich_news.post.user', 'read_only' => true))
            ->add('author','hidden', array('translation_domain' => 'IlichNewsBundle', 'label' => 'ilich_news.post.author', 'read_only' => true))
            ->add('categories', 'entity', 
                    array('class' => 'Ilich\NewsBundle\Entity\Category',                        
                          'translation_domain' => 'IlichNewsBundle',
                          'label' => 'ilich_news.categories',
                          'multiple' => true,
                        'attr' => array('size'=>'5')
                        )            
            )
            ->add('additionalImages', 'images', array(
                    'options' => array('context' => 'posts'),
                    'translation_domain' => 'IlichNewsBundle', 'label' => 'ilich_news.post.image',
                    'type' => new ImageType(),
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'required' => true
                    ))
            /*->add('youtubeVideos', 'collection', array('required' => false,
                    'type' => 'sonata_media_type',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'options' => array('provider' => 'sonata.media.provider.youtube',
                    'context' => 'posts')
                ))*/
            ->add('tags', 'entity', array(
                'translation_domain' => 'IlichNewsBundle', 'label' => 'ilich_news.post.tags',
                'class'=>'IlichTagBundle:Tag', 
                'multiple' => true, 
                'required' => false,
                'attr'=>array('style'=>'width: 100%;'))
            )
                ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilich\NewsBundle\Entity\Post'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ilich_newsbundle_post';
    }
}
