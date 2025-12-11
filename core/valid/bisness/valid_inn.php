<?php 

namespace system\core\valid\number;

use system\core\valid\item;

//https://github.com/rin-nas/php-inn/tree/master
class valid_inn extends item
{
    protected string $textError = 'ИНН указан не корректно';

    public function control()
    {
        if ($this->original && !$this->inn($this->original)) {

            $this->setError($this->textError);
            $this->setControl(false);
        }
    }

    public function getResult():mixed
    {
        return ($this->control ? (int) $this->original : null);
    }

	private function inn($n, $type = null)
	{
		if ($n === null) return null;

		$n = strval($n);
		if (! ctype_digit($n)) {
			return false;
		}
		
		//все нули удовлетворяют формуле
		if ((int)$n === 0) {
			return false;
		}
		
		//не может быть региона 00
		if (substr($n, 0, 2) === '00') {
			return false;
		}

		$len = strlen($n);
		
		#10 знаков -- организации, для которых обязательно д.б. КПП
		if ($len === 10)
		{
			$sum = 0;
			foreach ([2, 4, 10, 3, 5, 9, 4, 6, 8] as $i => $weight)
			{
				$sum += $weight * $n[$i];
			}
			return $sum % 11 % 10 === $n[9];
		}

		#12 знаков -- индивидуальные предприниматели, для которых КПП отсутствует
		if ($len === 12)
		{
			$sum1 = 0;
			foreach ([7, 2, 4, 10, 3, 5, 9, 4, 6, 8] as $i => $weight)
			{
				$sum1 += $weight * $n[$i];
			}
			if (($sum1 % 11 % 10) !== $n[10])
			{
				return false;
			}
			
			$sum2 = 0;
			foreach ([3, 7, 2, 4, 10, 3, 5, 9, 4, 6, 8] as $i => $weight)
			{
				$sum2 += $weight * $n[$i];
			}
			if (($sum2 % 11 % 10) !== $n[11])
			{
				return false;
			}
		}
		return false;
	}    
}