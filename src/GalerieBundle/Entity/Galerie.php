<?php

namespace GalerieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Cocur\Slugify\Slugify;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Galerie
 *
 * @ORM\Table(name="galerie")
 * @ORM\Entity(repositoryClass="GalerieBundle\Repository\GalerieRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Galerie
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetimetz")
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="changed", type="datetimetz", nullable=true)
     */
    private $changed;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="text")
     */
    private $slug;

	/**
	 * @ORM\ManyToOne(targetEntity="GalerieBundle\Entity\Categorie", inversedBy="galeries")
	 */
	private $categorie;

	/**
	 * @ORM\OneToMany(targetEntity="GalerieBundle\Entity\Image", mappedBy="galerie", cascade={"persist","remove"})
	 * @Assert\Valid
	 */
	private $images;

	/**
	 * @ORM\OneToMany(targetEntity="GalerieBundle\Entity\Commentaire", mappedBy="galerie", cascade={"persist","remove"})
	 * @Assert\Valid
	 */
	private $commentaires;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="etat", type="boolean", options={"default" : 1})
	 */
	private $etat;


	public function __construct()
	{
		$this->created = new \DateTime();
		$this->images = new ArrayCollection();
		$this->commentaires = new ArrayCollection();
	}

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Galerie
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set changed
     *
     * @param \DateTime $changed
     *
     * @return Galerie
     */
    public function setChanged($changed)
    {
        $this->changed = $changed;

        return $this;
    }

    /**
     * Get changed
     *
     * @return \DateTime
     */
    public function getChanged()
    {
        return $this->changed;
    }

	/**
	 * @ORM\PreUpdate()
	 */
	public function preChanged()
	{
		$this->changed = new \DateTime();
	}

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Galerie
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
     * Set slug
     *
     * @param string $slug
     *
     * @return Galerie
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
	 * @ORM\PrePersist()
	 * @ORM\PreUpdate()
	 */
	public function preSlug()
	{
		$slugify = new Slugify();
		$this->slug = $slugify->slugify($this->title);
	}

	/**
	 * Set categorie
	 *
	 * @param \GalerieBundle\Entity\Categorie $categorie
	 *
	 * @return Galerie
	 */
	public function setCategorie(\GalerieBundle\Entity\Categorie $categorie = null)
	{
		$this->categorie = $categorie;
		return $this;
	}
	/**
	 * Get categorie
	 *
	 * @return \GalerieBundle\Entity\Categorie
	 */
	public function getCategorie()
	{
		return $this->categorie;
	}


	public function getUploadRootDir()
	{
		return __DIR__.'/../../../web/uploads/galeries/';
	}

	/**
	 * Add image
	 *
	 * @param \GalerieBundle\Entity\Image $image
	 *
	 * @return Galerie
	 */
	public function addImage(\GalerieBundle\Entity\Image $image)
	{
		$this->images[] = $image;

		return $this;
	}

	/**
	 * Remove image
	 *
	 * @param \GalerieBundle\Entity\Image $image
	 */
	public function removeImage(\GalerieBundle\Entity\Image $image)
	{
		$this->images->removeElement($image);
	}

	/**
	 * Get images
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getImages()
	{
		return $this->images;
	}

	/**
	 * Add commentaire
	 *
	 * @param \GalerieBundle\Entity\Commentaire $commentaire
	 *
	 * @return Galerie
	 */
	public function addCommentaire(\GalerieBundle\Entity\Commentaire $commentaire)
	{
		$this->commentaires[] = $commentaire;

		return $this;
	}

	/**
	 * Remove commentaire
	 *
	 * @param \GalerieBundle\Entity\Commentaire $commentaire
	 */
	public function removeCommentaire(\GalerieBundle\Entity\Commentaire $commentaire)
	{
		$this->commentaires->removeElement($commentaire);
	}

	/**
	 * Get commentaires
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getCommentaires()
	{
		return $this->commentaires;
	}

	/**
	 * @return string
	 */
	public function getEtat()
	{
		return $this->etat;
	}

	/**
	 * @param string $etat
	 */
	public function setEtat($etat)
	{
		$this->etat = $etat;
	}

	/**
	 * Retourne 1 si actif 0 si pas actif
	 */
	public function reverseState()
	{
		$etat = $this->getEtat();
		return !$etat;
	}


}
