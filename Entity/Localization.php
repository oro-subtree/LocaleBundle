<?php

namespace Oro\Bundle\LocaleBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\EntityBundle\EntityProperty\DatesAwareInterface;
use Oro\Bundle\EntityBundle\EntityProperty\DatesAwareTrait;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\ConfigField;

/**
 * @ORM\Entity()
 * @ORM\Table(name="oro_localization")
 * @ORM\HasLifecycleCallbacks()
 * @Config(
 *      defaultValues={
 *          "entity"={
 *              "icon"="icon-list"
 *          },
 *          "security"={
 *              "type"="ACL",
 *              "group_name"=""
 *          },
 *      }
 * )
 */
class Localization implements DatesAwareInterface
{
    use DatesAwareTrait;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=64, unique=true, nullable=false)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="language_code", type="string", length=64, nullable=false)
     */
    protected $languageCode;

    /**
     * @var string
     *
     * @ORM\Column(name="formatting_code", type="string", length=64, nullable=false)
     */
    protected $formattingCode;

    /**
     * @var Localization
     *
     * @ORM\ManyToOne(targetEntity="Localization", inversedBy="childLocalizations")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $parentLocalization;

    /**
     * @var Collection|Localization[]
     *
     * @ORM\OneToMany(targetEntity="Localization", mappedBy="parentLocalization")
     */
    protected $childLocalizations;

    public function __construct()
    {
        $this->childLocalizations = new ArrayCollection();
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @param string $languageCode
     *
     * @return $this
     */
    public function setLanguageCode($languageCode)
    {
        $this->languageCode = $languageCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getLanguageCode()
    {
        return $this->languageCode;
    }

    /**
     * @param string $formattingCode
     *
     * @return $this
     */
    public function setFormattingCode($formattingCode)
    {
        $this->formattingCode = $formattingCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getFormattingCode()
    {
        return $this->formattingCode;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param Localization $parentLocalization
     *
     * @return $this
     */
    public function setParentLocalization(Localization $parentLocalization = null)
    {
        $this->parentLocalization = $parentLocalization;

        return $this;
    }

    /**
     * @return Localization
     */
    public function getParentLocalization()
    {
        return $this->parentLocalization;
    }

    /**
     * @param Collection|Localization[] $childLocalizations
     *
     * @return $this
     */
    public function setChildLocalizations($childLocalizations)
    {
        $this->childLocalizations = $childLocalizations;

        return $this;
    }

    /**
     * @return Collection|Localization[]
     */
    public function getChildLocalizations()
    {
        return $this->childLocalizations;
    }

    /**
     * @param Localization $localization
     * @return $this
     */
    public function addChildLocalization(Localization $localization)
    {
        if (!$this->hasChildLocalization($localization)) {
            $this->childLocalizations->add($localization);
        }

        return $this;
    }

    /**
     * @param Localization $localization
     * @return $this
     */
    public function removeChildLocalization(Localization $localization)
    {
        if ($this->hasChildLocalization($localization)) {
            $this->childLocalizations->removeElement($localization);
            $localization->setParentLocalization(null);
        }

        return $this;
    }

    /**
     * @param Localization $localization
     * @return boolean
     */
    public function hasChildLocalization(Localization $localization)
    {
        return $this->childLocalizations->contains($localization);
    }
}
