<?php

namespace Presidos\Model\Doctrine;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

/**
 * @author Jan Marek
 */
class Repository extends EntityRepository
{

	public function fetchColumn(Query $query, $column)
	{
		$data = array();

		foreach ($query->getScalarResult() as $row) {
			$data[] = $row[$column];
		}

		return $data;
	}

	public function fetchPairs(Query $query, $key, $value)
	{
		$data = array();

		foreach ($query->getArrayResult() as $row) {
			$data[$row[$key]] = $row[$value];
		}

		return $data;
	}

}