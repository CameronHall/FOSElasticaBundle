<?php

/*
 * This file is part of the FOSElasticaBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\ElasticaBundle\Event;

use Symfony\Component\EventDispatcher\Event as LegacyEvent;
use Symfony\Contracts\EventDispatcher\Event;

if (!class_exists(Event::class)) {
    /**
     * Symfony 3.4
     */

    /**
     * Index ResetEvent.
     *
     * @author Oleg Andreyev <oleg.andreyev@intexsys.lv>
     */
    class AbstractIndexResetEvent extends AbstractIndexEvent
    {
        /**
         * @LegacyEvent("FOS\ElasticaBundle\Event\IndexResetEvent")
         */
        const PRE_INDEX_RESET = 'elastica.index.pre_reset';
        /**
         * @LegacyEvent("FOS\ElasticaBundle\Event\IndexResetEvent")
         */
        const POST_INDEX_RESET = 'elastica.index.post_reset';
        /**
         * @var bool
         */
        private $force;
        /**
         * @var bool
         */
        private $populating;

        /**
         * @param string $index
         * @param bool   $populating
         * @param bool   $force
         */
        public function __construct($index, $populating, $force)
        {
            parent::__construct($index);

            $this->force = $force;
            $this->populating = $populating;
        }

        /**
         * @return bool
         */
        public function isForce()
        {
            return $this->force;
        }

        /**
         * @return bool
         */
        public function isPopulating()
        {
            return $this->populating;
        }

        /**
         * @param bool $force
         */
        public function setForce(bool $force)
        {
            $this->force = $force;
        }
    }
} else {
    /**
     * Symfony >= 4.3
     */

    /**
     * Index ResetEvent.
     *
     * @author Oleg Andreyev <oleg.andreyev@intexsys.lv>
     */
    class AbstractIndexResetEvent extends AbstractIndexEvent
    {
        /**
         * @Event("FOS\ElasticaBundle\Event\IndexResetEvent")
         */
        const PRE_INDEX_RESET = 'elastica.index.pre_reset';

        /**
         * @Event("FOS\ElasticaBundle\Event\IndexResetEvent")
         */
        const POST_INDEX_RESET = 'elastica.index.post_reset';

        /**
         * @var bool
         */
        private $force;

        /**
         * @var bool
         */
        private $populating;

        /**
         * @param string $index
         * @param bool   $populating
         * @param bool   $force
         */
        public function __construct($index, $populating, $force)
        {
            parent::__construct($index);

            $this->force = $force;
            $this->populating = $populating;
        }

        /**
         * @return bool
         */
        public function isForce()
        {
            return $this->force;
        }

        /**
         * @return bool
         */
        public function isPopulating()
        {
            return $this->populating;
        }

        /**
         * @param bool $force
         */
        public function setForce(bool $force)
        {
            $this->force = $force;
        }
    }
}