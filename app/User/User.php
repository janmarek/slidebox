<?php

namespace Presidos\User;

use Doctrine\ORM\Mapping as ORM;
use Nette\Security\IIdentity;
use Nette\Utils\Strings;
use Presidos\Model\Doctrine\Entity;
use Presidos\Model\Doctrine\Timestampable;

/**
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="Presidos\User\UserRepository")
 */
class User extends Entity implements IIdentity
{

	use Timestampable;

	/**
	 * @ORM\Column(type="string", length=150)
	 */
	private $name;

	/**
	 * @ORM\Column(type="string", length=60)
	 */
	private $email;

	/**
	 * @ORM\Column(type="string", length=40, nullable=true)
	 */
	private $password;

	/**
	 * @ORM\Column(type="string", length=10)
	 */
	private $salt;

	/**
	 * @ORM\Column(name="facebook_uid", type="string", length=60, nullable=true)
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

}
