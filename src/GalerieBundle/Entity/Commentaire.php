<?php

namespace GalerieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Commentaire
 *
 * @ORM\Table(name="commentaire")
 * @ORM\Entity(repositoryClass="GalerieBundle\Repository\CommentaireRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Commentaire
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
     * @var string
     *
     * @ORM\Column(name="utilisateur", type="string", length=255)
     */
    private $utilisateur;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text")
     */
    private $message;

	/**
	 * @ORM\ManyToOne(targetEntity="GalerieBundle\Entity\Galerie", inversedBy="commentaires")
	 */
	private $galerie;

	public function __construct()
	{
		$this->created = new \DateTime();
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
     * @return Commentaire
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
     * Set utilisateur
     *
     * @param string $utilisateur
     *
     * @return Commentaire
     */
    public function setUtilisateur($utilisateur)
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
     * Get utilisateur
     *
     * @return string
     */
    public function getUtilisateur()
    {
        return $this->utilisateur;
    }

    /**
     * Set message
     *
     * @param string $message
     *
     * @return Commentaire
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

	/**
	 * Set galerie
	 *
	 * @param \GalerieBundle\Entity\Galerie $galerie
	 *
	 * @return Image
	 */
	public function setGalerie(\GalerieBundle\Entity\Galerie $galerie = null)
	{
		$this->galerie = $galerie;

		return $this;
	}

	/**
	 * Get galerie
	 *
	 * @return \GalerieBundle\Entity\Galerie
	 */
	public function getGalerie()
	{
		return $this->galerie;
	}
}

