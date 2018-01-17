<?php

namespace GalerieBundle\Repository;

/**
 * ImageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ImageRepository extends \Doctrine\ORM\EntityRepository
{

	public function getImageGalerie($galerie)
	{
		$qb = $this->createQueryBuilder('a');

		if($galerie)
			$qb->where('a.galerie = :id')->setParameter('id', $galerie);


		return $query = $qb->getQuery()->getResult();
	}

}