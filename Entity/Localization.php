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
 * @ORM\Entity(repositoryClass="Oro\Bundle\LocaleBundle\Entity\Repository\LocalizationRepository")
 * @ORM\Table(name="oro_localization")
 * @ORM\HasLifecycleCallbacks()
 * @Config(
 *      routeName="oro_locale_localization_index",
 *      routeView="oro_locale_localization_view",
 *      routeUpdate="oro_locale_localization_update",
 *      defaultValues={
 *          "entity"={
 *              "icon"="icon-list"
 *          },
 *          "security"={
 *              "type"="ACL",
 *              "group_name"=""
 *          }
 *      }
 * )
 */
class Localization implements DatesAwareInterface
{
    use DatesAwareTrait;
    use FallbackTrait;

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
     * @ORM\Column(type="string", length=255, unique=true, nullable=false)
     */
    protected $name;

    /**
     * @var Collection|LocalizedFallbackValue[]
     *
     * @ORM\ManyToMany(
     *      targetEntity="Oro\Bundle\LocaleBundle\Entity\LocalizedFallbackValue",
     *      cascade={"ALL"},
     *      orphanRemoval=true
     * )
     * @ORM\JoinTable(
     *      name="oro_localization_title",
     *      joinColumns={
     *          @ORM\JoinColumn(name="localization_id", referencedColumnName="id", onDelete="CASCADE")
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="localized_value_id", referencedColumnName="id", onDelete="CASCADE", unique=true)
     *      }
     * )
     */
    protected $titles;

    /**
     * @var string
     *
     * @ORM\Column(name="language_code", type="string", length=16, nullable=false)
     */
    protected $languageCode;

    /**
     * @var string
     *
     * @ORM\Column(name="formatting_code", type="string", length=16, nullable=false)
     */
    protected $formattingCode;

    /**
     * @var Localization
     *
     * @ORM\ManyToOne(targetEntity="Localization", inversedBy="childs")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $parent;

    /**
     * @var Collection|Localization[]
     *
     * @ORM\OneToMany(targetEntity="Localization", mappedBy="parent")
     */
    protected $childs;

    public function __construct()
    {
        $this->childs = new ArrayCollection();
        $this->titles = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
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
     * @param Localization $parent
     *
     * @return $this
     */
    public function setParent(Localization $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Localization
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return Collection|Localization[]
     */
    public function getChilds()
    {
        return $this->childs;
    }

    /**
     * @param Localization $localization
     * @return $this
     */
    public function addChild(Localization $localization)
    {
        if (!$this->childs->contains($localization)) {
            $this->childs->add($localization);
        }

        return $this;
    }

    /**
     * @param Localization $localization
     * @return $this
     */
    public function removeChild(Localization $localization)
    {
        if ($this->childs->contains($localization)) {
            $this->childs->removeElement($localization);
            $localization->setParent(null);
        }

        return $this;
    }

    /**
     * @return Collection|LocalizedFallbackValue[]
     */
    public function getTitles()
    {
        return $this->titles;
    }

    /**
     * @param LocalizedFallbackValue $title
     *
     * @return $this
     */
    public function addTitle(LocalizedFallbackValue $title)
    {
        if (!$this->titles->contains($title)) {
            $this->titles->add($title);
        }

        return $this;
    }

    /**
     * @param LocalizedFallbackValue $title
     *
     * @return $this
     */
    public function removeTitle(LocalizedFallbackValue $title)
    {
        if ($this->titles->contains($title)) {
            $this->titles->removeElement($title);
        }

        return $this;
    }

    /**
     * @param Localization|null $localization
     * @return LocalizedFallbackValue
     */
    public function getTitle(Localization $localization = null)
    {
        return $this->getLocalizedFallbackValue($this->titles, $localization);
    }

    /**
     * @return LocalizedFallbackValue
     */
    public function getDefaultTitle()
    {
        return $this->getLocalizedFallbackValue($this->titles);
    }

    /**
     * @param $string
     * @return $this
     */
    public function setDefaultTitle($string)
    {
        $oldTitle = $this->getDefaultTitle();
        if ($oldTitle) {
            $this->removeTitle($oldTitle);
        }
        $newTitle = new LocalizedFallbackValue();
        $newTitle->setString($string);
        $this->addTitle($newTitle);

        return $this;
    }
}
