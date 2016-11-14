<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Workflow;

/**
 * MultipleStateMarking contains the place of every tokens.
 *
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 * @author Jules Pietri <jules@heahprod.com>
 */
class MultipleStateMarking extends Marking
{
    /**
     * @param string[] $representation Keys are the place name and values should be the number of tokens
     */
    public function __construct(array $representation = array())
    {
        foreach ($representation as $place => $nbToken) {
            $this->mark($place);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function mark($place)
    {
        if (!isset($this->places[$place])) {
            $this->places[$place] = 1;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function unmark($place)
    {
        unset($this->places[$place]);
    }
}
