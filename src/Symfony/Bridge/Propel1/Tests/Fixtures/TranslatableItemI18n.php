<?php

namespace Symfony\Bridge\Propel1\Tests\Fixtures;

use Edge5\PageBundle\Model\om\BaseArticleI18n;
use PropelPDO;

/**
 * Skeleton subclass for representing a row from the 'Article_i18n' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.src.Edge5.PageBundle.Model
 */
class TranslatableItemI18n implements \Persistent {

    private $id;

    private $locale;

    private $value;

    private $value2;

    private $item;

    public function __construct($id = null, $locale = null, $value = null)
    {
        $this->id = $id;
        $this->locale = $locale;
        $this->value = $value;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getPrimaryKey()
    {
        return $this->getId();
    }

    public function setPrimaryKey($primaryKey)
    {
        $this->setId($primaryKey);
    }

    public function isModified()
    {
        return false;
    }

    public function isColumnModified($col)
    {
        return false;
    }

    public function isNew()
    {
        return false;
    }

    public function setNew($b)
    {
    }

    public function resetModified()
    {
    }

    public function isDeleted()
    {
        return false;
    }

    public function setDeleted($b)
    {
    }

    public function delete(PropelPDO $con = null)
    {
    }

    public function save(PropelPDO $con = null)
    {
    }

    public function setLocale($locale)
    {

        $this->locale = $locale;
    }

    public function getLocale()
    {

        return $this->locale;
    }

    public function getItem()
    {
        return $this->item;
    }

    public function setItem($item)
    {
        $this->item = $item;
    }

    public function setValue($value)
    {

        $this->value = $value;
    }

    public function getValue()
    {

        return $this->value;
    }

    public function setValue2($value2)
    {

        $this->value2 = $value2;
    }

    public function getValue2()
    {

        return $this->value2;
    }
}
