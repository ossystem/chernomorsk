<?php

namespace Ilich\NewsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

use Ilich\TagBundle\Entity\Tag;
use Doctrine\ORM\Mapping\Index;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * Post
 *
 * @ORM\Table(name="news_post", uniqueConstraints={},indexes={@Index(name="search_idp", columns={ "created_at"})})
 * @ORM\Entity(repositoryClass="Ilich\NewsBundle\Entity\PostRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="\Ilich\NewsBundle\Repository\PostRepository") 
 */
class Post
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\ManyToMany(targetEntity="Category", inversedBy="posts")
     * @ORM\JoinTable(name="posts_categories")
     * // it is owning side
     */
    private $categories;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Ilich\TagBundle\Entity\Tag", inversedBy="post")
     * @ORM\JoinTable(name="news_post_tags")
     */
    protected $tags;
     
    /**
     * @return ArrayCollection
     */
    public function getTags()
    {
        return $this->tags;
    }
    
    public function addTag(Tag $tag)
    {
       $this->tags[] = $tag;
    }

    public function removeTag(Tag $tag)
    {
        return $this->tags->removeElement($tag);
    }
    
    /**
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", inversedBy="posts")
     */
    private $user;

    /**
     * @var string
     * @Gedmo\Translatable
     * @Gedmo\Slug(fields={"id","title"})
     * @ORM\Column(name="slug", type="string", length=255)
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
     * @ORM\Column(name="anounce", type="text", length=117, nullable=true)
     */
    private $anounce;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="source", type="string", length=255, nullable=true)
     */
    private $source; # field may be moved to metadata

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=255, nullable=true)
     */
    private $author; # field may be moved to metadata

    /**
     * @var string
     *
     * @ORM\Column(name="views", type="integer")
     */
    private $views = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="trust_level", type="integer")
     */
    private $trustLevel = 0; # field may be moved to metadata

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="specnews", type="boolean")
     */
    private $specnews = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="vkontakte", type="boolean")
     */
    private $vkontakte = false;

    /**
     * //variable that contains post id from the VK
     * @var integer
     *
     * @ORM\Column(name="vk_id", type="integer")
     */
    private $vkId = 0;

    /**
     * @var boolean
     *
     * @ORM\Column(name="facebook", type="boolean")
     */
    private $facebook = false;

    /**
     * //variable that contains post id from the FB
     * @var string
     *
     * @ORM\Column(name="fb_id", type="string", length=255, nullable=true)
     */
    private $fbId = 0;

    /**
     * @var boolean
     *
     * @ORM\Column(name="twitter", type="boolean")
     */
    private $twitter = false;

    /**
     * //variable that contains post id from the twitter
     * @var string
     *
     * @ORM\Column(name="twit_id", type="string", length=255, nullable=true)
     */
    private $twitId = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="posted_at", type="datetime" )
     */
    private $postedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * @var string
     *
     * @ORM\ManyToMany(targetEntity="\Application\Sonata\MediaBundle\Entity\Media" , cascade={"persist", "remove"})
     * @ORM\JoinTable(name="posts__media_image")
     */
    private $additionalImages;

    /**
     * @var string
     *
     * @ORM\ManyToMany(targetEntity="\Application\Sonata\MediaBundle\Entity\Media" , cascade="all", orphanRemoval=true)
     * @ORM\JoinTable(name="posts__media_youtube")
     */
    private $youtubeVideos;

    /**
     * @var string
     *
     * @ORM\ManyToMany(targetEntity="\Application\Sonata\MediaBundle\Entity\Media" , cascade="all", orphanRemoval=true)
     * @ORM\JoinTable(name="posts__media_vimeo")
     */
    private $vimeoVideos;
    
    
    /**
    * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Gallery" )
    */
    private $gallery;

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
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }
    
     /**
     * Set slug
     *
     * @return Post
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
        
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Post
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
     * Set content
     *
     * @param string $content
     * @return Post
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set anounce
     *
     * @param string $anounce
     * @return Post
     */
    public function setAnounce($anounce)
    {
        $this->anounce = $anounce;

        return $this;
    }

    /**
     * Get anounce
     *
     * @return string
     */
    public function getAnounce()
    {
        return $this->anounce;
    }

    /**
     * Set source
     *
     * @param string $source
     * @return Post
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set author
     *
     * @param string $author
     * @return Post
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set views
     *
     * @param string $views
     * @return Post
     */
    public function setViews($views)
    {
        $this->views = $views;

        return $this;
    }

    /**
     * Get views
     *
     * @return string
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Set trustLevel
     *
     * @param string $trustLevel
     * @return Post
     */
    public function setTrustLevel($trustLevel)
    {
        $this->trustLevel = $trustLevel;

        return $this;
    }

    /**
     * Get trustLevel
     *
     * @return string
     */
    public function getTrustLevel()
    {
        return $this->trustLevel;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Post
     */
    public function setEnabled($enabled)
    {
        $this->enabled = (bool)$enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }
    
    /**
     * Set specnews
     *
     * @param boolean $specnews
     * @return Post
     */
    public function setSpecnews($specnews)
    {
        $this->specnews = (bool)$specnews;

        return $this;
    }

    /**
     * Get specnews
     *
     * @return boolean
     */
    public function getSpecnews()
    {
        return $this->specnews;
    }

     /**
     * Set vkontakte
     *
     * @param boolean $vkontakte
     * @return Post
     */
    public function setVkontakte($vkontakte)
    {
        $this->vkontakte = (bool)$vkontakte;

        return $this;
    }

    /**
     * Get vkontakte
     *
     * @return boolean
     */
    public function getVkontakte()
    {
        return $this->vkontakte;
    }

     /**
     * Set vkontakte Id
     *
     * @param integer $vkId
     * @return Post
     */
    public function setVkId($vkId)
    {
        $this->vkId = $vkId;

        return $this;
    }

    /**
     * Get vkontakte Id
     *
     * @return integer
     */
    public function getVkId()
    {
        return $this->vkId;
    }

    /**
     * Set facebook
     *
     * @param boolean $facebook
     * @return Post
     */
    public function setFacebook($facebook)
    {
        $this->facebook = (bool)$facebook;
        return $this;
    }

    /**
     * Get facebook
     *
     * @return boolean
     */
    public function getFacebook()
    {
        return $this->facebook;
    }

    /**
     * Set facebook Id
     *
     * @param string $fbId
     * @return Post
     */
    public function setFbId($fbId)
    {
        $this->fbId = $fbId;
        return $this;
    }

    /**
     * Get facebook Id
     *
     * @return string
     */
    public function getFbId()
    {
        return $this->fbId;
    }

    /**
     * Set twitter
     *
     * @param boolean $twitter
     * @return Post
     */
    public function setTwitter($twitter)
    {
        $this->twitter = (bool)$twitter;

        return $this;
    }

    /**
     * Get twitter
     *
     * @return boolean
     */
    public function getTwitter()
    {
        return $this->twitter;
    }

    /**
     * Set twitter Id
     *
     * @param string $twitId
     * @return Post
     */
    public function setTwitId($twitId)
    {
        $this->twitId = $twitId;
        return $this;
    }

    /**
     * Get twitter Id
     *
     * @return string
     */
    public function getTwitId()
    {
        return $this->twitId;
    }

    /**
     * Set postedAt
     *
     * @param \DateTime $postedAt
     * @return Post
     */
    public function setPostedAt($postedAt)
    {
        $this->postedAt = $postedAt;

        return $this;
    }

    /**
     * Get postedAt
     *
     * @return \DateTime
     */
    public function getPostedAt()
    {
        return $this->postedAt > new \DateTime('0000-00-00 00:00:00') ? $this->postedAt : null;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Post
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt > new \DateTime('0000-00-00 00:00:00') ? $this->createdAt : null;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Post
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt > new \DateTime('0000-00-00 00:00:00') ? $this->updatedAt : null;
    }

    public function getCategories()
    {
        return $this->categories;
    }

    public function setCategories($categories)
    {
        $this->categories = $categories;

        return $this;
    }

    public function addCategories(Category $category)
    {
        $category->addPost($this);
        $this->categories[] = $category;

        return $this;
    }
        
    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    public function __construct()
    {
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
        $this->additionalImages = new \Doctrine\Common\Collections\ArrayCollection();
        $this->youtubeVideos = new \Doctrine\Common\Collections\ArrayCollection();
        $this->vimeoVideos = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        if (!$this->getCreatedAt()) {
            $this->setCreatedAt(new \DateTime);
        }
        
        if (!$this->getUpdatedAt()) {
            $this->setUpdatedAt(new \DateTime);
        }
        
        if (!$this->getPostedAt()) {
            $this->setPostedAt(new \DateTime);
        }
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->setUpdatedAt(new \DateTime);
    }

    /**
     * Add additionalImages
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $additionalImages
     * @return Project
     */
   public function addAdditionalImage(\Application\Sonata\MediaBundle\Entity\Media $additionalImages)
   {
       $this->additionalImages[] = $additionalImages;

       return $this;
   }

   /**
    * Remove sources
    *
    * @param \Application\Sonata\MediaBundle\Entity\Media $sources
    */
   public function removeAdditionalImage(\Application\Sonata\MediaBundle\Entity\Media $additionalImages)
   {
       $this->additionalImages->removeElement($additionalImages);
   }

    /**
     * Get additionalImages
     *
     * @return string
     */
    public function getAdditionalImages()
    {
        return $this->additionalImages;
    }

    /**
     * Add youtubeVideo
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $youtubeVideo
     * @return Project
     */
    public function addYoutubeVideo(\Application\Sonata\MediaBundle\Entity\Media $youtubeVideo)
    {
        $this->youtubeVideos[] = $youtubeVideo;

        return $this;
    }

    /**
     * Remove sources
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $youtubeVideo
     */
    public function removeYoutubeVideo(\Application\Sonata\MediaBundle\Entity\Media $youtubeVideo)
    {
        $this->youtubeVideos->removeElement($youtubeVideo);
    }

    /**
     * Get youtubeVideos
     *
     * @return string
     */
    public function getYoutubeVideos()
    {
        return $this->youtubeVideos;
    }

    /**
    * Set youtubeVideos
    *
    * @param Doctrine\Common\Collections\ArrayCollection $youtubeVideos
    */
    public function setYoutubeVideos($youtubeVideos)
    {
        $this->youtubeVideos = $youtubeVideos;

        return $this;
    }

    /**
     * Add VimeoVideo
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $vimeoVideo
     * @return Project
     */
    public function addVimeoVideo(\Application\Sonata\MediaBundle\Entity\Media $vimeoVideo)
    {
        $this->vimeoVideos[] = $vimeoVideo;

        return $this;
    }

    /**
     * Remove sources
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $vimeoVideo
     */
    public function removeVimeoVideo(\Application\Sonata\MediaBundle\Entity\Media $vimeoVideo)
    {
        $this->vimeoVideos->removeElement($vimeoVideo);
    }

    /**
     * Get vimeoVideos
     *
     * @return string
     */
    public function getVimeoVideos()
    {
        return $this->vimeoVideos;
    }

    /**
    * Set vimeoVideos
    *
    * @param Doctrine\Common\Collections\ArrayCollection $vimeoVideo
    */
    public function setVimeoVideos($vimeoVideo)
    {
        $this->vimeoVideos = $vimeoVideo;

        return $this;
    }
    
    
    /**
     * @param \Application\Sonata\MediaBundle\Entity\Gallery $gallery
     */
    public function setGallery(\Application\Sonata\MediaBundle\Entity\Gallery $gallery = null )
    {
        $this->gallery = $gallery;
    }

    /**
     * @return GalleryInterface
     */
    public function getGallery()
    {
        return $this->gallery;
    }
}
