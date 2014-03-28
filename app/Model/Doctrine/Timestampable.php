<?php

namespace Presidos\Model\Doctrine;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

trait Timestampable
{

	/**
	 * @ORM\Column(type="datetimetz")
	 */
	private $inserted;

	/**
	 * @ORM\Column(type="datetimetz")
	 */
	private $updated;

	protected function initDateTimes()
	{
		$this->inserted = $this->updated = new DateTime();
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
	public function getInserted()
	{
		return $this->inserted;
	}

}