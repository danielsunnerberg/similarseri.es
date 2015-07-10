<?php

namespace Sunnerberg\SimilarSeriesBundle\Entity;

use Doctrine\ORM\EntityRepository;

class GenreRepository extends EntityRepository
{
    public function getByName($name)
    {
        return $this->findOneBy(array('name' => $name));
    }

    public function getOrCreateByName($name)
    {
        $genre = $this->getByName($name);
        if (! $genre) {
            $genre = new Genre($name);
            $this->getEntityManager()->persist($genre);
            $this->getEntityManager()->flush($genre);
        }
        return $genre;
    }
}
