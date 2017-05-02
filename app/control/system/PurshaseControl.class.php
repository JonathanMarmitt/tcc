<?php
class PurshaseControl
{
	public static function onPurshase($param)
	{
		try
		{
			TTransaction::open('ship');

			$purshase = new Purshase($param['purshase_id']);

			$link = $param['link'];
			$price = $param['price'];

			$result = $purshase->addPeople(TSession::getValue('fb-id'), $link, $price);

			if($result)
				new TMessage('info', 'Adicionado com sucesso!');

			TTransaction::close();
		}
		catch(Exception $e)
		{
			new TMessage('error', $e->getMessage());
		}
	}

	public static function onCancelPurshase($param)
	{
		try
		{
			TTransaction::open('ship');

			$purshase = new Purshase($param['purshase_id']);

			$result = $purshase->removePeople(TSession::getValue('fb-id'));

			if($result)
				new TMessage('info', 'Removida com sucesso!');			

			TTransaction::close();
		}
		catch (Exception $e)
		{
			new TMessage('error', $e->getMessage());
		}
	}
}