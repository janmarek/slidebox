<?php

namespace SlideBox\Presentation;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use SlideBox\Doctrine\Entity;
use SlideBox\Doctrine\Timestampable;
use SlideBox\User\User;

/**
 * @author Jan Marek
 *
 * @ORM\Table(name="presentation")
 * @ORM\Entity(repositoryClass="SlideBox\Presentation\PresentationRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Presentation extends Entity implements \JsonSerializable
{

	const LOCKED_MINUTES = 15;

	use Timestampable;

	/** @ORM\Column(type="string", nullable=true) */
	private $name;

	/** @ORM\Column(type="text", nullable=true) */
	private $description;

	/** @ORM\Column(type="boolean") */
	private $nameLocked;

	/** @ORM\Column(type="text", nullable=true) */
	private $texy;

	/** @ORM\Column(type="boolean") */
	private $published;

	/**
	 * @ORM\Column(type="datetimetz", name="published_at", nullable=true)
	 */
	private $publishedAt;

	/** @ORM\Column(type="boolean") */
	private $deleted;

	/** @ORM\Column(type="integer") */
	private $visits;

	/** @ORM\ManyToOne(targetEntity="SlideBox\User\User") */
	private $user;

	/**
	 * @ORM\ManyToMany(targetEntity="SlideBox\User\User")
	 * @ORM\JoinTable(name="collaborator")
	 */
	private $collaborators;

	/**
	 * @ORM\ManyToOne(targetEntity="SlideBox\Presentation\ThemeVariant")
	 * @ORM\JoinColumn(name="theme_variant_id")
	 */
	private $themeVariant;

	/**
	 * @ORM\ManyToOne(targetEntity="SlideBox\User\User")
	 * @ORM\JoinColumn(name="last_user_id")
	 */
	private $lastUser;

	public function __construct(User $user)
	{
		$this->user = $user;
		$this->published = FALSE;
		$this->nameLocked = FALSE;
		$this->deleted = FALSE;
		$this->collaborators = new ArrayCollection();
		$this->visits = 0;
		$this->initDateTimes();
	}

	public function setName($name)
	{
		$this->name = $name ?: NULL;
	}

	public function getName()
	{
		return $this->name;
	}

	public function setDescription($description)
	{
		$this->description = $description ?: NULL;
	}

	public function getDescription()
	{
		return $this->description;
	}

	public function setTexy($texy)
	{
		$this->texy = $texy ?: NULL;
	}

	public function getTexy()
	{
		return $this->texy;
	}

	public function getUser()
	{
		return $this->user;
	}

	public function setThemeVariant(ThemeVariant $themeVariant)
	{
		$this->themeVariant = $themeVariant;
	}

	public function getThemeVariant()
	{
		return $this->themeVariant;
	}

	public function publish()
	{
		$this->published = TRUE;
		$this->publishedAt = new \DateTime();
	}

	public function isPublished()
	{
		return $this->published;
	}

	public function getPublishedAt()
	{
		return $this->publishedAt;
	}

	public function isDeleted()
	{
		return $this->deleted;
	}

	public function setDeleted($deleted)
	{
		$this->deleted = (bool) $deleted;
	}

	public function isNameLocked()
	{
		return $this->nameLocked;
	}

	public function lockName()
	{
		$this->nameLocked = TRUE;
	}

	public function getCollaborators()
	{
		return $this->collaborators->toArray();
	}

	public function setCollaborators(array $users)
	{
		$this->collaborators->clear();
		foreach ($users as $user) {
			$this->collaborators->add($user);
		}
	}

	public function removeCollaborator(User $user)
	{
		$this->collaborators->removeElement($user);
	}

	public function increaseVisits(User $user = NULL)
	{
		if (!$this->isEditableBy($user)) {
			$this->visits++;
		}
	}

	public function getVisits()
	{
		return $this->visits;
	}

	public function isEditableBy(User $user = NULL)
	{
		return $user !== NULL && ($this->isOwner($user) || $this->collaborators->contains($user));
	}

	public function isOwner(User $user)
	{
		return $this->user === $user;
	}

	public function getLastUser()
	{
		return $this->lastUser;
	}

	public function lockForEdit(User $user)
	{
		$this->lastUser = $user;
	}

	public function isLockedForEdit(User $user)
	{
		if ($this->lastUser === NULL || $this->lastUser === $user) {
			return FALSE;
		}

		$date = new \DateTime();
		$diff = $date->diff($this->getUpdated());

		return $diff->days === 0 && $diff->h === 0 && $diff->i < self::LOCKED_MINUTES;
	}

	public function jsonSerialize()
	{
		return [
			'id' => $this->getId(),
			'name' => $this->name,
			'nameLocked' => $this->nameLocked,
			'description' => $this->description,
			'texy' => $this->texy,
			'theme' => $this->themeVariant->getTheme(),
			'themeVariant' => $this->themeVariant,
			'user' => $this->user,
			'collaborators' => $this->getCollaborators(),
			'published' => $this->published,
			'updated' => $this->getUpdated()->format('c'),
			'created' => $this->getCreated()->format('c'),
		];
	}

}