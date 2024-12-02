<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\Product;

use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslation;
use Shopsys\FrameworkBundle\Component\String\TransformString;

/**
 * @ORM\Table(name="product_translations")
 * @ORM\Entity
 */
class ProductTranslation extends AbstractTranslation
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Product
     * @Prezent\Translatable(targetEntity="Shopsys\FrameworkBundle\Model\Product\Product")
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $translatable;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $name;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $variantAlias;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $namePrefix;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $nameSuffix;

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName($name)
    {
        $this->name = TransformString::getTrimmedStringOrNullOnEmpty($name);
    }

    /**
     * @return string|null
     */
    public function getVariantAlias()
    {
        return $this->variantAlias;
    }

    /**
     * @param string|null $variantAlias
     */
    public function setVariantAlias($variantAlias)
    {
        $this->variantAlias = TransformString::getTrimmedStringOrNullOnEmpty($variantAlias);
    }

    /**
     * @return string|null
     */
    public function getNamePrefix()
    {
        return $this->namePrefix;
    }

    /**
     * @param string|null $namePrefix
     */
    public function setNamePrefix($namePrefix)
    {
        $this->namePrefix = $namePrefix;
    }

    /**
     * @return string|null
     */
    public function getNameSuffix()
    {
        return $this->nameSuffix;
    }

    /**
     * @param string|null $nameSuffix
     */
    public function setNameSuffix($nameSuffix)
    {
        $this->nameSuffix = $nameSuffix;
    }
}
