<?php

namespace Presidos\Doctrine;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

trait Timestampable
{

	/**
	 * @ORM\Column(type="datetimetz")
	 */
	private $created;

	/**
	 * @ORM\Column(type="datetimetz")
	 */
	private $updated;

	protected function initDateTimes()
	{
		$this->created = $this->updated = new DateTime();
	}

	/**
	 * @return DateTime
	 */
	public function getUpdated()
	{
		return $this->updated;
	}

	/**
	 * @ORM\PreUpdate
	 */
	public function update()
	{
		$this->updated = new DateTime();
	}

	/**
	 * @return DateTime
	 */
	public function getCreated()
	{
		return $this->created;
	}

}