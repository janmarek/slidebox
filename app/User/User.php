<?php

namespace SlideBox\User;

use Doctrine\ORM\Mapping as ORM;
use Nette\Security\IIdentity;
use Nette\Utils\Strings;
use SlideBox\Doctrine\Entity;
use SlideBox\Doctrine\Timestampable;

/**
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="SlideBox\User\UserRepository")
 */
class User extends Entity implements IIdentity, \JsonSerializable
{

	use Timestampable;

	/**
	 * @ORM\Column(type="string", length=150)
	 */
	private $name;

	/**
	 * @ORM\Column(type="string", length=60, unique=true)
	 */
	private $email;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $description;

	/**
	 * @ORM\Column(type="string", length=40, nullable=true)
	 */
	private $password;

	/**
	 * @ORM\Column(type="string", length=10)
	 */
	private $salt;

	/**
	 * @ORM\Column(name="facebook_uid", type="string", length=60, nullable=true, unique=true)
	 */
	private $facebookUid;

	/**
	 * @ORM\Column(type="string", length=20, nullable=true)
	 */
	private $hash;

	/**
	 * @ORM\Column(name="is_admin", type="boolean")
	 */
	private $isAdmin;

	/**
	 * @ORM\Column(type="boolean")
	 */
	private $allowed;

	public function __construct()
	{
		$this->salt = Strings::random();
		$this->isAdmin = FALSE;
		$this->allowed = FALSE;
		$this->changeHash();
		$this->initDateTimes();
	}

	public function setFacebookUid($facebookUid)
	{
		$this->facebookUid = $facebookUid;
	}

	public function setName($name)
	{
		$this->name = $name;
	}

	public function getFacebookUid()
	{
		return $this->facebookUid;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getEmail()
	{
		return $this->email;
	}

	public function setEmail($email)
	{
		$this->email = $email;
	}

	public function isAdmin()
	{
		return $this->isAdmin;
	}

	public function getRoles()
	{
		return array($this->isAdmin ? 'admin' : 'user');
	}

	public function checkPassword($password)
	{
		return $this->hashPassword($password) === $this->password;
	}

	private function hashPassword($password)
	{
		return sha1($this->salt . $password);
	}

	public function changePassword($newPassword)
	{
		$this->password = $this->hashPassword($newPassword);
	}

	public function hasPassword()
	{
		return (bool) $this->password;
	}

	public function getHash()
	{
		return $this->hash;
	}

	public function changeHash()
	{
		$this->hash = Strings::random(20);
	}

	public function clearHash()
	{
		$this->hash = NULL;
	}

	public function allow()
	{
		$this->allowed = TRUE;
		$this->hash = NULL;
	}

	public function isAllowed()
	{
		return $this->allowed;
	}

	public function setDescription($description)
	{
		$this->description = $description ?: NULL;
	}

	public function getDescription()
	{
		return $this->description;
	}

	public function jsonSerialize()
	{
		return [
			'id' => $this->getId(),
			'name' => $this->name,
			'email' => $this->email,
			'allowed' => $this->allowed,
			'facebookUid' => $this->facebookUid,
		];
	}

}
