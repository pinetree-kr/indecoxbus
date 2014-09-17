<?php
/**
 * @class  indecoxbusController
 * @author CRA:pinetree (vanadate.kr@gmail.com)
 * @brief  indecoxbus module의 Controller class
 **/
class indecoxbusController extends indecoxbus {
	
	/**
	 * @function init
	 * 초기화 함수
	 */
	function init() {
		// 기본 response를 xml로 던진다.
		$response_type = Context::get('response_type');
		if (strtoupper($response_type) == 'JSON') {
			Context::setRequestMethod('JSON');
		} else {
			Context::setRequestMethod('XMLRPC');
		}
	}
	
	/**
	 * @function procIndecoxbusSetPosition
	 * @brief 버스의 현 위치를 서버에 업데이트 시킨다.
	 */
	function procIndecoxbusUpdateBusState() {
		// TODO 현재 사용자가 로그인 되었는지, 그리고 버스 기사의 아이디인지 확인하기
		
		// 데이터 받아서 업데이트
		$args->name = Context::get('name');
		$args->state = Context::get('state');
		$args->volume = Context::get('volume');
		$args->position = Context::get('position');
		
		$output = executeQuery('indecoxbus.updateBusState', $args);
		return $output;
	}
	
	/**
	 * @function procIndecoxbusLogin
	 * @brief 로그인 한다.
	 */
	function procIndecoxbusLogin() {
		// 데이터 받기...
		$user_id = Context::get('user_id');
		$password = Context::get('password');
		
		// 로그인 하기
		$oMemberController = &getController('member');
		return $oMemberController->procMemberLogin($user_id, $password, $keep_signed);
	}
	
	/**
	 * @function procIndecoxbusLogout
	 * @brief 로그아웃 한다.
	 */
	function procIndecoxbusLogout() {
		$oMemberController = &getController('member');
		return $oMemberController->procMemberLogout();
	}
}
?>
