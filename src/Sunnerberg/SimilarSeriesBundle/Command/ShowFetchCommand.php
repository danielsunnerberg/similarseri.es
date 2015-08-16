<?php

namespace Sunnerberg\SimilarSeriesBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Queues shows to be added to the database by retrieving e.g. the currently most popular ones.
 * This reduces the load time for users who try to add already fetched shows.
 *
 * Class ShowFetchCommand
 * @package Sunnerberg\SimilarSeriesBundle\Command
 */
class ShowFetchCommand extends ContainerAwareCommand {

    private $tvRepository;
    private $fetcherQueue;

    protected function configure()
    {
        $this
            ->setName('show:fetch')
            ->setDescription('Queues a list of shows to be added to the database')
            ->addArgument(
                'source',
                InputArgument::REQUIRED,
                'A valid _base-method_ for the TMDB-TV-API, e.g popular, top_rated or latest. Full documentation here: http://docs.themoviedb.apiary.io/#reference/tv'
            )
            ->addArgument(
                'count',
                InputArgument::REQUIRED,
                'How many shows which should, at most, be queued'
            )
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
        $this->tvRepository = $this->getContainer()->get('tmdb.tv_repository');
        $this->fetcherQueue = $this->getContainer()->get('old_sound_rabbit_mq.show_fetcher_producer');
    }

    private function fetchShowsBySource($source, array $options = array())
    {
        switch ($source) {
            case 'latest':
                return  $this->tvRepository->getLatest($options);
                break;
            case 'on_the_air':
                return  $this->tvRepository->getOnTheAir($options);
                break;
            case 'airing_today':
                return  $this->tvRepository->getAiringToday($options);
                break;
            case 'top_rated':
                return  $this->tvRepository->getTopRated($options);
                break;
            case 'popular':
                return  $this->tvRepository->getPopular($options);
                break;
            default:
                throw new \InvalidArgumentException(
                    'Invalid source entered. Please refer to TMDB-api documentation for valid TV-API base methods: http://docs.themoviedb.apiary.io/#reference/tv'
                );
        }
    }

    private function fetchShows($source, $count)
    {
        $response = $this->fetchShowsBySource($source);
        $shows = $response->getAll();

        if (count($shows) >= $count || count($shows) === $response->getTotalResults()) {
            return array_slice($shows, 0, $count);
        }

        // More shows are available and more are requested; fetch them
        $pageSize = count($shows);
        $pagesNeeded = ceil($count / $pageSize);

        for ($page = 2; $page <= $pagesNeeded; $page++) {

            if ($response->getTotalPages() < $page) {
                break;
            }

            $response = $this->fetchShowsBySource($source, ['page' => $page]);
            $shows = array_merge(
                $response->getAll(),
                $shows
            );
        }

        return array_slice($shows, 0, $count);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $source = $input->getArgument('source');
        $count = $input->getArgument('count');
        $shows = $this->fetchShows($source, $count);

        foreach ($shows as $show) {
            $this->fetcherQueue->publish(serialize(['tmdb_id' => $show->getId()]));
        }

        $output->write(sprintf('%d shows are now queued to be fetched.', count($shows)));
    }

}
