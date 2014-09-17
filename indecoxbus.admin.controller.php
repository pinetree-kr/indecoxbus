<?php
/**
 * @class  indecoxbusAdminController
 * @author CRA:pinetree (vanadate.kr@gmail.com)
 * @brief  indecoxbus module's admin controller class
 **/

class indecoxbusAdminController extends indecoxbus {
	 
	/**
	 * @brief initialization
	 **/
	function init() {
		
	}
	
	/**
	 * @function dispIndecoxbusAdminInsert
	 * @brief 버스를 새로 추가시킨다.
	 */
	function procIndecoxbusAdminInsert() {
		
		$isInsert = false;
		
		// 기본 값 받아서 $args변수에 넣기
		$args->name = Context::get('name');
		$args->max_volume = Context::get('max_volume');
		$args->bus_srl = Context::get('bus_srl');
		$args->member_srl = Context::get('member_srl');
		
		if (!$args->bus_srl) {
			$args->bus_srl = getNextSequence();
			$isInsert = true;
		}
		
		// 그 외에 필요한 값들 등록
		$args->state = "A";
		$args->volume = 0;
		$args->position = "";
		
		// Data validation
		// 1.5 버전이라면 룰셋에서 처리해 주지만 1.4일 경우 룰셋에서 처리를 못하므로 알아서 처리해 줘야 한다.
		if (number_format(__ZBXE_VERSION__) > 1.5) {
			
		}
		if ($isInsert) {
			// db에 쓰기
			$output = executeQuery('indecoxbus.insertBus', $args);
			if ($output->error) {
				$this->setError(0);
				$this->setMessage($output->variables['_query']);
				return;
			}
		} else {
			// db에 수정하기
			$output = executeQuery('indecoxbus.updateBus', $args);
			if ($output->error) {
				$this->setError(0);
				$this->setMessage($output->variables['_query']);
				return;
			}
		}
		return;
	}
	
	/**
	 * @function procIndecoxbusAdminDelete
	 * @brief bus_srl을 받아서 해당하는 버스를 삭제
	 */
	function procIndecoxbusAdminDelete() {
		$args->bus_srl = Context::get('bus_srl');
		if (!$args->bus_srl) {
			$this->setError(-1);
			$this->setMessage('Invalid bus_srl');
			return $this;
		}
		$output = executeQuery('indecoxbus.deleteBus', $args);
		$url = getUrl('', 'module', 'admin', 'act', 'dispIndecoxbusAdminList');
		$this->setRedirectUrl(htmlspecialchars_decode($url));
		return;
	}
}
?>
