<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\CssSelector\XPath\Extension;

use Symfony\Component\CssSelector\XPath\XPathExpr;

/**
 * XPath expression translator combination extension.
 *
 * This component is a port of the Python cssselector library,
 * which is copyright Ian Bicking, @see https://github.com/SimonSapin/cssselect.
 *
 * @author Jean-François Simon <jeanfrancois.simon@sensiolabs.com>
 *
 * @since v2.3.0
 */
class CombinationExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     *
     * @since v2.3.0
     */
    public function getCombinationTranslators()
    {
        return array(
            ' ' => array($this, 'translateDescendant'),
            '>' => array($this, 'translateChild'),
            '+' => array($this, 'translateDirectAdjacent'),
            '~' => array($this, 'translateIndirectAdjacent'),
        );
    }

    /**
     * @param XPathExpr $xpath
     * @param XPathExpr $combinedXpath
     *
     * @return XPathExpr
     *
     * @since v2.3.0
     */
    public function translateDescendant(XPathExpr $xpath, XPathExpr $combinedXpath)
    {
        return $xpath->join('/descendant-or-self::*/', $combinedXpath);
    }

    /**
     * @param XPathExpr $xpath
     * @param XPathExpr $combinedXpath
     *
     * @return XPathExpr
     *
     * @since v2.3.0
     */
    public function translateChild(XPathExpr $xpath, XPathExpr $combinedXpath)
    {
        return $xpath->join('/', $combinedXpath);
    }

    /**
     * @param XPathExpr $xpath
     * @param XPathExpr $combinedXpath
     *
     * @return XPathExpr
     *
     * @since v2.3.0
     */
    public function translateDirectAdjacent(XPathExpr $xpath, XPathExpr $combinedXpath)
    {
        return $xpath
            ->join('/following-sibling::', $combinedXpath)
            ->addNameTest()
            ->addCondition('position() = 1');
    }

    /**
     * @param XPathExpr $xpath
     * @param XPathExpr $combinedXpath
     *
     * @return XPathExpr
     *
     * @since v2.3.0
     */
    public function translateIndirectAdjacent(XPathExpr $xpath, XPathExpr $combinedXpath)
    {
        return $xpath->join('/following-sibling::', $combinedXpath);
    }

    /**
     * {@inheritdoc}
     *
     * @since v2.3.0
     */
    public function getName()
    {
        return 'combination';
    }
}
