<?php

namespace Sunnerberg\SimilarSeriesBundle\Fetcher;

use Doctrine\ORM\EntityManager;
use Monolog\Logger;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Sunnerberg\SimilarSeriesBundle\Entity\TvShowRepository;

/**
 * Consumes items from the RabbitMQ fetch queue and adds them to the database through the injected fetcher.
 *
 * Class ShowFetcherConsumer
 * @package Sunnerberg\SimilarSeriesBundle\Fetcher
 */
class ShowFetcherConsumer implements ConsumerInterface {

    private $showFetcher;
    private $showRepository;
    private $entityManager;
    private $logger;

    function __construct(
        TvShowFetcherInterface $showFetcher,
        TvShowRepository $showRepository,
        EntityManager $entityManager,
        Logger $logger
    ) {
        $this->showFetcher = $showFetcher;
        $this->showRepository = $showRepository;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * @param AMQPMessage $message
     * @return mixed false to reject and requeue, any other value to aknowledge
     */
    public function execute(AMQPMessage $message)
    {
        $data = unserialize($message->body);
        if (! is_array($data) || ! isset($data['tmdb_id'])) {
            $this->logger->error(
                'Failed to parse message data. A plain array with the key tmdb_id and an integer as value is expected.',
                $data
            );
            return null;
        }

        $tmdbId = $data['tmdb_id'];
        if ($this->showRepository->getByTmdbId($tmdbId)) {
            $this->logger->debug(sprintf('Skipping show with tmdb-id: %d -- already fetched.', $tmdbId));
            return true;
        }

        $show = $this->showFetcher->fetch($tmdbId);
        $this->entityManager->persist($show);
        $this->entityManager->flush();
        $this->logger->info(sprintf('Show with tmdb-id: %d was successfully fetched.', $tmdbId));
        return true;
    }
}
