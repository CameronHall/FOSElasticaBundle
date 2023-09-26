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
     * Index Populate Event.
     *
     * @author Oleg Andreyev <oleg.andreyev@intexsys.lv>
     */
    abstract class IndexPopulateEvent extends AbstractIndexEvent
    {
        /**
         * @LegacyEvent("FOS\ElasticaBundle\Event\IndexPopulateEvent")
         */
        const PRE_INDEX_POPULATE = 'elastica.index.index_pre_populate';
        /**
         * @LegacyEvent("FOS\ElasticaBundle\Event\IndexPopulateEvent")
         */
        const POST_INDEX_POPULATE = 'elastica.index.index_post_populate';
        /**
         * @var bool
         */
        protected $reset;
        /**
         * @var array
         */
        protected $options;

        /**
         * @param string $index
         * @param bool   $reset
         * @param array  $options
         */
        public function __construct($index, $reset, $options)
        {
            parent::__construct($index);

            $this->reset = $reset;
            $this->options = $options;
        }

        /**
         * @return bool
         */
        public function isReset()
        {
            return $this->reset;
        }

        /**
         * @return array
         */
        public function getOptions()
        {
            return $this->options;
        }

        /**
         * @param bool $reset
         */
        public function setReset(bool $reset)
        {
            $this->reset = $reset;
        }

        /**
         * @param string $name
         *
         * @return mixed
         *
         * @throws \InvalidArgumentException if option does not exist
         */
        public function getOption($name)
        {
            if (!isset($this->options[$name])) {
                throw new \InvalidArgumentException(sprintf('The "%s" option does not exist.', $name));
            }

            return $this->options[$name];
        }

        /**
         * @param string $name
         * @param mixed  $value
         */
        public function setOption(string $name, $value)
        {
            $this->options[$name] = $value;
        }
    }
} else {
    /**
     * Symfony >= 4.3
     */

    /**
     * Index Populate Event.
     *
     * @author Oleg Andreyev <oleg.andreyev@intexsys.lv>
     */
    abstract class AbstractIndexPopulateEvent extends AbstractIndexEvent
    {
        /**
         * @Event("FOS\ElasticaBundle\Event\IndexPopulateEvent")
         */
        const PRE_INDEX_POPULATE = 'elastica.index.index_pre_populate';
        /**
         * @Event("FOS\ElasticaBundle\Event\IndexPopulateEvent")
         */
        const POST_INDEX_POPULATE = 'elastica.index.index_post_populate';
        /**
         * @var bool
         */
        protected $reset;
        /**
         * @var array
         */
        protected $options;

        /**
         * @param string $index
         * @param bool   $reset
         * @param array  $options
         */
        public function __construct($index, $reset, $options)
        {
            parent::__construct($index);

            $this->reset = $reset;
            $this->options = $options;
        }

        /**
         * @return bool
         */
        public function isReset()
        {
            return $this->reset;
        }

        /**
         * @return array
         */
        public function getOptions()
        {
            return $this->options;
        }

        /**
         * @param bool $reset
         */
        public function setReset(bool $reset)
        {
            $this->reset = $reset;
        }

        /**
         * @param string $name
         *
         * @return mixed
         *
         * @throws \InvalidArgumentException if option does not exist
         */
        public function getOption($name)
        {
            if (!isset($this->options[$name])) {
                throw new \InvalidArgumentException(sprintf('The "%s" option does not exist.', $name));
            }

            return $this->options[$name];
        }

        /**
         * @param string $name
         * @param mixed  $value
         */
        public function setOption(string $name, $value)
        {
            $this->options[$name] = $value;
        }
    }
}