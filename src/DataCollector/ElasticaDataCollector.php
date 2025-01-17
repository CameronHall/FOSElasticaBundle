<?php

/*
 * This file is part of the FOSElasticaBundle package.
 *
 * (c) FriendsOfSymfony <https://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\ElasticaBundle\DataCollector;

use FOS\ElasticaBundle\Logger\ElasticaLogger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpKernel\Kernel;

if (Kernel::VERSION_ID >= 50000) {
    /**
     * Data collector collecting elastica statistics.
     *
     * @author Gordon Franke <info@nevalon.de>
     */
    class ElasticaDataCollector extends DataCollector
    {
        /**
         * @var \FOS\ElasticaBundle\Logger\ElasticaLogger
         */
        protected $logger;

        public function __construct(ElasticaLogger $logger)
        {
            $this->logger = $logger;
        }

        /**
         * @return void
         */
        public function collect(Request $request, Response $response, ?\Throwable $exception = null)
        {
            $this->data['nb_queries'] = $this->logger->getNbQueries();
            $this->data['queries'] = $this->logger->getQueries();
        }

        /**
         * @return mixed
         */
        public function getQueryCount()
        {
            return $this->data['nb_queries'];
        }

        /**
         * @return mixed
         */
        public function getQueries()
        {
            return $this->data['queries'];
        }

        /**
         * @return int
         */
        public function getTime()
        {
            $time = 0;
            foreach ($this->data['queries'] as $query) {
                $time += $query['engineMS'];
            }

            return $time;
        }

        /**
         * @return int
         */
        public function getExecutionTime()
        {
            $time = 0;
            foreach ($this->data['queries'] as $query) {
                $time += $query['executionMS'];
            }

            return $time;
        }

        public function getName(): string
        {
            return 'elastica';
        }

        public function reset(): void
        {
            $this->logger->reset();
            $this->data = [];
        }
    }
} else {
    /**
     * Data collector collecting elastica statistics.
     *
     * @author Gordon Franke <info@nevalon.de>
     */
    class ElasticaDataCollector extends DataCollector
    {
        protected $logger;

        public function __construct(ElasticaLogger $logger)
        {
            $this->logger = $logger;
        }

        public function collect(Request $request, Response $response, \Exception $exception = null)
        {
            $this->data['nb_queries'] = $this->logger->getNbQueries();
            $this->data['queries'] = $this->logger->getQueries();
        }

        /**
         * @return mixed
         */
        public function getQueryCount()
        {
            return $this->data['nb_queries'];
        }

        /**
         * @return mixed
         */
        public function getQueries()
        {
            return $this->data['queries'];
        }

        /**
         * @return int
         */
        public function getTime()
        {
            $time = 0;
            foreach ($this->data['queries'] as $query) {
                $time += $query['engineMS'];
            }

            return $time;
        }

        /**
         * @return int
         */
        public function getExecutionTime()
        {
            $time = 0;
            foreach ($this->data['queries'] as $query) {
                $time += $query['executionMS'];
            }

            return $time;
        }

        public function getName()
        {
            return 'elastica';
        }

        public function reset()
        {
            $this->logger->reset();
            $this->data = [];
        }
    }
}