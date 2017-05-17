<?php
class PurshaseControl
{
	/**
	* build one case for each method on this class
	*/
	public static function validate($param)
	{
		$debug = debug_backtrace();
		$args = $debug[1]['args'][0];
		$fields = array();

		switch ($debug[1]['function'])
		{
			case 'onPurshase':
				$fields = ['purshase_id' => 'Código',
				           'link'        => 'Link do produto',
				           'price'       => 'Preço'];
				break;
			case 'onRemovePeople':
				$fields = ['purshase_id' => 'Código'];
				break;
			case 'progress':
				$fields = ['id' => 'Código'];
				break;
			case 'cancel':
				$fields = ['id' => 'Código'];
				break;
			case 'editDate':
				$fields = ['id'   => 'Código',
				           'date' => 'Data'];
				break;
			case 'onPeopleWith':
				$fields = ['id'   => 'Código'];
				break;
			default:
				throw new Exception("Comando desconhecido!");
				break;
		}

		foreach($fields as $field => $label)
		{
			if(!isset($args[$field]) || !$args[$field])
				throw new Exception("Parâmetro inválido: {$label}");
		}
	}

	## PurshaseWith actions
	public static function onPurshase($param)
	{
		try
		{
			self::validate($param);
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

	public static function onRemovePeople($param)
	{
		try
		{
			self::validate($param);
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

	public static function onProgress($param)
	{
		$action = new TAction(array('PurshaseControl', 'progress'));
        $action->setParameters($param); // pass the key parameter ahead
        
        // shows a dialog to the user
        new TQuestion(TAdiantiCoreTranslator::translate('Prosseguir para próxima etapa?'), $action);
	}

	public static function progress($param)
	{
		try
		{
			self::validate($param);

			TTransaction::open('ship');

			$purshase = new Purshase($param['id']);

			switch ($purshase->status_id)
			{
				case 1:
					$purshase->getNextStatus();

					break;
				
				default:
					throw new Exception("Ocorreu um erro");
					break;
			}
			$purshase->store();

			new TMessage('info', 'Atualizado!');

			TTransaction::close();
		}
		catch(Exception $e)
		{
			new TMessage('error', $e->getMessage());	
		}
	}

	##Status actions
	public static function onCancel($param)
    {
        $action = new TAction(array('PurshaseControl', 'cancel'));
        $action->setParameters($param); // pass the key parameter ahead
        
        // shows a dialog to the user
        new TQuestion(TAdiantiCoreTranslator::translate('Do you really want to cancel?'), $action);
    }

	public static function cancel($param)
	{
		try
		{
			self::validate($param);
			TTransaction::open('ship');

			$purshase = new Purshase($param['id']);
			$purshase->cancel();

			new TMessage('info', 'Compra cancelada');
			TScript::create("$('#purshase_{$purshase->id}').hide('slow')");

			TTransaction::close();
		}
		catch(Exception $e)
		{
			new TMessage('error', $e->getMessage());
			return false;
		}
	}

	##Fields edit
	public static function editDate($param)
	{
		try
		{
			TTransaction::open('ship');
			self::validate($param);

			if($param['date'] < date('Y-m-d'))
				throw new Exception("A data não pode ser menor que hoje!");
			
			$purshase = new Purshase($param['id']);
			$purshase->date_until = $param['date'];
			$purshase->store();

			new TNotify('success', 'Data alterada com sucesso!');
			//new TMessage('info', 'Data alterada!');

			TScript::create("changeVal('date_{$purshase->id}', '".TDate::date2br($param['date'])."');");

			TTransaction::close();
		}
		catch(Exception $e)
		{
			new TMessage('error', $e->getMessage());
			return false;
		}
	}

	public static function onPeopleWith($param)
	{
		try
		{
			TTransaction::open('ship');
			self::validate($param);

			$purshase = new Purshase($param['id']);

			$table = new TTable('people');
			$table->class = 'table table-striped';
			$row = $table->addRow();
			$row->addCell("<b>Participante</b>");
			$row->addCell("<b>Conf. Depós.</b>");
			$row->addCell("#");

			foreach($purshase->getPeople() as $purshase_with)
			{
				$btn_receipt = new TButton('confirm_receipt');
				$btn_receipt->setImage('fa:check');
				$btn_receipt->class = 'btn btn-success';

				$btn_delete = new TButton('exclude');
				$btn_delete->setImage('fa:check');
				$btn_delete->class = 'btn btn-danger';

				$row = $table->addRow();

				$row->addCell($purshase_with->people->name);
				$row->addCell($btn_receipt);
				$row->addCell($btn_delete);
			}

			new TMessage('info', $table->getContents(), null, 'Participantes', false);

			TTransaction::close();
		}
		catch (Exception $e)
		{
			new TMessage('error', $e->getMessage());
			return false;	
		}
	}
}