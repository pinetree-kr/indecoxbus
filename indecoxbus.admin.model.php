<?php

/**
 * @class  indecoxbusAdminModel
 * @author CRA:pinetree (vanadate.kr@gmail.com)
 * @brief  indecoxbus module의 admin model class
 **/
class indecoxbusAdminModel extends indecoxbus {
	
	/**
	 * @function init
	 * @brief 초기화 함수
	 * response_type에 따라 뿌려주는 데이터 타입이 다르게 만든다.
	 */
	function init() {
	}
	
	/**
	 * @function getIndecoxbusAdminCheckOverlap
	 * @brief 겹치는 버스 이름이 있는지 확인한다.
	 * @return 겹치는 버스 값이 있을 경우 message = success, 그렇지 않을 경우 message = overlaps
	 */
	function getIndecoxbusAdminCheckBusNameOverlap() {
		
		$args->name = Context::get('name');
		$args->name = trim($args->name);
		
		$output = executeQuery('indecoxbus.getBusNameOverlap', $args);
		if (!$output->toBool()) {
			return $output;
		}
		
		// $output->data->count 값이 0이면 겹치는 값이 없다. (0 리턴, success message)
		if (number_format($output->data->count) == 0) {
			return new Object(0, 'success');
		}
		// $output->data->count 값이 1이면 겹치는 값이 있는 것 (0 리턴, overlaps message)
		else {
			return new Object(0, 'overlaps');
		}
		return;
	}
	
	/**
	 * @function getIndecoxbusAdminSearchNameId
	 * @brief member 테이블에서 user_id, user_name, nick_name 컬럼을 like로 검색해서 불러온다.
	 * Enter description here ...
	 */
	function getIndecoxbusAdminSearchNameId() {
		
		$args->s_user_id = $args->s_user_name = $args->s_nick_name = Context::get('key');
		$args->list_count = 7;
		$args->sort_order = 'desc';
		
		// 해당하는 키로 member 테이블에서 검색
		$output = executeQueryArray('indecoxbus.getSearchNameId', $args);
		if (!$output->toBool()) {
			return new Object($output->error, $output->message);
		}
		$this->add('user_info', $output->data);
		return;
	}
}
?>
