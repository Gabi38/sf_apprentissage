<?php

namespace GalerieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;

/**
 * Image
 *
 * @ORM\Table(name="image")
 * @ORM\Entity(repositoryClass="GalerieBundle\Repository\ImageRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Image
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
	 * @Assert\Image(
	mimeTypes = {"image/jpeg", "image/png"}),
	maxSize = "10M"
	 */
    private $file;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="nom", type="string", length=32)
	 */
	private $nom;

	/**
	 * @ORM\ManyToOne(targetEntity="GalerieBundle\Entity\Galerie", inversedBy="images")
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
     * @return Image
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
     * @return Image
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
	 * @return Image
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

	public function getFile()
	{
		return $this->file;
	}

	public function setFile(UploadedFile $file = null)
	{
		$this->file = $file;
		if (null !== $this->nom){
			$this->nom = null;
		}
	}

	public function uploadFile()
	{
		// Si jamais il n'y a pas de fichier (champ facultatif), on ne fait rien
		if (null === $this->file) {
			return;
		}

		$this->nom = uniqid().'.'.strtolower(pathinfo($this->file->getClientOriginalName(), PATHINFO_EXTENSION));

		$imagine = new Imagine();

		/* Création de le miniature pour la liste en front office */
		$size = new Box(1920,1080);
		$imagine->open($this->file)
			->thumbnail($size, 'inset')
			->save($this->getUploadRootDir().'images/'.$this->nom);

	}

	/**
	 * On retourne le chemin relatif vers les réactions pour notre code PHP
	 */
	public function getUploadRootDir()
	{
		return __DIR__.'/../../../web/uploads/galeries/';
	}

	/**
	 * @Assert\Callback
	 */
	public function isFileValid(ExecutionContextInterface $context)
	{

		if(empty($this->id)){
			if(empty($this->file)) $context->buildViolation('Complétez le champ image')->atPath('file')->addViolation();
		}

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

