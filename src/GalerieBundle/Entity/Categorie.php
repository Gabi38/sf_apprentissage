<?php

namespace GalerieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Categorie
 *
 * @ORM\Table(name="galerie_categorie")
 * @ORM\Entity(repositoryClass="GalerieBundle\Repository\CategorieRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Categorie
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
	 * @ORM\Column(name="nom", type="string", length=255)
	 * @Assert\NotBlank(message="Compléter le champ nom")
	 */
	private $nom;

	/**
	 * @ORM\OneToMany(targetEntity="GalerieBundle\Entity\Galerie", mappedBy="categorie")
	 */
	private $galeries;

	public function __construct()
	{
		$this->created = new \DateTime();
		$this->galeries = new ArrayCollection();
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
	 * @return Categorie
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
	 * @return Categorie
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
	 * Set nom
	 *
	 * @param string $nom
	 *
	 * @return Categorie
	 */
	public function setNom($nom)
	{
		$this->nom = $nom;

		return $this;
	}

	/**
	 * Get nom
	 *
	 * @return string
	 */
	public function getNom()
	{
		return $this->nom;
	}

	/**
	 * Add galerie
	 *
	 * @param \GalerieBundle\Entity\Galerie $galerie
	 *
	 * @return Categorie
	 */
	public function addGalerie(\GalerieBundle\Entity\Galerie $galerie)
	{
		$this->galeries[] = $galerie;

		return $this;
	}

	/**
	 * Remove galerie
	 *
	 * @param \GalerieBundle\Entity\Galerie $galerie
	 */
	public function removeGalerie(\GalerieBundle\Entity\Galerie $galerie)
	{
		$this->galeries->removeElement($galerie);
	}

	/**
	 * Get galeries
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getGaleries()
	{
		return $this->galeries;
	}

    /**
     * Add galery
     *
     * @param \GalerieBundle\Entity\Galerie $galery
     *
     * @return Categorie
     */
    public function addGalery(\GalerieBundle\Entity\Galerie $galery)
    {
        $this->galeries[] = $galery;

        return $this;
    }

    /**
     * Remove galery
     *
     * @param \GalerieBundle\Entity\Galerie $galery
     */
    public function removeGalery(\GalerieBundle\Entity\Galerie $galery)
    {
        $this->galeries->removeElement($galery);
    }
}
