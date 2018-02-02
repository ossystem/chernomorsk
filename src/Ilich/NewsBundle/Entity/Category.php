<?php

namespace Ilich\NewsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Category
 *
 * @Gedmo\Tree(type="nested")
 * @ORM\Table(name="news_category", uniqueConstraints={@ORM\UniqueConstraint(name="slug_idx", columns={"slug"})})
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="\Ilich\NewsBundle\Repository\CategoryRepository") 
 */
class Category
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\ManyToMany(targetEntity="Post", mappedBy="categories", cascade={"persist"})
     * // it is inverse side 
     */
    private $posts;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer")
     */
    private $lft;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer")
     */
    private $lvl;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer")
     */
    private $rgt;

    /**
     * @Gedmo\TreeRoot
     * @ORM\Column(name="root", type="integer", nullable=true)
     */
    private $root;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    private $children;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Category
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Category
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Category
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set parent
     *
     * @param Category $parent
     * @return Category
     */
    public function setParent(Category $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return Category 
     */
    public function getParent()
    {
        return $this->parent;
    }
    
    public function getPosts()
    {
        return $this->posts;
    }
    
    public function setPosts($posts)
    {
        $this->posts = $posts;
        
        return $this;
    }
    
    public function addPost(Post $post)
    {
        $this->posts[] = $post;
        
        return $this;
    }
    
    public function __construct() {
        $this->posts = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function __toString() {
        return $this->getTitle();
    }
    
    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        if (!$this->getSlug()) {
            $this->setSlug(sha1(time()));
        }
    }
    
    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        if (!$this->getSlug()) {
            $this->setSlug(sha1(time()));
        }
    }
    
     public function getIndentedTitle() {
        return str_repeat("--", $this->lvl).$this->title;
    }
}
