<?php

/**
 * GPS 위치를 표현하기 위해서 GLL을 사용하였습니다.
 * GLL 이란 Geographic Position - Latitude / Longitude 를 뜻하고 다음과 같은 꼴을 가지고 있습니다.
 * 최대 54 글자
 * 
 * $GPGLL, 3723.2475, N, 12158.3416, W, 161229.487, A*2C
 * 
 * Name				Example				Units		Description
 * Message ID		$GPGLL							GLL Protocol header
 * Latitude			3723.2475						ddmm.mmmm
 * N/S Indicator	N								E = East, W = West
 * Longitude		12158.3416						dddmm.mmmm
 * E/W Indicator	W								E = East, W = West
 * UTC Position		161229.487						hhmmss.sss
 * Status			A								A = data valid, V = data not valid
 * Checksum			*2C
 * <CR><LF>			\r\n							End of Message termination
 * 
 */


/**
 * @class  indecoxbusModel
 * @author CRA:pinetree (vanadate.kr@gmail.com)
 * @brief  indecoxbus module의 Model class
 **/
class indecoxbusModel extends indecoxbus {
	
	/**
	 * @function init
	 * @brief 초기화 함수
	 * response_type에 따라 뿌려주는 데이터 타입이 다르게 만든다.
	 */
	function init() {
		$response_type = Context::get('response_type');
		// 기본 response를 xml로 던진다.
		if (strtoupper($response_type == "JSON"))
			Context::setRequestMethod('JSON');
		else
			Context::setRequestMethod('XMLRPC');
	}
	
	/**
	 * @function getIndecoxbusState
	 * @brief 버스 name을 받아서 해당하는 버스의 위치, 이름, 상태, 시간, 학생 를 받아온다.
	 */
	function getIndecoxbusState() {
		// TODO 현재 사용자가 로그인 되어있는지 확인한다.
		
		// 데이터를 받아서 조회 후 받아오기
		$args->name = Context::get('name');
		
		$output = executeQuery('indecoxbus.getState', $args);
		if (!$output->toBool()) {
			$this->setError($output->error);
			$this->setMessage($output->message);
			return;
		}
		$this->add('data', $output->data);
		return;
	}
	
	/**
	 * @function getIndecoxbusStateList
	 * @brief 현재 모든 버스들의 상태를 불러온다.
	 */
	function getIndecoxbusStateList() {
		// TODO 현재 사용자가 로그인 되어있는지 확인한다.
		
		// 모든 cra_bus 테이블을 조회 후 받아오기
		$output = executeQueryArray('indecoxbus.getStateList');
		if (!$output->toBool()) {
			$this->setError($output->error);
			$this->setMessage($output->message);
			return;
		}
		foreach ($output->data as $key => $val) {
			$this->add('bus_'.$key, $val);
		}
		return;
	}
	
	function getIndecoxbusTest() {
		$this->add('test', 'zxcvzxcv');
	}
}
?>
