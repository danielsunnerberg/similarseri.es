<?php

namespace Sunnerberg\SimilarSeriesBundle\Patcher;

use Doctrine\ORM\EntityManager;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Sunnerberg\SimilarSeriesBundle\Entity\TvShow;
use Sunnerberg\SimilarSeriesBundle\Entity\TvShowRepository;
use Sunnerberg\SimilarSeriesBundle\Fetcher\TvShowFetcherInterface;
use Symfony\Bridge\Monolog\Logger;

/**
 * Fetches and updates a show with complementary data.
 *
 * When a show is extracted as a relative from another show, only basic data is included (due to limitations in the
 * TMDB-API). Hence, we must fetch the extra data, such as external IDs, images etc, in separate requests. This
 * is very time/request consuming, which is why the user cannot wait for it, and it has to be queued for later.
 *
 * Class TmdbShowFetcherInterface
 * @package Sunnerberg\SimilarSeriesBundle\Patcher
 */
class ShowPatcher implements ConsumerInterface {

    private $showFetcher;
    private $showRepository;
    private $entityManager;
    private $logger;

    public function __construct(
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

    public function execute(AMQPMessage $message)
    {
        $data = unserialize($message->body);
        if (! isset($data['tmdb_id'])) {
            $this->logger->error('Invalid argument: expected tmdb_id.');
            return;
        }
        $tmdbId = $data['tmdb_id'];

        $show = $this->showRepository->getByTmdbId($tmdbId);
        if (! $show) {
            $this->logger->error(sprintf('Found no show to patch with tmdb id: %d', $tmdbId));
            return;
        }

        $this->showFetcher->fetch($tmdbId, false, $show);
        $this->entityManager->flush();

        $this->logger->info(sprintf('Patched and persisted show with id: %d, tmdb_id: %d', $show->getId(), $tmdbId));

        return true;
    }
}
